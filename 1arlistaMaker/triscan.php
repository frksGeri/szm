<?php

set_time_limit(360);
require '../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

$path = 'y:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\P-T\TRISCAN\_NYERS\Prices 07042025 Szakal-Met-Al.xlsx';

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {
        $columns = ['C', 'N', 'Q', 'R'];
        return in_array($column, $columns);
    }
}

$reader = IOFactory::createReaderForFile($path);
$reader->setReadDataOnly(true);
$reader->setReadFilter(new MyReadFilter());
$spreadsheet = $reader->load($path);

$oldSheet = $spreadsheet->getSheet(0);
$newSpreadsheet = new Spreadsheet();
$newSheet = $newSpreadsheet->getActiveSheet();
$newSheet->setTitle('todb');

$row = 1;

$refundArray = [];

foreach ($oldSheet->getRowIterator(1) as $oldRow) {
    $cellIterator = $oldRow->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    $newCol = 1;
    $rowData = [];

    foreach ($cellIterator as $cell) {
        $colIndex = Coordinate::stringFromColumnIndex($newCol);

        if (in_array($colIndex, ['C', 'N', 'Q', 'R'])) {
            $cellValue = $cell->getValue();
            $rowData[$colIndex] = $cellValue;

            $newSheet->setCellValueExplicit($colIndex . $row, $cellValue, DataType::TYPE_STRING);
        }
        $newCol++;
    }

    if ($rowData['R'] != "0") {
        $refundArray[] = $rowData;
    }

    if ($newCol > 1) {
        $row++;
    }
}
$lastRow = $row - 1;

$highestRow = $newSheet->getHighestRow();

$headers = [
    "A" => "articlecode",
    "B" => "size",
    "C" => "code",
    "D" => "gyarto",

    "N" => "weight",
    "Q" => "price"
];

foreach ($headers as $col => $header) {
    $newSheet->setCellValue($col . "1", $header);
}

for ($row = 2; $row <= $highestRow; $row++) {
    $articleCode = $newSheet->getCell("C$row")->getValue();
    $newSheet->setCellValue("A$row", $articleCode . "_TRIS");
    $newSheet->setCellValue("D$row", "TRISCAN");



    $weightkg = $newSheet->getCell("N$row")->getValue();
    $newSheet->getStyle("N$row")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
    $newSheet->setCellValue("N$row", (float)$weightkg * 1000);
}


$startRow = $lastRow + 1;

foreach ($refundArray as $refundRow) {
    $newSheet->setCellValueExplicit("A$startRow", $refundRow['C'] . '_TRIS_KAUCIO', DataType::TYPE_STRING);
    $newSheet->setCellValueExplicit("C$startRow", $refundRow['C'] . '_DEPOSIT', DataType::TYPE_STRING);
    $newSheet->setCellValueExplicit("D$startRow", 'TRISCAN', DataType::TYPE_STRING);

    $newSheet->setCellValueExplicit("Q$startRow", $refundRow['R'], DataType::TYPE_STRING);

    if (isset($refundRow['N'])) {
        
        $newSheet->getStyle("N$startRow")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $newSheet->setCellValue("N$startRow", (float)$refundRow['N'] * 1000);
    }

    $startRow++;
}


for ($row = $lastRow + 1; $row <= $startRow; $row++) {
    $valueI = $newSheet->getCell("I$row")->getValue();
    if (stripos($valueI, 'EAN-code') !== false) {
        $newSheet->removeRow($row);
        $row--;
        $startRow--;
    }
}



$path = 'z:\szerző peti\TRISCAN.csv';
$getData = [];

if (($file = fopen($path, "r")) !== FALSE) {

    $header = fgetcsv($file, 1000, ",");

    while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
        $getData[] = $data[0];
    }

    fclose($file);
}
$sizeData = [];

foreach ($getData as $key => $value) {
    $asizeData = explode("\t", $value);

    if (!isset($asizeData[3]) || $asizeData[3] === '') {
        continue;
    }

    $sizeData[] = [
        "articlecode" => $asizeData[2],
        "size" => $asizeData[3]
    ];
}



foreach ($newSheet->getRowIterator(2) as $rowIndex => $row) {
    $articleCode = $newSheet->getCell('A' . $rowIndex)->getValue();


    foreach ($sizeData as $sizeRow) {
        if ($sizeRow['articlecode'] === $articleCode) {

            $newSheet->setCellValue('B' . $rowIndex, $sizeRow['size']);
            break;
        }
    }
}

$currentDate = date('Y.m.d');
$manufacturer = "TRISCAN";
$newFileName = sprintf(
    'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\%s_ÁRLISTA_%s.xlsx',
    $manufacturer,
    $currentDate
);

$writer = new Xlsx($newSpreadsheet);
$writer->save($newFileName);

echo ("Fájl mentve:" . $newFileName . "\n");
