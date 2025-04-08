<?php

require '../../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$path = 'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\AWD Pricelist 2024.xlsx';

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {

        if ($row < 5) {
            return false;
        }

        $columns = ['A', 'I', 'J', 'Q'];
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

foreach ($oldSheet->getRowIterator(6) as $oldRow) {
    $cellIterator = $oldRow->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    $newCol = 1;
    $rowData = [];

    foreach ($cellIterator as $cell) {
        $colIndex = Coordinate::stringFromColumnIndex($newCol);

        if (in_array($colIndex, ['A', 'I', 'J', 'Q'])) {
            $cellValue = $cell->getValue();
            $rowData[$colIndex] = $cellValue;
            $newSheet->setCellValue($colIndex . $newRow, $cellValue);
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
$newSheet->setCellValue('B1', 'articlecode');
$newSheet->setCellValue('I1', 'barcode');
$newSheet->setCellValue('J1', 'weight_kg');
$newSheet->setCellValue('Q1', 'price');



for ($row = 2; $row <= $highestRow; $row++) {
    $valueB = $newSheet->getCell("A$row")->getValue();
    $cellValueI = $newSheet->getCell("I$row")->getValue();
    $newSheet->setCellValue("B$row", $valueB . "_AWD");


    if (is_numeric($cellValueI)) {
        $newSheet->getCell('I' . $row)->setDataType(DataType::TYPE_NUMERIC);
        $newSheet->getStyle("I$row")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
    }
}


$currentDate = date('Y.m.d');
$newFileName = 'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\tesztAWD ÁRLISTA ' . $currentDate . '.xlsx';

$writer = new Xlsx($newSpreadsheet);
$writer->save($newFileName);

echo "A fájl mentve: " . $newFileName;
