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

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {

        $columns = ['A', 'J', 'T', 'U', 'V', 'X', 'Y', 'AD'];
        return in_array($column, $columns);
    }
}

function processExcelChunk($inputPath, $chunkSize = 1000)
{
    $reader = IOFactory::createReaderForFile($inputPath);
    $reader->setReadDataOnly(true);
    $reader->setReadFilter(new MyReadFilter());


    $newSpreadsheet = new Spreadsheet();
    $newSheet = $newSpreadsheet->getActiveSheet();
    $newSheet->setTitle('todb');


    $headers = [
        "A" => "code",
        "B" => "articlecode",
        "C" => "size",
        "E" => "sellingprice",
        "I" => "gyarto",
        "J" => "barcode",
        "T" => "moq",
        "U" => "price",
        "V" => "kaucioe",
        "X" => "recprice",
        "Y" => "weight",
        "AD" => "country"
    ];

    foreach ($headers as $col => $header) {
        $newSheet->setCellValue($col . "1", $header);
    }


    $worksheet = $reader->load($inputPath)->getActiveSheet();
    $highestRow = $worksheet->getHighestRow();

    $refundArray = [];
    $currentOutputRow = 2;

    for ($startRow = 2; $startRow <= $highestRow; $startRow += $chunkSize) {
        $endRow = min($startRow + $chunkSize - 1, $highestRow);

        for ($row = $startRow; $row <= $endRow; $row++) {
            $rowData = [
                'A' => $worksheet->getCell('A' . $row)->getValue(),
                'J' => $worksheet->getCell('J' . $row)->getValue(),
                'T' => $worksheet->getCell('T' . $row)->getValue(),
                'U' => $worksheet->getCell('U' . $row)->getValue(),
                'V' => $worksheet->getCell('V' . $row)->getValue(),
                'X' => $worksheet->getCell('X' . $row)->getValue(),
                'Y' => $worksheet->getCell('Y' . $row)->getValue(),
                'AD' => $worksheet->getCell('AD' . $row)->getValue()
            ];


            if (!empty($rowData['A'])) {
                $newSheet->setCellValueExplicit('A' . $currentOutputRow, $rowData['A'], DataType::TYPE_STRING);
                $newSheet->setCellValue('B' . $currentOutputRow, $rowData['A'] . '_METZ');
                $newSheet->setCellValue('I' . $currentOutputRow, 'METZGER');


                $sellingPriceData = convertToNumber($rowData['X']);
                $newSheet->setCellValue('E' . $currentOutputRow, $sellingPriceData / 0.75);
                $newSheet->getStyle("E$currentOutputRow")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);


                $weight = convertToNumber($rowData['Y']);
                $newSheet->setCellValue('Y' . $currentOutputRow, $weight * 1000);


                foreach (['J', 'T', 'U', 'X', 'AD'] as $col) {
                    if (!empty($rowData[$col])) {
                        $newSheet->setCellValueExplicit($col . $currentOutputRow, $rowData[$col], DataType::TYPE_STRING);
                    }
                }

                $currentOutputRow++;
            }


            if (!empty($rowData['V'])) {
                $refundArray[] = $rowData;
            }
        }


        unset($rowData);
        gc_collect_cycles();
    }


    foreach ($refundArray as $refundRow) {
        if (strpos($refundRow['A'], 'Metzger') === false) {
            $newSheet->setCellValue("A" . $currentOutputRow, $refundRow['A'] . '_DEPOSIT');
            $newSheet->setCellValue("B" . $currentOutputRow, $refundRow['A'] . '_METZ_KAUCIO');
            $newSheet->setCellValue("I" . $currentOutputRow, 'METZGER');
            $newSheet->setCellValue("U" . $currentOutputRow, (int)$refundRow['V']);
            $newSheet->setCellValue("T" . $currentOutputRow, $refundRow['T']);
            $w = convertToNumber($refundRow['Y']);

            $newSheet->setCellValue("Y" . $currentOutputRow, $w * 1000);
            $newSheet->setCellValue("AD" . $currentOutputRow, $refundRow['AD']);
            $currentOutputRow++;
        }
    }

    return $newSpreadsheet;
}

function convertToNumber($value)
{
    if (is_numeric($value)) {
        return (float)$value;
    }
    $value = str_replace(',', '.', $value);
    $value = preg_replace('/[^0-9.]/', '', $value);
    return is_numeric($value) ? (float)$value : 0;
}


function loadSizeData($csvPath)
{
    $sizeData = [];
    if (($file = fopen($csvPath, "r")) !== FALSE) {
        $header = fgetcsv($file, 1000, ",");
        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            $asizeData = explode("\t", $data[0]);
            if (!isset($asizeData[3]) || $asizeData[3] === '') {
                continue;
            }
            $sizeData[] = [
                "articlecode" => $asizeData[2],
                "size" => $asizeData[3]
            ];
        }
        fclose($file);
    }
    return $sizeData;
}


try {
    $inputPath = 'y:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\K-O\METZGER\_NYERS\Pricelist Szakal 27090.xlsx';
    $csvPath = 'Z:\szerző peti\METZGER.csv';


    $newSpreadsheet = processExcelChunk($inputPath);
    $newSheet = $newSpreadsheet->getActiveSheet();


    $sizeData = loadSizeData($csvPath);
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
    $newFileName = 'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\METZGER ÁRLISTA ' . $currentDate . '.xlsx';

    $writer = new Xlsx($newSpreadsheet);
    $writer->save($newFileName);

    echo "A fájl mentve: " . $newFileName;
} catch (Exception $e) {
    echo "Hiba történt: " . $e->getMessage();
}
