<?php

$sourceHost = "131.0.0.199"; 
$sourceUser = "geri"; 
$sourcePass = ""; 
$sourceDb = "napidump"; 

$targetHost = "localhost"; 
$targetUser = "root"; 
$targetPass = ""; 
$targetDb = "backup"; 

$sourceConn = new mysqli($sourceHost, $sourceUser, $sourcePass, $sourceDb, 3307);
$targetConn = new mysqli($targetHost, $targetUser, $targetPass, $targetDb);

if ($sourceConn->connect_error || $targetConn->connect_error) {
    die("Kapcsolódási hiba: " . $sourceConn->connect_error . " | " . $targetConn->connect_error);
}

$query = "
    SELECT 
        t1973.*,
        FROM_UNIXTIME(t1973.last_mod_time/1000) AS last_modified,
        t1004.articlecode 
    FROM napidump.t1973 t1973 
    JOIN napidump.t1004 t1004 ON t1973.mandatoryobjectid = t1004.objectid
";

$result = $sourceConn->query($query);

$today = date("Y-m-d");

if ($result) {
    
    $createTableQuery = "
        CREATE TABLE IF NOT EXISTS backup.t1973 (
            objectid BIGINT,
            created_by BIGINT,
            creation_time BIGINT,
            last_mod_by BIGINT,
            last_mod_time BIGINT,
            mandatoryobjectid BIGINT,
            arres DOUBLE,
            maxd BIGINT,
            apply_to_bazar_as_price SMALLINT,
            apply_to_bazar_as_arres SMALLINT,
            last_modified DATETIME,
            articlecode VARCHAR(255),
            stamp DATE
        )
    ";
    $targetConn->query($createTableQuery);

    $insertStmt = $targetConn->prepare("
        INSERT INTO backup.t1973 (
            objectid, created_by, creation_time, last_mod_by, last_mod_time,
            mandatoryobjectid, arres, maxd, apply_to_bazar_as_price,
            apply_to_bazar_as_arres, last_modified, articlecode, stamp
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $insertStmt->bind_param(
        "iiiidisdddsss",
        $objectid,
        $created_by,
        $creation_time,
        $last_mod_by,
        $last_mod_time,
        $mandatoryobjectid,
        $arres,
        $maxd,
        $apply_to_bazar_as_price,
        $apply_to_bazar_as_arres,
        $last_modified,
        $articlecode,
        $date
    );

    while ($row = $result->fetch_assoc()) {
        $objectid = $row['objectid'];
        $created_by = $row['created_by'];
        $creation_time = $row['creation_time'];
        $last_mod_by = $row['last_mod_by'];
        $last_mod_time = $row['last_mod_time'];
        $mandatoryobjectid = $row['mandatoryobjectid'];
        $arres = $row['arres'];
        $maxd = $row['maxd'];
        $apply_to_bazar_as_price = $row['apply_to_bazar_as_price'];
        $apply_to_bazar_as_arres = $row['apply_to_bazar_as_arres'];
        $last_modified = date("Y-m-d H:i:s", strtotime($row['last_modified']));
        $articlecode = $row['articlecode'];
        $date = $today;

        $insertStmt->execute();
    }

    $insertStmt->close();
    echo "Adatátvitel sikeresen befejezve.";
} else {
    echo "Hiba a lekérdezés futtatása közben: " . $sourceConn->error;
}

$sourceConn->close();
$targetConn->close();

?>
