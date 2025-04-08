<pre><?php

        include '../ggg/init.php';

        $select = run(
            "SELECT objectid from t1018 t1018  WHERE t1018.`code` LIKE '%ELRONTOTTG'"
        );

        $str = '';



        if (!empty($select)) {
            foreach ($select as $rows)

                $str .= "'" .  $rows['objectid'] ."'" . ',';
        }

        var_dump($str);


/*
$db = new mysqli('131.0.0.199', 'geri', '', 'gerisondump', '3307');

if ($db->connect_error) {
    die('nem sikerült csatlakozni az adatbazishoz--->' . $db->connect_error);
}

set_time_limit(200);

$db->set_charset('utf8mb4');

$select = ("SELECT * from t1018 t1018  WHERE t1018.`code` =?");

$select = $db->prepare($select);

$code = '24.0130-0104.1';

$select->bind_param('s',$code);

$select->execute();

$result = $select->get_result();

if ($result) {
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    if (!empty($rows)) {
        echo "<table border='1'><tr>";
        
        foreach (array_keys($rows[0]) as $column) {
            echo "<th>$column</th>";
        }
        echo "</tr>";
        
        foreach ($rows as $row) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        echo "<p>Összesen " . count($rows) . " találat.</p>";
    } else {
        echo "Nincs találat a megadott kódra.";
    }
        
}


$select->close();
$db->close();

//var_dump($rows,count($rows));

*/