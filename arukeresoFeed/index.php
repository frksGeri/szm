<pre><?php

$path = 'C:/Users/LP-GERGO/Desktop/Farkas Gergő test/arukereso_feed/vendeg_felhasznalo_arak.csv';
$path = 'Z:\shodan_vevo_arlistak\vendeg_felhasznalo_arak.csv';

$getData = [];

if (($file = fopen($path, "r")) !== FALSE) {
    $header = fgetcsv($file, 1000, ",");
    while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
        $getData[] = $data[0];
    }
    fclose($file);
}

$tombAmibeUPCesPRICEadatokVannak = [];

foreach ($getData as $key => $value) {
    $kapottAnyagAmiTartalmazzaAUPStEsAPricet = explode("\t", $value);

    if (!isset($kapottAnyagAmiTartalmazzaAUPStEsAPricet[7])) {
        continue;
    }

    $tombAmibeUPCesPRICEadatokVannak[] = [
        "upc" => $kapottAnyagAmiTartalmazzaAUPStEsAPricet[0],
        "net_price" => $kapottAnyagAmiTartalmazzaAUPStEsAPricet[7]
    ];
}

$pathMain = 'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\arukereso_feed\eneos_ar_MAIN_SAMPLE.csv';

$dataArray = [];

if (($file = fopen($pathMain, "r")) !== FALSE) {
    $header = fgetcsv($file, 1000, ";");
    while (($row = fgetcsv($file, 1000, ";")) !== FALSE) {
        $dataArray[] = array_combine($header, $row);
    }
    fclose($file);
}

foreach ($dataArray as $sablonIndex => $sablonValue) {
    foreach ($tombAmibeUPCesPRICEadatokVannak as $vendegIndex => $vendegValue) {
        if ($sablonValue['upc'] == $vendegValue['upc']) {
            $dataArray[$sablonIndex]['net_price'] = $vendegValue['net_price'];
          
            $calculatedPrice = $vendegValue['net_price'] * 1.27;
            $dataArray[$sablonIndex]['price'] = number_format($calculatedPrice, 2, '.', '');
            $dataArray[$sablonIndex]['price'] = explode(".", $dataArray[$sablonIndex]['price']);
            $dataArray[$sablonIndex]['price'] =$dataArray[$sablonIndex]['price'][0];
        }
    }
}




$csvOutputPath = 'Z:\shodan_vevo_arlistak\arukereso\arukereso_feed.csv';

if (($file = fopen($csvOutputPath, "w")) !== FALSE) {
    fputcsv($file, $header, ";");
    foreach ($dataArray as $row) {
        fputcsv($file, $row, ";");
    }
    fclose($file);
    echo "Az adatok sikeresen ki lettek mentve a '$csvOutputPath' fájlba.";
} else {
    echo "Hiba történt a fájl megnyitása során.";
}

?>