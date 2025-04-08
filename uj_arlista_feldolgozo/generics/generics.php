<?php

class SzakalGyarto
{
    public $nev;
    public $name_short;
    public $gy_kod;
    public $tecdoc_id;
    public $utotag;
    public $matchedStrings = array();

    function __construct($nev, $name_short, $gy_kod, $tecdoc_id, $utotag, $matchedStrings) {
        $this->nev = $nev;
        $this->$name_short = $name_short;
        $this->gy_kod = $gy_kod;
        $this->tecdoc_id = $tecdoc_id;
        $this->utotag = $utotag;
        if ($matchedStrings != null)
            $this->matchedStrings = array_values(json_decode($matchedStrings, true));
    }
}

class SqlAdat
{
    public $glid;
    public $gyarto;
    public $articlecode;
    public $size;
    public $tcs_szoveg;
    public $gy_kod;
    public $egyseg;
    public $code;
    public function __construct($glid, $gyarto, $articlecode, $size, $tcs_szoveg, $gy_kod, $egyseg, $code) {
        $this->glid = $glid;
        $this->gyarto = $gyarto;
        $this->articlecode = $articlecode;
        $this->size = $size;
        $this->tcs_szoveg = $tcs_szoveg;
        $this->gy_kod = $gy_kod;
        $this->egyseg = $egyseg;
        $this->code = $code;
    }
}

class Cikk
{
    public $code;
    public $articlecode;
    public $price;
    public $weight;
    public $ean;
    public $gyarto;
    public $sqlAdat = null;
    public $utotag = "";
    public $moq;
    public $oeCodes = array();
    public function __construct($code, $price, $weight, $ean, $gyarto, $utotag, $moq = 1, $oeCodes = array(), $articlecode = null) {
        $this->code = $code;
        $this->roundPrice($price);
        $this->convertWeightToGramm($weight);
        $this->ean = $ean;
        $this->gyarto = $gyarto;
        $this->utotag = $utotag;
        $this->moq = $moq;
        $this->oeCodes = $oeCodes;
        $this->generateArticleCode($articlecode);
    }

    function roundPrice($price)
    {
        if ($price == "")
        {
            $this->price = 0;
            return;
        }
        $this->price = round($price, 2);
    }

    public function generateSqlData($sqlConnection)
    {
        $sql = "SELECT * FROM products_v2 WHERE articlecode='$this->articlecode'";
        $result = $sqlConnection->query($sql);
        if ($result->num_rows < 1)
            return;
        $row = $result->fetch_assoc();
        $this->sqlAdat = new SqlAdat($row["glid"], $row["gyarto"], $row["articlecode"], $row["size"], $row["tcs_szoveg"], $row["gy_kod"], $row["egyseg"], $row["code"]);
    }

    function generateArticleCode($articlecode)
    {
        $c = ($articlecode == null) ? $this->code : $articlecode;
        if ($this->utotag == "")
        {
            $this->articlecode = $c;
            return;
        }
        $code = str_replace(" ","",$c);
        $ac = $code."_".$this->utotag;
        $this->articlecode = $ac;
    }

    function convertWeightToGramm($weight)
    {
        if ($weight == "")
            return;
        if ($weight == "0")
        {
            $this->weight = 0;
            return;
        }
        $w = $this->convertToNumber($weight);
        $we = $w * 1000;
        $this->weight = ($we<20) ? 20 : $we;
    }

    function convertToNumber($value)
    {
        if (is_numeric($value)) {
            return (float)$value;
        }


        $value = str_replace(',', '.', $value);


        $value = preg_replace('/[^0-9.]/', '', $value);

        return is_numeric($value) ? (float)$value : 0;
    }
}

class ReinzCikk extends Cikk
{
    public function __construct($articlecode, $price, $weight, $ean) {
        $this->articlecode = $articlecode;
        $this->roundPrice($price);
        $this->weight = $weight;
        $this->ean = $ean;
        $this->generateCode();
        $this->convertWeightToGramm($weight);
        $this->gyarto = "REINZ";
    }

    function generateCode()
    {
        $this->code = "000000000".str_replace("-","",$this->articlecode);
    }
}

