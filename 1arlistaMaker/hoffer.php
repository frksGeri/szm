<?php
require '../../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

$path = 'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\hoffer_price_list 2024.10.03.xlsx';
//$path = 'Y:\Árlista 2024\.....xlsx';
//megkell adni az új price list fájl elérési útját. ^^ fájl neve kell már csak

$reader = IOFactory::createReaderForFile($path);
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load($path);
$oldSheet = $spreadsheet->getSheet(0);

$newSpreadsheet = new Spreadsheet();
$newSheet = $newSpreadsheet->getActiveSheet();
$newSheet->setTitle('todb');

$maxAllowedCol = 11; 
$aColumnCount = 0;
$lastDataRow = 1; 
foreach ($oldSheet->getRowIterator(2) as $rowIndex => $row) {
    $value = $oldSheet->getCell('A' . $rowIndex)->getValue();
    if (!empty($value)) {
        $aColumnCount++;
        $lastDataRow = $rowIndex;
    }
}


$fullDataColumns = [];
$headerOnlyColumns = [];


for ($col = 1; $col <= $maxAllowedCol; $col++) {
    if ($col == 3) continue; 
    $colString = Coordinate::stringFromColumnIndex($col);
    $dataCount = 0;
    $hasHeader = !empty($oldSheet->getCell($colString . '1')->getValue());

    
    foreach ($oldSheet->getRowIterator(2) as $rowIndex => $row) {
        $value = $oldSheet->getCell($colString . $rowIndex)->getValue();
        if (!empty($value)) {
            $dataCount++;
        }
    }

    if ($dataCount == $aColumnCount || $col == 11) {
        $fullDataColumns[] = $col;
    } elseif ($hasHeader) {
        $headerOnlyColumns[] = $col;
    }
}

$newRow = 1;
foreach ($oldSheet->getRowIterator() as $rowIndex => $row) {
    $rowHasData = false;


    foreach ($fullDataColumns as $col) {
        $colString = Coordinate::stringFromColumnIndex($col);
        $value = $oldSheet->getCell($colString . $rowIndex)->getValue();


        if ($col == 11 || !empty($value)) {
            $rowHasData = true;


            if ($col == 1 && $rowIndex > 1) { 
                $value .= '_HOF';
            }
          

            $newSheet->setCellValue($colString . $newRow, $value);
        }
    }


    if ($rowIndex > 1 && $rowIndex <= $lastDataRow) {
        $newSheet->setCellValue('D' . $newRow, 'HOFFER');
    }


    if ($rowIndex == 1) {
        foreach ($headerOnlyColumns as $col) {
            $colString = Coordinate::stringFromColumnIndex($col);
            $headerValue = $oldSheet->getCell($colString . '1')->getValue();
            if (!empty($headerValue)) {
                $newSheet->setCellValue($colString . '1', $headerValue);
            }
        }
    }


    if ($rowHasData) {
        $newRow++;
    }
}


$newSheet->setCellValue('A1', 'articlecode');
$newSheet->setCellValue('B1', 'code');
$newSheet->setCellValue('C1', 'size');
$newSheet->setCellValue('D1', 'gyarto');
$newSheet->setCellValue('E1', 'moq');
$newSheet->setCellValue('K1', 'weight_kg');

$path = 'Z:\szerző peti\HOFFER.csv';

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
    $articleCode = $newSheet->getCell('A' . $rowIndex)->getValue(); 
    
    
    foreach ($sizeData as $sizeRow) {
        if ($sizeRow['articlecode'] === $articleCode) {
            
            $newSheet->setCellValue('C' . $rowIndex, $sizeRow['size']);
            break; 
        }
    }
}


$currentDate = date('Y.m.d');
$newFileName = 'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\tesztHOFFER ÁRLISTA ' . $currentDate . '.xlsx';
//$newFileName = 'Y:\Árlista 2024\FELTÖLTÖTT ÁRLISTÁK 2024\F-J\HOFFER\HOFFER ÁRLISTA ' . $currentDate . '.xlsx';
//ha élesbe akarod menteni az új fájlt 2024-ben


$writer = new Xlsx($newSpreadsheet);
$writer->save($newFileName);

echo "A fájl mentve: " . $newFileName;
?>
