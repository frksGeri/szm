<?php

set_time_limit(0);
require '../../phpspreadsheet/vendor/autoload.php';


use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

$path = 'y:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\A-E\DELPHI\_NYERS\Delphi_Price_List_2025_February_2G.xlsx';
$manufacturer = 'DELPHI';
class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {
        if ($row < 2) {
            return false;
        }
        $columns = ['B', 'G', 'M', 'O', 'R', 'T'];
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

foreach ($oldSheet->getRowIterator(2) as $oldRow) {
    $cellIterator = $oldRow->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    $newCol = 1;
    $rowData = [];

    foreach ($cellIterator as $cell) {
        $colIndex = Coordinate::stringFromColumnIndex($newCol);

        if (in_array($colIndex, ['B', 'G', 'M', 'O', 'R', 'T'])) {
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
    'C' => 'articlecode',
    'D' => 'size',
    'G' => 'moq',
    'M' => 'barcode',
    'O' => 'weight_kg',
    'R' => 'country',
    'T' => 'price'
];

foreach ($headers as $col => $header) {
    $newSheet->setCellValue($col . '1', $header);
}

for ($row = 2; $row <= $highestRow; $row++) {

    $newSheet->setCellValue("A$row", $manufacturer);
    $acode = $newSheet->getCell("B$row")->getValue();
    $newSheet->setCellValueExplicit("C$row", $acode . "_DEL", DataType::TYPE_STRING);
   
 
    
    /*$p = $newSheet->getCell("T$row")->getValue();
    if($p == 'Not Available' || empty($p) || $p == ''){
        $newSheet->removeRow($row);
        $row--;
        $highestRow--;
    }
*/
}


function loadSizeData()
{
    $path = 'z:\szerző peti\DELPHI.csv';

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
    $articleCode = $newSheet->getCell("C$rowIndex")->getValue();


    foreach ($sizeData as $sizeRow) {
        if ($sizeRow['articlecode'] === $articleCode) {
            $newSheet->setCellValue("D$rowIndex", $sizeRow['size']);
            break;
        }
    }
}

$currentDate = date('Y.m.d');
$newFileName = 'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\\' . $manufacturer . ' ÁRLISTA ' . $currentDate . '.xlsx';

$write = new Xlsx($newSpreadsheet);
$write->save($newFileName);

echo 'kesz';
