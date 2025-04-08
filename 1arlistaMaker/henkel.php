<?php

require '../../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use function PHPSTORM_META\type;

$path = 'y:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\F-J\HENKEL\_NYERS\Final ÚJ VRM Pricelist 2025.02.15.xlsx';

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {

        if ($row < 4) {
            return false;
        }

        $columns = ['A', 'H', 'J', 'N'];
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

foreach ($oldSheet->getRowIterator(5) as $oldRow) {
    $cellIterator = $oldRow->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    $newCol = 1;
    $rowData = [];

    foreach ($cellIterator as $cell) {
        $colIndex = Coordinate::stringFromColumnIndex($newCol);

        if (in_array($colIndex, ['A', 'H', 'J', 'N'])) {
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
$newSheet->setCellValue('A1', 'code');
$newSheet->setCellValue('B1', 'articlecode');
$newSheet->setCellValue('C1', 'size');
$newSheet->setCellValue('N1', 'barcode');
$newSheet->setCellValue('H1', 'moq');
$newSheet->setCellValue('J1', 'price');
$newSheet->setCellValue('D1', 'gyarto');

for ($row = 2; $row <= $highestRow; $row++) {
    $valueB = $newSheet->getCell("A$row")->getValue();
    $cellValueP = $newSheet->getCell("P$row")->getValue();
    $newSheet->setCellValue("B$row", $valueB . "_LOC");

    $price = $newSheet->getCell("J$row")->getValue();

    if (is_numeric($price)) {
        $price = round($price, 2);
    }
    
    $newSheet -> setCellValue("J$row", $price);


    if (is_numeric($cellValueP)) {
        $newSheet->getCell('P' . $row)->setDataType(DataType::TYPE_NUMERIC);
        $newSheet->getStyle("P$row")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
    }

    $cellValue = $newSheet->getCell("A$row")->getValue();
    if (empty($cellValue)) {
        $newSheet->removeRow($row, 1); // A teljes sor törlése
        $row--;
        $highestRow--;
    }


    if ($row > 1 && $row <= $lastRow) {
        $newSheet->setCellValue("D$row", 'LOCTITE');
    }
   


}

$path = 'z:\szerző peti\LOCTITE.csv';

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
$newFileName = 'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\HENKEL ÁRLISTA ' . $currentDate . '.xlsx';
//$newFileName = 'Y:\Árlista 2024\FELTÖLTÖTT ÁRLISTÁK 2024\F-J\HOFFER\HOFFER ÁRLISTA ' . $currentDate . '.xlsx';
//ha élesbe akarod menteni az új fájlt 2024-ben


$writer = new Xlsx($newSpreadsheet);
$writer->save($newFileName);

echo "A fájl mentve: " . $newFileName;
