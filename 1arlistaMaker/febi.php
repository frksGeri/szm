<?php
require '../../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$path = 'y:\Árlista 2025\Price List Febi & Blue Print 20.01.2025_Szakal Metal.xlsx';

$manufacturers = [
    "FEBI",
    "BLUE"
];




class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {
        return $row > 2 && in_array($column, ['A', "C", "L", "M", "V", "W"]);
    }
}

function loadSizeData($manufacturer)
{
    $manuFactType = $manufacturer == "FEBI" ? "FEBI" : "BLUE PRINT";
    $path = 'Z:\szerző peti\\' . $manuFactType . '.csv';
    $sizeData = [];


    if (!file_exists($path)) {
        return $sizeData;
    }

    if (($file = fopen($path, "r")) !== FALSE) {
        fgetcsv($file, 1000, ",");
        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            if (empty($data[0])) continue;

            $asizeData = explode("\t", $data[0]);

            if (!isset($asizeData[3]) || $asizeData[3] === '') {
                continue;
            }

            $sizeData[] = [
                "articlecode" => trim($asizeData[2]),
                "size" => trim($asizeData[3])
            ];
        }
        fclose($file);
    }

    return $sizeData;
}

function modifySheetByManufacturer($sheet, $manufacturer, $lastRow, $sizeData)
{
    $manuFactType = $manufacturer == "FEBI" ? "_FEBI" : "_BLP";
    for ($row = 2; $row <= $lastRow; $row++) {
        $articleCode = trim($sheet->getCell('A' . $row)->getValue());

        $sheet->setCellValueExplicit('B' . $row, $articleCode . $manuFactType, DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('C' . $row, $manufacturer == "FEBI" ? "FEBI" : "BLUE PRINT", DataType::TYPE_STRING);
        $w = $sheet->getCell("M$row")->getValue();
        $sheet->setCellValue("M$row", $w * 1000);
    }
}

$reader = IOFactory::createReaderForFile($path);
$reader->setReadDataOnly(true);
$reader->setReadFilter(new MyReadFilter());
$spreadsheet = $reader->load($path);
$oldSheet = $spreadsheet->getSheet(0);

$allData = [];
foreach ($oldSheet->getRowIterator(2) as $oldRow) {
    $cellIterator = $oldRow->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    $rowData = [];
    $col = 1;
    foreach ($cellIterator as $cell) {
        $colIndex = Coordinate::stringFromColumnIndex($col);
        if (in_array($colIndex, ['A', "C", "L", "M", "V", "W"])) {
            $rowData[$colIndex] = $cell->getCalculatedValue();
        }
        $col++;
    }
    if (!empty($rowData)) {
        $allData[] = $rowData;
    }
}


foreach ($manufacturers as $manufacturer) {
    $filteredData = array_filter($allData, function ($row) use ($manufacturer) {
        return stripos($row['C'], $manufacturer) !== false;
    });

    if (!empty($filteredData)) {
  
        $filteredDataArray = array_values($filteredData);

        $sizeData = loadSizeData($manufacturer);
        $newSpreadsheet = new Spreadsheet();
        $newSheet = $newSpreadsheet->getActiveSheet();
        $newSheet->setTitle('todb');

        $headers = [
            'A' => 'code',
            'B' => 'articlecode',
            'C' => 'gyarto',
            'D' => 'size',
            'L' => 'moq',
            'M' => 'weight',
            'V' => 'barcode',
            'W' => 'price'

        ];
        foreach ($headers as $col => $header) {
            $newSheet->setCellValue($col . '1', $header);
        }

        $rowIndex = 2;
        foreach ($filteredDataArray as $rowData) {
            foreach ($rowData as $col => $value) {
                $newSheet->setCellValueExplicit($col . $rowIndex, $value, DataType::TYPE_STRING);
            }
            $rowIndex++;
        }

        $lastRow = $rowIndex - 1;
        $newSheet = modifySheetByManufacturer($newSheet, $manufacturer, $lastRow, $sizeData);

        $manufacturer = $manufacturer == "FEBI" ? "FEBI" : "BLUE PRINT";


        $currentDate = date('Y.m.d');
        $newFileName = sprintf(
            'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\%s_ÁRLISTA_%s.xlsx',
            $manufacturer,
            $currentDate
        );

        $writer = new Xlsx($newSpreadsheet);
        $writer->save($newFileName);

        echo "Fájl mentve: " . $newFileName . "\n";
    }
}
