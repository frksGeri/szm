<?php
die();
set_time_limit(0);

$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "newszmdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("111111111111111111111111111111111 " . $conn->connect_error);
}

$conn->query("SET GLOBAL max_allowed_packet=67108864"); // 64MB

$tableName = "products";
$sql = "CREATE TABLE IF NOT EXISTS $tableName (
    glid VARCHAR(150),
    gyarto VARCHAR(150),
    articlecode VARCHAR(150),
    size VARCHAR(150),
    tcs_szoveg VARCHAR(255),
    gy_kod VARCHAR(50),
    egyseg VARCHAR(50),
    code VARCHAR(50),
    PRIMARY KEY (glid)
)";

if ($conn->query($sql) !== TRUE) {
    die("Hiba a $tableName tablice letrehozasakor: " . $conn->error);
}

$folderPath = "Z:/szerző peti/";
$files = glob($folderPath . "*.csv");
$batchSize = 1000; 

foreach ($files as $file) {
    if (($handle = fopen($file, "r")) !== FALSE) {
        $values = [];
        $processedCount = 0;

        while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {

            $values[] = sprintf("('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
                $conn->real_escape_string($data[0]),
                $conn->real_escape_string($data[1]),
                $conn->real_escape_string($data[2]),
                $conn->real_escape_string($data[3]),
                $conn->real_escape_string($data[4]),
                $conn->real_escape_string($data[5]),
                $conn->real_escape_string($data[6]),
                $conn->real_escape_string($data[7])
            );

            $processedCount++;

            if (count($values) >= $batchSize) {
                processBatch($conn, $tableName, $values);
                $values = [];
            }
        }

        if (!empty($values)) {
            processBatch($conn, $tableName, $values);
        }

        fclose($handle);
        echo "Fájl feldolgozva: $file (Összes rekord: $processedCount)\n";
    }
}

$conn->close();

function processBatch($conn, $tableName, $values) {

    $sql = "INSERT INTO $tableName 
            (glid, gyarto, articlecode, size, tcs_szoveg, gy_kod, egyseg, code)
            VALUES " . implode(',', $values) . "
            ON DUPLICATE KEY UPDATE
            gyarto = VALUES(gyarto),
            articlecode = VALUES(articlecode),
            size = VALUES(size),
            tcs_szoveg = VALUES(tcs_szoveg),
            gy_kod = VALUES(gy_kod),
            egyseg = VALUES(egyseg),
            code = VALUES(code)";

    if (!$conn->query($sql)) {
        error_log("bajvan: " . $conn->error . "\n", 3, "error_log.txt");
    }
}
?>