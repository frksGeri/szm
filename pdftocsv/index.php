<?php

require '../phpspreadsheet/vendor/autoload.php';
use Smalot\PdfParser\Parser;

$pdfPath = 'Invoice 21.01.2025.pdf';
$parser = new Parser();
$pdf = $parser->parseFile($pdfPath);

$text = $pdf->getText();


$pattern = '/(\d{6})\s+(E[0-9A-Z]+)\s*(?:P)?\s*-?\s*([^\n]+)\s+([\d]+\s*PC)\s+([\d,.]+\s*EUR\/\s*1PC)\s+([\d,.]+)(?:\s*(?:[\d,.]+))?/';

preg_match_all($pattern, $text, $matches, PREG_SET_ORDER);

if (!empty($matches)) {
    $csvFile = fopen('output.csv', 'w');

    
    fputcsv($csvFile, ['Item', 'Material', 'Description', 'Quantity', 'Price/Unit', 'Value', 'Discount Price', 'Country'], ';');

    foreach ($matches as $match) {
        $item = $match[1];
        $material = $match[2];
        $description = trim($match[3]);
        $quantity = $match[4];
        $pricePerUnit = $match[5];
        $value = $match[6];

       
        $detailPattern = "/({$item}.*?{$material}.*?)/s";
        preg_match($detailPattern, $text, $details);

        $discountPrice = '';
        $country = '';

        if (!empty($details[1])) {
           
            $countryPattern = "/Country of Origin: ([^\n]+)/";
            if (preg_match($countryPattern, $details[1], $countryMatch)) {
                $country = trim($countryMatch[1]);
            }

          
            $discountPattern = "/Net value.*?([\d,.]+)\s*EUR\/\s*1PC/";
            if (preg_match($discountPattern, $details[1], $discountMatch)) {
                $discountPrice = trim($discountMatch[1]);
            }
        }

      
        fputcsv($csvFile, [
            $item, 
            $material, 
            $description, 
            $quantity, 
            $pricePerUnit, 
            $value, 
            $discountPrice, 
            $country
        ], ';');
    }

    fclose($csvFile);
    echo "kész";
} else {
    echo "vanbaj";
}
?>