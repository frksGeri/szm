<pre>
<?php

include "generics.php";

$apiSession = new ApiSession();

$customerInfo = $apiSession->getMyCustomerInfo();
if (count($customerInfo) == 0)
{
    echo "Nincs belépve";
    echo "<br/>";
    echo $apiSession->sessionID;
    echo "<br/>";
    $response = $apiSession->doLogin("23446", "f1d327f7cc26a45821837fbef9a12e6e");
    //$response = $apiSession->doLogin("23447", "06499ca63e5d7c42ed0aaee8c9956021");
    if ($response) echo "Sikeres belépés";
    else echo "Sikertelen belépés";
}
else
{
    echo "Be van lépve";
}
echo "<br/>";

//var_dump($apiSession->getMyCustomerInfo());

?>