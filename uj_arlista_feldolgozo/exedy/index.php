<pre><?php

//**************************************************//
//**************************************************//
//**************************************************//
require __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../generics/generics.php';

$file = "A:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\A-E\EXEDY\_NYERS\Price List  - 170225.xlsx";

$dataStruct = array(    //Hányadik oszlopban található az adott adat
    "code" => 'A',
    "price" => 'F',
    "weight" => 'X',
    "ean" => 'X',
    "gyarto" => 'X',
    "moq" => 'G',
    "oeCodes" => 'X'
);

$conn = mysqli_connect("131.0.1.92", "robi", "", "newszmdb");
if($conn === false){
    die("ERROR: Could not connect. " 
        . mysqli_connect_error());
}

$arlista = new ÁrlistaKonverter($file,$dataStruct,$conn,"EXEDY","EXEDY ÁRLISTA", 3)
//**************************************************//
//**************************************************//
//**************************************************//

?>