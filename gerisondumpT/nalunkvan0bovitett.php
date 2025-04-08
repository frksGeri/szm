<pre><?php

        header('Content-Type: text/html; charset=utf-8');


        ob_implicit_flush(true);
        ob_end_flush();

        set_time_limit(0);
        ini_set('memory_limit', '50000M');


        require '../ggg/init.php';

        $beszalltioFilePath = 'y:\gerison_arlista\autonethu.csv';


        function t1018($id)
        {
            $t1018 = run(
                "SELECT t1110.id, t1018.code FROM gerisondump.t1018 t1018
         LEFT JOIN gerisondump.t1110 t1110 ON t1018.fromobjectid = t1110.objectid 
         LEFT JOIN gerisondump.t1004 t1004 ON t1018.toobjectid = t1004.objectid 
         WHERE t1004.articlecode = ?",
                [$id]
            );

            if (!empty($t1018)) {
                return $t1018;
            }
        }

        function t1004($artnr, $dlnr)
        {
            $t1004 = run("SELECT articlecode, glid FROM t1004 WHERE size != 'DEL' and artnr = ? AND dlnr = ?", [$artnr, $dlnr]);

            if (!empty($t1004) && isset($t1004[0])) {
                return $t1004[0];
            }
        }

        $beszallitok = [];
        $counter = 0;
        $missing_supplier_codes = [];
        $search_supplier = 'auton';

        if ($handle = fopen($beszalltioFilePath, 'r')) {
            $header = fgetcsv($handle, 1000, ";");

            $result = [];

            while (($row = fgetcsv($handle, 1000, ';')) !== FALSE) {
                $beszcode = $row[0];
                $dlnr = $row[2];
                $artnr = $row[1];

                $t1004 = t1004($artnr, $dlnr);

                if ($t1004) {
                    $acode = $t1004['articlecode'];
                    $glid = $t1004['glid'];
                    $result[$acode] = t1018($acode);

                    $supplier_found_with_matching_code = false;

                    if (!empty($result[$acode])) {
                        foreach ($result[$acode] as $entry) {

                            if ($entry['id'] === $search_supplier && $entry['code'] === $beszcode) {
                                $supplier_found_with_matching_code = true;
                                break;
                            }
                        }
                    }


                    if (!$supplier_found_with_matching_code) {
                        $missing_supplier_codes[$acode] =  [$beszcode, $glid];
                    }
                }
            }

            echo "Hiányzó '$search_supplier' beszállítós articlecode-ok, vagy ahol a t1018.code nem egyenlő a beszcode-dal:\n";
          //  var_dump($missing_supplier_codes);

            $srt = "code;articlecode;glid;\n";

            foreach ($missing_supplier_codes as $articlecode => $code) {
                $srt .= $code[0] . ";" . $articlecode . ";" . $code[1] . "\n";
            }

            $date = date('Y-m-d');
            if (file_put_contents('c:\Users\LP-GERGO\Desktop\NALUNKVAN0\autonet_nalunkvan0_' . $date . '.csv', $srt)) {
                echo "kiirás kész";
            }

            


            fclose($handle);
        }
