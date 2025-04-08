<?php
class FileAnalyzer
{
    private $directory;
    private $historyFile;
    private $supplierMapping;

    private $importantSuppliers = [
        'AUTO-PARTNER BLUE',
        'ABAKUS',
        'ABSPL',
        'AJS',
        'AUTO-MOTO RS s.r.o.',
        'AutopartnerHUF/Kulso Price',
        'AutopartnerHUF Stock',
        'AutopartnerHUFKulso Stock',
        'AUTO-MOTO RS s.r.o.',
        'Bilstein BluePrint',
        'JMAUTODILY',
        'EURO07',
        'Martex',
        'MSI',
        'SC Materom SRL',
        'Motogama',
        'Triscan GMBH',
        'GMT',
        'Láng Price',
        'Láng Stock',
        'Láng Külső',
        'Wilminkgroup_price',
        'Wilminkgroup_stock'

    ];
    public function __construct($directory)
    {
        $this->directory = $directory;
        $this->historyFile = __DIR__ . '/file_history.json';


        $this->supplierMapping = [
            '69973_PriceList_EUR.csv' => 'HART SK',
            '69973_Quantity.csv' => 'HART SK',
            '69973_Quantity_op.csv' => 'HART OP',
            'abakus.txt' => 'Abakus Sp. z o.o.',
            'absps.csv' => 'ABSPL',
            'aci.txt' => 'ACI-Auto Components International s.r.o',
            'ajs.csv' => 'AJS Parts',
            'alcarstock.csv' => 'ALCAR KFT.',
            'asmet_stock.csv' => 'ASMET_',
            'aspl.csv' => 'AS-PL sp. zoo',
            'autogroup.txt' => 'AUTOGROUP HUNGARY KFT.',
            'automotors.csv' => 'AUTO-MOTO RS s.r.o.',
            'autonova.csv' => 'AUTONOVA EXPORT-IMPORT KFT',
            'autopartner_blue.csv' => 'AUTO-PARTNER BLUE',
            'autopartner_price.csv' => 'Autopartner',
            'autopartner_price_hu.csv' => 'AutopartnerHUF/Kulso Price',
            'autopartner_stock.csv' => 'AutopartnerHUF Stock',
            'STANY_48_H.csv' => 'AutopartnerHUFKulso Stock',
            'bimida.txt' => 'Bimida',
            'bosal_stock.txt' => 'BOSAL CZ',
            'Cenik_se_stavy_SVATAVA.csv' => 'JMAUTODILY',
            'conex_2_ro.csv' => 'Conex',
            'conex_ro.csv' => 'ConexRo',
            'direct_fitting.txt' => 'Direct Fit Hungary KFT',
            'direct_katdpf.txt' => 'Direct Fit Hungary KFT',
            'drm.csv' => 'Dr.Motor',
            'elring.csv' => 'ElringKringel AG',
            'euro07.csv' => 'EURO07',
            'euroest_ro.csv' => 'S.C. EUROEST CAR S.R.L._ RO',
            'euroton.csv' => 'Euroton',
            'febestpricestock_bg.csv' => 'FEBEST Europe Distribution2 Bolgár',
            'febirs_hu_stock.csv' => 'Bilstein BluePrint',
            'federal.csv' => 'Federal Mogul GMBH.',
            'fiscom_price_stock.csv' => 'FIS COM',
            'forex.csv' => 'Forex Kft.',
            'gbg_price.txt' => 'GBG',
            'gbg_stock.txt' => 'GBG',
            'gmt_master.csv' => 'GMT',
            'gulf.txt' => 'LUBRICANTS HUNGARY Kft.',
            'INDEKS_PARAMETR.csv' => 'Autopartner Main File',
            'ipsa.csv' => 'IPSA Autoteile Großhandelsgeselschaft mbH',
            'Item_Master_Data_SAG.csv' => 'Cseh Stahl Main File',
            'ivanics.csv' => 'Ivanics Kft.',
            'ivanics_hyundai.csv' => 'Ivanics HYUNDAI Külső',
            'ivanics_volvo.csv' => 'Ivanics Volvo Külső',
            'jbm.txt' => 'J.B.M. CAMPLLONG S.L',
            'kamoka_stock.csv' => 'Kamoka_',
            'kavo_stock.csv' => 'KAVO',
            'kingstock.csv' => 'King_',
            'kocsis.csv' => 'Kocsis Imi',
            'LagerBG.csv' => 'Sanel BG',
            'LagerCA.csv' => 'Sanel CA',
            'LagerNS.csv' => 'Sanel NS',
            'LagerOstalo.csv' => 'Sanel O',
            'lang_arak.csv' => 'Láng Price',
            'keszlet10.csv' => 'Láng Stock',
            'External.csv' => 'Láng Külső',
            'mahle.csv' => 'Mahle Aftermarket',
            'marelli_stock.csv' => 'Magneti Marelli Aft.',
            'martex.txt' => 'Martex',
            'master_price.csv' => 'MASTERSPORT',
            'master_stock.csv' => 'MASTERSPORT',
            'materom.csv' => 'SC Materom SRL',
            'meatdoria_stock.csv' => 'M&D GROUP',
            'mercibolt.csv' => 'MB GARAGE',
            'mercibolt_rapid.csv' => 'MB GARAGE',
            'metzger_stock.csv' => 'WERNER METZGER GMBH',
            'minima.AudiVw.VW.MINIMA.txt' => 'MiNiMa',
            'minima.Bmw.BMW.MINIMA.txt' => 'MiNiMa',
            'minima.Chevrolet_AU.CHEVROLET.MINIMA_AU.txt' => 'MiNiMa',
            'minima.Citroen_AU.CITROËN.MINIMA_AU.txt' => 'MiNiMa',
            'minima.Daihatsu.DAIHATSU.MINIMA.txt' => 'MiNiMa',
            'minima.Fiat_Au.FIAT.MINIMA_AU.txt' => 'MiNiMa',
            'minima.Fiat_Chrysler_Jeep.FIAT.MINIMA.txt' => 'MiNiMa',
            'minima.Ford_AU.FORD.MINIMA_AU.txt' => 'MiNiMa',
            'minima.Honda.HONDA.MINIMA.txt' => 'MiNiMa',
            'minima.Honda_AU.HONDA.MINIMA_AU.txt' => 'MiNiMa',
            'minima.Hyundai.HYUNDAI.MINIMA.txt' => 'MiNiMa',
            'minima.Hyundai_AU.HYUNDAI.MINIMA_AU.txt' => 'MiNiMa',
            'minima.Jaguar.JAGUAR.MINIMA.txt' => 'MiNiMa',
            'minima.Kia.KIA.MINIMA.txt' => 'MiNiMa',
            'minima.Kia_AU.KIA.MINIMA_AU.txt' => 'MiNiMa',
            'minima.Landrover.LAND ROVER.MINIMA.txt' => 'MiNiMa',
            'minima.Mazda.MAZDA.MINIMA.txt' => 'MiNiMa',
            'minima.Mazda_AU.MAZDA.MINIMA_AU.txt' => 'MiNiMa',
            'minima.Mercedes.MERCEDES-BENZ.MINIMA.txt' => 'MiNiMa',
            'minima.Mitsubishi.MITSUBISHI.MINIMA.txt' => 'MiNiMa',
            'minima.Nissan.NISSAN.MINIMA.txt' => 'MiNiMa',
            'minima.Nissan_AU.NISSAN.MINIMA_AU.txt' => 'MiNiMa',
            'minima.Opel_AU.OPEL.MINIMA_AU.txt' => 'MiNiMa',
            'minima.Peugeot.PEUGEOT.MINIMA.txt' => 'MiNiMa',
            'minima.Peugeot_AU.PEUGEOT.MINIMA_AU.txt' => 'MiNiMa',
            'minima.Porsche_AU.PORSCHE.MINIMA_AU.txt' => 'MiNiMa',
            'minima.Renault.RENAULT.MINIMA.txt' => 'MiNiMa',
            'minima.Renault_AU.RENAULT.MINIMA_AU.txt' => 'MiNiMa',
            'minima.Seat_AU.SEAT.MINIMA_AU.txt' => 'MiNiMa',
            'minima.Skoda_AU.SKODA.MINIMA_AU.txt' => 'MiNiMa',
            'minima.Subaru.SUBARU.MINIMA.txt' => 'MiNiMa',
            'minima.Suzuki_AU.SUZUKI.MINIMA_AU.txt' => 'MiNiMa',
            'minima.Volkswagen_AU.VW.MINIMA_AU.txt' => 'MiNiMa',
            'minima.Volvo.VOLVO.MINIMA.txt' => 'MiNiMa',
            'minima.Volvo_AU.VOLVO.MINIMA_AU.txt' => 'MiNiMa',
            'mm.txt' => 'Magneti Marelli Aft.',
            'msi.csv' => 'MSI',
            'motogama.csv' => 'Motogama',
            'motogama_2.csv' => 'Motogama',
            'motoprofil.csv' => 'Moto-Profil SP z.o.o.',
            'motorol.csv' => 'MOTOROL',
            'nimfas.csv' => 'Nimfas-Corporation Bt.',
            'novopolma.txt' => 'Novopolma',
            'npr.txt' => 'Npr Europe',
            'nrf_stock_pol.txt' => 'NRF',
            'oegermany.csv' => 'OE Germany (beszállító)',
            'optibelt.csv' => 'Optibelt_',
            'parts4france.csv' => 'Parts4France',
            'q4y_uj.txt' => 'Q4Y S.A',
            'rotinger_stock.csv' => 'Rotinger pl',
            'schaefer_stock_price.csv' => 'Schäferbarthold GmbH.',
            'schaefer_stock_price_0.csv' => 'Schäferbarthold GmbH.',
            'schaefer_stock_price_1.csv' => 'Schäferbarthold GmbH. +1',
            'schaefer_stock_price_2.csv' => 'Schäferbarthold GmbH. +2',
            'schaefer_stock_price_3.csv' => 'Schäferbarthold GmbH. +3',
            'shipman.csv' => 'SHIPMAN',
            'signeda_stock.csv' => 'DF Tuning Kft',
            'skv_stock.csv' => 'ESEN SKF',
            'stahl_price.csv' => 'Stahlgruber',
            'stahl_stock.csv' => 'Stahlgruber',
            'stahlcz_price.csv' => 'Stahlgruber cz.',
            'stahlcz_price_new.csv' => 'Stahlgruber cz.',
            'stahlcz_stock.csv' => 'Stahlgruber cz.',
            'suder.csv' => 'SUDER & SUDER',
            'sziklai_arlista.csv' => 'Sziklai Kft.',
            'sziklai_termekek.csv' => 'Sziklai Kft.',
            'tiki.txt' => 'Tiki vent',
            'TokicPrice.csv' => 'TOKIC',
            'TokicPrice_ro.csv' => 'TokicRo',
            'TokicStock.csv' => 'TOKIC',
            'TokicStock_ro.csv' => 'TokicRo',
            'topex_stock.csv' => 'GrupaTopex',
            'triscan_stock.txt' => 'Triscan GMBH',
            'trucktec.csv' => 'Trucktec_',
            'valeo_stock.csv' => 'Valeo_',
            'variens.csv' => 'VariensBP',
            'VWStock.csv' => 'AVP Autoland GMBH.',
            'WGArticleInfo.csv' => 'Wilminkgroup',
            'WGArticleInfo2.csv' => 'Wilminkgroup',
            'WGPriceList.csv' => 'Wilminkgroup_price',
            'WGStock.csv' => 'Wilminkgroup_stock'

        ];
    }

