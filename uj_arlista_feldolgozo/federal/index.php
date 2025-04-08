<pre><?php

//**************************************************//
//**************************************************//
//**************************************************//
require __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../generics/generics.php';

$file = "A:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\F-J\FEDERAL\_NYERS\DRiV Champion Braking 1074 reduced prices from 17.03.2025 .xlsx";

$dataStruct = array(    //Hányadik oszlopban található az adott adat
    "code" => 'A',
    "price" => 'N',
    "weight" => 'E',
    "barcode" => 'G',
    "gyarto" => 'AV',
    "moq" => 'AV',
    "oeCodes" => 'AV'
);

$conn = mysqli_connect("131.0.1.92", "robi", "", "newszmdb");
if($conn === false){
    die("ERROR: Could not connect. " 
        . mysqli_connect_error());
}

ob_implicit_flush(true);
$arlista = new ÁrlistaKonverter($file,$dataStruct,$conn,"CHAMPION","FEDERAL CHAMPION", 2);
ob_end_flush();
//**************************************************//
//**************************************************//
//**************************************************//

?>