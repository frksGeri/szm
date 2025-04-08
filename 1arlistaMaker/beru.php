<?php

require '../../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$path = 'c:\Users\LP-GERGO\Desktop\Farkas Gergő test\BERU Pricelist 2024 EU_NON-EU.xlsx';

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {

        if ($row < 5) {
            return false;
        }

        $columns = ['A', 'B', 'K', 'L', 'V'];
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

        if (in_array($colIndex, ['A', 'B', 'K', 'L', 'V'])) {
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
$newSheet->setCellValue('C1', 'articlecode');
$newSheet->setCellValue('K1', 'barcode');
$newSheet->setCellValue('L1', 'weight_kg');
$newSheet->setCellValue('V1', 'price');

for ($row = 2; $row <= $highestRow; $row++) {
    $valueC = $newSheet->getCell("B$row")->getValue();
    $cellValueK = $newSheet->getCell("K$row")->getValue();
    $newSheet->setCellValue("C$row", $valueC . "_BERU");


    if (is_numeric($cellValueK)) {
        $newSheet->getCell('K' . $row)->setDataType(DataType::TYPE_NUMERIC);
        $newSheet->getStyle("K$row")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
    }
}


$currentDate = date('Y.m.d');
$newFileName = 'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\tesztBERU ÁRLISTA ' . $currentDate . '.xlsx';

$writer = new Xlsx($newSpreadsheet);
$writer->save($newFileName);

echo "A fájl mentve: " . $newFileName;