    public function analyzeFiles()
    {
        $files = scandir($this->directory);
        $fileDetails = [];
        $history = $this->loadHistory();
        $currentTime = time();
        $cutoffDate = strtotime('2024-09-01');

        foreach ($files as $file) {
            if (
                $file == '.' ||
                $file == '..' ||
                strpos($file, 'hipol') !== false ||
                strpos($file, 'Cenik_bez_stavu.csv') !== false ||
                strpos($file, 'Cenik_se_stavy_PRAHA.csv') !== false ||
                strpos($file, 'Intesa.csv') !== false ||
                strpos($file, 'TECDOC_MTS.csv') !== false ||
                strpos($file, 'V0092.csv') !== false ||
                strpos($file, 'akh_stock.csv') !== false ||
                strpos($file, 'anet.csv') !== false ||
                strpos($file, 'avp_prices.txt') !== false ||
                strpos($file, 'conex_2.csv') !== false ||
                strpos($file, 'difer_stocklist.csv') !== false ||
                strpos($file, 'direct_fitting.txt') !== false ||
                strpos($file, 'emilfrey.csv') !== false ||
                strpos($file, 'federal_all.csv') !== false ||
                strpos($file, 'federal_fp.csv') !== false ||
                strpos($file, 'fiscom_price_stock.csv') !== false ||
                strpos($file, 'import_result.txt') !== false ||
                strpos($file, 'ivanics.csv') !== false ||
                strpos($file, 'ivanics_hyundai.csv') !== false ||
                strpos($file, 'ivanics_volvo.csv') !== false ||
                strpos($file, 'kocsis.csv.1') !== false ||
                strpos($file, 'kocsis.csv.2') !== false ||
                strpos($file, 'mercibolt szemely 390.csv') !== false ||
                strpos($file, 'mercibolt teher 390.csv') !== false ||
                strpos($file, 'mercibolt1.csv') !== false ||
                strpos($file, 'nemvisszaruzhato.csv') !== false ||
                strpos($file, 'original_codes.csv') !== false ||
                strpos($file, 'polcar_product.csv') !== false ||
                strpos($file, 'polcar_product.xml') !== false ||
                strpos($file, 'q4y_uj.txt.1') !== false ||
                strpos($file, 'q4y_uj.txt.2') !== false ||
                strpos($file, 'schaefer_stock_price.csv.1') !== false ||
                strpos($file, 'schaefer_stock_price.csv.2') !== false ||
                strpos($file, 'schaefer_stock_price.csv.3') !== false ||
                strpos($file, 'schaefer_stock_price.csv.4') !== false ||
                strpos($file, 'schaefer_stock_price.csv.5') !== false ||
                strpos($file, 'schaefer_stock_price.csv.6') !== false ||
                strpos($file, 'schaefer_stock_price.csv.7') !== false ||
                strpos($file, 'suder.json') !== false ||
                strpos($file, 'suder.json2') !== false ||
                strpos($file, 'tecdoc.csv') !== false ||
                strpos($file, 'vonalkod.csv') !== false ||
                strpos($file, 'zip') !== false

            ) continue;

            $fullPath = $this->directory . '/' . $file;

            if (is_file($fullPath)) {
                if (!is_readable($fullPath)) {
                    continue;
                }

                $lastModified = @filemtime($fullPath);
                if ($lastModified <= $cutoffDate) continue;

                $contentValidation = $this->validateFileContent($fullPath);

                $fileInfo = [
                    'name' => $file,
                    'supplier' => $this->supplierMapping[$file] ?? 'Ismeretlen',
                    'size' => @filesize($fullPath),
                    'size_mb' => round(@filesize($fullPath) / (1024 * 1024), 2),
                    'last_modified' => $lastModified,
                    'last_modified_readable' => date('Y-m-d H:i:s', $lastModified),
                    'is_valid_content' => $contentValidation['is_valid'],
                    'content_details' => $contentValidation['details']
                ];

                if (isset($history[$file])) {
                    $sizeDiff = abs($fileInfo['size'] - $history[$file]['size']);
                    $fileInfo['size_change'] = $sizeDiff;
                    $fileInfo['previous_size_mb'] = round($history[$file]['size'] / (1024 * 1024), 2);
                    $fileInfo['significant_size_change'] = $sizeDiff > 1024000;
                } else {
                    $fileInfo['previous_size_mb'] = 0;
                }

                $daysSinceModified = ($currentTime - $fileInfo['last_modified']) / (24 * 3600);
                $fileInfo['days_since_modified'] = round($daysSinceModified, 2);
                $fileInfo['old_file'] = $daysSinceModified > 2;

                $fileDetails[] = $fileInfo;
            }
        }
        usort($fileDetails, function ($a, $b) {
            return strcasecmp($a['supplier'], $b['supplier']);
        });

        $this->updateHistory($fileDetails);
        $this->generateReport($fileDetails);
    }

