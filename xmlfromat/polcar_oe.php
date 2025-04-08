<?php

try {
    
    $xmlFile = 'c:\Users\LP-GERGO\Desktop\polcar_OgraniczeniaSprzedazy.xml';
    $xml = simplexml_load_file($xmlFile);
    
    if ($xml === false) {
        throw new Exception('Error loading XML file');
    }

    
    $csvFile = 'c:\Users\LP-GERGO\Desktop\polcar_Romania_no_' . date('Ymd_His') . '.csv';
    $fp = fopen($csvFile, 'w');
    
    if ($fp === false) {
        throw new Exception('Error creating CSV file');
    }

    
    fwrite($fp, "\xEF\xBB\xBF");
    
    
    $headers = ['NumerPC', 'PartName', 'Warning'];
    fputcsv($fp, $headers);

    
    foreach ($xml->OgSp as $item) {
        $row = [
            (string)$item['NumerPC'],
            (string)$item['PartName'],
            (string)$item
        ];
        fputcsv($fp, $row);
    }

    fclose($fp);
    echo "CSV file has been created successfully: $csvFile";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

?>