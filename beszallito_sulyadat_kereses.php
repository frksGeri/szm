<?php

$szPeti = "B:\szerző peti\beszkod\gmt.csv";
$fromFile = "c:\Users\LP-KATALOGUS1\Desktop\gmt_master.csv";

$fromFile_eleresek = array(
    "beszcode" => 0,
    "suly" => 10
);

function convertToNumber($value)
{
    if (is_numeric($value)) {
        return (float)$value;
    }


    $value = str_replace(',', '.', $value);


    $value = preg_replace('/[^0-9.]/', '', $value);

    return is_numeric($value) ? (float)$value : 0;
}

$handle = fopen($szPeti, "r");
$glids = array();
fgetcsv($handle);
while (($row = fgetcsv($handle, null, ";")) !== FALSE)
{
    $glids[$row[0]] = $row[1];
}
//$uniques = $row[0].";"."ERROR".";".$row[5]."\n";
fclose($handle);

$handle = fopen($fromFile, "r");
$str = "suppcode;glid;weight\n";
fgetcsv($handle);
while (($row = fgetcsv($handle, null, ";")) !== FALSE)
{
    if (!key_exists($row[$fromFile_eleresek["beszcode"]],$glids))
        continue;
    if (!key_exists($fromFile_eleresek["suly"],$row))
        continue;
    if ($row[$fromFile_eleresek["suly"]] == "")
        continue;
    $weight = ($row[$fromFile_eleresek["suly"]] != "") ? convertToNumber($row[10]) : 0;
    $weight = $weight * 1000;
    $weight = ($weight < 20) ? 20 : $weight;
    $str .= $row[$fromFile_eleresek["beszcode"]].";".$glids[$row[$fromFile_eleresek["beszcode"]]].";".$weight."\n";
}
//$uniques = $row[0].";"."ERROR".";".$row[5]."\n";
fclose($handle);
file_put_contents("C:\\Users\\LP-KATALOGUS1\\Desktop\\exports\\gmt_weights_v2.csv",$str);

?>