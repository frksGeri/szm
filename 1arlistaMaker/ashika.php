<?php

require '../../phpspreadsheet/vendor/autoload.php';


use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

$path = 'y:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\A-E\ASHIKA\_NYERS\Japanparst_newpricelist___17.02.2025.xlsx';
$manufacturer = 'ASHIKA';
class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {
        if ($row < 1) {
            return false;
        }
        $columns = ['C', 'D', 'J','P'];
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

foreach ($oldSheet->getRowIterator() as $oldRow) {
    $cellIterator = $oldRow->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    $newCol = 1;
    $rowData = [];

    foreach ($cellIterator as $cell) {
        $colIndex = Coordinate::stringFromColumnIndex($newCol);

        if (in_array($colIndex, ['C', 'D', 'J','P'])) {
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
    'B' => 'size',
    'C' => 'code',
    'D' => 'barcode',
    'F' => 'articlecode',
    'J' => 'price',
    'P' => 'weight'
];

foreach ($headers as $col => $header) {
    $newSheet->setCellValue($col . '1', $header);
}

for ($row = 2; $row <= $highestRow; $row++) {
    $newSheet->setCellValue("A$row",'ASHIKA');
    $acode = $newSheet->getCell("C$row")->getValue();
    $newSheet->setCellValueExplicit("F$row",$acode . "_ASH",DataType::TYPE_STRING);

    
}


function loadSizeData()
{
    $path = 'z:\szerző peti\ASHIKA.csv';

    $getData = [];
    if (($file = fopen($path, "r")) != FALSE) {
        $header = fgetcsv($file, 1000, ",");
        while (($data = fgetcsv($file, 1000, ",")) !== false) {

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
    return $sizeData;
}

$sizeData = loadSizeData();

foreach ($newSheet->getRowIterator(1) as $rowIndex => $row) {
    $articleCode = $newSheet->getCell("F$rowIndex")->getValue();


    foreach ($sizeData as $sizeRow) {
        if ($sizeRow['articlecode'] === $articleCode) {
            $newSheet->setCellValue("B$rowIndex", $sizeRow['size']);
            break;
        }
    }
}

$currentDate = date('Y.m.d');
$newFileName = 'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\\' . $manufacturer . ' ÁRLISTA ' . $currentDate . '.xlsx';

$write = new Xlsx($newSpreadsheet);
$write->save($newFileName);

echo 'kesz';
