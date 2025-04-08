<pre><?php

        require '../ggg/init.php';

        $path = 'c:\Users\LP-GERGO\Documents\aa.csv';


        $srt = "code;articlecode;glid\n";

        if ($handle = fopen($path, "r")) {

            while (($row = fgetcsv($handle, 1000, ";")) !== FALSE) {

                $code = $row[2];

                $select = run("SELECT articlecode,glid FROM t1004 where size != 'DEL' and artnr =? and dlnr =?", [$row[0], $row[1]]);

                if ($select) {
                    $result = $select[0];


                    $srt .= $code . ";" . $result['articlecode'] . ";" . $result['glid'] . "\n";
                }
            }

            fclose($handle);
        }

        file_put_contents("c:\Users\LP-GERGO\Desktop\autonetFabianUtaniNalunkvan0Bekotes.csv", $srt);
        echo "kesz";
