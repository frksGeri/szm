<?php
require '../../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    private $columns = ['C', 'D', 'F', 'K', 'M'];
    
    public function readCell($column, $row, $worksheetName = ''): bool
    {
        return in_array($column, $this->columns);
    }
}

class ExcelProcessor {
    private $sufixArticlecodes = [
        "BORSEHUNG" => "_BOR",
        "DPA" => "_DPA",
        "GARRETT" => "_GAR",
        "POLAND" => "_POL",
        "VAG" => "_VAG",
        "VIKA" => "_VIKA",
        "SKF" => "_SKF",
        "MASTER SPORT" => "_MS",
        "LPR" => "_LPR",
        "INA" => "_INA",
        "GRAF" => "_GRAF",
        "FA1" => "_FA1"
    ];

    private $headers = [
        "B" => "size",
        "C" => "price",
        "D" => "code",
        "E" => "articlecode",
        "F" => "gyarto",
        "K" => "barcode",
        "M" => "weight_kg"
    ];

    private $sizeData = [];
    private $newSpreadsheet;
    private $newSheet;

    public function processExcel(string $inputPath, string $outputDir): string 
    {
        $reader = IOFactory::createReaderForFile($inputPath);
        $reader->setReadDataOnly(true);
        $reader->setReadFilter(new MyReadFilter());
        
        $spreadsheet = $reader->load($inputPath);
        $oldSheet = $spreadsheet->getSheet(0);

        $this->newSpreadsheet = new Spreadsheet();
        $this->newSheet = $this->newSpreadsheet->getActiveSheet();
        $this->newSheet->setTitle('todb');

        foreach ($this->headers as $header => $value) {
            $this->newSheet->setCellValue($header . '1', $value);
        }

        $data = [];
        foreach ($oldSheet->getRowIterator(2) as $row) {
            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $column = $cell->getColumn();
                if (in_array($column, ['C', 'D', 'F', 'K', 'M'])) {
                    $rowData[$column] = $cell->getValue();
                }
            }
            if (!empty($rowData)) {
                $data[] = $rowData;
            }
        }

        $this->loadSizeData();
        $this->writeAndFormatData($data, $oldSheet);

        $currentDate = date('Y.m.d');
        $newFileName = sprintf(
            '%s/automotors_ÁRLISTA_%s.xlsx',
            rtrim($outputDir, '/'),
            $currentDate
        );

        $writer = new Xlsx($this->newSpreadsheet);
        $writer->save($newFileName);

        return $newFileName;
    }

    private function loadSizeData(): void 
    {
        foreach ($this->sufixArticlecodes as $gyarto => $suffix) {
            $filePath = "z:\\szerző peti\\{$gyarto}.csv";
            if (!file_exists($filePath)) continue;

            $handle = fopen($filePath, "r");
            if ($handle === false) continue;

            fgetcsv($handle, 1000, ",");

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $asizeData = explode("\t", $data[0]);
                if (isset($asizeData[3]) && $asizeData[3] !== '') {
                    $this->sizeData[$asizeData[2]] = $asizeData[3];
                }
            }
            fclose($handle);
        }
    }

    private function writeAndFormatData(array $data, $oldSheet): void 
    {
        $row = 2;
        foreach ($data as $rowData) {
            foreach ($rowData as $column => $value) {
                $this->newSheet->setCellValue($column . $row, $value);
            }

            $articleCodePre = $rowData['D'];
            $gyarto = $rowData['F'];
            $suffix = $this->sufixArticlecodes[$gyarto] ?? "";
            $articleCode = $articleCodePre . $suffix;
            $this->newSheet->setCellValue("E" . $row, $articleCode);

            if (isset($this->sizeData[$articleCode])) {
                $this->newSheet->setCellValue("B" . $row, $this->sizeData[$articleCode]);
            }

            if (is_numeric($rowData['K'])) {
                $this->newSheet->getCell('K' . $row)->setDataType(DataType::TYPE_NUMERIC);
                $this->newSheet->getStyle("K" . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            }

            
            $this->newSheet->setCellValue("M" . $row, $oldSheet->getCell("M" . $row)->getCalculatedValue());

            if ($this->newSheet->getCell("M" . $row)->getValue() === '#HIV!') {
                $this->newSheet->setCellValue("M" . $row, '0');
            }

            $row++;
        }

        $lastRow = $row - 1;
        $this->newSheet->getStyle('D2:D' . $lastRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
        $this->newSheet->getStyle('M2:M' . $lastRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
        $this->newSheet->getStyle('C2:C' . $lastRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
    }

    private function writeAndFormatDate(array $data, $oldSheet): void{
        $row = 2;
        foreach($data as $rowData){
            foreach($rowData as $column => $value){
                $this->newSheet->setCellValue($column.$row,$value);
            }
            $articleCodePre = $rowData['D'];
            $gyarto = $rowData['F'];
            $suffix = $this->sufixArticlecodes[$gyarto] ??"";
            $articleCode = $articleCodePre .$suffix;
            $this->newSheet->setCellValue("E$row",$articleCode);
        }
    }
    
}

$processor = new ExcelProcessor();
$inputPath = 'c:\Users\LP-GERGO\Desktop\Farkas Gergő test\automotors nov1.xlsx';
$outputDir = 'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista';
$newFileName = $processor->processExcel($inputPath, $outputDir);

echo "Fájl mentve: " . $newFileName . "\n";
