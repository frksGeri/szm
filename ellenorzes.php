<?php

$oldFilePath = 'z:/FGergo/glid_sulyadatok_regi.csv';
$newFilePath = 'z:/glid_sulyadatok.csv';
$resultFilePath = 'c:/Users/LP-GERGO/Desktop/weighsJavitoShodanosokUtani.csv';


ini_set('memory_limit', '512M');
set_time_limit(0);


function logError($message) {
    echo "Hiba: $message\n";
    error_log($message, 3, 'error.log');
}


$oldData = [];
if (($handle = fopen($oldFilePath, 'r')) !== false) {
    while (($data = fgetcsv($handle, 1000, ';')) !== false) {
        if (count($data) >= 2) {
            $glid = trim($data[0]);
            $weight = trim($data[1]);
            $oldData[$glid] = $weight;
        }
    }
    fclose($handle);
} else {
    logError("Nem sikerült megnyitni a régifájlt $oldFilePath");
    exit;
}


$resultHandle = fopen($resultFilePath, 'w');
if ($resultHandle === false) {
    logError("Nem sikerült létrehozni a fájlt: $resultFilePath");
    exit;
}
fputcsv($resultHandle, ['glid', 'weight'], ';');

$changedRows = 0; 
if (($handle = fopen($newFilePath, 'r')) !== false) {
    while (($data = fgetcsv($handle, 1000, ';')) !== false) {
        if (count($data) >= 2) {
            $glid = trim($data[0]);
            $weight = trim($data[1]);


            if (isset($oldData[$glid])) {
                $oldWeight = $oldData[$glid];
                    if ($weight != $oldWeight) {
                    fputcsv($resultHandle, [$glid, $weight], ';');
                    $changedRows++;
                }
            }
        }
    }
    fclose($handle);
} else {
    logError("Nemsikerült megnytni: $newFilePath");
    fclose($resultHandle);
    exit;
}

fclose($resultHandle);

echo "kesz a check $changedRows sorban történt változás.\n";
echo "itt a fájl: $resultFilePath\n";

?>