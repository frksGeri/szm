<pre><?php

//**************************************************//
//**************************************************//
//**************************************************//
require __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../generics/generics.php';

$file = "A:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\K-O\NRF\_NYERS\Price List Full NRF 2025_04_01 All - distributor.xlsx";

$dataStruct = array(    //Hányadik oszlopban található az adott adat
    "code" => 'A',
    "price" => 'AU',
    "weight" => 'AV',
    "ean" => 'R',
    "gyarto" => 'AV',
    "moq" => 'AV',
    "oeCodes" => 'S'
);

$secondDataStruct = array(
    "code" => 'A',
    "price" => 'AA',
    "weight" => 'I',
    "ean" => 'AA',
    "gyarto" => 'AA',
    "moq" => 'AA',
    "oeCodes" => 'S'
);

$conn = mysqli_connect("131.0.1.92", "robi", "", "newszmdb");
if($conn === false){
    die("ERROR: Could not connect. " 
        . mysqli_connect_error());
}

ob_implicit_flush(true);
$arlista = new ÁrlistaKonverter($file,$dataStruct,$conn,"NRF","NRF ÁRLISTA", 4,$secondDataStruct,"Product Data",2);
ob_end_flush();
//**************************************************//
//**************************************************//
//**************************************************//

?>