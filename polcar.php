<?php

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="sample.csv"');

$typeDatas = "B:\\FGergo\\TECDOC KTYP lista\\2025.02.25\\td_type_datas.csv";
$polcarBody = "C:\Users\LP-KATALOGUS1\Desktop\Copy of Body parts and lighting data SZAKAL.csv";

$convert = array(
    "COUPE" => "Kupé",
    "CABRIO" => "Kabrió",
    "GT" => "Gran Turismo",
    "GC" => "Gran Coupe"
);

function getSpecificationTypeData($data)
{
    $return = array(NULL);
    $start = strpos($data, " ", 0);
    $end = strpos($data, "(", 1);
    $len = $end - $start;
    if ($len <= 1)
        return $return;
    $result = substr($data, $start + 1, $len - 2);
    array_push($return, $result);
    return $return;
}

function splitTypeData($data)
{
    $start = strpos($data, "(", 1);
    $end = strpos($data, ")", $start + 1);
    $len = $end - $start;
    $result = substr($data, $start + 1, $len - 1);
    return explode(",", $result);
}

function constructNewName($data, $newName, $spec = "")
{
    $start = strpos($data, " ", 0);
    $first = substr($data, 0, $start);
    if (strlen($first) > 3)
        $first = substr($data, 0, $start-1);
    //$first = str_replace($explode[count($explode)-1], "", $data);
    if ($spec == NULL)
        return $first."  (".trim($newName).")"; //Miért kell extra szóköz??
    else
        return $first." ".$spec." (".trim($newName).")";
}

class TypeData
{
    public $brand;
    public $typ;
    public $fullname;
    public $manuf_from;
    public $manuf_to;
    public $ktypnr;

    public function __construct(string $_brand, string $_typ, string $_fullname, string $_manuf_from, string $_manuf_to, string $_ktypnr)
    {
        $this->brand = $_brand;
        $this->typ = $_typ;
        $this->fullname = $_fullname;
        $this->manuf_from = $_manuf_from;
        $this->manuf_to = $_manuf_to;
        $this->ktypnr = $_ktypnr;
    }
}

class PolcarData
{
    public $beszcodes = array();
    public $leading;
    public $types = array();
    public $brandings = array();
    public $startDate;
    public $endDate;
    public $savedData;
    public $matches = array();

    public function __construct($data, $carBrand, $typeNames, $beszcode)
    {
        $this->addBeszcode($beszcode);
        $this->savedData = $data;
        $this->getLeading($data);
        if ($carBrand == "BMW")
        {
            $this->getTypes($data);
            $this->convertTypeNames($typeNames);
        }
        $this->getBrandings($data);
    }

    function convertTypeNames($convert)
    {
        $arr = array();
        foreach ($this->types as $value) {
            $value = trim($value);
            if (key_exists($value, $convert))
                array_push($arr, $convert[$value]);
            else
            array_push($arr, $value);
        }
        array_push($arr, "");
        $this->types = $arr;
    }

    function getTypes($data)
    {
        $firstSpace = strpos($data, " ", 1);
        $firstParant = strpos($data, "(", $firstSpace + 1);
        $len = $firstParant - $firstSpace;
        if ($len > 1)
        {
            $result = substr($data, $firstSpace + 1, $len - 1);
            $this->types = explode("/", $result);
            return;
        }
        $lastParant = strpos($data, ")", 1);
        $lastComma = strrpos($data, ",", $lastParant + 1);
        $len = $lastComma - $lastParant;
        if ($len > 1)
        {
            $result = substr($data, $lastParant + 2, $len - 2);
            $this->types = explode("/", $result);
            return;
        }
    }

    function getLeading($data)
    {
        $firstWord = explode("/", explode(" ", $data)[0])[0];
        //Validation goes here
        $this->leading = $firstWord;
    }

