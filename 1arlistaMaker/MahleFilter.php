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

$path = 'A:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\K-O\MAHLE\_NYERS\MAHLE Filters_Price list 2025_04_SEE.xlsx';

class MyReadFilterF implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {

        $columns = ['A', 'B', 'F', 'H'];
        return in_array($column, $columns);
    }
}

$reader = IOFactory::createReaderForFile($path);
$reader->setReadDataOnly(true);
$reader->setReadFilter(new MyReadFilterF());
$spreadsheet = $reader->load($path);

$oldSheet = $spreadsheet->getSheet(0);
$newSpreadsheet = new Spreadsheet();
$newSheet = $newSpreadsheet->getActiveSheet();
$newSheet->setTitle('todb');

$newRow = 2;

foreach ($oldSheet->getRowIterator(5) as $oldRow) {
    $cellIterator = $oldRow->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    $newCol = 1;
    $rowData = [];

    foreach ($cellIterator as $cell) {
        $colIndex = Coordinate::stringFromColumnIndex($newCol);

        if (in_array($colIndex, ['A', 'B', 'F', 'H'])) {
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
    "A" => "code",
    "B" => "size",
    "C" => "articlecode",
    "D" => "gyarto",
    "F" => "moq",
    "H" => "price"
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
$average = 15;

for ($row = 2; $row <= $highestRow; $row++) {

    $code = $newSheet->getCell("A$row")->getValue();
    $articlecode = str_replace(" ", "", $code)."_MAHLE";
    $newSheet->setCellValue("C$row", $articlecode);

    $newSheet->setCellValue("D$row", "MAHLE");

    $priceValue = $newSheet->getCell("H$row")->getValue();
    $priceNum = convertToNumber($priceValue);
    if ($priceNum > $average * 3)
        echo "Filter: Átlagtól eltérően magas ár a(z) $articlecode cikkszámon: $priceValue <br/>";
    if ($priceNum < 0.2)
        echo "Filter: Nagyon alacsony ár a(z) $articlecode cikkszámon: $priceValue <br/>";
    $newSheet->setCellValue("H$row", str_replace(".", ",", $priceValue));

    $sizeValue = $newSheet->getCell("B$row")->getValue();
    if ($sizeValue == "set = 2 pieces")
        $newSheet->setCellValue("B$row", "2db");
    else
        if (array_key_exists($articlecode, $sizeData))
            $newSheet->setCellValue("B$row", $sizeData[$articlecode]);
        else
            $newSheet->setCellValue("B$row", "");
}

$currentDate = date('Y.m.d');
$newFileName = 'C:\Users\LP-KATALOGUS1\Desktop\generált_árlisták\MAHLE DE FILTERS ÁRLISTA ' . $currentDate . '.xlsx';

$writer = new Xlsx($newSpreadsheet);
$writer->save($newFileName);

echo "A fájl mentve: " . $newFileName;

?>
