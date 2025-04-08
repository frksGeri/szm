<?php
/*
set_time_limit(0);

require '../../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

$path = 'y:\Szakal_Abakus_pricelist_2024_12_10.xlsx';

$manufacturers = [
    "ATE",
    "BREMBO",
    "DEPO",
    "INA",
    "KAYABA",
    "LORO",
    "MAHLE",
    "VALEO"
];

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {
        return $row > 1 && in_array($column, ['A', 'F', 'I', 'M', 'Q', 'R', 'S', 'T']);
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

foreach ($oldSheet->getRowIterator(1) as $oldRow) {
    $cellIterator = $oldRow->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    $newCol = 1;
    $rowData = [];

    foreach ($cellIterator as $cell) {
        $colIndex = Coordinate::stringFromColumnIndex($newCol);

        if ($colIndex === 'A' && $colIndex === "I") {
            $cellValue = $cell->getValue();
            $rowData[$colIndex] = $cellValue;
            $newSheet->setCellValueExplicit($colIndex . $newRow, $cellValue, DataType::TYPE_STRING);
        } elseif ($colIndex === 'C') {
            $cellValue = $cell->getValue();


            if (is_numeric($cellValue)) {
                $rowData[$colIndex] = number_format((float)$cellValue, 2, '.', '');
                $newSheet->setCellValue($colIndex . $newRow, $rowData[$colIndex]);
            } else {
                $rowData[$colIndex] = $cellValue;
                $newSheet->setCellValue($colIndex . $newRow, $cellValue);
            }
        } else {
            $cellValue = $cell->getValue();
            $rowData[$colIndex] = $cellValue;
            $newSheet->setCellValue($colIndex . $newRow, $cellValue);
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
    'A' => 'code',
    'B' => 'articlecode',
    'C' => 'price',
    'D' => 'size',
    'F' => 'gyarto',
    'I' => 'barcode',
    'M' => 'weight_kg',
    'Q' => 'oe1',
    'R' => 'oe2',
    'S' => 'oe3',
    'T' => 'oe4'
];

foreach ($headers as $col => $header) {
    $newSheet->setCellValue($col . "1", $header);
}

for ($row = 2; $row <= $highestRow; $row++) {

    $gyartok = $newSheet->getCell("F$row")->getValue();
    $gyartok = strtoupper($gyartok);

    if (strpos($gyartok, "LORO") !== FALSE) {
        $newSheet->setCellValue("F$row", "LORO");
    } elseif (strpos($gyartok, "DEPO") !== FALSE && strpos($gyartok,"MIRROR") !== FALSE) {
        $newSheet->setCellValue("F$row", "DEPO");
    }

    $articleCode = $newSheet->getCell("A$row")->getValue();
    $gyartokAgain = $newSheet->getCell("F$row")->getValue();

    if ($gyartokAgain == "ATE") {
        $newSheet->setCellValue("B$row", $articleCode . "_ATE");
    } elseif ($gyartokAgain == "BREMBO") {
        $newSheet->setCellValue("B$row", $articleCode . "_BREMBO");
    } elseif ($gyartokAgain == "DEPO") {
        $newSheet->setCellValue("B$row", $articleCode . "_AB");
    } elseif ($gyartokAgain == "INA") {
        $newSheet->setCellValue("B$row", $articleCode . "_INA");
    } elseif ($gyartokAgain == "KAYABA") {
        $newSheet->setCellValue("B$row", $articleCode . "_KYB");
    } elseif ($gyartokAgain == "LORO") {
        $newSheet->setCellValue("B$row", $articleCode . "_AB");
    } elseif ($gyartokAgain == "MAHLE") {
        $newSheet->setCellValue("B$row", $articleCode . "_MAHLE");
    } elseif ($gyartokAgain == "VALEO") {
        $newSheet->setCellValue("B$row", $articleCode . "_VALEO");
    }
}

foreach ($manufacturers as $manufacturer) {
    $path = 'Z:\szerző peti\\' . $manufacturer . '.csv';

    $getData = [];

    if (!file_exists($path) && $manufacturer == "KAYABA") {
        $manufacturer = "KYB";
        $path = 'Z:\szerző peti\\' . $manufacturer . '.csv';
    }

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

                $newSheet->setCellValue('D' . $rowIndex, $sizeRow['size']);
                break;
            }
        }
    }
}

$currentDate = date('Y.m.d');
$newFileName = sprintf(
    'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\ABAKUS2_ÁRLISTÁK_%s.xlsx',
    $currentDate
);

$writer = new Xlsx($newSpreadsheet);
$writer->save($newFileName);

echo "Fájl mentve: " . $newFileName . "\n";
echo "Feldolgozás befejezve!";
*/

