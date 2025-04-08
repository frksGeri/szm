<?php
/*
$db = new mysqli("localhost", "root", "", "gerison_import_data");

if ($db->connect_error) {
    die("valami elhalt. hiba:" . $db->connect_error);
}

$db->set_charset("utf8mb4");

$besz = 'jmauto_st';

$stmt = $db->prepare("SELECT * FROM gerison_import_data.null_hiba WHERE `name` = ? order by dateStamp desc");

if (!$stmt) {
    die("lekérdezési hiba." . $db->error);
}

$stmt->bind_param('s', $besz);

if ($stmt->execute()) {
    $result = $stmt->get_result();

    $rows =  $result->fetch_all(MYSQLI_ASSOC);
}

var_dump($rows);


$stmt->close();


*/

$db = new mysqli("localhost", "root", "", "gerison_import_data");

if ($db->connect_error) {
    die("kapcsolodási hiba. üzenet: " . $db->connect_error);
}


$besz = 'polcar';

$sql = ("SELECT `value`,DATE(dateStamp) as dateStamp from gerison_import_data.null_hiba WHERE name ='" . $besz . "'");

$select = $db->query($sql);

$labels = [];
$values = [];

if ($select->num_rows > 0) {
    foreach ($select as $row) {
        $labels[] = $row['dateStamp'];
        $values[] = $row['value'];
    };
}

$db->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kimutatás</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h1{
            text-align: center;
            color: #333;
        }
        .container{
            max-width: 1200px;
            margin: 0 auto;
        }
        table{
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table, td, th{
            border:1px solid
        }
        th, td{
            padding: 10px;
            text-align: center;
        }
        td:nth-child(2){
            font-weight: bold;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        canvas {
            max-width: 100%;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Nalunk van 0 Kimutatás</h1>

        <table>
            <thead>
                <tr>
                    <th>Dátum</th>
                    <th>Nalunk van 0 db szám</th>
                </tr>
            </thead>
            <tbody>
                <?php
                for ($i = count($labels) - 1; $i >= 0; $i--) {
                    echo "<tr>";
                    echo "<td>" . $labels[$i] . "</td>";
                    echo "<td>" . $values[$i] . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        <canvas id="nullHibaChart"></canvas>
    </div>
    <script>
    const ctx = document.getElementById('nullHibaChart').getContext('2d');
    const nullHibaChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_reverse($labels)); ?>,
            datasets: [{
                label: 'Nálunk van 0 (db) - jmauto_st',
                data: <?php echo json_encode(array_reverse($values)); ?>,
                borderColor: 'rgba(0, 123, 255, 1)', 
                backgroundColor: 'rgba(0, 123, 255, 0.1)', 
                pointBackgroundColor: 'rgba(0, 123, 255, 1)',
                pointBorderColor: '#fff',
                pointRadius: 5, 
                pointHoverRadius: 8,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            animation: {
                duration: 2000, 
                easing: 'easeInOutQuad' 
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Nálunk van 0 (db)',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)' 
                    },
                    ticks: {
                        font: {
                            size: 12
                        }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Dátum',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    },
                    grid: {
                        display: false 
                    },
                    ticks: {
                        font: {
                            size: 12
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: {
                            size: 14
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 12
                    }
                }
            }
        }
    });
</script>
</body>


</html>