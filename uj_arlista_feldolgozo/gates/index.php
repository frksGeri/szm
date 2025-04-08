<pre><?php

//**************************************************//
//**************************************************//
//**************************************************//
require __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../generics/generics.php';

$file = "A:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\F-J\GATES\_NYERS\Field Bulletin Mv belt MOQ Update - April2025.xlsx";

$dataStruct = array(    //Hányadik oszlopban található az adott adat
    "code" => 'A',
    "price" => 'V',
    "weight" => 'G',
    "barcode" => 'E',
    "gyarto" => 'V',
    "moq" => 'K',
    "oeCodes" => 'V',
    "articlecode" => 'B'
);

$conn = mysqli_connect("131.0.1.92", "robi", "", "newszmdb");
if($conn === false){
    die("ERROR: Could not connect. " 
        . mysqli_connect_error());
}

ob_implicit_flush(true);
$arlista = new ÁrlistaKonverter($file,$dataStruct,$conn,"GATES","GATES ÁRLISTA", 10);
ob_end_flush();
//**************************************************//
//**************************************************//
//**************************************************//

?>