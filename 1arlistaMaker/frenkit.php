<?php

require '../../phpspreadsheet/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


$path = 'y:\Árlista 2025\Frenkit 2025 price list kits.xlsx';

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter{

    public function readCell($column,$row,$worksheetName = ''): bool
    {
        if($row < 16){
            return false;
        }
        return in_array($column, ['A','D','F','H','J']);
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

foreach ($oldSheet->getRowIterator(16) as $oldRow) {
    $cellIterator = $oldRow->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    $newCol = 1;
    $rowData = [];

    foreach ($cellIterator as $cell) {
        $colIndex = Coordinate::stringFromColumnIndex($newCol);

        if (in_array($colIndex, ['A','D','F','H','J'])) {
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

$lastRow = $newRow -1;
$highestRow = $newSheet->getHighestRow();

$headers = [
    'A' => 'code',
    'B' => 'articlecode',
    'D' => 'barcode',
    'F' => 'weight',
    'G' => 'gyarto',
    'H' => 'price',
    'J' => 'moq'
];

foreach($headers as $col => $header){
    $newSheet->setCellValue($col . '1', $header);
}

for($row = 1; $row <= $highestRow; $row++){

    $acode = $newSheet->getCell("A$row")->getValue();
    $newSheet->setCellValueExplicit("B$row",$acode . "_FR",DataType::TYPE_STRING);
    $newSheet->setCellValue("G$row",'FRENKIT');

}


$currentDate = date('Y.m.d');
$newFileName = 'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\FRENKIT ÁRLISTA ' . $currentDate . '.xlsx';

$writer = new Xlsx($newSpreadsheet);
$writer->save($newFileName);

echo 'Mentve ide:' . $newFileName;

?>
