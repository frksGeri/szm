<?php

require '../../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$path = 'y:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\A-E\BANKOSAN\_NYERS\DOLZ PRICE LIST -A-13.02.2025.xlsx';

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {
        return in_array($column, ['A', 'B']);
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

        if (in_array($colIndex, ['A','B'])) {
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
    "A" => "code",
    "B" => 'price',
    "C" => "articlecode",
    "D" => 'size',
    "E" => 'gyarto'
];

foreach ($headers as $col => $header) {
    $newSheet->setCellValue($col . '1', $header);
}

for ($row = 2; $row <= $highestRow; $row++) {
    $articlecodeValue = $newSheet->getCell("A$row")->getValue();
    $newSheet->setCellValue("C$row", trim($articlecodeValue) . "_DOLZ");

    $price = $newSheet ->getCell("B$row")-> getValue();
    $newSheet->setCellValue("B$row", round($price,2));
    $newSheet->setCellValue("E$row", "DOLZ");
}


$path = 'Z:\szerző peti\DOLZ.csv';

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
    $articleCode = $newSheet->getCell('C' . $rowIndex)->getValue();


    foreach ($sizeData as $sizeRow) {
        if ($sizeRow['articlecode'] === $articleCode) {

            $newSheet->setCellValue('D' . $rowIndex, $sizeRow['size']);
            break;
        }
    }
}

$currentDate = date('Y.m.d');
$manufacturer = "DOLZ";
$newFileName = sprintf(
    'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\%s_ÁRLISTA_%s.xlsx',
    $manufacturer,
    $currentDate
);

$writer = new Xlsx($newSpreadsheet);
$writer->save($newFileName);

echo ("Fájl mentve:" . $newFileName . "\n");
