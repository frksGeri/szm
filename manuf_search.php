<pre
<?php

/* Végig nézi az adott árlista fájlt, és a benne található vicces gyártó neveket megkísérli szakál gyártókhoz kötni */
/* Az összekötött vicces gyártó neveket felrakjuk DB-be, későbbi felhasználásra */
/*  NE HASZNÁLD */

$szakalManufacturers_FILE = "C:\Users\LP-KATALOGUS1\Desktop\GYARTOK.csv";   //Ezt át kellene írni DB beolvasásra. DB aktiálisabb. Vagy, szedd le a DB adatokat ebbe a fájlba.
$compareManufacturers_FILE = "A:\gerison_arlista\lang_arak.csv";
$compareManufacturers_FILE_SEPERATOR = ";";

class SzakalManuf
{
    public $name;
    public $manuf_code;
    public $tecdoc_id;
    public $utotag;
    public $shortName;
    public $matchedNames = array();

    public function __construct($name, $manuf_code, $tecdoc_id, $utotag, $shortName, $sqlResults) {
        $this->name = $name;
        $this->manuf_code = $manuf_code;
        $this->tecdoc_id = $tecdoc_id;
        $this->utotag = $utotag;
        $this->shortName = $shortName;
    }

    function parseSqlResult($sqlResults)
    {
        $this->matchedNames = array_values(json_decode($sqlResults->fetch_assoc()["matched_strings"], true));
    }

    public function addMatch($name)
    {
        if (in_array($name,$this->matchedNames))
            return;
        array_push($this->matchedNames,$name);
    }
}

class ComparisonManuf
{
    public $name;
    public $possibleNames = array();
    public $match = null;

    public function __construct($name) {
        $this->name = $name;
        array_push($this->possibleNames,$name);
        $this->oeCode($name);
        $this->addExplode($name);
        $this->clean($name);
    }

    public function setMatch($match)
    {
        $this->match = $match;
    }

    function oeCode($name)
    {
        array_push($this->possibleNames,"OE ".$name);
        array_push($this->possibleNames,$name." OE");
    }

    function addExplode($name)
    {
        $e = explode(" ",$name);
        if (count($e)>1)
            foreach($e as $new)
                array_push($this->possibleNames,$new);
    }

    function clean($name)
    {
        array_push($this->possibleNames,str_replace(".","",$name));
        array_push($this->possibleNames,str_replace("_","",$name));
        array_push($this->possibleNames,str_replace("."," ",$name));
        array_push($this->possibleNames,str_replace("_"," ",$name));
    }
}

$fetch = mysqli_connect("131.0.1.92", "robi", "", "newszmdb");
if($fetch === false){
    die("ERROR: Could not connect. " 
        . mysqli_connect_error());
}

$szakalManufacturers = array();
$handle = fopen($szakalManufacturers_FILE, "r");
fgetcsv($handle);
while (($row = fgetcsv($handle, null, ";")) !== FALSE)
{
    $sqlResult = array();
    if (strlen($row[0]) == 0 ||$row[0][0] == "*")
        continue;
    try {
        $sqlResult = $fetch->query("SELECT matched_strings FROM gyartok WHERE nev='$row[0]'");
    } catch (Exception $e){
        echo $e;

    }
    $data = new SzakalManuf(
        $row[0],
        $row[2],
        $row[3],
        $row[4],
        $row[1],
        $sqlResult
    );
    array_push($szakalManufacturers, $data);
}
fclose($handle);

$compareManufacturers = array();
$handle = fopen($compareManufacturers_FILE, "r");
$manufCol = 9;
fgetcsv($handle);
while (($row = fgetcsv($handle, null, ";")) !== FALSE)
{
    if (key_exists($row[$manufCol],$compareManufacturers) || $row[$manufCol] == "")
        continue;
    $data = new ComparisonManuf(
        $row[$manufCol] //HÁNYADIK OSZLOPBAN VAN A GYÁRTÓ
    );
    $compareManufacturers[$row[$manufCol]] = $data;
}
fclose($handle);

foreach($compareManufacturers as $compare)
{
    foreach($szakalManufacturers as $szakal)
    {
        if ($compare->match != null)
            break;
        foreach($compare->possibleNames as $name)
        {
            if (strtoupper($name) == strtoupper($szakal->name) || strtoupper($name) == strtoupper($szakal->shortName))
                {$compare->match = $szakal; $szakal->addMatch($compare->name); break;}
        }
    }
}

/*foreach($compareManufacturers as $compare)
    if ($compare->match == null)
        echo "Compare ".$compare->name." matched with ".$compare->match->name."<br/>";*/
$conn = mysqli_connect("131.0.1.92", "robi", "", "newszmdb");
if($conn === false){
    die("ERROR: Could not connect. " 
        . mysqli_connect_error());
}

foreach ($szakalManufacturers as $szakal)
{
    if (count($szakal->matchedNames) == 0)
        continue;
    $json = json_encode($szakal->matchedNames, JSON_FORCE_OBJECT);
    $sql = "UPDATE gyartok SET matched_strings='$json' WHERE nev = '$szakal->name'";
    mysqli_query($conn, $sql);
}

?>