require '../../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ExcelProcessor {
    private array $manufacturers = [
        "ATE" => "_ATE",
        "BREMBO" => "_BREMBO",
        "DEPO" => "_AB",
        "INA" => "_INA",
        "KAYABA" => "_KYB",
        "LORO" => "_AB",
        "MAHLE" => "_MAHLE",
        "VALEO" => "_VALEO"
    ];
    
    private $sizeDataCache = [];
    
    public function process(string $inputPath, string $outputPath): void {
        $spreadsheet = $this->loadSpreadsheet($inputPath);
        $newSpreadsheet = $this->createNewSpreadsheet($spreadsheet);
        $this->loadAllSizeData();
        $this->processRows($newSpreadsheet->getActiveSheet());
        $this->saveSpreadsheet($newSpreadsheet, $outputPath);
    }
    
    private function loadSpreadsheet(string $path): Spreadsheet {
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $reader->setReadFilter(new class implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {
            public function readCell($column, $row, $worksheetName = ''): bool {
                return $row > 1 && in_array($column, ['A', 'C', 'G', 'I']);
            }
        });
        return $reader->load($path);
    }
    
    private function createNewSpreadsheet(Spreadsheet $oldSpreadsheet): Spreadsheet {
        $newSpreadsheet = new Spreadsheet();
        $newSheet = $newSpreadsheet->getActiveSheet();
        $newSheet->setTitle('todb');
        
        
        $headers = ['A' => 'code', 'B' => 'articlecode', 'C' => 'price', 
                   'D' => 'size', 'G' => 'gyarto', 'I' => 'oe1'];
        foreach ($headers as $col => $header) {
            $newSheet->setCellValue($col . "1", $header);
        }
        
        
        $oldSheet = $oldSpreadsheet->getSheet(0);
        $newRow = 2;
        
        foreach ($oldSheet->getRowIterator(2) as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            
            $code = $cellIterator->current()->getValue();
            $price = number_format($cellIterator->seek('C')->current()->getValue(), 2, '.', '');
            $gyarto = $cellIterator->seek('G')->current()->getValue();
            $oe1 = $cellIterator->seek('I')->current()->getValue();
            
            $newSheet->setCellValueExplicit("A$newRow", $code, DataType::TYPE_STRING);
            $newSheet->setCellValue("C$newRow", $price);
            $newSheet->setCellValue("G$newRow", $gyarto);
            $newSheet->setCellValue("I$newRow", $oe1);
            
            $newRow++;
        }
        
        return $newSpreadsheet;
    }
    
    private function loadAllSizeData(): void {
        foreach ($this->manufacturers as $manufacturer => $suffix) {
            $path = 'Z:\szerző peti\\' . ($manufacturer === 'KAYABA' ? 'KYB' : $manufacturer) . '.csv';
            if (!file_exists($path)) continue;
            
            $this->sizeDataCache[$manufacturer] = [];
            $handle = fopen($path, 'r');
            fgets($handle); 
            
            while (($line = fgets($handle)) !== false) {
                $data = explode("\t", trim($line));
                if (!empty($data[2]) && !empty($data[3])) {
                    $this->sizeDataCache[$manufacturer][$data[2]] = $data[3];
                }
            }
            fclose($handle);
        }
    }
    
    private function processRows($sheet): void {
        $highestRow = $sheet->getHighestRow();
        
        for ($row = 2; $row <= $highestRow; $row++) {
            $gyarto = strtoupper($sheet->getCell("G$row")->getValue());
            
            
            if (strpos($gyarto, "LORO") !== FALSE) {
                $gyarto = "LORO";
            } elseif (strpos($gyarto, "DEPO") !== FALSE) {
                $gyarto = "DEPO";
            }
            $sheet->setCellValue("G$row", $gyarto);
            
            
            $code = $sheet->getCell("A$row")->getValue();
            if (isset($this->manufacturers[$gyarto])) {
                $articleCode = $code . $this->manufacturers[$gyarto];
                $sheet->setCellValue("B$row", $articleCode);
                
            
                if (isset($this->sizeDataCache[$gyarto][$articleCode])) {
                    $sheet->setCellValue("D$row", $this->sizeDataCache[$gyarto][$articleCode]);
                }
            }
        }
    }
    
    private function saveSpreadsheet(Spreadsheet $spreadsheet, string $outputPath): void {
        $writer = new Xlsx($spreadsheet);
        $writer->save($outputPath);
        echo "Fájl mentve: $outputPath\n";
    }
}


$processor = new ExcelProcessor();
$inputPath = 'y:\Árlista 2024\FELTÖLTÖTT ÁRLISTÁK 2024\A-E\ABAKUS\_NYERS\SzakalM_Abakus_pricelist_2024_11_13.xlsx';
$outputPath = 'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\ABAKUS2_ÁRLISTÁK_' . date('Y.m.d') . '.xlsx';
$processor->process($inputPath, $outputPath);
echo "Feldolgozás befejezve!";
