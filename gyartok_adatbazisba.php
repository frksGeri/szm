<pre>
<?php

$start = microtime(true);

$conn = mysqli_connect("131.0.1.92", "robi", "", "newszmdb");
if($conn === false){
    die("ERROR: Could not connect. " 
        . mysqli_connect_error());
}


$folderPath = "B:/szerző peti/";
$files = glob($folderPath . "*.csv");

ob_implicit_flush(true);
foreach ($files as $file) {
    if (!ctype_upper($file[0]))
        continue;
    $abort = true;
    $sql = "INSERT INTO products_v2 (glid, gyarto, articlecode, size, tcs_szoveg, gy_kod, egyseg, code) VALUES ";
    if (($handle = fopen($file, "r")) !== FALSE) 
    {
        while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE)
        {
            $abort = false;
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
            $sql .= "('".$data[0]."','".$data[1]."','".$data[2]."','".$data[3]."','".$data[4]."','".$data[5]."','".$data[6]."','".$data[7]."'), ";
        }
    }

    if ($abort)
        continue;

    $sql = substr($sql, 0, -2);

    $sql .= " ON DUPLICATE KEY UPDATE gyarto = VALUES(gyarto), articlecode = VALUES(articlecode), size = VALUES(size), tcs_szoveg = VALUES(tcs_szoveg), gy_kod = VALUES(gy_kod), egyseg = VALUES(egyseg), code = VALUES(code)";

    //echo $sql."<br/>";
    mysqli_query($conn, $sql);
    $i++;
}

echo "<br/><br/>";
echo microtime(true) - $start." seconds";
ob_end_flush();

?>