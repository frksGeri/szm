<?php
require '../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class FileAnalyzer {
    private $directory;
    private $historyFile;

    public function __construct($directory) {
        $this->directory = $directory;
        $this->historyFile = __DIR__ . '/file_history.json';
    }

    public function analyzeFiles() {
        $files = scandir($this->directory);
        $fileDetails = [];
        $history = $this->loadHistory();
        $currentTime = time();
        $cutoffDate = strtotime('2024-09-01');

        foreach ($files as $file) {
            if ($file == '.' || $file == '..' || strpos($file, 'hipol') !== false || strpos($file, 'zip') !== false) continue;

            $fullPath = $this->directory . '/' . $file;
            
            if (is_file($fullPath)) {
                if (!is_readable($fullPath)) {
                    continue;
                }

                $lastModified = @filemtime($fullPath);
                
                if ($lastModified <= $cutoffDate) continue;

                $fileDetails[] = [
                    'name' => $file,
                    'last_modified' => $lastModified,
                    'last_modified_readable' => date('Y-m-d H:i:s', $lastModified)
                ];
            }
        }

        $this->generateExcelReport($fileDetails);
    }

    private function loadHistory() {
        if (!file_exists($this->historyFile)) {
            return [];
        }
        
        $historyContent = @file_get_contents($this->historyFile);
        $history = $historyContent ? json_decode($historyContent, true) : [];
        
        return $history ?: [];
    }
/*
    private function isWorkingHours($timestamp) {
        $hour = (int)date('H:i', $timestamp);
        return ($hour >= 08:00 && $hour < 19:45);
    }
*/
    private function isWorkingHours($timestamp) {
        $hour = (int)date('H', $timestamp); 
        $minute = (int)date('i', $timestamp); 
     
        $start = 17 * 60; 
        $end = 19 * 60 + 45; 
        
        $current = $hour * 60 + $minute;
    
        return ($current >= $start && $current < $end);
    }
    

    private function generateExcelReport($fileDetails) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $sheet->setCellValue('A1', 'Fájl neve');
        $sheet->setCellValue('B1', 'Utolsó módosítás');


        $sheet->getStyle('A1:B1')->getFont()->setBold(true);
        
        $row = 2;
        foreach ($fileDetails as $file) {
            $sheet->setCellValue('A' . $row, $file['name']);
            $sheet->setCellValue('B' . $row, $file['last_modified_readable']);
            

            if ($this->isWorkingHours($file['last_modified'])) {
                $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->getColor()
                    ->setARGB(Color::COLOR_RED);
            }
            
            $row++;
        }

        
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);


        $writer = new Xlsx($spreadsheet);
        

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="file_analysis_report.xlsx"');
        header('Cache-Control: max-age=0');
        

        $writer->save('C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\gerison_mappa.xlsx');
        exit;
    }
}

$analyzer = new FileAnalyzer('y:\gerison_arlista');
$analyzer->analyzeFiles();
?>