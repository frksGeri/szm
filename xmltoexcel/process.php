<?php
require '../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function xmlToXlsx($xmlFilePath, $outputFilePath) {
    $xml = simplexml_load_file($xmlFilePath);
    if ($xml === false) {
        die("Error loading XML file");
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $headers = ['Beszcode', 'Quantity', 'Unit Price', 'Line Total'];
    $sheet->fromArray($headers, null, 'A1');

    $row = 2;
    foreach ($xml->M_INVOIC->G_SG26 as $lineItem) {
        $supplierCode = (string)($lineItem->S_PIA[1]->C_C212->D_7140 ?? '');
        $quantity = (float)($lineItem->S_QTY->C_C186->D_6060 ?? 0);
        $unitPrice = (float)($lineItem->G_SG29->S_PRI[0]->C_C509->D_5118 ?? 0);
        $lineTotal = (float)($lineItem->G_SG27[0]->S_MOA->C_C516->D_5004 ?? 0);

        $data = [$supplierCode, $quantity, $unitPrice, $lineTotal];
        $sheet->fromArray($data, null, "A$row");
        $row++;
    }

    foreach (range('A', 'D') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    $sheet->getStyle('B2:B' . $row)->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle('C2:D' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

    $writer = new Xlsx($spreadsheet);
    $writer->save($outputFilePath);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['xmlFile'])) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $xmlFile = $_FILES['xmlFile'];
    $xmlFileName = basename($xmlFile['name']);
    $xmlFilePath = $uploadDir . $xmlFileName;

    
    $fileExtension = strtolower(pathinfo($xmlFileName, PATHINFO_EXTENSION));
    if ($fileExtension !== 'xml') {
        die("Csak XML fájlokat tölthetsz fel!");
    }

    
    if (!move_uploaded_file($xmlFile['tmp_name'], $xmlFilePath)) {
        die("Hiba történt a fájl feltöltése közben!");
    }

    
    $outputFileName = pathinfo($xmlFileName, PATHINFO_FILENAME) . '.xlsx';
    $outputFilePath = $uploadDir . $outputFileName;

    
    try {
        xmlToXlsx($xmlFilePath, $outputFilePath);
    } catch (Exception $e) {
        die("Hiba történt az XLSX generálása közben: " . $e->getMessage());
    }

    
    if (file_exists($outputFilePath)) {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $outputFileName . '"');
        header('Cache-Control: max-age=0');
        readfile($outputFilePath);

      
        unlink($xmlFilePath);
        unlink($outputFilePath);
        exit;
    } else {
        die("Az XLSX fájl nem található!");
    }
} else {
    die("Nincs fájl feltöltve!");
}