class ÁrlistaKonverter
{
    public $data = array();
    public $file;
    public $dataStruct;
    public $conn;
    public $fileName;
    public $gyarto;
    public $fixedCode;
    public $szakalManufacturers = array();
    public $startRow;
    public $secondDataStruct = array();
    public $secondSheetName = "";
    public $secondStartRow;
    public $validDatas = array("code","articlecode","size");

    public function __construct($file,$dataStruct,$conn,$gyarto,$fileName,$startRow = 1,$secondDataStruct = array(),$secondSheetName = "",$secondStartRow = 1) {
        $this->file = $file;
        $this->dataStruct = $dataStruct;
        $this->conn = $conn;
        $this->gyarto = $gyarto;
        $this->fileName = $fileName;
        $this->startRow = $startRow;

        if ($secondSheetName != "")
        {
            $this->secondSheetName = $secondSheetName;
            $this->secondDataStruct = $secondDataStruct;
            $this->secondStartRow = $secondStartRow;
        }

        $this->gyartoGyujtes();
        $this->utotagKereses();
        $this->cikkGen();
        $this->createFile();
    }

    function cikkGen()
    {
        $excel = \avadim\FastExcelReader\Excel::open($this->file);
        $sheet = $excel->sheet();

        var_dump($this->dataStruct)."<br/>";
        foreach ($sheet->nextRow([$this->dataStruct["code"] => 'code', $this->dataStruct["price"] => 'price', $this->dataStruct["weight"] => 'weight', $this->dataStruct["barcode"] => 'barcode', $this->dataStruct["moq"] => 'moq', $this->dataStruct["oeCodes"] => 'oeCodes', $this->dataStruct["articlecode"] => 'articlecode'], \avadim\FastExcelReader\Excel::KEYS_FIRST_ROW) as $rowNum => $rowData) {
            if ($rowNum < $this->startRow) continue;
            // $rowData is array ['One' => ..., 'Two' => ...]
            // ...
            
            foreach ($this->dataStruct as $d => $v) {
                if (key_exists($d,$rowData) && !in_array($d,$this->validDatas) && $d != "oeCodes")
                    array_push($this->validDatas,$d);
            }

            $this->createCikk($rowData);
        }

        //Második lap beolvasása, ha van
        if ($this->secondSheetName == "")
            return;
        $excel = \avadim\FastExcelReader\Excel::open($this->file);
        $sheet = $excel->sheet($this->secondSheetName);

        foreach ($sheet->nextRow([$this->secondDataStruct["code"] => 'code', $this->secondDataStruct["price"] => 'price', $this->secondDataStruct["weight"] => 'weight', $this->secondDataStruct["barcode"] => 'ean', $this->secondDataStruct["moq"] => 'moq', $this->dataStruct["oeCodes"] => 'oeCodes'], \avadim\FastExcelReader\Excel::KEYS_FIRST_ROW) as $rowNum => $rowData) {
            if ($rowNum < $this->secondStartRow) continue;
            // $rowData is array ['One' => ..., 'Two' => ...]
            // ...

            foreach ($this->secondDataStruct as $d) {
                if (key_exists($d,$rowData) && !in_array($d,$this->validDatas))
                    array_push($d,$this->validDatas);
            }

            $this->cikkFrissites($rowData);
        }
    }

    function cikkFrissites($rowData)
    {
        if (!key_exists($rowData["code"],$this->data))
            return;
        $cikk = $this->data[$rowData["code"]];
        if (key_exists("price",$rowData)) $cikk->price = $rowData["price"];
        if (key_exists("weight",$rowData)) $cikk->convertWeightToGramm($rowData["weight"]);
        if (key_exists("moq",$rowData)) $cikk->moq = $rowData["moq"];
        $this->data[$rowData["code"]] = $cikk;
    }

    function utotagKereses(): void
    {
        foreach ($this->szakalManufacturers as $manuf)
        {
            if (in_array($this->gyarto, $manuf->matchedStrings))
            {
                $this->fixedCode = $manuf->utotag;
                break;
            }
        }
    }

