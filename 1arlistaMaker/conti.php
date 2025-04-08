<?php

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require '../ggg/init.php';
require '../phpspreadsheet/vendor/autoload.php';


$manufacturer = 'CONTITECH';
$date = date('Y.m.d');
$inputFile = 'y:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\A-E\CONTI\_NYERS\ContiTech PTG_Net Price List EMEA_valid from 01.01.24_update 08-2024.xlsx';
$outputFileName = 'c:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\\' . $manufacturer . '_árlista_' . $date . '.xlsx';
$columns = ['A', 'E', 'F', 'G', 'H', 'J'];

$newSpreadsheet = filterSpreadsheetColumns($inputFile, $columns,3);

$newSheet = $newSpreadsheet->getActiveSheet();

$highestRow = $newSheet->getHighestRow();

$sizeData = loadSizeData($manufacturer);

$headers = [
    'A' => 'code',
    'B' => 'articlecode',
    "C" => 'size',
    "D" => 'gyarto',
    "E" => "price",
    "F" => "moq",
    "G" => "barcode",
    "H" => "weight",
    "J" => "country"
];

foreach ($headers as $col => $header) {
    $newSheet->setCellValue($col . "1", $header);
}


for ($row = 5; $row <= $highestRow; $row++) {

    $a = $newSheet->getCell("A$row")->getValue();
    $newSheet->setCellValue("B$row", $a . "_CONTI");

    $b = $newSheet->getCell("B$row")->getValue();

    if (isset($sizeData['articlecode'][$b])) {
        $newSheet->setCellValue('C' . $row, $sizeData['articlecode'][$b]);
    }


}

$writer = new Xlsx($newSpreadsheet);
$writer->save($outputFileName);

echo "A fájl mentve: " . $outputFileName;
