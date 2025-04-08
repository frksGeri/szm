<?php

require '../phpspreadsheet/vendor/autoload.php';
require '../ggg/init.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$path = 'y:\Árlista 2024\GYÁRI ÁRLISTÁK 2024\GABLINI\Gablini árlista Peugeot 2024.11.11.xlsx';
$path = 'y:\Árlista 2025\GYÁRI ÁRLISTÁK 2025\GABLINI\GABLINI PSA TEST.xlsx';


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
        $newSheet->setCellValueExplicit('F' . $newRow, round($discountedPrice, 2), DataType::TYPE_STRING);
    }

    if ($newCol > 1) {
        $newRow++;
    }
}


$manufacturer = "psa_gablini";
$newFileName = sprintf(
    'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\%s.csv',
    $manufacturer
);


$writer = new Csv($newSpreadsheet);
$writer->setDelimiter(';');
$writer->setLineEnding("\r\n");
$writer->setSheetIndex(0);

$writer->save($newFileName);

echo "kesz a mentes";