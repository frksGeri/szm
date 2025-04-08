<?php

require '../../phpspreadsheet/vendor/autoload.php';
require '../ggg/init.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

$path = 'y:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\K-O\MAGNETI MARELLI\_NYERS\netPrices_ZPEX90_CPA01313_SZAKAL-MET-AL ZRT.xlsx';
$manufacturer = 'MAGNETI MARELLI';
$columnsKeep = ['C', 'D', 'M', 'O', 'S', 'U', 'W'];


$newSpreadsheet = filterSpreadsheetColumns($path, $columnsKeep);


$newSheet = $newSpreadsheet->getActiveSheet();


$highestRow = $newSheet->getHighestRow();

$headers = [
    'A' => 'gyarto',
    'B' => 'size',
    'C' => 'code',
    'E' => 'articlecode',
    'M' => 'country',
    'O' => 'barcode',
    'S' => 'price',
    'U' => 'moq',
    'W' => 'weight'
];


foreach ($headers as $col => $header) {
    $newSheet->setCellValue($col . '1', $header);
}


foreach (range('A', 'W') as $columnID) {
    $newSheet->getColumnDimension($columnID)->setAutoSize(true);
}


for ($row = 2; $row <= $highestRow; $row++) { 
    $newSheet->setCellValue("A$row", $manufacturer);

    $a = $newSheet->getCell("D$row")->getValue();
    $newSheet->setCellValueExplicit("E$row", $a . "_MM", DataType::TYPE_STRING2);

    $w = $newSheet->getCell("W$row")->getValue();
    if ($w !== null && is_numeric($w)) {
        $newSheet->setCellValueExplicit("W$row", $w * 1000, DataType::TYPE_NUMERIC);
    }
}


$sizeData = loadSizeData($manufacturer);


foreach ($newSheet->getRowIterator(2) as $rowIndex => $row) { 
    $articleCode = $newSheet->getCell("E$rowIndex")->getValue();

    foreach ($sizeData as $sizeRow) {
        if ($sizeRow['articlecode'] === $articleCode) {
            $newSheet->setCellValue("B$rowIndex", $sizeRow['size']);
            break;
        }
    }
}


$currentDate = date('Y.m.d');
$newFileName = 'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\\' . $manufacturer . ' ÁRLISTA ' . $currentDate . '.xlsx';

$writer = new Xlsx($newSpreadsheet);
$writer->save($newFileName);

echo 'kesz';
