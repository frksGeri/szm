<?php

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="bmw.csv"');

$typeDatas = "B:\\FGergo\\TECDOC KTYP lista\\2025.02.25\\td_type_datas.csv";
$polcarBody = "C:\Users\LP-KATALOGUS1\Desktop\Copy of Body parts and lighting data SZAKAL.csv";

$manufRenames = array(
    "VOLKSWAGEN" => "VW",
    "MERCEDES" => "MERCEDES-BENZ",
    "ROVER/MG" => "ROVER",
    "ROLLS ROYCE" => "ROLLS-ROYCE"
);

$convert = array(
    "COUPE" => "Kupé",
    "CABRIO" => "Kabrió",
    "GT" => "Gran Turismo",
    "GC" => "Gran Coupe"
);

$hasTrailing = array(
    "AUDI",
    "PEUGEOT",
    "VW"
);

$specAsType = array(
    "AUDI"
);

function splitLeadingNames($leading)
{
    trim($leading, " ");
    if (str_contains($leading, "/"))
        return explode("/",$leading);
    if (str_contains($leading, " "))
        return explode(" ",$leading);
    return array($leading);
}

function getSpecificationTypeData($data)
{
    if (!str_contains($data, "("))
        return array("");
    $return = array("");
    $start = strpos($data, " ", 0);
    $end = strpos($data, "(", 1);
    $len = $end - $start;
    if ($len <= 1)
        return array(NULL);
    $result = substr($data, $start + 1, $len - 2);
    if ($result == "I" || $result == "II" || $result == "III" || $result == "IV" || $result == "V" || $result == "VI" || $result == "VII" || $result == "VII" || $result == "VIII" || $result == "IX" || $result == "X")
        return array(null);
    array_push($return, $result);
    if (str_contains($result, " "))
    {
        $exploded = explode(" ", $result);
        foreach ($exploded as $explode)
            if ($explode == "I" || $explode == "II" || $explode == "III" || $explode == "IV" || $explode == "V" || $explode == "VI" || $explode == "VII" || $explode == "VII" || $explode == "VIII" || $explode == "IX" || $explode == "X")
                continue;
            else
                array_push($return,$explode);
    }
    return $return;
}

function removeTrailings($explodeData)
{
    $return = array();
    foreach ($explodeData as $data)
    {
        array_push($return,$data);
        array_push($return,substr($data, 0, -1));
        $len = strlen($data);
        array_push($return,substr($data, 1, $len-1));
    }
    return $return;
}

function sanetizeType($types)
{
    $return = array();
    foreach ($types as $type)
    {
        if (str_contains($type,"_")) array_push($return,$type);
        while (substr($type,-1) == "/" || substr($type,-1) == " " || substr($type,-1) == "_")
            $type = substr($type,0,-1);
        array_push($return,$type);
    }
    return $return;
}

function splitTypeData($data, $removeTrailings = false)
{
    if (!str_contains($data, "("))
        return array("");
    $start = strpos($data, "(", 1);
    $end = strpos($data, ")", $start + 1);
    $len = $end - $start;
    $result = substr($data, $start + 1, $len - 1);
    $return = explode(",", $result);
    if ($removeTrailings)
        $return = removeTrailings($return);
    $return = sanetizeType($return);
    if (count($return) > 0)
        array_push($return, "");
    return $return;
}

function sanetizeFirst($first)
{
    while (substr($first,-1) == "/" || substr($first,-1) == " " || substr($first,-1) == ",")
        $first = substr($first,0,-1);
    return $first;
}

function constructNewName($data, $newName, $spec = "")
{
    if ($spec == "/") $spec = "";
    if (str_contains($data,"("))
    {
        $start = strpos($data, " ", 0);
        $first = substr($data, 0, $start);
        $first = sanetizeFirst($first);
        /*if (strlen($first) > 3)
            $first = substr($data, 0, $start-1);*/
        //$first = str_replace($explode[count($explode)-1], "", $data);
        if ($spec == NULL && $newName == "")
            return array($first);
        if ($spec == NULL)
            return array($first." (".trim($newName, " ").")");
        if ($newName == "")
            return array($first." ".$spec);
        if ($spec == $newName)
            return array($first." (".trim($newName).")");
        return array($first." ".$spec." (".trim($newName).")");
    }

    $leadings = splitLeadingNames($data);
    $return = array();
    foreach ($leadings as $leading)
    {
        $str = $leading;
        if ($spec != "")
            $str = $leading." ".$spec;
        array_push($return,$str);
        if (count($leadings) > 1)
            foreach ($leadings as $extra)
                if ($leading != $extra)
                    array_push($return, $leading." (".$extra.")");
    }
    return $return;
}

