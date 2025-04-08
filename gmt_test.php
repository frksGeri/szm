<pre>
<?php

class SzakalGyarto
{
    public $nev;
    public $name_short;
    public $gy_kod;
    public $tecdoc_id;
    public $utotag;
    public $matchedStrings = array();

    function __construct($nev, $name_short, $gy_kod, $tecdoc_id, $utotag, $matchedStrings) {
        $this->nev = $nev;
        $this->$name_short = $name_short;
        $this->gy_kod = $gy_kod;
        $this->tecdoc_id = $tecdoc_id;
        $this->utotag = $utotag;
        if ($matchedStrings != null)
            $this->matchedStrings = array_values(json_decode($matchedStrings, true));
    }
}

$compareManufacturers_FILE = "Y:\gerison_arlista\gmt_master.csv";

$conn = mysqli_connect("localhost", "root", "", "newszmdb");
if($conn === false){
    die("ERROR: Could not connect. " 
        . mysqli_connect_error());
}

$sql = "SELECT * FROM gyartok";
$result = $conn->query($sql);

$szakalManufacturers = array();
if ($result->num_rows > 0) {
// output data of each row
    while($row = $result->fetch_assoc()) {
        array_push($szakalManufacturers, new SzakalGyarto($row["nev"], $row["name_short"], $row["gy_kod"], $row["tecdoy_id"], $row["utotag"], $row["matched_strings"]));
    }
}

$handle = fopen($compareManufacturers_FILE, "r");
$uniques = array("start" => "beszallito_gyarto;szakal_gyarto;szakal_shortname;tecdoc_id;utotag\n");
fgetcsv($handle);
while (($row = fgetcsv($handle, null, ";")) !== FALSE)
{
    foreach ($szakalManufacturers as $szakal)
    {
        if (count($szakal->matchedStrings) == 0)
            continue;
        if (in_array($row[3],$szakal->matchedStrings))
        {
            if (!key_exists($row[3],$uniques))
                $uniques[$row[3]] = $row[3].";".$szakal->nev.";".$szakal->name_short.";".$szakal->tecdoc_id.";".$szakal->utotag."\n";
            break;
        }
    }
}

fclose($handle);

$txt = implode("\n",array_values($uniques));
file_put_contents("c:\Users\LP-GERGO\Desktop\gmt_brand.csv",$txt);

?>