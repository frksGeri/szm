<pre><?php

//**************************************************//
//**************************************************//
//**************************************************//
require __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../generics/generics.php';

$file = "A:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\K-O\KAMOKA\_NYERS\\20250311_KAMOKA_price_offer_gaskets_DP.xlsx";

$dataStruct = array(    //Hányadik oszlopban található az adott adat
    "code" => 'A',
    "price" => 'C',
    "weight" => 'X',
    "ean" => 'I',
    "gyarto" => 'X',
    "moq" => 'X',
    "oeCodes" => 'X'
);

$conn = mysqli_connect("131.0.1.92", "robi", "", "newszmdb");
if($conn === false){
    die("ERROR: Could not connect. " 
        . mysqli_connect_error());
}

$arlista = new ÁrlistaKonverter($file,$dataStruct,$conn,"KAMOKA","KAMOKA ÁRLISTA", 3)
//**************************************************//
//**************************************************//
//**************************************************//

?>