    function createCikk($rowData)
    {
        $cikk = new Cikk(
            (key_exists("code",array: $rowData)) ? $rowData["code"] : "",
            (key_exists("price",$rowData)) ? $rowData["price"] : "",
            (key_exists("weight",$rowData)) ? $rowData["weight"] : "",
            (key_exists("barcode",$rowData)) ? $rowData["barcode"] : "",
            $this->gyarto,
            $this->fixedCode,
            (key_exists("moq",$rowData)) ? $rowData["moq"] : 1,
            (key_exists("oeCodes",$rowData)) ? $rowData["oeCodes"] : 1,
            (key_exists("articlecode",$rowData)) ? $rowData["articlecode"] : 1
        );
        $cikk->generateSqlData($this->conn);
        //array_push($this->data, $cikk);
        $this->data[$rowData["code"]] = $cikk;
    }

    function createFile()
    {
        $excel = \avadim\FastExcelWriter\Excel::create(['Sheet1']);
        $sheet = $excel->sheet();

        $sheet->writeRow(array_values($this->validDatas));

        foreach ($this->data as $code => $cikk) {
            $send = array();

            array_push($send,$cikk->code);
            array_push($send,$cikk->articlecode);
            array_push($send,($cikk->sqlAdat != null) ? $cikk->sqlAdat->size : "");
            if (in_array("price",$this->validDatas)) array_push($send,$cikk->price);
            if (in_array("weight",$this->validDatas)) array_push($send,$cikk->weight);
            if (in_array("barcode",$this->validDatas)) array_push($send,$cikk->ean);
            if (in_array("gyarto",$this->validDatas)) array_push($send,$cikk->gyarto);
            if (in_array("moq",$this->validDatas)) array_push($send,$cikk->moq);

            $sheet->writeRow($send);
        }

        $currentDate = date('Y.m.d');
        $newFileName = 'C:\Users\LP-KATALOGUS1\Desktop\exports\\'.$this->fileName.' ' . $currentDate . '.xlsx';
        $excel->save($newFileName);
    }

    function gyartoGyujtes(): void
    {
        $sql = "SELECT * FROM gyartok";
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
        // output data of each row
            while($row = $result->fetch_assoc()) {
                array_push($this->szakalManufacturers, new SzakalGyarto($row["nev"], $row["name_short"], $row["gy_kod"], $row["tecdoy_id"], $row["utotag"], $row["matched_strings"]));
            }
        }
    }
}

class ReinzÁrlistaKonverter extends ÁrlistaKonverter
{
    function createCikk($rowData, $fixedCode = "")
    {
        $cikk = new ReinzCikk(
            (key_exists("code",$rowData)) ? $rowData["code"] : "",
            (key_exists("price",$rowData)) ? $rowData["price"] : "",
            (key_exists("weight",$rowData)) ? $rowData["weight"] : "",
            (key_exists("barcode",$rowData)) ? $rowData["barcode"] : "",
        );
        $cikk->generateSqlData($this->conn);
        array_push($this->data, $cikk);
    }
}

class GyartosÁrlistaKonverter extends ÁrlistaKonverter
{
    public function __construct($file,$dataStruct,$conn,$fileName) {
        $this->file = $file;
        $this->dataStruct = $dataStruct;
        $this->conn = $conn;
        $this->fileName = $fileName;

        $this->gyartoGyujtes();
        $this->cikkGen();
        $this->createFile();
    }

    function cikkGen()
    {
        $excel = \avadim\FastExcelReader\Excel::open($this->file);
        $sheet = $excel->sheet();

        foreach ($sheet->nextRow([$this->dataStruct["code"] => 'code', $this->dataStruct["price"] => 'price', $this->dataStruct["weight"] => 'weight', $this->dataStruct["barcode"] => 'ean', $this->dataStruct["gyarto"] => 'gyarto'], \avadim\FastExcelReader\Excel::KEYS_FIRST_ROW) as $rowNum => $rowData) {
            // $rowData is array ['One' => ..., 'Two' => ...]
            // ...
            foreach ($this->dataStruct as $d) {
                if (key_exists($d,$rowData))
                    array_push($d,$this->validDatas);
            }
            $this->createCikk($rowData);
        }
    }

