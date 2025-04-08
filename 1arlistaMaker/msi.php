<?php
require '../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$path = 'y:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\K-O\MSI\_NYERS\MSI Price list from 01.04.2025.xlsx';

$manufacturers = [
    "BF Original",
    "KOLBENSCHMIDT",
    "PIERBURG",
    "TRW",
];

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {
        $columns = ['B', 'C', 'D', 'H', 'N', 'AC', 'AV'];
        return in_array($column, $columns);
    }
}

function modifySheetByManufacturer($sheet, $manufacturer, $lastRow, $sizeData)
{
    switch ($manufacturer) {
        case 'BF Original':
        case 'KOLBENSCHMIDT':
        case 'PIERBURG':
        case 'TRW':


            for ($row = 2; $row <= $lastRow; $row++) {
                $value = trim($sheet->getCell('B' . $row)->getValue());
                $sheet->setCellValueExplicit("I$row", $value, DataType::TYPE_STRING2);

                $articleCode = trim($sheet->getCell('I' . $row)->getValue());

                if (!empty($sizeData)) {
                    foreach ($sizeData as $sizeRow) {
                        $sizeArticleCode = preg_replace('/\s+/', '', strtolower(trim($sizeRow['articlecode'])));
                        $currentArticleCode = preg_replace('/\s+/', '', strtolower(trim($articleCode)));

                        if ($sizeArticleCode === $currentArticleCode) {
                            $sheet->setCellValue('J' . $row, $sizeRow['size']);
                            break;
                        }
                    }
                }


                $valueN = $sheet->getCell("N" . $row)->getValue();
                $sefix = explode(",", $valueN);
                if (isset($sefix[0]) && !empty($sefix[0])) {
                    $sheet->setCellValue("N$row", $sefix[0]);
                }

                $price = $sheet->getCell("AV$row")->getValue();
                if (!isset($price) || $price == NULL || $price == 0 || empty($price)) {
                    continue;
                }

                $weight = $sheet->getCell("AC$row")->getValue();
                $sheet->setCellValue("AC$row", $weight * 1000);

                if($manufacturer == 'TRW'){
                    $sheet->setCellValueExplicit("I$row",$value . "_TRWE",DataType::TYPE_STRING);
                    $sheet->setCellValue("D$row","TRW engine component");
                }
            }
            break;
    }
    return $sheet;
}


function loadSizeData($manufacturer)
{
    $path = 'Z:\szerző peti\\' . $manufacturer . '.csv';

    if($manufacturer == 'TRW'){
        $manufacturer == "TRW engine component";
    }
    $sizeData = [];

    if (!file_exists($path)) {
        return $sizeData;
    }

    if (($file = fopen($path, "r")) !== FALSE) {
        $header = fgetcsv($file, 1000, ",");
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

$reader = IOFactory::createReaderForFile($path);
$reader->setReadDataOnly(true);
$reader->setReadFilter(new MyReadFilter());
$spreadsheet = $reader->load($path);
$oldSheet = $spreadsheet->getSheet(0);

$allData = [];
foreach ($oldSheet->getRowIterator(1) as $oldRow) {
    $cellIterator = $oldRow->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    $rowData = [];
    $col = 1;
    foreach ($cellIterator as $cell) {
        $colIndex = Coordinate::stringFromColumnIndex($col);
        if (in_array($colIndex,  ['B', 'C', 'D', 'H', 'N', 'AC', 'AV'])) {
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
        return stripos($row['D'], $manufacturer) !== false;
    });

    if (!empty($filteredData)) {
        $newSpreadsheet = new Spreadsheet();
        $newSheet = $newSpreadsheet->getActiveSheet();
        $newSheet->setTitle('todb');

        $headers = [
            'B' => 'code',
            'D' => 'gyarto',
            'I' => 'articlecode',
            'J' => 'size',
            'H' => 'barcode',
            'N' => 'moq',
            "AC" => "weight",
            'AV' => 'price',
        ];

        foreach ($headers as $col => $header) {
            $newSheet->setCellValue($col . '1', $header);
        }

        $rowIndex = 2;
        foreach ($filteredData as $rowData) {
            foreach ($rowData as $col => $value) {
                $newSheet->getCell($col . $rowIndex)->setValueExplicit($value, DataType::TYPE_STRING);
            }
            $rowIndex++;
        }

        $lastRow = $rowIndex - 1;

        $sizeData = loadSizeData($manufacturer);
        $newSheet = modifySheetByManufacturer($newSheet, $manufacturer, $lastRow, $sizeData);

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

echo "Feldolgozás befejezve!";
