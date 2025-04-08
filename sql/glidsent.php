<?php

require '../ggg/init.php';

$path = 'z:\szerző peti\beszkod\polcar.csv';
$csvContent = file_get_contents($path);
$rows = explode("\n", $csvContent);

$counter = 0;

foreach ($rows as $row) {
    $columns = explode(";", $row);
    if (count($columns) < 2) {
        continue;
    }

    
    $col1 = $columns[0];
    $col2 = $columns[1];
    $col3 = 'polcar';

    run("INSERT ignore INTO glid_to_code (code, glid, beszallito) VALUES (?, ?, ?)", [$col1, $col2, $col3]);
    $counter++;

}

echo $counter . " db lett INSERTALVA."

?>
