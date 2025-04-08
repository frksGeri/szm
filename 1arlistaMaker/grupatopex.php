<?php

require '../../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

$path = 'y:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\F-J\GRUPATOPEX\_NYERS\Listaárlista 2025 03 01 Szakál végleges.xlsx';

$manufacturers = [
    'GRAPHITE' => '_GRAPH',
    'NEO' => '_NEO',
    'NEO TOOLS' => '_NEO',
    'PRESSOL' => '_PRESS',
    'Top Tools' => '_TOPTOOLS',
    'TOPEX' => '_TOPEX'
];

$manufacturerSet = array_flip(array_keys($manufacturers));

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {
        return $row > 1 && in_array($column, ['A', 'C', 'E', 'F', 'J'], true);
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

$headers = [
    'A' => 'code',
    'B' => 'articlecode',
    'C' => 'moq',
    'D' => 'size',
    'E' => 'gyarto',
    'F' => 'barcode',
    'J' => 'price'
];

foreach ($headers as $col => $header) {
    $newSheet->setCellValue($col . "1", $header);
}


$sizeDataCache = [];
foreach ($manufacturers as $manufacturer => $suffix) {
    if($manufacturer == 'NEO TOOLS'){
        $manufacturer = 'NEO';
    }
    $path = 'Z:\szerző peti\\' . strtoupper($manufacturer) . '.csv';
    if (($file = fopen($path, "r")) !== FALSE) {
        fgetcsv($file, 1000, ","); 
        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            $asizeData = explode("\t", $data[0]);
            if (!empty($asizeData[3])) {
                $articleCode = $asizeData[2] ?? '';
                if ($articleCode) {
                    $sizeDataCache[$articleCode] = $asizeData[3];
                }
            }
        }
        fclose($file);
    }
}


$newRow = 2;
$columnMap = [
    'A' => 0,  // code
    'C' => 2,  // moq
    'E' => 4,  // gyarto
    'F' => 5,  // barcode
    'J' => 9   // price
];

foreach ($oldSheet->getRowIterator(1) as $row) {
    $rowIndex = $row->getRowIndex();
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    
    $rowData = [];
    foreach ($columnMap as $col => $index) {
        $coordinate = Coordinate::stringFromColumnIndex($index + 1) . $rowIndex;
        $value = $oldSheet->rangeToArray($coordinate . ':' . $coordinate, null, true, false)[0][0];
        $rowData[$col] = $value;
    }
    

    if (!isset($manufacturerSet[$rowData['E']])) {
        continue;
    }


    $newSheet->setCellValueExplicit("A$newRow", $rowData['A'], DataType::TYPE_STRING);
    $newSheet->setCellValueExplicit("B$newRow", $rowData['A'] . $manufacturers[$rowData['E']], DataType::TYPE_STRING);
    $newSheet->setCellValueExplicit("C$newRow", str_replace($rowData['C'], "db", ""), DataType::TYPE_STRING);
    $newSheet->setCellValueExplicit("E$newRow", $rowData['E'], DataType::TYPE_STRING);
    $newSheet->setCellValueExplicit("F$newRow", $rowData['F'], DataType::TYPE_STRING);
    $newSheet->setCellValueExplicit("F$newRow",$rowData['F'],DataType::TYPE_STRING);
    $newSheet->setCellValueExplicit("J$newRow", round((float)$rowData['J'], 2), DataType::TYPE_STRING);


    $articleCode = $rowData['A'] . $manufacturers[$rowData['E']];
    if (isset($sizeDataCache[$articleCode])) {
        $newSheet->setCellValue("D$newRow", $sizeDataCache[$articleCode]);
    }

    $newRow++;
}


$currentDate = date('Y.m.d');
$newFileName = sprintf(
    'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\GRUPATOPEX_ÁRLISTÁK_%s.xlsx',
    $currentDate
);

$writer = new Xlsx($newSpreadsheet);
$writer->save($newFileName);

echo "Fájl mentve: " . $newFileName;
