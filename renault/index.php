<?php

require '../phpspreadsheet/vendor/autoload.php';
require '../ggg/init.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$path = 'y:\Árlista 2025\GYÁRI ÁRLISTÁK 2025\RENAULT\Renault árlista 2025.02.07.xlsx';


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
    '1' => '0.11',
    '2' => '0.15',
    '3' => '0.20',
    '4' => '0.22',
    '5' => '0.27',
    '6' => '0.30',
    '7' => '0.35',
    '8' => '0.17',
    '9' => '0.05',
    '0' => '0.05'
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
    if (isset($rowData['D']) && isset($rabat[$rowData['D']])) {

        $price = floatval($rowData['F']);
        $discount = floatval($rabat[$rowData['D']]);
        $discountedPrice = $price * (1 - $discount);
        $newSheet->setCellValueExplicit('F' . $newRow, round($discountedPrice, 2), DataType::TYPE_NUMERIC);
    }

    if ($newCol > 1) {
        $newRow++;
    }
}


$output = 'y:\kezi_arlista\renault2.csv';
saveSpreadsheetToCsv($newSpreadsheet,$output);
