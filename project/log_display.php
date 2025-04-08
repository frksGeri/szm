<html>
<head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<link rel="stylesheet" href="test.css">
</head>

<body>
    <div class="szakal-display">
        <?php
            $conn = mysqli_connect("localhost", "root", "", "work_log");
            if($conn === false){
                die("ERROR: Could not connect. " 
                    . mysqli_connect_error());
            }

            $sql = "SELECT title,worker,dateStamp FROM logs";
            $result = $conn->query($sql);
            while($row = $result->fetch_assoc()) {
                echo "<h4>" . $row["title"] . "<br>";
            }
        ?>
    </div>
</body>

</html>