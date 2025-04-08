<?php

set_time_limit(300);

require '../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$path = "A:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\K-O\KAMOKA\_NYERS\\20250217_KAMOKA_new_pricelist_01.03.2025_DP.xlsx";

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {

        $columns = ['A', 'C', 'N', 'O', 'S'];
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

$newRow = 1;
$refundArray = [];

foreach ($oldSheet->getRowIterator(1) as $oldRow) {
    $cellIterator = $oldRow->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    $newCol = 1;
    $rowData = [];

    foreach ($cellIterator as $cell) {
        $colIndex = Coordinate::stringFromColumnIndex($newCol);

        if (in_array($colIndex, ['A', 'C', 'N', 'O', 'S'])) {
            $cellValue = $cell->getValue();

            
            if ($colIndex == 'A') {
                $newSheet->setCellValueExplicit($colIndex . $newRow, $cellValue, DataType::TYPE_STRING);
            } else {
                $newSheet->setCellValue($colIndex . $newRow, $cellValue);
            }
        }
        $newCol++;
    }
    if ($newCol > 1) {
        $newRow++;
    }
}

$lastRow = $newRow - 1;
$highestRow = $newSheet->getHighestRow();

$newSheet->setCellValue('A1', 'code');
$newSheet->setCellValue('B1', 'price');
$newSheet->setCellValue('C1', 'articlecode');
$newSheet->setCellValue('D1', 'size');
$newSheet->setCellValue('M1', 'moq');
$newSheet->setCellValue('P1', 'barcode');
$newSheet->setCellValue('E1', 'gyarto');
$newSheet->setCellValue('Q1', 'weight');

for ($row = 2; $row <= $highestRow; $row++) {
    $valueC = $newSheet->getCell("A$row")->getValue();
    $newSheet->setCellValue("C$row", $valueC . "_KAM");

    $cellValueP = $newSheet->getCell("N$row")->getValue();
    $moqValue = $newSheet->getCell("O$row")->getValue();

    $weight = $newSheet->getCell("S$row")->getValue();

    $newSheet->setCellValueExplicit("S$row",$weight,DataType::TYPE_STRING);

    if (is_numeric($cellValueP)) {
        $newSheet->getCell("P$row")->setDataType(DataType::TYPE_NUMERIC);
        $newSheet->getStyle("P$row")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
    }

    if ($row > 1 && $row <= $highestRow) {
        $newSheet->setCellValue("E$row", "KAMOKA");
    }
    if ($moqValue == "PCS.") {
        $newSheet->setCellValueExplicit("M$row", null, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NULL);
    }
}

$path = 'B:\szerző peti\KAMOKA.csv';

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
    $articleCode = $newSheet->getCell('C' . $rowIndex)->getValue();

    foreach ($sizeData as $sizeRow) {
        if ($sizeRow['articlecode'] === $articleCode) {
            $newSheet->setCellValueExplicit('D' . $rowIndex, $sizeRow['size'],DataType::TYPE_STRING);
            break;
        }
    }
}

$currentDate = date('Y.m.d');
$newFileName = 'C:\Users\LP-KATALOGUS1\Desktop\exports\KAMOKA ÁRLISTA ' . $currentDate . '.xlsx';

$writer = new Xlsx($newSpreadsheet);
$writer->save($newFileName);

echo "A fájl mentve: " . $newFileName;