    function getBrandings($data)
    {
        $start = strpos($data, "(", 1);
        $end = strpos($data, ")", $start + 1);
        $len = $end - $start;
        $result = substr($data, $start + 1, $len - 1);
        $this->brandings = explode("/", $result);
    }

    public function getAllCombinations()
    {
        $combinations = array();

        if (count($this->types) > 0)
        {
            foreach ($this->types as $type) {
                foreach ($this->brandings as $branding) {
                    $string = $this->leading." ".$type." (".$branding.")";
                    array_push($combinations, $string);
                }
            }
        }
        else
        {
            foreach ($this->brandings as $branding) {
                $string = $this->leading." (".$branding.")";
                array_push($combinations, $string);
            }
        }

        return $combinations;
    }

    public function tryAddMatch(TypeData $typeData)
    {
        if (key_exists($typeData->ktypnr, $this->matches))
            return;
        $this->matches[$typeData->ktypnr] = $typeData;
    }

    public function addBeszcode($code)
    {
        if (in_array($code, $this->beszcodes, true))
            return;
        array_push($this->beszcodes, $code);
    }
}

$arr = array();
$typesByBrand = array();

$handle = fopen($typeDatas, "r");
$lastKey = "ABARTH";
fgetcsv($handle);
while (($row = fgetcsv($handle, null, ";")) !== FALSE) {
    if ($row[0] != $lastKey)
    {
        //echo $lastKey."<br/>";
        $typesByBrand[$lastKey] = $arr;
        $arr = array();
        $lastKey = $row[0];
    }
    $names = splitTypeData($row[1]);
    $specs = getSpecificationTypeData($row[1]);
    foreach ($names as $value) {
        foreach ($specs as $spec)
        {
            $name = constructNewName($row[1], $value, $spec);
            //echo $name."<br/>";
            $data = new TypeData(
                $row[0],
                $name,
                $row[2],
                $row[3],
                $row[4],
                $row[13]
            );
            array_push($arr, $data);
        }
    }
}
fclose($handle);

$arr = array();
$polcarDataByBranding = array();

$handle = fopen($polcarBody, "r");
$lastKey = "ACURA";
fgetcsv($handle);fgetcsv($handle);
while (($row = fgetcsv($handle, null, ";")) !== FALSE) {
    if ($row[6] != $lastKey)
    {
        $polcarDataByBranding[$lastKey] = $arr;
        if (key_exists($row[6], $polcarDataByBranding))
            $arr = $polcarDataByBranding[$row[6]];
        else
            $arr = array();
        $lastKey = $row[6];
    }
    if (key_exists($row[7], $arr))
    {
        //echo $row[7];
        $arr[$row[7]]->addBeszcode($row[0]);
        continue;
    }
    $data = new PolcarData(
        $row[7],
        $row[6],
        $convert,
        $row[0]
    );
    $arr[$row[7]] = $data;
}
fclose($handle);

foreach ($polcarDataByBranding["BMW"] as $polcar) {
    foreach ($polcar->getAllCombinations() as $combo) {
        foreach ($typesByBrand["BMW"] as $type) {
            if ($combo == "Z4 (E85)")
                echo $combo." -> ".$type->typ;
            if ($combo == $type->typ)
                $polcar->tryAddMatch($type);
        }
    }
}

/*foreach ($polcarDataByBranding["BMW"] as $value) {
    if (count($value->matches) > 0)
        echo $value->savedData." matches with ".count($value->matches)." ; example (".array_rand($value->matches).")"."<br/>";
    else
        echo $value->savedData." matches with ".count($value->matches)."<br/>";
}*/

$fp = fopen('php://output', 'wb');

foreach ($polcarDataByBranding["BMW"] as $polcar) {
    foreach ($polcar->beszcodes as $beszcode)
    {
        foreach ($polcar->matches as $match)
        {
            $arr = array(
                $beszcode,
                $match->ktypnr
            );
            fputcsv($fp, $arr, ';');
        }
    }
}
fclose($fp);

?>