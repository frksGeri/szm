<?php

require '../../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$path = 'y:\Árlista 2024\FELTÖLTÖTT ÁRLISTÁK 2024\K-O\OE GERMANY\_NYERS\OE Germany NEWS December 55.xlsx';

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {
        return in_array($column, ['A',  'D', 'E']);
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

        if (in_array($colIndex, ['A', 'D', 'E'])) {
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
    "D" => "price",
    "E" => "moq",
    "C" => "gyarto"
];

foreach($headers as $col => $header){
    $newSheet->setCellValue($col.'1',$header);
}

for($row = 2; $row <= $highestRow; $row++){
    $articlecodeValue = $newSheet->getCell("A$row")->getValue();
    $articlecodeValue = str_replace(" ","",$articlecodeValue);
    $newSheet->setCellValue("B$row",$articlecodeValue . "_OEG");

    $newSheet->setCellValue("C$row","OE GERMANY");

    $newSheet->getStyle("U$row")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
}

$currentDate = date('Y.m.d');
$manufacturer = "OEGERMANY";
$newFileName = sprintf('C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\%s_ÁRLISTA_%s.xlsx',
$manufacturer,$currentDate);

$writer = new Xlsx($newSpreadsheet);
$writer->save($newFileName);

echo("Fájl mentve:" . $newFileName . "\n");

?>
