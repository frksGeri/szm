<?php

$start = microtime(true);

try {
    $conn = new PDO("mysql:host=127.0.0.1;dbname=newszmdb", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Sikeres kapcsolódás!";
} catch (PDOException $e) {
    echo "Hiba a kapcsolódásnál: " . $e->getMessage();
}

$folderPath = "Z:/szerző peti/";
$files = glob($folderPath . "*.csv");

$sql =
    "INSERT INTO products_v2 (glid, gyarto, articlecode, size, tcs_szoveg, gy_kod, egyseg, code)
    VALUES (:glid, :gyarto, :articlecode, :size, :tcs_szoveg, :gy_kod, :egyseg, :code)
    ON DUPLICATE KEY UPDATE
    gyarto = VALUES(gyarto),
    articlecode = VALUES(articlecode),
    size = VALUES(size),
    tcs_szoveg = VALUES(tcs_szoveg),
    gy_kod = VALUES(gy_kod),
    egyseg = VALUES(egyseg),
    code = VALUES(code)"
;
$stmt = $conn->prepare($sql);

foreach ($files as $file) {
    if (!ctype_upper($file[0]))
        continue;
    $values = array();
   
    if (($handle = fopen($file, "r")) !== FALSE) 
    {
        while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE)
        {
           
            if (count($data) < 3)
                break;
            //Nemtom miért így csinálom. Egyből mehetne stmt->execute -ba
            $arr = array(
                "glid"=>$data[0],
                "gyarto"=>$data[1],
                "articlecode"=>$data[2],
                "size"=>$data[3],
                "tcs_szoveg"=>$data[4],
                "gy_kod"=>$data[5],
                "egyseg"=>$data[6],
                "code"=>$data[7]
            );
            array_push($values, $arr);
        }
    }
    fclose($handle);

    $conn->beginTransaction();
    foreach ($values as $value)
        $stmt->execute($value);

    try
    {
        $conn->commit();
    }
    catch (PDOException $e)
    {
        $conn->rollBack();
        echo "Hiba: ".$e->getMessage();
    }
}

echo "<br/><br/>";
echo microtime(true) - $start." seconds";

//$conn->close();
?>