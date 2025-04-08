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

$path = "A:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\K-O\MAHLE\_NYERS\MAHLE Thermostats_Pricelist 2025_04_MESE.xlsx";

class MyReadFilterTS implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {

        $columns = ['B', 'D'];
        return in_array($column, $columns);
    }
}

$reader = IOFactory::createReaderForFile($path);
$reader->setReadDataOnly(true);
$reader->setReadFilter(new MyReadFilterTS());
$spreadsheet = $reader->load($path);

$oldSheet = $spreadsheet->getSheet(0);
$newSpreadsheet = new Spreadsheet();
$newSheet = $newSpreadsheet->getActiveSheet();
$newSheet->setTitle('todb');

$newRow = 2;

foreach ($oldSheet->getRowIterator(3) as $oldRow) {
    $cellIterator = $oldRow->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    $newCol = 1;
    $rowData = [];

    foreach ($cellIterator as $cell) {
        $colIndex = Coordinate::stringFromColumnIndex($newCol);

        if (in_array($colIndex, ['B', 'D'])) {
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
    "B" => "code",
    "D" => "price",
    "C" => "size",
    "E" => "gyarto"
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
$average = 40;

for ($row = 2; $row <= $highestRow; $row++) {

    $code = $newSheet->getCell("B$row")->getValue();
    $articlecode = str_replace(" ", "", $code)."_MAHLE";
    $newSheet->setCellValue("A$row", $articlecode);

    $newSheet->setCellValue("E$row", "MAHLE");

    $priceValue = $newSheet->getCell("D$row")->getValue();
    $priceNum = convertToNumber($priceValue);
    if ($priceNum > $average * 3)
        echo "Thermostat: Átlagtól eltérően magas ár a(z) $articlecode cikkszámon: $priceValue <br/>";
    if ($priceNum < 0.2)
        echo "Thermostat: Nagyon alacsony ár a(z) $articlecode cikkszámon: $priceValue <br/>";
    $newSheet->setCellValue("D$row", str_replace(".", ",", $priceValue));

    if (array_key_exists($articlecode, $sizeData))
        $newSheet->setCellValue("C$row", $sizeData[$articlecode]);
}

$currentDate = date('Y.m.d');
$newFileName = 'C:\Users\LP-KATALOGUS1\Desktop\generált_árlisták\MAHLE DE THERMOSTAT ÁRLISTA ' . $currentDate . '.xlsx';

$writer = new Xlsx($newSpreadsheet);
$writer->save($newFileName);

echo "A fájl mentve: " . $newFileName;

?>
