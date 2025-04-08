<?php

require '../../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$path = 'y:\Árlista 2024\FELTÖLTÖTT ÁRLISTÁK 2024\K-O\_NYERS\KYB_SA_CS_MK_PK_SP_Price_List_2024_12_01.xlsx';


class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = ''): bool
    {

        return true;
    }
}


$reader = IOFactory::createReaderForFile($path);
$reader->setReadDataOnly(true);
$reader->setReadFilter(new MyReadFilter());
$spreadsheet = $reader->load($path);




$sheet1 = $spreadsheet->getSheet(0);
$sheet2 = $spreadsheet->getSheet(1);


$newSpreadsheet = new Spreadsheet();
$newSheet = $newSpreadsheet->getActiveSheet();
$newSheet->setTitle('todb');


$headers = [
    'A' => 'code',
    'B' => 'price',
    'C' => 'barcode',
    'D' => 'country',
    'E' => 'weight',
    'F' => 'gyarto',
    'G' => 'articlecode',
    'H' => 'size',
    'I' => 'glid'
];



foreach ($headers as $col => $header) {
    $newSheet->setCellValue($col . '1', $header);
}

$newRow = 2;

$highestRow = max($sheet1->getHighestRow(), $sheet2->getHighestRow());

for ($row = 2; $row <= $highestRow; $row++) {

    $code = $sheet1->getCell('A' . $row)->getValue();

    if (!empty($code)) {

        $price = $sheet1->getCell('J' . $row)->getValue();
        $barcode = $sheet1->getCell('K' . $row)->getValue();




        $country = $sheet2->getCell('G' . $row)->getValue();
        $weight = $sheet2->getCell('H' . $row)->getValue();
        $gyarto = $sheet2->getCell('O' . $row)->getValue();


        $newSheet->setCellValue('A' . $newRow, $code);
        $newSheet->setCellValue('B' . $newRow, $price);
        $newSheet->setCellValue('C' . $newRow, $barcode);
        $newSheet->setCellValue('D' . $newRow, $country);
        $newSheet->setCellValue('E' . $newRow, $weight);
        //$newSheet->setCellValue('F' . $newRow, "KYB " . $gyarto);

        $newSheet->setCellValue("G$row", $code . "_KYB");


        if ($country == "ARG") {
            $newSheet->setCellValue("D$row", "AR");
        }
        if ($country == "AUS") {

            $newSheet->setCellValue("D$row", "AT");
        }
        if ($country == "BEL") {

            $newSheet->setCellValue("D$row", "BE");
        }
        if ($country == "BRA") {

            $newSheet->setCellValue("D$row", "BR");
        }
        if ($country == "CHN") {

            $newSheet->setCellValue("D$row", "CN");
        }
        if ($country == "CZE") {

            $newSheet->setCellValue("D$row", "CZ");
        }
        if ($country == "DEU") {

            $newSheet->setCellValue("D$row", "DE");
        }
        if ($country == "ESP") {

            $newSheet->setCellValue("D$row", "ES");
        }
        if ($country == "FRA") {

            $newSheet->setCellValue("D$row", "FR");
        }
        if ($country == "GER") {

            $newSheet->setCellValue("D$row", "GR");
        }
        if ($country == "IND") {

            $newSheet->setCellValue("D$row", "IN");
        }
        if ($country == "ITA") {

            $newSheet->setCellValue("D$row", "IT");
        }
        if ($country == "JPN") {

            $newSheet->setCellValue("D$row", "JP");
        }
        if ($country == "KOR") {

            $newSheet->setCellValue("D$row", "KR");
        }
        if ($country == "MEX") {

            $newSheet->setCellValue("D$row", "MX");
        }
        if ($country == "MYS") {

            $newSheet->setCellValue("D$row", "MY");
        }
        if ($country == "NLD") {

            $newSheet->setCellValue("D$row", "NL");
        }
        if ($country == "POL") {

            $newSheet->setCellValue("D$row", "PL");
        }
        if ($country == "ROU") {

            $newSheet->setCellValue("D$row", "RU");
        }
        if ($country == "THA") {

            $newSheet->setCellValue("D$row", "TH");
        }
        if ($country == "TUN") {

            $newSheet->setCellValue("D$row", "TN");
        }
        if ($country == "TUR") {

            $newSheet->setCellValue("D$row", "TR");
        }
        if ($country == "TWN") {

            $newSheet->setCellValue("D$row", "TW");
        }
        if ($country == "USA") {

            $newSheet->setCellValue("D$row", "US");
        }
        if ($country == "ZAF") {

            $newSheet->setCellValue("D$row", "ZA");
        }

        $newSheet->getStyle("C$row")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $newSheet->getCell('A' . $row)->setDataType(DataType::TYPE_STRING);


        $newRow++;
    }
}


foreach (range('A', 'F') as $col) {
    $newSheet->getColumnDimension($col)->setAutoSize(true);
}


$path = 'Z:\szerző peti\KYB.csv';

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


    $sizeData[] = [
        "articlecode" => $asizeData[2],
        "size" => $asizeData[3],
        "glid" => $asizeData[0],
        "gyarto" => $asizeData[1],

    ];
}



foreach ($newSheet->getRowIterator(2) as $rowIndex => $row) {
    $articleCode = $newSheet->getCell('G' . $rowIndex)->getValue();


    foreach ($sizeData as $sizeRow) {
        if ($sizeRow['articlecode'] === $articleCode) {

            $newSheet->setCellValue('H' . $rowIndex, $sizeRow['size']);
            $newSheet->setCellValue('I' . $rowIndex, $sizeRow['glid'] ?? '');
            $gyarto = $sizeRow['gyarto'] ?: 'KYB'; 
            $newSheet->setCellValue('F' . $rowIndex, $gyarto);
           
            break;
        }
    }
}



$currentDate = date('Y.m.d');
$manufacturer = "kyb";
$newFileName = sprintf(
    'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\%s_ÁRLISTA_%s.xlsx',
    $manufacturer,
    $currentDate
);

$writer = new Xlsx($newSpreadsheet);
$writer->save($newFileName);

echo "Fájl mentve: " . $newFileName . "\n";
