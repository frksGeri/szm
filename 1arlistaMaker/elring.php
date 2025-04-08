<?php


require '../../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


$path = 'y:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\A-E\ELRING\_NYERS\ELRING 2025_Szakal-Met.xlsx';

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {
        if ($row < 9) {
            return false;
        }
        $columns = ['A', 'Q', 'U', 'V', 'AF', 'AH'];
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

foreach ($oldSheet->getRowIterator(10) as $oldRow) {
    $cellIterator = $oldRow->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    $newCol = 1;
    $rowData = [];

    foreach ($cellIterator as $cell) {
        $colIndex = Coordinate::stringFromColumnIndex($newCol);

        if (in_array($colIndex, ['A', 'Q', 'U', 'V', 'AF', 'AH'])) {
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
    "B" => "articlecode",
    "C" => "size",
    "D" => "gyarto",
    "Q" => "moq",
    "U" => "barcode",
    "V" => "weight",
    "AF" => "price",
    "AH" => "country"
];

foreach ($headers as $col => $header) {
    $newSheet->setCellValue($col . '1', $header);
}

for ($row = 2; $row <= $highestRow; $row++) {
    $newSheet->setCellValue("D$row", "ELRING");

    
    $newSheet->getCell('B' . $row)->setDataType(DataType::TYPE_STRING);

    $moq = $newSheet->getCell("Q$row")->getValue();
    $moq = ltrim($moq, '0'); 
    if ($moq === '') { 
        $moq = '0';
    }
    $newSheet->setCellValue("Q$row", $moq);

    $weight = $newSheet->getCell("V$row")->getValue(); 
    $weight = is_numeric($weight) ? (float)$weight : 0;    
    
    $newSheet->setCellValue("V$row", $weight * 1000);
    
    $newSheet->getStyle("V$row")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

    $articlecodeValue = $newSheet->getCell("A$row")->getValue();
    $newSheet->setCellValue("B$row", $articlecodeValue . "_ELR");

    $barcodeValue = $newSheet->getCell("U$row")->getValue();
    if (is_numeric($barcodeValue)) {
        $newSheet->getCell('U' . $row)->setDataType(DataType::TYPE_NUMERIC);
        $newSheet->getStyle("U$row")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
    }
}


$path = 'Z:\szerző peti\ELRING.csv';

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
$newFileName = 'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\ELRING ÁRLISTA ' . $currentDate . '.xlsx';

$writer = new Xlsx($newSpreadsheet);
$writer->save($newFileName);

echo "A fájl mentve: " . $newFileName;
