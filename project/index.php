<html>
<head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<link rel="stylesheet" href="test.css">
</head>

<?php 

$month = date('m');
$day = date('d');
$year = date('Y');

$today = $year . '-' . $month . '-' . $day;
?>

<body>
    <div class="szakal-form">
        <form method="POST" action="insert_record.php">
            <div class="form-group">
                <label for="titleInput">Feladat neve</label>
                <input type="text" class="form-control" id="titleInput" name="titleInput">
            </div>
            <div class="row">
                <div class="col-sm">
                    <select class="form-select" id="userSelect" name="userSelect">
                        <option value="1">Gergő</option>
                        <option value="2">Róbert</option>
                    </select>
                </div>
                <div class="col-sm">
                    <input type="date" name="dateInput" id="dateInput" value="<?php echo $today; ?>">
                </div>
                <div class="col-sm">
                    <button type="submit" class="btn btn-success">Küldés</button>
                </div>
            </div>
        </form>
    </div>
</body>

<div class="szakal-form">
    <h2>Sikeres adatfelvétel
</div>
</html>