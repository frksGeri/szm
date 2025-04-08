<?php

class ApiCommand
{
    private $url = "https://api.motogama.pl:8891/ws";
    public $command;
    public $params;

    public function __construct($command, $params, $quickfire = false) {
        if ($command == "" || $command == null) $this->debug_to_console("ApiCommand Error 0001");
        if ($command == "" || $command == null || count($params) == 0) $this->debug_to_console("ApiCommand Error 0002");

        $this->command = $command;
        $this->params = $params;
        if ($quickfire) $this->sendCall();
    }

    public function sendCall()
    {
        $json = $this->generateJSON();
        if (is_bool($json)) { $this->debug_to_console("ApiCommand Error 0011"); return array("apiCallStatus" => false); }

        $curl = curl_init($this->url);
        curl_setopt( $curl, CURLOPT_POSTFIELDS, $json );
        curl_setopt( $curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        $result = curl_exec($curl);
        $res = json_decode($result, true);
        if (key_exists("error", $res))
        {
            $this->debug_to_console(array("ApiCommand Error 0012", $res["error"]["code"]));
            return array("apiCallStatus" => false);
        }

        $res["apiCallStatus"] = true;
        return $res;
    }

    function generateJSON()
    {
        $arr = array($this->command => $this->params);
        return json_encode($arr, JSON_FORCE_OBJECT);
    }

    function debug_to_console($message) {
        $output = $message;
        if (is_array($output))
            $output = implode(',', $output);
    
        echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
    }
}

class ApiProduct
{
    public $id;
    public $reference;
    public $tecidd;
    public $tecnum;
    public $ILkod;
    public $motonet;
}

class ApiSession
{
    public $sessionID = "";
    private $loginRetry = false;
    private $loginUsername = "";
    private $loginPassword = "";
    private $mysqlAdress = "131.0.1.92";
    private $mysqlUser = "robi";
    private $mysqlDb = "cairo_ws";
    private $mysqlPort = 3306;

    public function __construct() {
        $this->sessionID = $this->getLatestSessionId();
    }

    private function loginRetry($function)
    {
        $loginRetry = true;
        $this->debug_to_console("Attempting to get new sessionID...");
        //$this->doLogin();
    }

    public function getProductsInfo($productList, $showExternalStockInfo, $showAdditionalInfo, $showPhotos, $showSubProductsInfo): array
    {
        if ($this->sessionID == "") return array();
        $params = array("sessionId" => $this->sessionID);

        foreach ($productList as $product) {
            
        }

        return array();
    }

    public function getMyPackages($packageIdList = array()): array
    {
        if ($this->sessionID == "") return array();
        $strPackageIdList = array();
        foreach ($packageIdList as $id) array_push($strPackageIdList, (string)$id);

        $apiCall = new ApiCommand("doCreateReturn", array("sessionId" => $this->sessionID, "packageIdList" => $strPackageIdList));
        $response = $apiCall->sendCall();

        if ($response["apiCallStatus"] == true)
            return $response["doCreateReturnResponse"];

        return array();
    }

    public function doDeleteReturn($returnId, $deleteIfSent = 0): array
    {
        if ($this->sessionID == "") return array();
        $id = (string)$returnId;

        $apiCall = new ApiCommand("doCreateReturn", array("sessionId" => $this->sessionID, "returnId" => $id, "deleteIfSent" => $deleteIfSent));
        $response = $apiCall->sendCall();

        if ($response["apiCallStatus"] == true)
            return $response["doCreateReturnResponse"];

        return array();
    }

    public function doCreateReturn($productId, $quantity, $scrDocumentId = ""): array
    {
        if ($this->sessionID == "") return array();
        $prodId = (string)$productId;
        $quant = (string)$quantity;

        $apiCall = new ApiCommand("doCreateReturn", array("sessionId" => $this->sessionID, "productId" => $prodId, "warehouseId" => "01", "quantity" => $quant));
        $response = $apiCall->sendCall();

        if ($response["apiCallStatus"] == true)
            return $response["doCreateReturnResponse"];

        return array();
    }

