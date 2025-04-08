<?php


$xmlFile = 'z:\Farkas Gergo\polcar\polcar.xml'; 
$csvFile = 'z:\Farkas Gergo\polcar\xmlpolcar.csv';
$xml = simplexml_load_file($xmlFile);

if ($xml === false) {
    die('Hiba az XML fájl beolvasasnal.');
}

$handle = fopen($csvFile, 'w');

if ($handle === false) {
    die('Hiba a CSV fájl létrehozásanal.');
}


$headers = [
    'NumerPOLCAR',
    'NazwaGrupy',
    'NazwaRodzaju',
    'NazwaCzesci',
    'Zastosowanie',
    'NazwaNaFakture',
    'Ilosc',
    'Magazyn',
    'Dostawa',
    'NazwaJakosci',
    'OE',
    'CenaKlienta',
    'Producent',
    'EAN13',
    'WagaBrutto',
    'NazwaStanuTowaru',
    'PCN',
    'GTU',
    'DataGeneracjiRaportu',
    'Jednostka',
    'CenaKlienta1pc'
];

fputcsv($handle, $headers);

foreach ($xml->DANE as $dane) {
    $row = [
        (string)$dane['NumerPOLCAR'],
        (string)$dane['NazwaGrupy'],
        (string)$dane['NazwaRodzaju'],
        (string)$dane['NazwaCzesci'],
        (string)$dane['Zastosowanie'],
        (string)$dane['NazwaNaFakture'],
        (string)$dane['Ilosc'],
        (string)$dane['Magazyn'],
        (string)$dane['Dostawa'],
        (string)$dane->RJ['NazwaJakosci'],
        (string)$dane->RJ['OE'],
        (string)$dane->RJ['CenaKlienta'],
        (string)$dane->RJ->KAT['Producent'],
        (int)$dane->RJ->KAT['EAN13'],
        (string)$dane->RJ->KAT['WagaBrutto'],
        (string)$dane->RJ->KAT['NazwaStanuTowaru'],
        (string)$dane->RJ->KAT->tNP['PCN'],
        (string)$dane->RJ->KAT->tNP['GTU'],
        (string)$dane->RJ->KAT->tNP['DataGeneracjiRaportu'],
        (string)$dane->RJ->KAT->tNP->CJ['Jednostka'],
        (string)$dane->RJ->KAT->tNP->CJ['CenaKlienta1pc']
    ];


    fputcsv($handle, $row);
}


fclose($handle);

echo "A CSV fájl sikeresen létrejött: $csvFile\n";

?>