    private function loadHistory()
    {
        if (!file_exists($this->historyFile)) {
            return [];
        }

        $historyContent = @file_get_contents($this->historyFile);
        $history = $historyContent ? json_decode($historyContent, true) : [];

        return $history ?: [];
    }

    private function updateHistory($fileDetails)
    {
        $history = [];
        foreach ($fileDetails as $file) {
            $history[$file['name']] = [
                'size' => $file['size'],
                'last_modified' => $file['last_modified']
            ];
        }

        $jsonHistory = json_encode($history, JSON_PRETTY_PRINT);
        @file_put_contents($this->historyFile, $jsonHistory);
    }

    private function validateFileContent($filePath)
    {
        $validation = [
            'is_valid' => true,
            'details' => []
        ];

        if (!is_readable($filePath)) {
            $validation['is_valid'] = false;
            $validation['details'][] = "Nem olvasható fájl";
            return $validation;
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $allowedExtensions = ['csv', 'txt', 'xlsx', 'xls', 'json', 'xml'];
        if (!in_array($extension, $allowedExtensions)) {
            $validation['is_valid'] = false;
            $validation['details'][] = "Nem támogatott fájltípus";
        }

        try {
            $handle = @fopen($filePath, 'r');
            if ($handle === false) {
                $validation['is_valid'] = false;
                $validation['details'][] = "Nem sikerült megnyitni a fájlt";
                return $validation;
            }

            $nonEmptyLineFound = false;
            while (($line = fgets($handle)) !== false) {
                if (trim($line) !== '') {
                    $nonEmptyLineFound = true;
                    break;
                }
            }
            @fclose($handle);

            if (!$nonEmptyLineFound) {
                $validation['is_valid'] = false;
                $validation['details'][] = "Üres vagy csak whitespace tartalom";
            }
        } catch (Exception $e) {
            $validation['is_valid'] = false;
            $validation['details'][] = "Hiba a fájl olvasása közben: " . $e->getMessage();
        }

        $fileSize = @filesize($filePath);
        if ($fileSize < 10) {
            $validation['is_valid'] = false;
            $validation['details'][] = "Túl kicsi fájlméret";
        }

        return $validation;
    }


    private function generateReport($fileDetails)
    {
        header('Content-Type: text/html; charset=utf-8');


        $importantFiles = [];
        $otherFiles = [];

        foreach ($fileDetails as $file) {
            if (in_array($file['supplier'], $this->importantSuppliers)) {
                $importantFiles[] = $file;
            } else {
                $otherFiles[] = $file;
            }
        }

        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>Fájl Elemzés Jelentés</title>
            <link href='https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap' rel='stylesheet'>
            <style>
            :root {
                --bg-primary: #f4f4f8;
                --text-primary: #333;
                --border-color: #e0e0e0;
                --size-change-color: #9fd3c7;
                --old-file-color: #385170;
                --invalid-content-color: #d1c4e9;
                --zero-size-color:rgb(248, 171, 178);
                --separator-color: #666;
            }
            body { 
                font-family: 'Inter', Arial, sans-serif; 
                font-size: 16px; 
                color: var(--text-primary); 
                background-color: var(--bg-primary);
                margin: 0;
                padding: 20px;
            }
            .container {
                max-width: 1200px;
                margin: 0 auto;
                background-color: white;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                border-radius: 8px;
            }
            .report-header {
                padding: 20px;
                background-color: #f8f9fa;
                border-bottom: 1px solid var(--border-color);
            }
            .legend {
                display: flex;
                gap: 10px;
                font-size: 14px;
                flex-wrap: wrap;
            }
            .legend-item {
                display: flex;
                align-items: center;
                gap: 5px;
                padding: 5px 10px;
                background-color: #f0f0f0;
                border-radius: 4px;
            }
            .legend-item span {
                width: 15px;
                height: 15px;
                display: block;
                border-radius: 3px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
            }
            th, td {
                border: 1px solid var(--border-color);
                padding: 10px;
                text-align: left;
            }
            th {
                background-color: #f1f3f5;
                position: sticky;
                top: 0;
            }
            .size-change { background-color: var(--size-change-color); }
            .old-file { background-color: var(--old-file-color); color: white; }
            .invalid-content { background-color: var(--invalid-content-color); }
            .zero-size { background-color: var(--zero-size-color); }
            .separator-row td {
                background-color: var(--separator-color) !important;
                height: 3px;
                padding: 0 !important;
                border: none;
            }
            tr:nth-child(even) { background-color: #f9f9f9; }
            tr:hover { background-color: #eef2f7; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='report-header'>
                    <h1>Fájl Elemzés Jelentés</h1>
                    <div class='legend'>
                        <div class='legend-item'>
                            <span class='size-change'></span> Méretváltozás
                        </div>
                        <div class='legend-item'>
                            <span class='old-file'></span> Régi fájl (>30 nap)
                        </div>
                        <div class='legend-item'>
                            <span class='invalid-content'></span> Érvénytelen tartalom
                        </div>
                        <div class='legend-item'>
                            <span class='zero-size'></span> Nullás fájlméret vagy kisebb mint 0,01 mb
                        </div>
                    </div>
                </div>
                <table>
                    <tr>
                        <th>Szállító</th>
                        <th>Fájl neve</th>
                        <th>Jelenlegi méret (MB)</th>
                        <th>Előző napi méret (MB)</th>
                        <th>Utolsó módosítás</th>
                        <th>Módosítás óta eltelt napok</th>
                        <th>Tartalom érvényes</th>
                        <th>Tartalom részletek</th>
                    </tr>";


        foreach ($importantFiles as $file) {
            $this->printFileRow($file);
        }


        echo "<tr class='separator-row'><td colspan='8'></td></tr>";


        foreach ($otherFiles as $file) {
            $this->printFileRow($file);
        }

        echo "</table>
            </div>
        </body>
        </html>";
        exit;
    }
    private function printFileRow($file)
    {
        $rowClass = '';
        if ($file['size_mb'] == 0 || $file['size_mb'] == '0' || $file['size_mb'] < 0.03 || empty($file['size_mb'])) {
            $rowClass = 'zero-size';
        } elseif ($file['days_since_modified'] > 30) {
            $rowClass = 'old-file';
        } elseif ($file['size_mb'] !== $file['previous_size_mb']) {
            $rowClass = 'size-change';
        } elseif (!$file['is_valid_content']) {
            $rowClass = 'invalid-content';
        }

        echo "<tr class='$rowClass'>
                <td>{$file['supplier']}</td>
                <td>{$file['name']}</td>
                <td>{$file['size_mb']}</td>
                <td>{$file['previous_size_mb']}</td>
                <td>{$file['last_modified_readable']}</td>
                <td>{$file['days_since_modified']}</td>
                <td>" . ($file['is_valid_content'] ? 'Igen' : 'Nem') . "</td>
                <td>" . implode(', ', $file['content_details']) . "</td>
              </tr>";
    }
}

$analyzer = new FileAnalyzer('y:\\gerison_arlista');
$analyzer->analyzeFiles();
