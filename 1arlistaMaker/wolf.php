<?php

require '../../phpspreadsheet/vendor/autoload.php';


use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

$path = 'y:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\U-Z\WOLF\_NYERS\PRICELIST_13922 wolf.xlsx';
$manufacturer = 'WOLF';
class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {
        if ($row < 4) {
            return false;
        }
        $columns = ['B', 'E', 'K', 'L', 'Q'];
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

foreach ($oldSheet->getRowIterator(4) as $oldRow) {
    $cellIterator = $oldRow->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    $newCol = 1;
    $rowData = [];

    foreach ($cellIterator as $cell) {
        $colIndex = Coordinate::stringFromColumnIndex($newCol);

        if (in_array($colIndex, ['B', 'E', 'K', 'L', 'Q'])) {
            $cellValue = $cell->getValue();
            $rowData[$colIndex] = $cellValue;
            $newSheet->setCellValueExplicit($colIndex . $newRow, $cellValue, DataType::TYPE_STRING);
        }
        $newCol++;
    }

    if ($newCol > 1) {
        $newRow++;
    }
}

$lastRow = $newRow - 1;
$highestRow = $newSheet->getHighestRow();

$headers = [
    'A' => 'gyarto',
    'B' => 'code',
    'C' => 'size',
    'E' => 'barcode',
    'K' => 'moq',
    'L' => 'price',
    'Q' => 'weight'
];

foreach ($headers as $col => $header) {
    $newSheet->setCellValue($col . "1", $header);
}

for($row = 2; $row <= $highestRow; $row++){

    $newSheet->setCellValue("A$row", 'WOLF');
    
    $w = $newSheet->getCell("Q$row")->getValue();
    $newSheet->setCellValueExplicit("Q$row",(int)$w * 1000,DataType::TYPE_STRING);

}

$currentDate = date('Y.m.d');
$newFileName = 'y:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\U-Z\WOLF\\' . $manufacturer . ' ÁRLISTA ' . $currentDate . '.xlsx';

$write = new Xlsx($newSpreadsheet);
$write->save($newFileName);

echo 'kesz';
