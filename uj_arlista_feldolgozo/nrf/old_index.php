<?php


require '../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$path = 'y:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\K-O\NRF\_NYERS\202500101 Official Pricelist EURII2025 NRF.xlsx';

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {
        if ($row < 3) {
            return false;
        }
        $columns = ['A', 'Q', 'U', 'X', 'BC'];
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

        if (in_array($colIndex, ['A', 'Q', 'U', 'X', 'BC'])) {
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
function convertToNumber($value)
{
    if (is_numeric($value)) {
        return (float)$value;
    }


    $value = str_replace(',', '.', $value);


    $value = preg_replace('/[^0-9.]/', '', $value);

    return is_numeric($value) ? (float)$value : 0;
}


$headers = [
    "A" => "code",
    "B" => "articlecode",
    "C" => "size",
    "D" => "gyarto",
    "Q" => "weight",
    "U" => "barcode",
    "X" => "oes",
    "Y" => "oe1",
    "Z" => "oe2",
    "AA" => "oe3",
    "BC" => "price"
];

foreach ($headers as $col => $header) {
    $newSheet->setCellValue($col . '1', $header);
}

for ($row = 2; $row <= $highestRow; $row++) {

    $newSheet->setCellValue("D$row", "NRF");

    $weight = convertToNumber($newSheet->getCell("Q$row")->getValue());

    $newSheet->setCellValue("Q$row", $weight * 1000);

    $articlecodeValue = $newSheet->getCell("A$row")->getValue();
    $newSheet->setCellValue("B$row", $articlecodeValue . "_NRF");

    $oes = $newSheet->getCell("X$row")->getValue();

    $oesArray = explode("/", $oes);

    $oe1 = $oesArray[0];

    if (isset($oesArray[1]) || !empty($oesArray[1])) {
        $oe2 = $oesArray[1];
    }

    if (isset($oesArray[2]) || !empty($oesArray[2])) {
        $oe3 = $oesArray[2];
    }

    $newSheet->setCellValueExplicit("Y$row", $oe1,DataType::TYPE_STRING);
    $newSheet->setCellValueExplicit("Z$row", $oe2, DataType::TYPE_STRING);
    $newSheet->setCellValueExplicit("AA$row", $oe3, DataType::TYPE_STRING);
}


$path = 'Z:\szerző peti\NRF.csv';

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
$newFileName = 'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\NRF ÁRLISTA ' . $currentDate . '.xlsx';

$writer = new Xlsx($newSpreadsheet);
$writer->save($newFileName);

echo "A fájl mentve: " . $newFileName;
