<?php

include '../ggg/init.php';

$outputFile = 'c:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\polcar_glids_ktypnr.csv';
$inputPath = 'z:\FGergo\polcarkkkk.csv';

$beszcodeToGlid = [];

$sqlTableData = run("select * from newszmdb.glid_to_code WHERE beszallito = 'polcar'");

foreach($sqlTableData as $row){
    $beszcodeToGlid[$row['code']] = $row['glid'];
}


$inputHandle = fopen($inputPath,"r");
$outputHandle = fopen($outputFile,"w");

$counter = 0;

if(isset($inputHandle) && isset($outputHandle)){
    fputcsv($outputHandle,['glid','ktypnr']);

    $header = fgetcsv($inputHandle,0,";");

    while(($data = fgetcsv($inputHandle,0,";")) !== FALSE){
        $beszcode = $data[0];
        $ktypnr = $data[1];

        if(isset($beszcodeToGlid[$beszcode])){
           $glid = $beszcodeToGlid[$beszcode];
           fputcsv($outputHandle, [$glid,$ktypnr]);
           $counter++;
        }
        
      
    }

    fclose($inputHandle);
    fclose($outputHandle);
    echo 'siker ' . $counter . " sor kelletkezet";
}else{
    echo 'hiba a fajlok megnyitasanal';
}



?>