<?php
require '../phpspreadsheet/vendor/autoload.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use phpseclib3\Net\SFTP as SecuresFTP;

class ExcelProcessor {
    private $sftp;
    private $sftp_config;

    public function __construct($host, $port, $username, $password) {
        $this->sftp_config = [
            'host' => $host,
            'port' => $port,
            'username' => $username,
            'password' => $password
        ];
    }

    private function connectSFTP() {
        $this->sftp = new SecuresFTP($this->sftp_config['host'], $this->sftp_config['port']);
        if (!$this->sftp->login($this->sftp_config['username'], $this->sftp_config['password'])) {
            echo "SFTP Login Failed";
        }
    }

    private function findLatestExcelFile() {
        $files = $this->sftp->nlist('up');
        if ($files === false) {
            throw new Exception("Failed to list directory contents");
        }
    
        $excel_files = array_filter($files, fn($file) => preg_match('/\.(xlsx|xls)$/i', $file));
        if (empty($excel_files)) {
            throw new Exception("No Excel files found");
        }
    
        $file_times = [];
        foreach ($excel_files as $file) {
            $file_times[$file] = $this->sftp->stat("up/" . $file)['mtime'];
        }
        
        arsort($file_times);
        $latest_file = key($file_times);
    
        return $latest_file;
    }

    private function processExcelFile($local_file) {
        $reader = IOFactory::createReaderForFile($local_file);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($local_file);
        $oldSheet = $spreadsheet->getSheet(0);
    
        $newSpreadsheet = new Spreadsheet();
        $newSheet = $newSpreadsheet->getActiveSheet();
        $newSheet->setTitle('dieselczesci_uj');
    
        $sizeData = $this->loadSizeData();
    
        $uniqueValues = [];
        $processedData = [];
        $highestRow = $oldSheet->getHighestRow();
        $highestColumn = $oldSheet->getHighestColumn();
    
        
        $headers = [];
        for ($col = 'A'; $col <= $highestColumn; $col++) {
            $headers[] = $oldSheet->getCell($col . '1')->getValue();
        }
        $headers[] = 'glid';
        $processedData[] = $headers;
    
        
        for ($row = 2; $row <= $highestRow; $row++) {
            $rowData = [];
            $aColumnValue = $oldSheet->getCell('A' . $row)->getValue();
    
            if (!empty($aColumnValue) && !isset($uniqueValues[$aColumnValue])) {
                $uniqueValues[$aColumnValue] = true;
    
    
                for ($col = 'A'; $col <= $highestColumn; $col++) {
                    $cellValue = $oldSheet->getCell($col . $row)->getValue();
                    $rowData[] = $cellValue;
                }
    
    
                $foundGlid = '';
                $searchValue = trim($aColumnValue);
                foreach ($sizeData as $sizeRow) {
                    $comparisonCode = trim($sizeRow['code']);
                    $comparisonArticleCode = trim($sizeRow['articlecode']);
    
                    if ((!empty($comparisonCode) && $comparisonCode !== '\N' && strcasecmp($comparisonCode, $searchValue) === 0) || 
                        (empty($comparisonCode) || $comparisonCode === '\N') && strcasecmp($comparisonArticleCode, $searchValue) === 0) {
                        $foundGlid = $sizeRow['glid'];
                        break;
                    }
                }
    
    
                $rowData[] = $foundGlid;
                $processedData[] = $rowData;
            }
        }
    
    
        foreach ($processedData as $rowIndex => $rowData) {
            foreach ($rowData as $colIndex => $value) {
                $colLetter = Coordinate::stringFromColumnIndex($colIndex + 1);
                $newSheet->setCellValue($colLetter . ($rowIndex + 1), $value);
            }
        }
    
        $this->formatDecimalColumn($newSpreadsheet);
        return $newSpreadsheet;
    }
    

    private function formatDecimalColumn($spreadsheet) {
        $worksheet = $spreadsheet->getActiveSheet();
        
        
        $highestRow = $worksheet->getHighestRow();
        
        
        for ($row = 1; $row <= $highestRow; $row++) {
            $cell = $worksheet->getCell('D' . $row);
            $value = $cell->getValue();
            
            
            if (is_numeric($value) && strpos($value, '.') !== false) {
                
                $formattedValue = str_replace('.', ',', $value);
                $cell->setValue($formattedValue);
            }
        }
        
        return $spreadsheet;
    }

    public function loadSizeData() {
        $path = 'z:\szerző peti\Proparts Diesel.csv';
        $sizeData = [];
        
        if (($handle = fopen($path, "r")) !== FALSE) {
            
            fgets($handle);
            
            while (($line = fgets($handle)) !== false) {
                $parts = explode("\t", $line);
                
                if (isset($parts[0]) && isset($parts[2])) {
                    
                    $articleCode = trim($parts[2]);
                    
                    $articleCode = str_replace('_PRO', '', $articleCode);

                    $code = trim($parts[7]);
                    
                    $sizeData[] = [
                        "articlecode" => $articleCode,
                        "glid" => trim($parts[0]),
                        "code" => $code
                    ];
                }
            }
            fclose($handle);
        }
        
        return $sizeData;
    }

    public function processLatestExcelFile() {
        $this->connectSFTP();
        $latest_file = $this->findLatestExcelFile();
        
        $local_temp_file = sys_get_temp_dir() . '/' . $latest_file;
        $this->sftp->get("up/" . $latest_file, $local_temp_file);

        $newSpreadsheet = $this->processExcelFile($local_temp_file);
        
        $output_file = 'y:\kezi_arlista\dieselczesci_uj.csv';
        $writer = new Csv($newSpreadsheet);
        $writer->setDelimiter(';');
        $writer->setEnclosure('"');
        $writer->setLineEnding("\r\n");
        $writer->setSheetIndex(0);
        $writer->setUseBOM(true);
        $writer->save($output_file);

        echo "mentve";
    }
}

$processor = new ExcelProcessor(
    'store.szakalmetal.hu', 
    1221, 
    'diesel_czesci', 
    'AireiJa5loow'
);

$processor->processLatestExcelFile();

