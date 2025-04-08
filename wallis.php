<pre><?php

        $uploadedFile = 'Y:\Árlista 2025\GYÁRI ÁRLISTÁK 2025\WALLIS\Cikklista2.zip';

        if (!file_exists($uploadedFile)) {
            die("1");
        }

        $zip = new ZipArchive();
        if ($zip->open($uploadedFile) !== TRUE) {
            die("2");
        }

        $txtFile = null;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            if (pathinfo($filename, PATHINFO_EXTENSION) === 'txt') {
                $txtFile = $filename;
                break;
            }
        }

        if (!$txtFile) {
            $zip->close();
            die("3");
        }

        $txtContent = $zip->getFromName($txtFile);
        $zip->close();

        if ($txtContent === false) {
            die("4");
        }

        $txtContent = iconv('CP852', 'UTF-8//IGNORE', $txtContent);

        $lines = explode("\n", trim($txtContent));
        $csvOutput = [];

        $kedvezmeny = [
            "A" => 14,
            "B" => 18,
            "C" => 22,
            "D" => 26,
            "E" => 30,
            "F" => 34,
            "G" => 38,
            "H" => 42,
            "I" => 46,
            "K" => 50
        ];

        foreach ($lines as $line) {

            $line = trim($line);
            if (!empty($line)) {
                $row = str_getcsv($line, ';');
                if (!empty($row) && (!isset($row[2]) || $row[2] !== "L") && (!isset($row[0]) || !empty($row[0]))) {
                    foreach ($row as $key => &$field) {
                        $field = str_replace('˙', '', $field);
                        if ($key === 3) {
                            $field = str_replace(" ", "", $field);
                        }
                        
                    }

                    if (isset($row[2]) && isset($row[3])) {
                        $discountKey = strtoupper(trim($row[2]));

                        $amount = $row[3];
                        $amountFixed = str_replace(",", ".", $amount);
                        
                        
                        if (is_numeric($amountFixed) && isset($kedvezmeny[$discountKey])) {
                                                        
                            $discount = $kedvezmeny[$discountKey];
                            
                            $newAmount = $amountFixed *  (1 - $discount / 100);

                            $newAmount = number_format($newAmount, 2, '.', '');

                            
                            $row[3] = $newAmount;
                        }
                    }
                    $csvOutput[] = implode(';', $row);
                }
            }
    } 

    
    
        //$csvFilename = 'C:\Users\LP-GERGO\Desktop\Farkas Gergő test\mentett_árlista\wallis.csv';
        $csvFilename = 'y:\kezi_arlista\wallis2.csv';

        $dir = dirname($csvFilename);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($csvFilename, "\xEF\xBB\xBF" . implode("\n", $csvOutput));

        echo "mentve ide: " . $csvFilename;
    