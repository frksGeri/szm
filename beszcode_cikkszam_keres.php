<pre>
<?php

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
//
//DUPLIKÁCIÓS FÁJLT DOLGOZ FEL
//HASZNÁLD ELŐSZÖR A `beszcode_duplikacio_keres.php` SCRIPTET
//
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

$file = "C:\Users\LP-KATALOGUS1\Desktop\\exports\langkul_duplicates.csv";

$conn = mysqli_connect("131.0.1.92", "robi", "", "newszmdb");
if($conn === false){
    die("ERROR: Could not connect. " 
        . mysqli_connect_error());
}

$str = "code;glid;glid2;articlecode;articlecode2\n";
$handle = fopen($file, "r");
fgetcsv($handle);
while (($row = fgetcsv($handle, null, ";")) !== FALSE)
{
    $sql = "SELECT * FROM products_v2 WHERE glid='$row[1]'";
    $result = $conn->query($sql);
    $sql = "SELECT * FROM products_v2 WHERE glid='$row[2]'";
    $result2 = $conn->query($sql);

    $str .= $row[0].";".$row[1].";".$row[2].";";

    ($result->num_rows > 0) ? $str .= $result->fetch_assoc()["articlecode"].";" : ";";
    ($result2->num_rows > 0) ? $str .= $result2->fetch_assoc()["articlecode"].";" : ";";
    $str .= "\n";
}

file_put_contents("C:\\Users\\LP-KATALOGUS1\\Desktop\\exports\\langkul_duplicates_matches.csv",$str);

/*die();

$str = "code;glid;articlecode\n";
$handle = fopen($file, "r");
fgetcsv($handle);
while (($row = fgetcsv($handle, null, ";")) !== FALSE)
{
    $sql = "SELECT * FROM products_v2 WHERE glid='$row[1]'";
    $result = $conn->query($sql);
    if ($result->num_rows < 1)
    {
        $str .= $row[0].";".$row[1]."\n";
    }
    else
    {
        $res = $result->fetch_assoc();
        $str .= $row[0].";".$row[1].";".$res["articlecode"].";".$res["gyarto"]."\n";
    }
    $gyarto = $res["gyarto"];
    $sql = "SELECT * FROM gyartok WHERE nev ='$gyarto'";
    $result = $conn->query($sql);
    if ($result->num_rows < 1)
    {
        $str .= $row[0].";".$row[1]."\n";
    }
    else
    {
        $ress = $result->fetch_assoc();
        $str .= $row[0].";".$row[1].";".$res["articlecode"].";".$res["gyarto"]."\n";
    }
}

file_put_contents("C:\\Users\\LP-KATALOGUS1\\Desktop\\exports\\langkul_matches.csv",$str);*/

?>