class TypeData
{
    public $brand;
    public $typ;
    public $fullname;
    public $manuf_from;
    public $manuf_to;
    public $ktypnr;
    public $specs = array();
    public $matchedCombo;

    public function __construct(string $_brand, string $_typ, string $_fullname, string $_manuf_from, string $_manuf_to, string $_ktypnr, array $specs)
    {
        $this->brand = $_brand;
        $this->typ = $_typ;
        $this->fullname = $_fullname;
        $this->manuf_from = $_manuf_from;
        $this->manuf_to = $_manuf_to;
        $this->ktypnr = $_ktypnr;
        $this->specs = $specs;
    }

    public function setMatchCombo($combo)
    {
        $this->matchedCombo = $combo;
    }
}

class PolcarData
{
    public $beszcodes = array();
    public $leadings = array();
    public $types = array();
    public $brandings = array();
    public $startDate;
    public $endDate;
    public $savedData;
    public $matches = array();

    public function __construct($data, $carBrand, $typeNames, $beszcode)
    {
        $data = $this->removeDoubleParenthesis($data);
        $this->addBeszcode($beszcode);
        $this->savedData = $data;
        $this->getLeadings($data);
        if ($carBrand == "BMW")
        {
            $this->getTypes($data);
            $this->convertTypeNames($typeNames);
        }
        $this->getBrandings($data);
    }

