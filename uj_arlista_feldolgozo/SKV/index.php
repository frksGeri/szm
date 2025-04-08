<pre><?php

//**************************************************//
//**************************************************//
//**************************************************//
require __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../generics/generics.php';

$file = "A:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\P-T\SKV\_NYERS\ESEN SKV - new products Jan-Feb 2025.xlsx";

$dataStruct = array(    //Hányadik oszlopban található az adott adat
    "code" => 'A',
    "price" => 'I',
    "weight" => 'AV',
    "ean" => 'AV',
    "gyarto" => 'AV',
    "moq" => 'AV',
    "oeCodes" => 'E'
);

$conn = mysqli_connect("131.0.1.92", "robi", "", "newszmdb");
if($conn === false){
    die("ERROR: Could not connect. " 
        . mysqli_connect_error());
}

ob_implicit_flush(true);
$arlista = new ÁrlistaKonverter($file,$dataStruct,$conn,"ESEN SKV","SKV", 2);
ob_end_flush();
//**************************************************//
//**************************************************//
//**************************************************//

?>