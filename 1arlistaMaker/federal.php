<?php
require '../../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$path = 'y:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\F-J\FEDERAL\_NYERS\Moog  2339 reduced prices  valid from 10.03.2025.xlsx';

$manufacturers = [
    'AE',
    'BERAL',
    'BERU',
    'CHAMPION',
    'MOGUL',
    'FERODO',
    'GLYCO',
    'GOETZE',
    'JURID',
    'MONROE',
    'MOOG',
    'NURAL',
    'PAYEN',
    'WALKER'
];

$manufacturers = ['MOOG'];

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {
        return $row > 2 && in_array($column, ['A', 'B', 'E', 'G', 'H', 'I', 'O']);
    }
}

function loadSizeData($manufacturer)
{
    $path = 'Z:\szerző peti\\' . $manufacturer . '.csv';
    $sizeData = [];

    if (!file_exists($path) && $manufacturer === 'FERODO') {
        $manufacturer = 'FERODO PREMIER';
        $path = 'Z:\szerző peti\\' . $manufacturer . '.csv';
    }

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

function convertToNumber($value)
{
    if (is_numeric($value)) {
        return (float)$value;
    }

    $value = str_replace(',', '.', $value);
    $value = preg_replace('/[^0-9.]/', '', $value);

    return is_numeric($value) ? (float)$value : 0;
}

function modifySheetByManufacturer($sheet, $manufacturer, $lastRow, $sizeData)
{
    for ($row = 2; $row <= $lastRow; $row++) {
        $articleCode = trim($sheet->getCell('A' . $row)->getValue());

        $sheet->setCellValueExplicit('A' . $row, $articleCode, DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('D' . $row, $articleCode, DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('G' . $row, $sheet->getCell('G' . $row)->getValue(), DataType::TYPE_STRING);

        $weight = convertToNumber($sheet->getCell("E$row")->getValue());

        $sheet->setCellValue("E$row", $weight * 1000);

        if ($weight == "0") {
            $sheet->setCellValue("E$row", '');
        }

        $country = $sheet->getCell("H$row")->getValue();

        if ($country == '#N/A' || $country == 'N/A' || $weight == '#N/A') {
            $sheet->setCellValue("H$row", '');
            $sheet->setCellValue("I$row", '');
        }

        foreach ($sizeData as $sizeRow) {
            if (strtolower($articleCode) === strtolower($sizeRow['articlecode'])) {
                $sheet->setCellValue('C' . $row, $sizeRow['size']);
                break;
            }
        }

        $valueA = $sheet->getCell("A$row")->getValue();
        $sefix = explode(" ", $valueA);


        switch ($manufacturer) {
            case 'AE':
                $sheet->setCellValueExplicit('D' . $row, $articleCode, DataType::TYPE_STRING);
                break;
            case 'BERAL':
                $sheet->setCellValueExplicit('D' . $row, $articleCode . "_BERAL", DataType::TYPE_STRING);
                break;
            case 'BERU':
                $sheet->setCellValueExplicit('D' . $row, $articleCode . "_BERU", DataType::TYPE_STRING);
                break;
            case "JURID":
                $sheet->setCellValueExplicit("D" . $row, $articleCode . "_JURID", DataType::TYPE_STRING);
                break;
            case "MONROE":
                $sheet->setCellValueExplicit("D" . $row, $articleCode . "_MON", DataType::TYPE_STRING);
                break;
            case "WALKER":
                $walkerCodes = $sheet->getCell("A$row")->getValue();


                if (strlen($walkerCodes) < 5) {
                    $sheet->setCellValueExplicit("A$row", '0' . $walkerCodes, DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit("D$row", '0' . $walkerCodes . "_WAL", DataType::TYPE_STRING);
                } else {
                    $sheet->setCellValueExplicit("D$row", $walkerCodes . "_WAL", DataType::TYPE_STRING);
                }
                $sheet->setCellValueExplicit("D" . $row, $articleCode . "_WAL", DataType::TYPE_STRING);
                break;
            default:
                $sheet->setCellValueExplicit('D' . $row, $articleCode, DataType::TYPE_STRING);

                if ($manufacturer == "GLYCO") {
                    if (isset($sefix[0]) && !empty($sefix[0])) {
                        $sheet->setCellValueExplicit("D" . $row, $sefix[0], DataType::TYPE_STRING);
                    }

                    if (isset($sefix[1]) && !empty($sefix[1])) {
                        $formattedValue = str_replace("MM", "", $sefix[1]);
                        $formattedValue = str_replace("STD", "0.00", $formattedValue);
                        $sheet->setCellValue("C" . $row, $formattedValue);
                    }
                    $sheet->getStyle('C2:C' . $lastRow)
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
                }

                if ($manufacturer == "NURAL") {
                    $sheet->getStyle('F2:F' . $lastRow)
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

                    $replaceArticle = str_replace(" ", "", $valueA);
                    $sheet->setCellValueExplicit("D" . $row, $replaceArticle, DataType::TYPE_STRING);
                }

                if ($manufacturer == "FERODO") {
                    $sheet->setCellValue("B$row", "FERODO PREMIER");
                }
        }
    }
    return $sheet;
}

function removeDuplicates(&$allData)
{
    $uniqueData = [];
    $seenCodes = [];

    
    usort($allData, function ($a, $b) {
        $filledFieldsA = count(array_filter($a, function ($value) {
            return !empty($value) && $value !== '#N/A' && $value !== 'N/A';
        }));

        $filledFieldsB = count(array_filter($b, function ($value) {
            return !empty($value) && $value !== '#N/A' && $value !== 'N/A';
        }));

        
        return $filledFieldsB - $filledFieldsA;
    });

    
    foreach ($allData as $row) {
        $code = trim($row['A']); 

        if (!isset($seenCodes[$code])) {
          
            $seenCodes[$code] = true;
            $uniqueData[] = $row;
        } else {
          
            $currentFilledFields = count(array_filter($row, function ($value) {
                return !empty($value) && $value !== '#N/A' && $value !== 'N/A';
            }));

            echo "Duplikált kód kezelve: " . $code . " (" . $currentFilledFields . " kitöltött mező)\n";
        }
    }

    $allData = $uniqueData;
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
        if (in_array($colIndex, ['A', 'B', 'E', 'G', 'H', 'I', 'O'])) {
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
        return stripos($row['B'], $manufacturer) !== false;
    });

    if (!empty($filteredData)) {
        
        $filteredDataArray = array_values($filteredData);
        removeDuplicates($filteredDataArray);

        $sizeData = loadSizeData($manufacturer);
        $newSpreadsheet = new Spreadsheet();
        $newSheet = $newSpreadsheet->getActiveSheet();
        $newSheet->setTitle('todb');

        $headers = [
            'A' => 'code',
            'B' => 'gyarto',
            'C' => 'size',
            'D' => 'articlecode',
            'E' => 'weight',
            'G' => 'barcode',
            'H' => 'country',
            'I' => 'moq',
            'O' => 'price'
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
