<?php
    if ($_SERVER["REQUEST_METHOD"] != "POST") return;
    set_time_limit(0);

    $servername = "127.0.0.1";
    $username = "root";
    $password = "";
    $dbname = "newszmdb";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("111111111111111111111111111111111 " . $conn->connect_error);
    }

    
    $conn->query("SET GLOBAL max_allowed_packet=67108864");
        
    $workTitle = $_POST["titleInput"];
    $worker = $_POST["userSelect"];
    $date = $_POST["dateInput"];
    $sql = "INSERT INTO logs(title,worker,dateStamp) VALUES ('$workTitle', '$worker', '$date')";

    mysqli_query($conn, $sql);
    header("Location: index.php");
    die();
?>