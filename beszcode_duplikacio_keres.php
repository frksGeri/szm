<pre>
<?php

$file = "B:\szerző peti\beszkod\langkul.csv";

$codes = array();
$duplicates = array();
$handle = fopen($file, "r");
fgetcsv($handle);
while (($row = fgetcsv($handle, null, ";")) !== FALSE)
{
    if (key_exists($row[0],$codes) && $codes[$row[0]] != $row[1])
    {
        $arr = array();
        if (key_exists($row[0], $duplicates))
            $arr = $duplicates[$row[0]];
        array_push($arr,$row[1]);
        $duplicates[$row[0]] = $arr;
        continue;
    }
    if (!key_exists($row[0],$codes))
        $codes[$row[0]] = $row[1];
}

$str = "code;glid;glid2\n";
foreach ($duplicates as $key => $value) {
    foreach ($value as $glid) {
        $str .= $key.";".$glid.";".$codes[$key]."\n";
    }
}

file_put_contents("C:\\Users\\LP-KATALOGUS1\\Desktop\\exports\\langkul_duplicates.csv",$str);

?>