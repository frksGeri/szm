<?php

require '../../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use ZipStream\Exception\FileNotReadableException;

$path = 'y:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\A-E\EXEDY\_NYERS\Price List  - 170225.xlsx';

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {
        return $row > 2 && in_array($column, ['A', 'F', 'G']);
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

        if (in_array($colIndex, ['A', 'F', 'G'])) {
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

$headers =  [
    "A" => "code",
    "B" => "articlecode",
    "C" => "gyarto",
    "D" => "size",
    "F" => "price",
    "G" => "moq"
];

foreach($headers as $key => $header){
    $newSheet->setCellValue($key . "1",$header);

}

for ($row = 2; $row <= $highestRow; $row++) {
    $newSheet->setCellValue("C$row",'EXEDY');
    $acode = $newSheet->getCell("A$row")->getValue();
    $newSheet->setCellValueExplicit("B$row",$acode . "_EXEDY",DataType::TYPE_STRING);

    
}

for($row = $highestRow; $row > 2; $row--){
    $moqValue = $newSheet->getCell("G$row")->getValue();
    if(is_numeric($moqValue) && $moqValue > 1){
        $newSheet->removeRow($row);
    }
}
$path = 'Z:\szerző peti\EXEDY.csv';

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

            $newSheet->setCellValue('D' . $rowIndex, $sizeRow['size']);
            break;
        }
    }
} 

$currentDate = date('Y.m.d');
$newFileName = 'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\EXEDY ÁRLISTA ' . $currentDate . '.xlsx';

$writer = new Xlsx($newSpreadsheet);
$writer->save($newFileName);

echo "A fájl mentve: " . $newFileName;

?>
