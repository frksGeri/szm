<?php


require '../../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$path = "y:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\A-E\BOSAL\NYERS\CE_PriceLists_Bosal_aftermarket_from_25_11_2024.xlsx";

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {
        if ($row < 4) {
            return false;
        }
        $columns = ['B', 'C', 'I', 'J', 'O'];
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

        if (in_array($colIndex, ['B', 'C', 'I', 'J', 'O'])) {
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
    "A" => "articlecode",
    "B" => "code",
    "C" => "price",
    "D" => "size",
    "E" => "gyarto",
    "I" => "packingunit",
    "J" => "barcode",
    "O" => "weight"
];

foreach ($headers as $col => $header) {
    $newSheet->setCellValue($col . '1', $header);
}

for ($row = 2; $row <= $highestRow; $row++) {

    $articlecode = $newSheet->getCell("B$row")->getValue();
    $newSheet->setCellValue("A$row", $articlecode . "_BOSAL");

    $newSheet->setCellValue("E$row", "BOSAL");

    $weight = convertToNumber($newSheet->getCell("O$row")->getValue());

    $newSheet->setCellValue("O$row", $weight * 1000);

    $ideiglenesMoq = $newSheet->getCell("I$row")->getValue();
    $price = convertToNumber($newSheet->getCell("C$row")->getValue());

    if (!empty($ideiglenesMoq) && $ideiglenesMoq != "1") {
        $newSheet->setCellValue("C$row", $price / $ideiglenesMoq);
    }
}


$path = 'z:\szerző peti\BOSAL.csv';
$sizeData = [];

if (($file = fopen($path, "r")) !== FALSE) {
    $header = fgetcsv($file, 1000, ",");

    while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
        if (!empty($data[0])) {
            $asizeData = explode("\t", $data[0]);

            if (isset($asizeData[2], $asizeData[3]) && !empty($asizeData[3])) {
                $sizeData[] = [
                    "articlecode" => $asizeData[2],
                    "size" => $asizeData[3]
                ];
            }
        }
    }
    fclose($file);
}

foreach ($newSheet->getRowIterator(2) as $row) {
    $rowIndex = $row->getRowIndex();
    $articleCode = $newSheet->getCell('A' . $rowIndex)->getValue();

    foreach ($sizeData as $sizeRow) {
        if ($sizeRow['articlecode'] === $articleCode) {
            $newSheet->setCellValue('D' . $rowIndex, $sizeRow['size']);
            break;
        }
    }
}

$currentDate = date('Y.m.d');
$newFileName = 'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\BOSAL_ÁRLISTA ' . $currentDate . '.xlsx';

$writer = new Xlsx($newSpreadsheet);
$writer->save($newFileName);

echo "A fájl mentve: " . $newFileName;
