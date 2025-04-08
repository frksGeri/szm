<?php

require '../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$path = 'y:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\P-T\TRUCKTEC\_NYERS\PL_59008 Szakal_01.04.2025.xlsx';

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {
        return in_array($column, ['A', 'G', 'H', 'K']);
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


$uniqueValues = [];
$newRow = 2;

foreach ($oldSheet->getRowIterator(2) as $row) {
    $rowIndex = $row->getRowIndex();
    $cellValue = $oldSheet->getCell("A$rowIndex")->getValue();

    if (!in_array($cellValue, $uniqueValues)) {
        $uniqueValues[] = $cellValue;
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);
        $newCol = 1;

        foreach ($cellIterator as $cell) {
            $colIndex = Coordinate::stringFromColumnIndex($newCol);
            if (in_array($colIndex, ['A', 'G', 'H', 'K'])) {
                $newSheet->setCellValueExplicit($colIndex . $newRow, $cell->getValue(),DataType::TYPE_STRING2);
            }
            $newCol++;
        }

        $newRow++;
    }
}


$headers = [
    "A" => "code",
    "B" => "articlecode",
    "C" => "gyarto",
    "D" => "size",
    "G" => "barcode",
    "H" => "weight",
    "K" => "price"
];
foreach ($headers as $col => $header) {
    $newSheet->setCellValue("$col" . "1", $header);
}


for ($row = 2; $row < $newRow; $row++) {
    $articlecode = $newSheet->getCell("A$row")->getValue();
    $newSheet->setCellValue("B$row", $articlecode);
    $newSheet->setCellValue("C$row", "TRUCKTEC");
    $newSheet->getStyle("G$row")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
    $newSheet->getStyle("H$row")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
}


$path = 'Z:\szerző peti\TRUCKTEC.csv';
$sizeData = [];
if (($file = fopen($path, "r")) !== FALSE) {
    fgetcsv($file, 1000, ","); 
    while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
        $asizeData = explode("\t", $data[0]);
        if (isset($asizeData[3]) && $asizeData[3] !== '') {
            $sizeData[$asizeData[2]] = $asizeData[3];
        }
    }
    fclose($file);
}


foreach ($newSheet->getRowIterator(2) as $row) {
    $rowIndex = $row->getRowIndex();
    $articleCode = $newSheet->getCell('B' . $rowIndex)->getValue();
    if (isset($sizeData[$articleCode])) {
        $newSheet->setCellValue('D' . $rowIndex, $sizeData[$articleCode]);
    }
}

// Fájl mentése
$currentDate = date('Y.m.d');
$manufacturer = "TRUCKTEC";
$newFileName = sprintf(
    'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\%s_ÁRLISTA_%s.xlsx',
    $manufacturer,
    $currentDate
);

$writer = new Xlsx($newSpreadsheet);
$writer->save($newFileName);

echo ("Fájl mentve:" . $newFileName . "\n");