    function removeDoubleParenthesis($data)
    {
        if (!str_contains($data,"))"))
            return $data;
        $firstPos = strpos($data,"(");
        $data = substr_replace($data,'',$firstPos,1);
        $lastPos = strrpos($data,")");
        $data = substr_replace($data,'',$lastPos,1);

        return $data;
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
        //array_push($arr, "");
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
            if ($result == "I" || $result == "II" || $result == "III" || $result == "IV" || $result == "V" || $result == "VI" || $result == "VII" || $result == "VII" || $result == "VIII" || $result == "IX" || $result == "X")
                return array();
            $this->types = explode("/", $result);
            return;
        }
        $lastParant = strpos($data, ")", 1);
        $lastComma = strrpos($data, ",", $lastParant + 1);
        $len = $lastComma - $lastParant;
        if ($len > 1)
        {
            $result = substr($data, $lastParant + 2, $len - 2);
            if ($result == "I" || $result == "II" || $result == "III" || $result == "IV" || $result == "V" || $result == "VI" || $result == "VII" || $result == "VII" || $result == "VIII" || $result == "IX" || $result == "X")
                return array();
            $this->types = explode("/", $result);
            return;
        }
    }

    function getLeadings($data)
    {
        $this->leadings = explode("/", explode(" ", $data)[0]);
        //Validation goes here
        //$this->leadings = $firstWord;
    }

    function wildCardCheck(array $brandings)
    {
        $return = array();
        foreach($brandings as $branding)
        {
            if (substr($branding,-1) != "_")
            {
                array_push($return,$branding);
                continue;
            }
            $str = substr($branding,0,-1);
            foreach (range('A', 'Z') as $wildcard)
                array_push($return,$str.$wildcard);
            foreach (range(0, 9) as $wildcard)
                array_push($return,$str.$wildcard);
            array_push($return,$branding);
        }
        return $return;
    }

    function getBrandings($data)
    {
        if (!str_contains($data,"("))
            return;
        $start = strpos($data, "(", 1);
        $end = strpos($data, ")", $start + 1);
        $len = $end - $start;
        $result = substr($data, $start + 1, $len - 1);
        $return = explode("/", $result);
        $return = $this->wildCardCheck($return);
        $this->brandings = $return;
        /*if (count($this->brandings) > 0)
            array_push($this->brandings, "");*/
    }

    public function getAllCombinations()
    {
        $combinations = array();

        if (count($this->types) > 0)
        {
            foreach ($this->types as $type) {
                foreach ($this->brandings as $branding) {
                    foreach ($this->leadings as $leading)
                    {
                        $string = $leading." ".$type." (".$branding.")";
                        if ($branding == "")
                            $string = $leading.$type;
                        array_push($combinations, $string);
                    }
                }
            }
        }
        else if (count($this->brandings) > 0)
        {
            foreach ($this->brandings as $branding) {
                foreach ($this->leadings as $leading)
                {
                    $string = $leading." (".$branding.")";
                    if ($branding == "")
                        $string = $leading;
                    array_push($combinations, $string);
                }
            }
        }
        else
        {
            foreach ($this->leadings as $leading)
            {
                $string = trim($leading,",");
                array_push($combinations, $string);
            }
        }

        return $combinations;
    }

    public function tryAddMatch(TypeData $typeData, $combo)
    {
        if (key_exists($typeData->ktypnr, $this->matches))
            return;
        //We create typeDatas with only leading characters, even when we have more data
        //But sometimes, leading data is all we have
        /*if (str_contains($typeData->fullname,"(") && !str_contains($typeData->typ," "))
            foreach ($this->matches as $match)
                if ($match->typ == $typeData->typ && !str_contains($match->fullname, "("))
                    return;*/
        if (count($typeData->specs)>0)
            foreach ($this->matches as $match)
                if ($match->typ == $typeData->typ && count($match->specs) == 0)
                    return;
        $typeData->setMatchCombo($combo);
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
    $names = splitTypeData($row[1], in_array($row[0],$hasTrailing));
    $specs = getSpecificationTypeData($row[1]);
    //if (in_array($row[0],$specAsType))
    foreach ($specs as $spec)
        if ($spec != "")
            array_push($names, $spec);
    foreach ($names as $value) {
        foreach ($specs as $spec)
        {
            $names = constructNewName($row[1], $value, $spec);
            foreach ($names as $name)
            {
                //echo $name."<br/>";
                $data = new TypeData(
                    $row[0],
                    $name,
                    $row[2],
                    $row[3],
                    $row[4],
                    $row[13],
                    $names
                );
                array_push($arr, $data);
            }
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

//$manuf = "VOLKSWAGEN";
foreach (array_keys($polcarDataByBranding) as $manuf) {
    foreach ($polcarDataByBranding[$manuf] as $polcar) {
        foreach ($polcar->getAllCombinations() as $combo) {
            foreach ($typesByBrand[(key_exists($manuf,$manufRenames)) ? $manufRenames[$manuf] : $manuf] as $type) {
                //if ($combo == "PASSAT (36)")
                //    echo $combo." -> ".$type->typ." ; ".$type->fullname." ".$type->ktypnr."<br/>";
                //echo $combo."<br/>";
                if ($combo == $type->typ)
                    $polcar->tryAddMatch($type, $combo);
            }
        }
    }
}

/*foreach ($polcarDataByBranding[$manuf] as $value) {
    if (count($value->matches) > 0)
        echo $value->savedData." matches with ".count($value->matches)." ; example (".array_rand($value->matches).")"."<br/>";
    else
        echo $value->savedData." matches with ".count($value->matches)."<br/>";
}*/

$fp = fopen('php://output', 'wb');
fputcsv($fp,array("beszcode","ktypnr","typ","matchedCombo","fullname","brand","polcarData"),";");
//fputcsv($fp,array("polcarData","combos"),";");
foreach (array_keys($polcarDataByBranding) as $manuf) {
    foreach ($polcarDataByBranding[$manuf] as $polcar) {
        foreach ($polcar->beszcodes as $beszcode)
        {
            foreach ($polcar->matches as $match)
            {
                $arr = array(
                    $beszcode,
                    $match->ktypnr,
                    /*$match->typ,
                    $match->matchedCombo,
                    $match->fullname,
                    $match->brand,
                    $polcar->savedData*/
                );
                fputcsv($fp, $arr, ';');
            }
        }
        /*if (count($polcar->matches) == 0)
        {
            $arr = array($polcar->savedData);
            foreach ($polcar->beszcodes as $combo)
                array_push($arr,$combo);
            fputcsv($fp,$arr,";");
        }*/
    }
}
fclose($fp);

?>