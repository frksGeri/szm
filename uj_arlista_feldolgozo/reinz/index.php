<?php

//**************************************************//
//**************************************************//
//**************************************************//
require __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../generics/generics.php';

$file = "A:\Árlista 2025\FELTÖLTÖTT ÁRLISTÁK 2025\P-T\REINZ\_NYERS\Pricelist 01.04.2025.XLSX";

$dataStruct = array(    //Hányadik oszlopban található az adott adat
    "code" => 'A',
    "price" => 'C',
    "weight" => 'K',
    "ean" => 'P'
);

$conn = mysqli_connect("131.0.1.92", "robi", "", "newszmdb");
if($conn === false){
    die("ERROR: Could not connect. " 
        . mysqli_connect_error());
}

$arlista = new ReinzÁrlistaKonverter($file,$dataStruct,$conn,"REINZ","REINZ ÁRLISTA")
//**************************************************//
//**************************************************//
//**************************************************//

/*$data = array();
// Open XLSX-file
$excel = \avadim\FastExcelReader\Excel::open($file);
$sheet = $excel->sheet();
// Read all values as a flat array from current sheet
foreach ($sheet->nextRow([$dataStruct["code"] => 'code', $dataStruct["price"] => 'price', $dataStruct["weight"] => 'weight', $dataStruct["ean"] => 'ean'], \avadim\FastExcelReader\Excel::KEYS_FIRST_ROW) as $rowNum => $rowData) {
    // $rowData is array ['One' => ..., 'Two' => ...]
    // ...
    $cikk = new ReinzCikk(
        $rowData["code"],
        $rowData["price"],
        $rowData["weight"],
        $rowData["ean"]
    );
    $cikk->generateSqlData($conn);
    array_push($data, $cikk);
}

$excel = \avadim\FastExcelWriter\Excel::create(['Sheet1']);
$sheet = $excel->sheet();

$sheet->writeRow(['code', 'articlecode', 'size', 'ean', 'price', 'weight']);

foreach ($data as $cikk) {
    $sheet->writeRow([$cikk->code,$cikk->articlecode,($cikk->sqlAdat != null) ? $cikk->sqlAdat->size : '',$cikk->ean,$cikk->price,$cikk->weight]);
}

$currentDate = date('Y.m.d');
$newFileName = 'C:\Users\LP-KATALOGUS1\Desktop\exports\REINZ ÁRLISTA ' . $currentDate . '.xlsx';
$excel->save($newFileName);*/

?>