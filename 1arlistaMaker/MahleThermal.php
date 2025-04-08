<?php

ini_set('memory_limit', '2G');
set_time_limit(300);

require '../../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

$path = "A:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\K-O\MAHLE\_NYERS\MAHLE CEE THERMAL PRICELIST April 2025.xlsx";

class MyReadFilterTH implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {

        $columns = ['C', 'M', 'O', 'I', 'AA', 'AH'];
        return in_array($column, $columns);
    }
}

$reader = IOFactory::createReaderForFile($path);
$reader->setReadDataOnly(true);
$reader->setReadFilter(new MyReadFilterTH());
$spreadsheet = $reader->load($path);

$oldSheet = $spreadsheet->getSheet(0);
$newSpreadsheet = new Spreadsheet();
$newSheet = $newSpreadsheet->getActiveSheet();
$newSheet->setTitle('todb');

$newRow = 2;

foreach ($oldSheet->getRowIterator(2) as $oldRow) {
    $cellIterator = $oldRow->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    $newCol = 1;
    $rowData = [];

    foreach ($cellIterator as $cell) {
        $colIndex = Coordinate::stringFromColumnIndex($newCol);

        if (in_array($colIndex, ['C', 'M', 'O', 'I', 'AA', 'AH'])) {
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

$headers = [
    "A" => "articlecode",
    "C" => "code",
    "D" => "gyarto",
    "M" => "moq",
    "O" => "country",
    "I" => "price",
    "AA" => "barcode",
    "AH" => "weight",
    "B" => "size"
];

foreach ($headers as $col => $header) {
    $newSheet->setCellValue($col . "1", $header);
}

$path = 'B:\szerző peti\MAHLE.csv';
$sizeData = [];
if (($file = fopen($path, "r")) !== FALSE) {
    while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
        if (!empty($data[0])) {
            $asizeData = explode("\t", $data[0]);

            if (isset($asizeData[2], $asizeData[3]) && !empty($asizeData[3])) {
                $sizeData[$asizeData[2]] = $asizeData[3];
            }
        }
    }
    fclose($file);
}

$lastRow = $newRow - 1;
$highestRow = $newSheet->getHighestRow();
$average = 300;

for ($row = 2; $row <= $highestRow; $row++) {

    $code = $newSheet->getCell("C$row")->getValue();
    $articlecode = str_replace(" ", "", $code)."_MAHLE";
    $newSheet->setCellValue("A$row", $articlecode);

    $newSheet->setCellValue("D$row", "MAHLE");

    $weightKg = convertToNumber($newSheet->getCell("AH$row")->getValue());
    $weightG = $weightKg * 1000;
    if ($weightG < 20)
        $newSheet->setCellValue("AH$row", 20);
    else
        $newSheet->setCellValue("AH$row", $weightG);

    $priceValue = $newSheet->getCell("I$row")->getValue();
    if ($priceValue == "-")
        $newSheet->setCellValue("I$row", "");
    else
    {
        $priceNum = convertToNumber($priceValue);
        if ($priceNum > $average * 3)
            echo "Thermal: Átlagtól eltérően magas ár a(z) $articlecode cikkszámon: $priceValue <br/>";
        if ($priceNum < 0.2)
            echo "Thermal: Nagyon alacsony ár a(z) $articlecode cikkszámon: $priceValue <br/>";
        $newSheet->setCellValue("I$row", str_replace(".", ",", $priceValue));
    }

    if (array_key_exists($articlecode, $sizeData))
        $newSheet->setCellValue("B$row", str_replace(".", ",", $sizeData[$articlecode]));
}

$currentDate = date('Y.m.d');
$newFileName = 'C:\Users\LP-KATALOGUS1\Desktop\generált_árlisták\MAHLE DE THERMAL ÁRLISTA ' . $currentDate . '.xlsx';

$writer = new Xlsx($newSpreadsheet);
$writer->save($newFileName);

echo "A fájl mentve: " . $newFileName;

?>
