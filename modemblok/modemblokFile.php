<pre><?php

        $PolcarPath = 'z:\Farkas Gergo\modemblok\polcar_Romania_no_20250407_093415.csv';

        if ($handle = fopen($PolcarPath, "r")) {

            $header = fgetcsv($handle, 1000, ";");

            $counter = 0;

            $beszcodeAmitTiltaniKell = [];

            while (($row = fgetcsv($handle, 4000, ";")) !== false) {
                $code = $row[0];
                $info = $row[2];

                if (strpos($info, 'Roman') !== false) {

                    $beszcodeAmitTiltaniKell[] = $code;
                }


                if ($counter > 1000) {

                    break;
                }

                $counter++;
            }
            var_dump($beszcodeAmitTiltaniKell);
        }

        ?>