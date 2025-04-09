<pre><?php
        /*
        $PolcarPath = 'z:\Farkas Gergo\modemblok\polcar_Romania_no_20250407_093415.csv';

        if ($handle = fopen($PolcarPath, "r")) {

            $header = fgetcsv($handle, 1000, ";");

            $beszcodeAmitTiltaniKell = '';

            while (($row = fgetcsv($handle, 1000, ";")) !== FALSE) {

                $code = $row[0];
                $info = $row[2];

                if (strpos($info, "Roman") !== FALSE) {

                    $beszcodeAmitTiltaniKell.= $code . "\n";
                }
            }
            fclose($handle);
        }

        $output = 'c:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\polcar_romania_modemblock.csv';
        file_put_contents($output,$beszcodeAmitTiltaniKell);
        echo 'kesz';
*/

        require '../ggg/init.php';


        function t1018($id)
        {
            $t1018 = run(
                "SELECT * FROM t1018 t1018
                JOIN t1004 t1004 ON t1018.toobjectid = t1004.objectid
                WHERE t1018.`code` = ?",
                [$id]
            );

            if (!empty($t1018)) {
                return $t1018[0]['glid'];
            }
        }

        $outputPath = 'y:\kezi_arlista\modemblokk.csv';

        try {
            $inputPath = 'z:\Farkas Gergo\modemblok\polcar_Romania_no_20250407_093415.csv';

            if (!file_exists($inputPath)) {
                throw new Exception("A megadott bemeneti fájl nem található: $inputPath");
            }

            $codesToBlock = [];

            if (($handle = fopen($inputPath, "r")) === false) {
                throw new Exception("Nem sikerült megnyitni a bemeneti fájlt: $inputPath");
            }




            while (($row = fgetcsv($handle, 4000, ";")) !== false) {
                if (count($row) < 3) {
                    continue;
                }

                $code = trim($row[0]);
                $info = $row[2];

                if (stripos($info, 'Roman') !== false) {
                    $codesToBlock[] = $code;
                }
            }

            fclose($handle);


            if (($handleOut = fopen($outputPath, "w")) === false) {
                throw new Exception("Nem sikerült megnyitni a kimeneti fájlt: $outputPath");
            }

            foreach ($codesToBlock as $code) {

                $glid = t1018($code);

                if (!empty($glid)) {
                    $codeHasGlid = $code . ";" . $glid . ";";
                    fwrite($handleOut, $codeHasGlid . "RO" . PHP_EOL);
                }
            }

            fclose($handleOut);

            echo "Unix sorvégű fájl sikeresen elkészült: ";
        } catch (Exception $e) {
            echo "Hiba történt: " . $e->getMessage();
        }

        ?>
        