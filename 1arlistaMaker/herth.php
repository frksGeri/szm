<?php

set_time_limit(360);
require '../../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

$path = 'y:\Árlista 2025\Price_update_Herth+Buss_Customer_No._1903964.csv';

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {
        $columns = ['A', 'F', 'G', 'H', 'I', 'M', 'N'];
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

$row = 1;
$refundArray = [];

foreach ($oldSheet->getRowIterator(1) as $oldRow) {
    $cellIterator = $oldRow->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    $newCol = 1;
    $rowData = [
        'A' => '',
        'F' => '',
        'G' => '',
        'H' => '',
        'I' => '',
        'M' => '',
        'N' => ''
    ];

    foreach ($cellIterator as $cell) {
        $colIndex = Coordinate::stringFromColumnIndex($newCol);

        if (in_array($colIndex, ['A', 'F', 'G', 'H', 'I', 'M', 'N'])) {
            $cellValue = $cell->getValue();
            $rowData[$colIndex] = $cellValue;

            $newSheet->setCellValueExplicit($colIndex . $row, $cellValue, DataType::TYPE_STRING);
        }
        $newCol++;
    }

    if (!empty($rowData['M']) && $rowData['M'] !== "0,00") {
        $refundArray[] = $rowData;
    }

    if ($newCol > 1) {
        $row++;
    }
}

$lastRow = $row - 1;

$headers = [
    "A" => "code",
    "B" => "articlecode",
    "C" => "size",
    "D" => "gyarto",
    "F" => "price",
    "G" => "szorzo",
    "H" => "moq",
    "I" => "weight_kg",
    "M" => "kauciolesze",
    "N" => "barcode"
];

foreach ($headers as $col => $header) {
    $newSheet->setCellValue($col . "1", $header);
}

$highestRow = $newSheet->getHighestRow();


function convertToNumber($value)
{
    if (is_numeric($value)) {
        return (float)$value;
    }


    $value = str_replace(',', '.', $value);


    $value = preg_replace('/[^0-9.]/', '', $value);

    return is_numeric($value) ? (float)$value : 0;
}

for ($row = 2; $row <= $highestRow; $row++) {
    $articleCode = $newSheet->getCell("A$row")->getValue();
    $newSheet->setCellValue("B$row", $articleCode . "_HTB");
    $newSheet->setCellValue("D$row", "HERTH+BUSS ELPARTS");
    
    $weight = convertToNumber($newSheet->getCell("I$row")->getValue());

    $newSheet->setCellValue("I$row", $weight * 1000);


    $price = convertToNumber($newSheet->getCell("G$row")->getValue());
    $priceSzorzo = convertToNumber($newSheet->getCell("G$row")->getValue());


    $newSheet->getStyle("F$row")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);


    if ($price > 0 && $priceSzorzo == 100) {
        $newSheet->setCellValue("F$row", $price / $priceSzorzo);
    }
}

$startRow = $lastRow + 1;

foreach ($refundArray as $refundData) {
    if (!empty($refundData['A'])) {
        $newSheet->setCellValue("A$startRow", $refundData['A'] . '_KAUCIO');
        $newSheet->setCellValue("B$startRow", $refundData['A'] . '_HTB_KAUCIO');
        $newSheet->setCellValue("D$startRow", 'HERTH+BUSS ELPARTS');

 
        $refundPrice = convertToNumber($refundData['M']);
        $newSheet->setCellValue("F$startRow", $refundPrice);

        $newSheet->setCellValue("H$startRow", $refundData['H']);
        $newSheet->setCellValueExplicit("N$startRow", $refundData['N'], DataType::TYPE_STRING);
        $startRow++;
    }
}


for ($row = $highestRow; $row >= 2; $row--) {
    $valueN = $newSheet->getCell("N$row")->getValue();
    $valueA = $newSheet->getCell("A$row")->getValue();

    if (
        empty($valueA) || trim($valueA) === "" || $valueA === null ||
        strpos($valueN, 'EAN') !== false
    ) {
        $newSheet->removeRow($row, 1);
    }
}


$startRow = $newSheet->getHighestRow() + 1;

$path = 'z:\szerző peti\HERTH+BUSS ELPARTS.csv';
$sizeData = [];

if (($file = fopen($path, "r")) !== FALSE) {
    $header = fgetcsv($file, 1000, ",");

    while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
        if (!empty($data[0])) {
            $asizeData = explode("\t", $data[0]);

            if (isset($asizeData[2], $asizeData[3]) && !empty($asizeData[3])) {
                $sizeData[] = [
                    "articlecode" => $asizeData[2],
                    "size" => $asizeData[3]
                ];
            }
        }
    }
    fclose($file);
}

foreach ($newSheet->getRowIterator(2) as $row) {
    $rowIndex = $row->getRowIndex();
    $articleCode = $newSheet->getCell('B' . $rowIndex)->getValue();

    foreach ($sizeData as $sizeRow) {
        if ($sizeRow['articlecode'] === $articleCode) {
            $newSheet->setCellValue('C' . $rowIndex, $sizeRow['size']);
            break;
        }
    }
}

$currentDate = date('Y.m.d');
$newFileName = 'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\HERTH_ÁRLISTA ' . $currentDate . '.xlsx';

$writer = new Xlsx($newSpreadsheet);
$writer->save($newFileName);

echo "A fájl mentve: " . $newFileName;
