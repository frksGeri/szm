<?php

set_time_limit(0);
ini_set('memory_limit', '500000M');

include '../ggg/init.php';


$select = run("select glid,code from newszmdb.products where code != '' and
                        code not like '%*%' and code != '#N/A' and
                        code not like '%--%' and
                        code not like '%\%' and
                        code not like '%/%'
                        ");

foreach ($select as $row) {
   run("update newszmdb.products set alter_code = '" . str_replace(" ","",$row["code"]) ."' where glid = '" . $row["glid"] . "'" );
}

echo 'kesz';
