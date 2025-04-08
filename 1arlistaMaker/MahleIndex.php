<?php

function convertToNumber($value)
{
    if (is_numeric($value)) {
        return (float)$value;
    }
    $value = str_replace(',', '.', $value);
    $value = preg_replace('/[^0-9.]/', '', $value);

    return is_numeric($value) ? (float)$value : 0;
}

include "engine.php";
echo "<br/>";
include "filter.php";
echo "<br/>";
include "thermal.php";
echo "<br/>";
include "thermostat.php";

?>
