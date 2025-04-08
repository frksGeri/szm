<?php

include '../ggg/init.php';

$inputPath = 'y:\gerison_arlista\gmt_master.csv';

$outputDir = 'c:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\\'; 
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true); }


$jsonPath = $outputDir . 'processed_codes.json';


$outputFileName = 'gmt_master_filtered_' . date('Ymd_His') . '.csv';
$outputPath = $outputDir . $outputFileName;

$previousData = [];
if (file_exists($jsonPath)) {
    $jsonContent = file_get_contents($jsonPath);
    $previousData = json_decode($jsonContent, true);
    if ($previousData === null) {
        $previousData = []; 
    }
}


$csvData = file_get_contents($inputPath);
$rows = explode("\n", $csvData);


$newData = [];


foreach ($rows as $key => $row) {
    
    if ($key == 0) {
        continue;
    }

    $row = explode(";", $row);

    if (isset($row[0]) && isset($row[8])) {
        $code = $row[0];
        $country = $row[8];

       
        if ($country != '  ') {
            if (!isset($previousData[$code])) {
                $newData[] = [$code, $country];
             
                $previousData[$code] = ['country' => $country];
            }
        }
    }
}

if (!empty($newData)) {
    $outputFile = fopen($outputPath, 'w');
    if ($outputFile === false) {
        die('Nem sikerült megnyitni a kimeneti fájlt: ' . $outputPath);
    }

  
    foreach ($newData as $dataRow) {
        fputcsv($outputFile, $dataRow, ';');
    }

    fclose($outputFile);

    echo "Új kimeneti fájl létrehozva: $outputPath\n";
} else {
    echo "Nincsenek új cikkszámok, kimeneti fájl nem készült.\n";
}

$jsonFile = fopen($jsonPath, 'w');
if ($jsonFile === false) {
    die('Nem sikerült megnyitni a JSON fájlt: ' . $jsonPath);
}
fwrite($jsonFile, json_encode($previousData, JSON_PRETTY_PRINT));
fclose($jsonFile);

echo "JSON fájl frissítve: $jsonPath\n";

//var_dump($newData);

?>