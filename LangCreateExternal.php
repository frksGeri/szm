<?php

$path = 'y:\gerison_arlista\External.csv';

$csv = file_get_contents($path);

$csv = iconv("CP1250", "UTF-8", $csv);

$csv = explode("\n", $csv);

$data = [];


$neededRows = [
    '16:00-ig leadott rendelés esetén az áru a következő munkanap délelőtt érkezik a megrendelőhöz',
    '15:45-ig leadott rendelés esetén az áru a következő munkanap délelőtt érkezik a megrendelőhöz',
    'Munkanapokon 15:30-ig rendelve az áru a következő munkanap érkezik a megrendelőhöz ',
    '14:00-ig leadott rendelés esetén az áru a következő munkanap délelőtt érkezik a megrendelőhöz'
];

$counter = 0;

foreach ($csv as $values) {

    $externalData = explode(';', $values);
    if (!empty($externalData[0])) {

        if (in_array($externalData[2], $neededRows, true)) {
            $data[] = $externalData;
        }
    }
   /* if ($counter >= 10) {
        break;
    }

    $counter++;
    */
}

if (empty($data)) {
    echo 'nincs megfelelő adat a $data fájlba ';
}

$csvOutput = 'Y:\kezi_arlista\External2.csv';

$csvtoData = "\xEF\xBB\xBF";

$csvtoData .= "Item;Supplier;DeliveryInfo;Stock\n";

foreach ($data as $row) {

    $csvtoData .= implode(';', $row) . "\n";
}

file_put_contents($csvOutput, $csvtoData);

echo 'kesz';
