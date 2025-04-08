<pre>
<?php

/* * * * * * * * * * * * * */
//Olyan fájlokon használjuk, ahol a beszállító nagyon rossz súlyadatod ad meg (pl. 10 tonna egy csavarra)
//A beszállítói fájlnak tartalmazni kell egy "termékcsoport" oszlopot

$filePath = "C:\Users\LP-KATALOGUS1\Desktop\Copy of article info EAN-HS code-COO-Weight-Dimensions 12.02.2025.csv";

$eleresek = array(
    "code" => '0',
    "tcs" => '1',
    "weight" => '5'
);

/* * * * * * * * * * * * * */

function convertToNumber($value)
{
    if (is_numeric($value)) return (float)$value;

    $value = str_replace(',', '.', $value);
    $value = preg_replace('/[^0-9.]/', '', $value);
    return is_numeric($value) ? (float)$value : 0;
}

function EvaluateWeight($tcs, $weight, $tcsWeightData)
{
    foreach ($tcsWeightData[$tcs] as $key => $value) {
        if ($weight>$key*25)
            continue;
        if ($weight<$key/25)
        array_push($tcsWeightData[$tcs][$key], $weight);
        return $tcsWeightData;
    }
    $tcsWeightData[$tcs][$weight] = array($weight);
    return $tcsWeightData;
}

ob_implicit_flush(true);
echo "reading<br/>";
$tcsWeightData = array();

//Ez történik mikor lusta vagy OOP kódot írni
//Absolute cinema
$handle = fopen($filePath, "r");
fgetcsv($handle);
while (($row = fgetcsv($handle, null, ";")) !== FALSE)
{
    if ($row[$eleresek["weight"]] == "") continue;
    $weight = convertToNumber($row[$eleresek["weight"]]);
    if ($weight == 0) continue;
    $tcs = $row[$eleresek["tcs"]];
    if (key_exists($tcs, $tcsWeightData))
        $tcsWeightData = EvaluateWeight($tcs, $weight, $tcsWeightData);
    else
        $tcsWeightData["validWeights"] = array($weight => array($weight));
}
fclose($handle);
echo count($tcsWeightData)."<br/>";

echo "evaluating<br/>";
foreach ($tcsWeightData as $tcs => $values) {
    $most = 0;
    $mostKey = 0;
    foreach (array_keys($values) as $key) {
        if (count($tcsWeightData[$tcs][$key]) > $most) {$mostKey = $key; $most = count($tcsWeightData[$tcs][$key]); }
    }
    $tcsWeightData[$tcs]["most"] = $mostKey;
}

echo "writing<br/>";
$newHandle = fopen('C:\Users\LP-KATALOGUS1\Desktop\exports\\wilmink_sulyadat_fixed.csv', 'w');
$handle = fopen($filePath, "r");
fgetcsv($handle);
while (($row = fgetcsv($handle, null, ";")) !== FALSE)
{
    if ($row[$eleresek["weight"]] == "") continue;
    $weight = convertToNumber($row[$eleresek["weight"]]);
    $w = $weight * 1000;
    if ($weight == 0) continue;
    $tcs = $row[$eleresek["tcs"]];
    $most = $tcsWeightData[$tcs]["most"];
    if (in_array($weight, $tcsWeightData[$tcs][$most]))
        fputcsv($newHandle, array($row[$eleresek["code"]], ($w > 20) ? $w : 20), ";");
}
fclose($handle);
fclose($newHandle);

ob_end_flush();

?>