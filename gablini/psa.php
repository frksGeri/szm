<?php

set_time_limit(0);
ini_set('memory_limit', '500000M');

$start_time = microtime(true);

require '../phpspreadsheet/vendor/autoload.php';
require '../ggg/init.php';

$path = 'y:\Árlista 2025\GYÁRI ÁRLISTÁK 2025\GABLINI\Gablini Árlista 2025.01.01_204122_PE.xlsx';
#$path = 'y:\Árlista 2025\GYÁRI ÁRLISTÁK 2025\GABLINI\GABLINI PSA TEST.xlsx';


use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;



class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {
        return in_array($column, ['A', 'B', 'C', 'D', 'E', 'F']);
    }
}

$reader = IOFactory::createReaderForFile($path);
$reader->setReadDataOnly(true);
$reader->setReadFilter(new MyReadFilter());
$spreadsheet = $reader->load($path);

$oldSheet = $spreadsheet->getSheet(0);
$newSpreadsheet = new Spreadsheet();
$newSheet = $newSpreadsheet->getActiveSheet();

$newRow = 1;
$rabat = [
    '7C' => '0.05',
    '7D' => '0.10',
    '7E' => '0.15',
    '7F' => '0.20',
    '7G' => '0.25',
    '7J' => '0.30',
    '7K' => '0.35',
    '7L' => '0.40',
    '7M' => '0.45',
    '7N' => '0.50'
];


foreach ($oldSheet->getRowIterator(1) as $oldRow) {
    $cellIterator = $oldRow->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    $newCol = 1;
    $rowData = [];

    foreach ($cellIterator as $cell) {
        $colIndex = Coordinate::stringFromColumnIndex($newCol);

        if (in_array($colIndex, ['A', 'B', 'C', 'D', 'E', 'F'])) {
            $cellValue = $cell->getValue();
            $rowData[$colIndex] = $cellValue;
            $newSheet->setCellValueExplicit($colIndex . $newRow, $cellValue, DataType::TYPE_STRING);
        }
        $newCol++;
    }
    if (isset($rowData['E']) && isset($rabat[$rowData['E']])) {
        $price = floatval($rowData['D']);
        $discount = floatval($rabat[$rowData['E']]);
        $discountedPrice = $price * (1 - $discount);

        $newSheet->setCellValueExplicit('F' . $newRow, round($discountedPrice, 2), DataType::TYPE_NUMERIC);
        $newSheet->setCellValue('F1', 'price');
    }
    $codeLength = $newSheet->getCell("A" . $newRow)->getValue();
    if (strlen($codeLength) < 10) {
        $paddedValue = str_pad($codeLength, 10, "0", STR_PAD_LEFT);
        $newSheet->setCellValueExplicit("A" . $newRow, $paddedValue, DataType::TYPE_STRING);
    }
    if ($newCol > 1) {
        $newRow++;
    }
}



$output = 'y:\kezi_arlista\psa_gablini.csv';
#$output = 'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\psa_gablini.csv';
saveSpreadsheetToCsv($newSpreadsheet, $output);

$end_time = microtime(true);

$time = $end_time - $start_time;

echo PHP_EOL . "futási idő: " . $time/60 . " perc";
