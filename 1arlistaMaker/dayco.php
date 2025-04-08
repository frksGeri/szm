<?php

require '../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$path = 'y:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\A-E\DAYCO\_NYERS\DAYCO updated PL new items 04.04.2025 and FY25 TOP Balkan Hun.xlsx';

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {
        return in_array($column, ['A', 'F', 'G', 'H', 'I']);
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

foreach ($oldSheet->getRowIterator(1) as $oldRow) {
    $cellIterator = $oldRow->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    $newCol = 1;
    $rowData = [];

    foreach ($cellIterator as $cell) {
        $colIndex = Coordinate::stringFromColumnIndex($newCol);

        if (in_array($colIndex, ['A', 'F', 'G', 'H', 'I'])) {
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

$headers = [
    "A" => "code",
    "B" => "articlecode",
    "C" => "size",
    "D" => "gyarto",
    "F" => "moq",
    "I" => "price"
];
foreach ($headers as $col => $header) {
    $newSheet->setCellValue($col . '1', $header);
}

for ($row = 2; $row <= $highestRow; $row++) {
    $articlecodeValue = $newSheet->getCell("A$row")->getValue();
    $newSheet->setCellValue("B$row", trim($articlecodeValue) . "_DAYCO");

    $newSheet->setCellValue("D$row", "DAYCO");

    $priceValue = $newSheet->getCell("I$row")->getCalculatedValue();
    $priceValue = round($priceValue, 2);
    $newSheet->setCellValue("I$row", $priceValue);
}

$path = 'Z:\szerző peti\DAYCO.csv';

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

$currentDate = date('Y.m.d');
$manufacturer = "DAYCO";
$newFileName = sprintf(
    'y:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\A-E\DAYCO\_NYERS\%s_ÁRLISTA_%s.xlsx',
    $manufacturer,
    $currentDate
);

$writer = new Xlsx($newSpreadsheet);
$writer->save($newFileName);

echo ("Fájl mentve:" . $newFileName . "\n");
