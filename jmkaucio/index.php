<?php

$path = 'z:\Farkas Gergo\jm\VazanePolozky.csv';
$weHaveItPath = 'z:\szerző peti\beszkod\jmauto_st_ideiglenes.csv';
$outputPath = "Z:\Farkas Gergo\jm\\newJMkaucioGen2_" . date("Y.m.d") . ".csv";


function readCsv($filePath)
{
    $data = [];
    if (($file = fopen($filePath, "r")) === FALSE) {
        return false;
    }

    while (($row = fgetcsv($file, 1000, ";")) !== FALSE) {
        $encoding = mb_detect_encoding(implode("", $row), ["ISO-8859-2", "UTF-8"], true);
        $data[] = array_map(function ($item) use ($encoding) {
            return mb_convert_encoding($item, "UTF-8", $encoding);
        }, $row);
    }
    fclose($file);
    return $data;
}


$arrayWithZaloha = [];
if ($getData = readCsv($path)) {
    foreach ($getData as $rows) {
        if ((isset($rows[4]) && ($rows[4] === "Záloha " || strpos($rows[4], "Záloh") !== false)) ||
            (isset($rows[2]) && strpos($rows[2], "_X") !== false)
        ) {
            $arrayWithZaloha[] = $rows;
        }
    }
}


$lookup = [];
if ($weHaveItAll = readCsv($weHaveItPath)) {
    foreach ($weHaveItAll as $row) {
        $key = strstr(implode(";", $row), "#", true) ?: implode(";", $row);
        $lookup[$key] = true;
    }
}


$result = [];

foreach ($arrayWithZaloha as $zalohaRow) {

    if (isset($lookup[$zalohaRow[0]])) {
        $result[] = $zalohaRow;
    }
}

$filteredResult = [];
$priceMap = [];

foreach ($result as $row) {
    $cikkszam = $row[0];
    $price = $row[6];

    if (!isset($priceMap[$cikkszam]) || $price > $priceMap[$cikkszam]['price']) {
        $priceMap[$cikkszam] = [
            'price' => $price,
            "row" => $row
        ];
    }
}

foreach ($priceMap as $cikkszam => $data) {
    $filteredResult[] = $data['row'];
}


if (!empty($filteredResult)) {
    if ($outputFile = fopen($outputPath, "w")) {
        fwrite($outputFile, chr(0xEF) . chr(0xBB) . chr(0xBF));
        foreach ($filteredResult as $row) {
            fputcsv($outputFile, $row, ";");
        }
        fclose($outputFile);
        echo 'sikeres mentés';
    } else {
        echo 'Hiba a fájl írásakor';
    }
} else {
    echo 'Nincs találat';
}