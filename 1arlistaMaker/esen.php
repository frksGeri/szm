<?php

require '../../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$path = 'y:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\P-T\SKV\_NYERS\SKV Price List 14.01.2025 FULL PRICE LIST.xlsx';

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {
        // Az első sort kihagyjuk
        if ($row == 1) {
            return false;
        }
        $columns = ['A', 'G', 'H', 'L'];
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
$refundArray = [];

foreach ($oldSheet->getRowIterator(2) as $oldRow) {
    $cellIterator = $oldRow->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    $newCol = 1;
    $rowData = [];

    foreach ($cellIterator as $cell) {
        $colIndex = Coordinate::stringFromColumnIndex($newCol);

        if (in_array($colIndex, ['A', 'G', 'H', 'L'])) {
            $cellValue = $cell->getValue();
            $rowData[$colIndex] = $cellValue;
            $newSheet->setCellValueExplicit($colIndex . $newRow, $cellValue, DataType::TYPE_STRING);
        }
        $newCol++;
    }

    if (!empty($rowData['U'])) {
        $refundArray[] = $rowData;
    }

    if ($newCol > 1) {
        $newRow++;
    }
}

$lastRow = $newRow - 1;
function convertToNumber($value)
{
    if (is_numeric($value)) {
        return (float)$value;
    }


    $value = str_replace(',', '.', $value);


    $value = preg_replace('/[^0-9.]/', '', $value);

    return is_numeric($value) ? (float)$value : 0;
}
$newSheet->setCellValue('A1', 'code');
$newSheet->setCellValue('B1', 'articlecode');
$newSheet->setCellValue('C1', 'gyarto');
$newSheet->setCellValue('E1', 'size');
$newSheet->setCellValue('G1', 'barcode');
$newSheet->setCellValue('H1', 'price');
$newSheet->setCellValue('L1', 'weight_kg');


$highestRow = $newSheet->getHighestRow();

for ($row = 2; $row <= $highestRow; $row++) {

    $valueB = $newSheet->getCell("A$row")->getValue();
    $newSheet->setCellValue("B$row", $valueB . "_SKV");

    $cellValueH = $newSheet->getCell('H' . $row)->getValue();
    $newSheet->setCellValue('C' . $row, 'ESEN SKV');

    $price = convertToNumber($newSheet->getCell("H$row")->getValue());

    $newSheet->setCellValue("H$row", $price);

    $newSheet->getStyle("H$row")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
}

$path = 'Z:\szerző peti\ESEN SKV.csv';

$getData = [];

if (($file = fopen($path, "r")) !== FALSE) {

    $header = fgetcsv($file, 1000, ",");

    while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
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



foreach ($newSheet->getRowIterator(2) as $rowIndex => $row) {
    $articleCode = $newSheet->getCell('B' . $rowIndex)->getValue();


    foreach ($sizeData as $sizeRow) {
        if ($sizeRow['articlecode'] === $articleCode) {

            $newSheet->setCellValue('E' . $rowIndex, $sizeRow['size']);
            break;
        }
    }
}




$currentDate = date('Y.m.d');
$newFileName = 'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\ESEN ÁRLISTA ' . $currentDate . '.xlsx';


$writer = new Xlsx($newSpreadsheet);
$writer->save($newFileName);

echo "A fájl mentve: " . $newFileName;
