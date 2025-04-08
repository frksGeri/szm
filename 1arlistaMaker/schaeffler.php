<?php

require '../../phpspreadsheet/vendor/autoload.php';
require '../ggg/init.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$path = 'y:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\P-T\SCHAEFFLER\_NYERS\12_Info_Service_12-2024+01-2025.xlsx';

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {
        return in_array($column, ['A', 'L', 'M', 'P', 'U','W']);
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

        if (in_array($colIndex, ['A', 'L', 'M', 'P', 'U','W'])) {
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
    'A' => 'code',
    "B" => 'articlecode',
    "C" => "size",
    "L" => "gyarto",
    "M" => "barcode",
    "P" => "weight",
    "U" => "country",
    "W" => "price"
];

foreach ($headers as $col => $header) {
    $newSheet->setCellValue($col . "1", $header);
}

for ($row = 2; $row <= $highestRow; $row++) {

    $articleCode = $newSheet->getCell("A$row")->getValue();
    $gyarto = $newSheet->getCell("L$row")->getValue();
    $gyartok = strtoupper($gyarto);
    $newSheet->setCellValue("L$row", $gyartok);

    $weight = $newSheet->getCell("P$row")->getValue();
    $weight = convertToNumber($weight);
    $newSheet->setCellValue("P$row", $weight * 1000);


    if ($gyartok == "LUK") {
        $newSheet->setCellValue("B$row", $articleCode . "_LUK");
    } elseif ($gyartok == "FAG") {
        $newSheet->setCellValue("B$row", $articleCode . "_FAG");
    } elseif ($gyartok == "INA") {
        $newSheet->setCellValue("B$row", $articleCode . "_INA");
    }
}

$manufacturers = [
    "LUK",
    "FAG",
    "INA"
];

foreach ($manufacturers as $manufacturer) {
    $path = 'Z:\\szerző peti\\' . $manufacturer . '.csv';

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
                $newSheet->setCellValue('C' . $rowIndex, $sizeRow['size']);
                
                break;
            }
        }
    }
}


$currentDate = date('Y.m.d');
$newFileName = sprintf(
    'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\SCHAEFFLER_ÁRLISTA_%s.xlsx',
    $currentDate
);

$writer = new Xlsx($newSpreadsheet);
$writer->save($newFileName);

echo "Fájl mentve: " . $newFileName ;