    function createCikk($rowData)
    {
        $dynamicCode = $this->findDynamicCode($rowData["gyarto"]);
        $cikk = new Cikk(
            (key_exists("code",$rowData)) ? $rowData["code"] : "",
            (key_exists("price",$rowData)) ? $rowData["price"] : "",
            (key_exists("weight",$rowData)) ? $rowData["weight"] : "",
            (key_exists("barcode",$rowData)) ? $rowData["barcode"] : "",
            (key_exists("gyarto",$rowData)) ? $rowData["gyarto"] : "",
            $dynamicCode,
            (key_exists("moq",$rowData)) ? $rowData["moq"] : 1,
        );
        $cikk->generateSqlData($this->conn);
        array_push($this->data, $cikk);
    }

    function findDynamicCode($gyarto)
    {
        foreach ($this->szakalManufacturers as $manuf)
        {
            if (in_array($gyarto, $manuf->matchedStrings))
            {
                return $manuf->utotag;
            }
        }
    }
}

class NrfÁrlistaKonverter extends ÁrlistaKonverter
{
    function oeKodKifejtes($cell)
    {
        $return = array();
        $oeCodes = explode('/',$cell);
        foreach ($oeCodes as $oeCode)
        {
            if (count($return) >= 5) break;
            $sql = "SELECT `code` FROM products_v2 WHERE `code`='$oeCode'";
            $result = $this->conn->query($sql);
            if ($result->num_rows < 1)
                return;
            array_push($return,$oeCode);
        }

        return $return;
    }

    function createCikk($rowData)
    {
        $cikk = new Cikk(
            (key_exists("code",array: $rowData)) ? $rowData["code"] : "",
            (key_exists("price",$rowData)) ? $rowData["price"] : "",
            (key_exists("barcode",$rowData)) ? $rowData["barcode"] : "",
            (key_exists("weight",$rowData)) ? $rowData["weight"] : "",
            $this->gyarto,
            $this->fixedCode,
            (key_exists("moq",$rowData)) ? $rowData["moq"] : 1,
            $this->oeKodKifejtes($rowData["oeCodes"])
        );
        $cikk->generateSqlData($this->conn);
        //array_push($this->data, $cikk);
        $this->data[$rowData["code"]] = $cikk;
    }

    function createFile()
    {
        $excel = \avadim\FastExcelWriter\Excel::create(['Sheet1']);
        $sheet = $excel->sheet();

        $sheet->writeRow(array_values(array_merge($this->validDatas,array("oe1","oe2","oe3","oe4","oe5"))));

        foreach ($this->data as $code => $cikk) {
            $send = array();

            array_push($send,$cikk->code);
            array_push($send,$cikk->articlecode);
            array_push($send,($cikk->sqlAdat != null) ? $cikk->sqlAdat->size : "");
            if (in_array("price",$this->validDatas)) array_push($send,$cikk->price);
            if (in_array("weight",$this->validDatas)) array_push($send,$cikk->weight);
            if (in_array("barcode",$this->validDatas)) array_push($send,$cikk->ean);
            if (in_array("gyarto",$this->validDatas)) array_push($send,$cikk->gyarto);
            if (in_array("moq",$this->validDatas)) array_push($send,$cikk->moq);
            //Forgive me, for I have sinned
            if (count($cikk->oeCodes)>0) array_push($send,$cikk->oeCodes[0]);
            if (count($cikk->oeCodes)>1) array_push($send,$cikk->oeCodes[1]);
            if (count($cikk->oeCodes)>2) array_push($send,$cikk->oeCodes[2]);
            if (count($cikk->oeCodes)>3) array_push($send,$cikk->oeCodes[3]);
            if (count($cikk->oeCodes)>4) array_push($send,$cikk->oeCodes[4]);

            $sheet->writeRow($send);
        }

        $currentDate = date('Y.m.d');
        $newFileName = 'C:\Users\LP-KATALOGUS1\Desktop\exports\\'.$this->fileName.' ' . $currentDate . '.xlsx';
        $excel->save($newFileName);
    }
}

?>