<pre><?php

//**************************************************//
//**************************************************//
//**************************************************//
require __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../generics/generics.php';

$file = "A:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\A-E\CORTECO\_NYERS\\202503_2250005337_38828.xlsx";

$dataStruct = array(    //Hányadik oszlopban található az adott adat
    "code" => 'A',
    "price" => 'AU',
    "weight" => 'AV',
    "ean" => 'R',
    "gyarto" => 'AV',
    "moq" => 'AV'
);

$conn = mysqli_connect("131.0.1.92", "robi", "", "newszmdb");
if($conn === false){
    die("ERROR: Could not connect. " 
        . mysqli_connect_error());
}

ob_implicit_flush(true);
$arlista = new ÁrlistaKonverter($file,$dataStruct,$conn,"CORTECO","CORTECO ÁRLISTA", 2);
ob_end_flush();
//**************************************************//
//**************************************************//
//**************************************************//

?>