<?php
require '../ggg/init.php';

$path = 'z:\szerző peti\beszkod\polcar.csv';
$handle = fopen($path, "r");
if ($handle === false) {
    die("nemtudtam megnyitni ezt a fájlt: $path");
}

fgetcsv($handle, null, ";"); 

$glid = [];
$chunk = 1000;
$results = [];
$rows = [];

$output = "code;glid;articlecode\n";

while (($row = fgetcsv($handle, null, ";")) !== FALSE) {
    $glid[] = $row[1];
    $rows[] = $row;

    if (count($glid) >= $chunk) {
        $glids = "'" . implode("','", array_map('addslashes', $glid)) . "'"; 
        $query = "SELECT glid, articlecode FROM newszmdb.products_v2 WHERE glid IN ($glids)";
        $result = run($query);
        $results = array_merge($results, $result);
        $glid = [];
    }
}

if (!empty($glid)) {
    $glids = "'" . implode("','", array_map('addslashes', $glid)) . "'";
    $query = "SELECT glid, articlecode FROM newszmdb.products_v2 WHERE glid IN ($glids)";
    $result = run($query);
    $results = array_merge($results, $result);
}

fclose($handle);


$indexed_results = [];
foreach ($results as $result_row) {
    $indexed_results[$result_row['glid']] = $result_row['articlecode'] ?? '';
}


foreach ($rows as $row) {
    $code = $row[0];
    $current_glid = $row[1];
    $articlecode = $indexed_results[$current_glid] ?? '';
    $output .= "$code;$current_glid;$articlecode\n";
}

//echo $output;
 file_put_contents('c:\Users\LP-GERGO\Downloads\polcar_glid_articlecode.csv', $output);