<?php
require '../../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;


$path = "Y:\Árlista 2024\PISTON HASTINGS Price list Szakal-Metal 01.12.2024.xlsx";
$path = 'C:/Users/LP-GERGO/Desktop/Farkas Gergő test/PISTON HASTINGS Price list Szakal-Metal 01.12.2024.xlsx';

$reader = IOFactory::createReaderForFile($path);
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load($path); 

$oldSheet = $spreadsheet->getSheet(0); 

$newSpreadsheet = new Spreadsheet();
$newSheet = $newSpreadsheet->getActiveSheet();

$newSheet->setTitle('todb');

$row = 1; 
foreach ($oldSheet->getRowIterator() as $oldRow) {
    $cellIterator = $oldRow->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false); 
    $newCol = 1; 
    foreach ($cellIterator as $cell) {
        if ($cell->getValue() !== null) {
            $cellCoordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($newCol) . $row;
            if ($newCol == 1) { 
                $newSheet->setCellValueExplicit($cellCoordinate, $cell->getValue(), DataType::TYPE_STRING);
            } else {
                $newSheet->setCellValue($cellCoordinate, $cell->getValue());
            }
            
            $newCol++;
        }
    }
    if ($newCol > 1) { 
        $row++;
    }
}

$newSheet->setCellValue('A1','code');
$newSheet->setCellValue('B1','articlecode');

$row = 2; 
while ($newSheet->getCell("B$row")->getValue() !== null) {
    $valueB = $newSheet->getCell("A$row")->getValue();
    $newSheet->setCellValue("B$row", $valueB . '_HAS');
    $row++;
}

$newSheet->setCellValue('C1','price');
$newSheet->setCellValue('E1','size');

$path = 'Z:\szerző peti\HASTINGS.csv';

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
    $articleCode = $newSheet->getCell('B' . $rowIndex)->getValue(); 
    
    foreach ($sizeData as $sizeRow) {
        if ($sizeRow['articlecode'] === $articleCode) {
            $sizeValue = floatval($sizeRow['size']);
            $roundedSize = round($sizeValue, 2);
            
            $newSheet->setCellValue('E' . $rowIndex, $roundedSize);
            $newSheet->getStyle('E' . $rowIndex)->getNumberFormat()
                     ->setFormatCode('0.00');
            break; 
        }
    }
}

$lastRow = $newSheet->getHighestRow();
for ($i = 2; $i <= $lastRow; $i++) {
    $cellValue = $newSheet->getCell('E' . $i)->getValue();
    if (is_string($cellValue) && strpos($cellValue, ',') !== false) {
        $newValue = str_replace(',', '.', $cellValue);
        $newSheet->setCellValue('E' . $i, $roundedValue);
    }
}

$newSheet->getStyle('E2:E' . $lastRow)->getNumberFormat()
         ->setFormatCode('0.00');

$currentDate = date('Y.m.d');
$newFileName = 'Y:\Árlista 2024\FELTÖLTÖTT ÁRLISTÁK 2024\P-T\PISTON RINGS KOMAROV (HASTINGS)\PISTON ÁRLISTA '. $currentDate . '.xlsx';

$writer = new Xlsx($newSpreadsheet);
$writer->save($newFileName);


?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PISTON HASTINGS - Sikeres Művelet</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            text-align: center;
            max-width: 600px;
            width: 90%;
        }
        .success-icon {
            color: #4CAF50;
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        h1 {
            color: #333;
            margin-bottom: 1rem;
        }
        .file-path {
            background-color: #f5f5f5;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
            word-break: break-all;
        }
        .details {
            margin-top: 2rem;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }
        .detail-item {
            flex: 1;
            min-width: 150px;
            margin: 0.5rem;
        }
        .detail-label {
            font-weight: bold;
            color: #666;
        }
        .detail-value {
            margin-top: 0.5rem;
            color: #333;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body>
    <div class="container animate">
        <i class="fas fa-check-circle success-icon"></i>
        <h1>Sikeres Művelet</h1>
        <p>A PISTON HASTINGS árlista sikeresen feldolgozva és mentve!</p>
        
        <div class="file-path">
            <strong>Mentés helye:</strong><br>
            <?php echo $newFileName; ?>
        </div>
        
        <div class="details">
            <div class="detail-item">
                <div class="detail-label">Dátum</div>
                <div class="detail-value"><?php echo $currentDate; ?></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Státusz</div>
                <div class="detail-value">Sikeres</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Művelet</div>
                <div class="detail-value">Árlista Feldolgozás</div>
            </div>
        </div>
    </div>

    <script>
        setTimeout(() => {
            document.querySelector('.container').style.opacity = '0.7';
        }, 10000);
    </script>
</body>
</html>
