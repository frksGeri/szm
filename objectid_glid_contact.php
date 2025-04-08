<?php

ini_set('memory_limit', '512M');
set_time_limit(0);

$time_start = microtime(true);

function connectToDatabase()
{
    $host = '131.0.0.199';
    $username = 'geri';
    $password = '';
    $dbname = 'gerisondump';

    $conn = new mysqli($host, $username, $password, $dbname, 3307);

    if ($conn->connect_error) {
        die("Kapcsolódási hiba: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
    return $conn;
}

$db = connectToDatabase();

function run($query, $params = [])
{
    global $db;

    $stmt = $db->prepare($query);
    if (!$stmt) {
        die("Lekérdezési hiba: " . $db->error);
    }

    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...array_values($params));
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    $stmt->close();
    return true;
}


$path = 'z:\wilmink_weights_nyers.csv';


$handle = fopen($path, "r");

$str = "code;nalunk_levo_weight;kapott_weights;objecti;egyeb\n";
while (($row = fgetcsv($handle, 1000, ";")) !== FALSE) {
    $glid = $row[2];
    $weightFromCsv = $row[1];
    $code = $row[0];

    $select = run("SELECT objectid,weight FROM t1004 WHERE glid = ?", [$glid]);
    $equal = 'egyenlő';
    if (!empty($select)) {
        $oid = $select[0]['objectid'];
        $weight = $select[0]['weight'];

        if (intval($weight) != intval($weightFromCsv)) {
            $equal = 'nem egyenlő';
        } elseif (
            intval($weight) > 0.001
        ) {
            $equal = 'van már megadva súlyadat';
        }

        //$str .= 'UPDATE t1004 SET weight = ' .  $row[1] . ' WHERE objectid =' . $oid . ";\n";
        $str .= $code . ";" . $weight . ";" . $weightFromCsv . ";" . $oid . ";" . $equal . "\n";
    }
}


fclose($handle);


if (!empty($str)) {
    file_put_contents("z:\wilmink_weights_CSEK_UTOELLENROZES.csv", $str);
}

$time_end = microtime(true);


$execution_time = ($time_end - $time_start) / 60;

echo '<b>Futási idő:</b> ' . $execution_time . ' perc';