    public function getMyReturns($returnIds = array(), $state = -1, $packageNumber = -1): array
    {
        if ($this->sessionID == "") return array();
        $params = array();
        $params["sessionId"] = $this->sessionID;
        if (count($returnIds) > 0) $params["returnIdList"] = array_values($returnIds);
        if (count($state) != -1) $params["filterOptions"]["state"] = $state;
        if (count($packageNumber) != -1) $params["filterOptions"]["packageNumber"] = $packageNumber;

        $apiCall = new ApiCommand("getMyPayments", $params);
        $response = $apiCall->sendCall();

        if ($response["apiCallStatus"] == true)
            return $response["getMyReturnsResponse"];

        return array();
    }

    public function getMyPayments($paid = 0): array
    {
        if ($this->sessionID == "") return array();
        $apiCall = new ApiCommand("getMyPayments", array("sessionId" => $this->sessionID, "paymentStatus" => $paid));
        $response = $apiCall->sendCall();

        if ($response["apiCallStatus"] == true)
            return $response["getMyPaymentsResponse"];

        return array();
    }

    public function getMyCustomerInfo(): array
    {
        if ($this->sessionID == "") return array();
        $apiCall = new ApiCommand("getMyCustomerInfo", array());
        $response = $apiCall->sendCall();

        if ($response["apiCallStatus"] == true)
            return $response["getMyCustomerInfoResponse"];
        
        return array();
    }

    public function doLogin($username, $password): bool
    {
        if (!is_string($username) || $username == "" || $username == null) { $this->debug_to_console("ApiSession Error 0001"); return false; };
        if (!is_string($username) || $password == "" || $password == null) { $this->debug_to_console("ApiSession Error 0002"); return false; };

        $apiCall = new ApiCommand("doLogin", array("userLogin" => $username, "userPassword" => $password));
        $response = $apiCall->sendCall();

        if ($response["apiCallStatus"])
        {
            $this->sessionID = $response["doLoginResponse"]["sessionId"];
            $this->debug_to_console($response["doLoginResponse"]["sessionId"]);

            $conn = mysqli_connect($this->mysqlAdress, $this->mysqlUser, "", $this->mysqlDb, $this->mysqlPort);
            if($conn === false){
                $this->debug_to_console("ApiSession Error 0101");
            } else {
                $sql = "UPDATE sessiondata SET sessionId = ".$this->sessionID." WHERE id=0";
                $conn->query($sql);
            }
        }

        return $response["apiCallStatus"];
    }

    function debug_to_console($message)
    {
        $output = $message;
        if (is_array($output))
            $output = implode(',', $output);
    
        echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
    }

    function getLatestSessionId(): string
    {
        $conn = mysqli_connect($this->mysqlAdress, $this->mysqlUser, "", $this->mysqlDb, $this->mysqlPort);
        if($conn === false){
            $this->debug_to_console("ApiSession Error 0101");
            return "ERROR";
        }

        $sql = "SELECT sessionId FROM sessiondata";
        $result = $conn->query($sql);
        if ($result->num_rows > 0)
            return $result->fetch_assoc()["sessionId"];

        return "ERROR";
    }

    function parseApiProduct(ApiProduct $apiProduct): array
    {
        if ($apiProduct->id != "")
            return array("id" => $apiProduct->id);
        if ($apiProduct->reference != "")
            return array("reference" => $apiProduct->reference);
        if ($apiProduct->tecidd != "")
            return array("tecidd" => $apiProduct->tecidd);
        if ($apiProduct->tecnum != "")
            return array("tecnum" => $apiProduct->tecnum);
        if ($apiProduct->ILkod != "")
            return array("ILkod" => $apiProduct->ILkod);
        if ($apiProduct->motonet != "")
            return array("motonet" => $apiProduct->motonet);
        return array();
    }
}

?>