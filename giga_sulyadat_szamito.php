<pre>
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

class GlidData
{
    public $beszcodes = array();
    public $glid;
    public $beszallitok = array();
    public $sulyok = array();
    public $returnSuly = 0;
    public $warning = false;

    public function __construct($beszallito, $beszcode, $glid) {
        $this->addBeszcode($beszallito,$beszcode);
        $this->glid = $glid;
    }

    public function addBeszcode($beszallito, $beszcode)
    {
        if (key_exists($beszallito, $this->beszcodes))
            return;
        $this->beszcodes[$beszallito] = $beszcode;
    }

    public function addSuly($beszallito, $suly)
    {
        if (key_exists($beszallito, $this->sulyok))
            return;
        if (count($this->sulyok) > 0)
            if ($this->returnSuly + $this->returnSuly * 1 < $suly)
                return;
                //$this->warning = true;
        $this->sulyok[$beszallito] = $suly;
        if ($suly > $this->returnSuly)
            $this->returnSuly = $suly;
    }
}

$glidDatas = array();

$beszallitok = array(
    "autopartner" => array("B:\szerző peti\beszkod\autopartner.csv", "A:\gerison_arlista\INDEKS_PARAMETR.csv"),
    "autopartner_blue" => array("B:\szerző peti\beszkod\auto-partner-blue.csv", "A:\gerison_arlista\autopartner_blue.csv"),
    "suder" => array("B:\szerző peti\beszkod\suder.csv", "A:\gerison_arlista_backup\\2025.03.06\suder.csv"),
    "euro07" => array("B:\szerző peti\beszkod\\euro07.csv", "A:\gerison_arlista\\euro07.csv"),
    "avp" => array("B:\szerző peti\beszkod\\vw.csv", "A:\gerison_arlista\avp_prices.txt"),
    "gmt" => array("B:\szerző peti\beszkod\gmt.csv", "c:\Users\LP-KATALOGUS1\Desktop\gmt_master.csv"),
    "autogroup" => array("B:\szerző peti\beszkod\autogroup.csv", "A:\gerison_arlista\autogroup.txt"),
    "motoprofil" => array("B:\szerző peti\beszkod\motoprofil.csv", "A:\gerison_arlista\motoprofil.csv")
);

$eleresek = array(
    "autopartner_blue" => array("beszcode" => 3, "suly" => 13, "elvalaszto" => ";"),
    "suder" => array("beszcode" => 10, "suly" => 8, "elvalaszto" => "\t"),
    "autopartner" => array("beszcode" => 0, "suly" => 6, "elvalaszto" => ";"),
    "euro07" => array("beszcode" => 0, "suly" => 11, "elvalaszto" => ";"),
    "gmt" => array("beszcode" => 0, "suly" => 10, "elvalaszto" => ";"),
    "avp" => array("beszcode" => 0, "suly" => 4, "elvalaszto" => ";"),
    "autogroup" => array("beszcode" => 0, "suly" => 17, "elvalaszto" => "\t"),
    "motoprofil" => array("beszcode" => 0, "suly" => 5, "elvalaszto" => ";")
);

ob_implicit_flush(true);
foreach ($beszallitok as $beszallito => $fajlok) {
    echo $beszallito."<br/>";
    $beszcodeGlid = array();
    $beszcodeDuplicates = array();
    $handle = fopen($fajlok[0], "r");
    $glidDatasByGlid = array();
    fgetcsv($handle);
    while (($row = fgetcsv($handle, null, ";")) !== FALSE)
    {
        $glidData = new GlidData($beszallito, $row[0], $row[1]);
        if (key_exists($row[1],$glidDatas))
            $glidDatas[$row[1]]->addBeszcode($beszallito, $row[0]);
        else
            $glidDatas[$row[1]] = $glidData;

        if (key_exists($row[0],$beszcodeGlid))
        {
            array_push($beszcodeDuplicates, $row[0]);
            continue;
        }
        $beszcodeGlid[$row[0]] = $row[1];
    }
    fclose($handle);

    $handle = fopen($fajlok[1], "r");
    fgetcsv($handle);
    while (($row = fgetcsv($handle, null, $eleresek[$beszallito]["elvalaszto"])) !== FALSE)
    {
        $beszCode = $row[$eleresek[$beszallito]["beszcode"]];
        if (in_array($beszCode, $beszcodeDuplicates)) continue;
        if (!key_exists($eleresek[$beszallito]["suly"], $row)) continue;
        $sulyAdat = $row[$eleresek[$beszallito]["suly"]];
        if (!key_exists($beszCode, $beszcodeGlid))
            continue;
        $weight = (!empty($sulyAdat)) ? convertToNumber($sulyAdat) : 0;
        if ($weight == 0) continue;
        $weight = $weight * 1000;
        $weight = ($weight < 20) ? 20 : $weight;
        //$glidDatasByBeszcode[$row[$eleresek[$beszallito]["beszcode"]]]->addSuly($beszallito, $weight);
        $glidDatas[$beszcodeGlid[$beszCode]]->addSuly($beszallito, $weight);
    }
    //$uniques = $row[0].";"."ERROR".";".$row[5]."\n";
    fclose($handle);
}
ob_end_flush();

/*$i = 0;
foreach ($glidDatas as $key => $glidData) {
    $i++;
    if (count($glidData->sulyok) < 2)
        continue;
    $str = $key." ; ";
    foreach (array_keys($glidData->sulyok) as $key)
        $str .= $key." -> ".$glidData->sulyok[$key]." (".$glidData->beszcodes[$key].") ; ";

    echo $str."<br/>";
}
echo $i;*/

$output = "glid;weight;warning\n";

foreach ($glidDatas as $glid => $glidData) {
    $warning = false;
    if ($glidData->returnSuly == 0)
        continue;
    $output .= "$glid;$glidData->returnSuly";
    if ($glidData->warning) $output .= ";warning\n"; else $output .= "\n"; 
};

file_put_contents("C:\\Users\\LP-KATALOGUS1\\Desktop\\exports\\giga_sulyadat.csv",$output);

?>