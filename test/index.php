<pre><?php

$manufacturer = 'KOLBENSCHMIDT';
function loadSizeData($manufacturer)
{
    $path = 'Z:\szerző peti\\' . $manufacturer . '.csv';
    $sizeData = [];

    if (!file_exists($path)) {
        return $sizeData;
    }

    if (($file = fopen($path, "r")) !== FALSE) {
        $header = fgetcsv($file, 1000, ",");
        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            if (empty($data[0])) continue;

            $asizeData = explode("\t", $data[0]);

            if (!isset($asizeData[7]) || $asizeData[7] === '' ) {
                continue;
            }

            if (!isset($asizeData[3]) || $asizeData[3] === '' ) {
                continue;
            }
            

            $sizeData[] = [
                "articlecode" => trim($asizeData[2]),
                "size" => trim($asizeData[3]),
                "code" => $asizeData[7]
                
            ];
        }
        fclose($file);
    }


    return $sizeData;
}


$m = loadSizeData($manufacturer);
var_dump($m);

//echo $_SERVER[ "HTTP_HOST" ] . "<br>" . $_SERVER['REQUEST_URI']  ;

/*

$file = "B:\szerző peti\beszkod\gmt.csv";

$conn = mysqli_connect("131.0.1.92", "robi", "", "newszmdb");
if($conn === false){
    die("ERROR: Could not connect. " 
        . mysqli_connect_error());
}

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
    $sql = "SELECT * FROM gyartok WHERE ='$gyarto'";
    $result = $conn->query($sql);
    if ($result->num_rows < 1)
    {
        $str .= $row[0].";".$row[1]."\n";
    }
}

file_put_contents("C:\\Users\\LP-KATALOGUS1\\Desktop\\exports\\gmt_matches_v3.csv",$str);

/*
$manufacturers = [
    "LUK"
];

foreach ($manufacturers as $manufacturer) {


    $path = 'Z:\\szerző peti\\' . $manufacturer . '.csv';

    if (!file_exists($path)) {
        die("Hiba: A fájl nem található - $path\n");
    }
    
    $getData = [];

    if (($file = fopen($path, "r")) !== FALSE) {

        $header = fgetcsv($file, 1000, ",");

        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            $getData[] = $data[0];
        }

        fclose($file);
    }

    $sizeData = [];

    foreach ($getData as $key => $value) {
        $asizeData = explode("\t", $value);

        if (!isset($asizeData[3]) || $asizeData[3] === '') {
            continue;
        }

        $sizeData[] = [
            "articlecode" => $asizeData[2],
            "size" => $asizeData[3]
        ];
    }
}

var_dump($sizeData);


/*
function filterManufacturers($inputFile, $outputFile, $allowedManufacturers) {
    $inputFile = str_replace('\\', '/', $inputFile);
    $outputFile = str_replace('\\', '/', $outputFile);
    
    if (!file_exists($inputFile)) {
        die("Input fájl nem található: " . $inputFile);
    }
    
    $content = file_get_contents($inputFile);
    if ($content === false) {
        die("Nem sikerült beolvasni az input fájlt");
    }

    $lines = explode("\n", $content);
    $filteredLines = [];
    $previousLineWasEmpty = false;

    foreach ($lines as $line) {
        $trimmedLine = trim($line);
        
        if (empty($trimmedLine)) {
            if (!$previousLineWasEmpty) {
                $filteredLines[] = '';
                $previousLineWasEmpty = true;
            }
            continue;
        }
        
        $previousLineWasEmpty = false;
        $parts = explode(';', $line);
        $manufacturer = trim($parts[0]);

        foreach ($allowedManufacturers as $allowed) {
            if (strcasecmp($manufacturer, $allowed) === 0) {
                $filteredLines[] = $line;
                break;
            }
        }
    }

    if (file_put_contents($outputFile, implode("\n", $filteredLines)) === false) {
        die("Nem sikerült írni a kimeneti fájlba: " . $outputFile);
    }
    
    echo "Sikeres feldolgozás.";
}


$allowedManufacturers = ['3M','3RG','4MAX','ABARTH OE','ABC','ABE','ABS','AC Hydraulic','AC ROLCAR','ACDELCO','ACKOJA','ACL','ACPS-ORIS','AE','AE TOPLIGHT','AET','AFTERMARKET','AGROKÉMIA','AIC','AIR FREN','AIRMATIC','AIRTEX','AISIN','AJUSA','AKEBONO','AKONEN','AKRON MALO','AKUSAN','AL','AL-KO','Alabama','Alba Mineral','ALCA','ALCAR','ALCO','Alfa e-Parts','ALKAR','AMA','AMC','AMC Filter','AMITY AP','AMP','AMPRO','AMTRA','AP','APA','APC','API','AQUERER','ARAL','ARCEK','ARCO','ARGO','ARMAFILT','ARMORALL','ARNOTT','ARP','AS','AS-PL','AS-PL ECONOMY','AS-PL PREMIUM','AS-PL STANDARD','ASAM','ASHIKA','ASMET','ASPOCK','ASSO','AST','ASTER','ASTONISH','ATE','ATHENA','ATK AUTOTECHNIK','ATORN','AUGER','AUGUSZT','AUTEL','AUTEX','AUTO FEDERN PASSAU','AUTOFREN','AUTOMAX','AUTOMEGA','AUTOTEILE','AVA','AWD','AXO SCINTEX','AYFAR','AZ','B-CAR','BALDWIN','BALO-Motortex','BANDO','BANNER','BÁRDI','BARUM','BASON','BEHR','BEHR THERMOT-TRONIk','Belcord','BENDIX','BEPCO','BERAL','BERNER','BERU','BERU BY DRIV','BEST GLASS','BETA','BF','BGA','BGS','BH SENS Huf','BIG RED','BILSTEIN','BIMET','BIRTH','BLIC','BLUE LINE','BLUE PRINT','BLUE TECH','BM CATALYST','BMW OE','BOHANG','BORG & BECK','BORG WARNER','BORSEHUNG','BOSAL','BOSCH','BOSCH AERO ECO','BOSCH AEROTWIN','BOSCH AEROTWIN MULTICLIP','BOSCH AEROTWIN PLUS','BOSCH ECO','BOSCH S3','BOSCH S4','BOSCH S5','BOSCH TWIN','BOSCH TWIN SPOILER','BOSMA','BOTTARI','BPW','BRADAS','Brainbee','BRASNER','BREDA LORETT','BREMBO','BREMI','BRIDGESTONE','BRIGECIOL','Brilliant Tools','BRISK','BTA','BTS BL','BTS turbo','BU AUTOMOTIVE','BUDWEG','BUGIAD','BURKERT','BUSCHING','BV PSH','CAFFARO','CALIFORNIA SCENTS','CALORSTAT by Vernet','CAMMYO','CAMPRO','CARELLO','CARFACE','CARGO','CARGOPARTS','CARMOTION','CARPRISS','CARTECHNIC','CARTREND','CASCO','CASSENA','CASTROL','CATERPILLAR','CAUTEX','CEI','CHAMPION','CHEVROLET OE','CHINY','CIFAM','CLEAN FILTER','CleansBerg','CLEVITE','COFLE','COJALI','COMETIC','COMLINE','CONTINENTAL','CONTITECH','CONTITECH AIR SPRING','COOPERS FIAAM','CORAM','CORTECO','CORVEN','COSIBO','COSPEL','COVEIN','COVIND','COYOTE','CP CARILLO','CRAFT','CS GERMANY','CSEPELIÖ','CSY','CTEK','CTR','CUMMINS','CX','CZM','DACIA OE','DACO','DAEWOO OE','DAF OE','DAIHATSU OE','DAYCO','DELCO REMY','DELPHI','DENCKERMANN','DENSO','DEPO','DETA POWER','DETA SENATOR','DEUTZ OE','DEXWAL','DEZENT','DID','DIESEL TECHNIC','DINEX','DINITROL','Direct Fit','DJ AUTO','DLO','DOKUJI','DOLZ','DOMAR','DONALDSON','DORMAN','DOTZ','DPA','DPF','Dr.Motor Automotive','DRI','DRIFTERS','DRIVE+','DRM','DTP','Dunlop Airsuspension','DUPLI COLOR','DURA-BOND','DURACELL','DYS','EAGLE OE','EAI-France','EATON','EBER','EBERSPRECHER','ECS','EDERPARTS','EDEX','EIBACH','EICHNER','EIDEMBACH','ELECTRIC LIFE','ELECTRIC POWER','ELF','ELIT','ELRING','ELSTAR','ELSTOCK','ELWIS ROYAL','EMMERRE','EMOS','ENEOS','ENERGY','ENGINE PRO','ENGITECH','EPS','ERA Benelux','ERA OE','ERENDA','ERICH JAEGER','ERMAX','ERNST','ERREVI','ESEN SKV','ESTIMA','ET ENGINETEAM','EU','EUROCAMS','EUROCLUTCH','EURORICAMBI','EXEDY','EXIDE','EXIDE AGM','EXIDE CLASSIC','EXIDE EXCELL','EXIDE HD','EXIDE PREMIUM','EXIDE START-STOP','F-DIESEL','FA1','FACET','FAE','FAG','FAI','FALKEN','FAST','FCA OE','FEB','FEBEST','FEBI','FEL-PRO','FELPRO','FER','FERODO DS PERFORMANCE','FERODO PREMIER','FERODO RACING','FERODO SL','FERODO TQ','FEROZ','FERREA','FERROZ','FGM','FIAT OE','FILFILTER','FILTRON','FIRAD','FIRESTONE','FIRST LINE','FISCHER','FLEETGUARD','FLENNOR','FOLSER','FORCE','FORD OE','FORMPART','FORTUNE LINE','FPDIESEL','FRAM','FRAP','FRECCIA','FRENKIT','FRIGAIR','FRISTOM','FROGUM','FTE','GARRETT','Gate','GATES','GEBE','GEDORE','General Ricambi','GENMOT','GF','GFT','GITI','GK','GKN','GLASER','GLYCO','GLYSANTIN','GM OE','GMB','GOETZE','GOETZE ENGINE','GOLD LINE','GOMET','GOODYEAR','GORILLA GLUE','GRAF','GRAPHITE','GSP','GT RADIAL','GT-BERGMANN','GUARNITAUTO','Gumárny Zubrí','GÜNES','GYS','HACO','HAGEN','HALDEX','HANKOOK','HANLIN','HART','HASTINGS','HATTAT','HATZ OE','HAYNESPRO','HAZET','HC-CARGO','HEFAL','HEGYALJA','HEKO','HELLA','HELLA-PAGID','HENGST','HEPU','HERTH+BUSS ELPARTS','HERTH+BUSS JAKOPARTS','HEYNER ','HIDRIA','HIFI','HIFLO','HITACHI','HJS','HOBI','HOFFER','HOFMANN','HOLSET','HONDA OE','HONEYWELL','HORPOL','HORSE-POWER','HORTUM','HP','HP Tool','HÜCO','HÜNERSDORFF','HUTCHINSON','HYUNDAI OE','HYVA','IBERMAN','IHAROS','IJS','IMASAF','INA','Industria Argentina','INFINITY','INTERMOTOR','INTERVALVES','IPD','IPPON','IPSA','IR','ISAM','ISUZU OE','ITALMATIC','IVECO OE','iwis Motorsysteme','IZAWIT','JAGUAR OE','JANMOR','JAPANPARTS','JAPKO','JÁSZ-AKKU','JBL','JBM','JESTIC','JMJ','JOHN DEERE OE','JOHNS','JONNESWAY','JOST','JP','JP GROUP','JPN','JRONE','JURATEK','JURID','K&K','K&N Filters','K+F','K2','KAHVECI','KALE OTO RADIATÖR','KAMOKA','KAVO PARTS','KAWE','KEBRON','KELLE','KIA OE','KILEN','KING','Kingstar','KKK','KLANN','KLEBER','KLOKKERHOLM','KMP','KNECHT','KNIPEX','KNORR','KNOTT','KO CHOU','KOGEL','KOLBENSCHMIDT','KOMATSU','KONGSBERG','KONI','KOREA','KOSFA','KOYO','KP JAPAN','KRAJ','KRONE','KSTOOLS','KUBOTA OE','KUMHO','KUNZER','KURTSAN','KW','KYB','KYB EXCEL-G','KYB GAS-A-JUST','KYB KLASSIC','KYB PREMIUM','KYB ULTRA SR','LADA OE','LAMIRO','LAMPA','LANDPORT','LANDROVER OE','Láng Kft','LARTE Design GmbH','LASER TOOLS','LASO','LAUNCH','LCC PRODUCTS','LEITENBERGER','LEMA','LEMFÖRDER','LENCO','LESJOFORS','LEXA','LIEBHERR OE','LIFT-TEK','LINDE','LINEX','LIPE CLUTCH','LIQUI MOLY','LIZARTE','LKQ','LLE','LÖBRO','LOCTITE','LOMBARDINI','LORO','LPR','LRT','LUCAS','LUK','LUMAG','Maestro','MAGNETI MARELLI','MAGNUM Technology','MAHLE','MALO','MAN OE','MANDO','MANN-FILTER','MANNOL','MAPCO','Marelli eQual','MARIX','MARK-MOTO','MARS TECH','MASERATI OE','MASTER SPORT','MAXGEAR','MAXIFORCE','MAZDA OE','MB Kft.','MC Gasket','MEAT&DORIA','MEC-DIES','MECAFILTER','MECARM','MEGA DRIVE','MEKRA','MELENIR','MELETT','MELLING','MERCATOR','MERCEDES OE','MERITOR','MERLO','METABO','METALCAUCHO','METELLI','METZGER','MEYLE','MIBA','MICHELIN','MICUGA','MILWAUKEE','MINTEX','Miraglio','MISFAT','MITSUBISHI AM','MITSUBISHI OE','MITSUBOSHI','MOBIL','MOBILETRON','MOJE AUTO','MOLYSLIDE','MONROE','MONROE ADVENTURE','MONROE INTELLIGENT S.','MONROE OESpectrum','MONROE ORIGINAL','MONROE REFLEX','MONROE Roadmatic','MONROE SENSA-TRAC','MONROE VAN-MAGNUM','MOOG','MOPAR','MORFU','MOTIP','MOTIVE','MOTOFIT','MOTORAD','MOTUL','MPM','MTA','MTECH','MTR','MTS','MTX','MVI','MVPARTS','MY SHALDAN','MZ','NAKAMOTO','NANKANG','NARVA','NATIONAL','NEO','NEOLUX','NEOTEC','NEXEN','NGK','NIPPARTS','Nippon','NISSAN AM','NISSAN OE','NISSENS','NK','NN','No1 Batteries','NORD GLASS','NORMA','NOVOPOLM','NPR','NPREUROPE','NRF','NSK','NTY','NURAL','NWG','OCAP','ODYSSEY','OE','OE GERMANY','OEM/OES','OGNIOCHRON','OMCAR','OMP','OPEL AM','OPEL OE','OPTIBELT','OPTIMA BLUE TOP','OPTIMAL','OREX','ORIGINAL','ORIGINAL IMPERIUM','ORLEN','OSRAM','OSSCA','OSVAT','OTP OTOMOTIVE','OTSA','OXIMO','OYODO','PAL DUGATTYU','PARADOWSKI','PARKER','PASCAL','PAYEN','PERKINS OE','PERMATEX','PETEC','PETERS','PETEX','PEUGEOT OE','PHILIPS','PHOENIX','PIERBURG','PILKINGTON','PIRELLI','PITTATOR','PLASTIKOREN','PLATINUM PLUS','PNEUMATICS','POLCAR','POLCAR O','POLCAR P','POLCAR PC','POLCAR PJ','POLCAR Q','POLCAR Z','POLCAR ZJ','POLMO','POLMOSTROW','PORSCHE OE','POVERPLAST','POWERMAX','POWERSEAL','PRASCO','PRELIX','PREMIERE','PRESSOL','PRESTO','PREVENT','PRIMA','PRO-T','PRO-TEC','PROMO','Proparts Diesel','PROTECHNIC','PROVIA','PS','PSA OE','PURFLUX','PWT','Q8','QUARO','QUICK BRAKE','QUICKSTEER','QUINTON HAZELL','RABA OE','RACOR','RAFAELA MOTORES','RAINY DAY','RAPID','RAPRO','RAUFOSS','RAVENOL','REDAELLI','REINZ','RELIANCE','REMA TIPTOP','REMANTE','REMSA','RENAULT OE','REPSOL','RETOV','REVOLUTION','REYCON','REZAW','RIDERO','RIDEX','RIGUM','RIK','RINGFEDER','ROADHOUSE','ROCKET','ROCKINGER','ROLL','ROLLING','ROMIX','Romlag','ROTINGER','ROULUNDS RUBBER','ROVER OE','RTS','RUKO','RUVILLE','RYMEC','S.s.p','SAAB OE','SABO','SACHS','SACHS (ZF SRE)','SAF OE','SAHIN','SAILUN','SALERI','SAMKO','SAMPA','SAMPIYON','SANICO','SANZ','SASIC','SATA','SAUERMANN','SAULIN','SBAUTOPARTS','SBI','SBP','SBS','SCANGRIP','SCANIA OE','SCHAFFER','SCHLIECKMANN','SCHLÜTTER TURBOLADER','SCHMITZ CARGOBULL OE','SCHÖNEK','SCHÖTTLE','SCHRADER','SCHWITZER','SCT Germany','SEALED POWER','SEAT OE','SEINSA','SEM LASTIK','SENSOR','SERTPLAS','SF','SH','SHAFTEC','SHELL','SHW Performance','SIBERIA','SICIT','SIDAT','SIDEM','SIGAM','SIKA','SILCO','SILVOLITE','SISA','SKF','SKODA','SKODA AM','SMART OE','SMP','SNR','SOFIMA','SOGEFIPRO','SOLIGHT','SONAX','SORL','SPARCO','SPIDAN','SPMP','SRL','SSANGYONG OE','STABILUS','STANDARD','STANLEY','STAR DIESEL','STARLINE','STC','STEYR OE','STOP&GO','STP','STR','SUBARU OE','SÜDPFALZ','SULBUS','SUPERDIESEL','SUPERFLEX','SUPERTECH','SUPLEX','SUZUKI OE','SW-STAHL','SWAG','SWF','SWF REFILLS','SWF STANDARD','SWF TRUCK','SWF VISIONEXT','SWF VISIONEXT ALTERNATIVE','SWF VISIONEXT OE','SWISSTEN','SZATUNA','Sziklai','SZILORETT','SZMETAL','T.Y.C.','TAB MAGIC','TAB POLAR TRUCK','TAKLER','TAKOMA','TANGDE','TARABUSI','TCMATIC','TDC','TECHSOL','TEDGUM','TEIKIN','TEKNOROT','TEMPLIN','TEROSON','TESAM','TESLA','TEVEMA','TEXA','TEXTAR','THERMIX','THERMOTEC','THULE','TIMKEN','TMI','TOKEI','TOMEX','TOP QUALITY','TOP TOOLS','TOPEX','TOPRAN','TOTAL','TOTAL SOURCE','TOYA','TOYO','TOYOTA AM','TOYOTA OE','TQ','TRAKMOTIVE','TRICO','TRIFA','TRISCAN','TRUCK EXPERT','TRUCKLIGHT','TRUCKTEC','TRW','TRW engine component','TSTURBO','TUNGSRAM','TURBO SYSTEM','TURBORAIL','TURCJA','TVH','TVH FORKLIFT','TWINTEC','TYC','TZERLI','UE','UFI','UJGUR','ULO','UNIBRAKE','UNIFLUX','UNIPOINT','UNITED ENGINE','UNIVERSAL COMPONENTS','UYGUR','VADEN','VAICO','VALEO','VALEO CANOPY','VALEO CLASSIC','VALEO COMPACT CARDBOARD','VALEO COMPACT EVO','VALEO COMPACT REVOLUTION','VALEO FIRST','VALEO FIRST HYBRID','VALEO FIRST MULTI.','VALEO HYDROCONNECT','VALEO HYDROCONNECT UPGRADE','VALEO MULTIC.','VALEO REMAN','VALEO SILENCIO CARDBOARD','VALEO SILENCIO HYBRID BLADE','VALEO SILENCIO PERFORMANCE','VALEO SILENCIO REFILLS','VALEO SILENCIO X.TRM','VALEO STAND. EXCHANGE','VALEO TIR','VAN WEZEL','VANCOOLER','VANDERVE','VANSTAR','VAPORMATIC','VARIOUS SUPPLIERS','VARROC','VARTA BLACK','VARTA BLUE','VARTA HIGH ENERGY','VARTA LONGLIFE','VARTA MOTOR','VARTA PROFESSIONAL','VARTA PROMOTIVE','VARTA SILVER','VARTA START-STOP','VDO','VEMA','VEMO','VENEPORTE','VERBIS','Vernet','VERTO','VICMA','VIEW MAX','VIGNAL','VIGOR','VIKA','VILEDA','VIP','VISTEON','VM','VOLT','VOLVO OE','VOSS','VW OE','WABCO','WAECO','WAGNER','WAHLER','WAI','WALKER','WAS','WD40','WEBASTO','WESTFALIA','WEWELER','WILMINK GROUP','WIX FILTERS','WOLF','WOSIMAN','WOSM','WP Tool','WT','WUNDERBAUM','WÜRTEMBERG','WÜRTH','XACTA','YAMATO','YANMAR','YATO','YENMAK','YOKOHAMA','YUASA','YUMAK','ZAFFO','ZERO Pollution','ZETOR','ZF parts','ZIMKOR','ZIMMERMANN','ZKW']; 
filterManufacturers('c:\Users\LP-GERGO\Desktop\cloude api\brands_descriptions.txt', 'c:\Users\LP-GERGO\Desktop\cloude api\brands_descriptions_cutted.txt', $allowedManufacturers);











*/
/*
$path = 'Z:\szerző peti\KYB.csv';

$getData = [];

if (($file = fopen($path, "r")) !== FALSE) {

    $header = fgetcsv($file, 1000, ",");

    while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
        $getData[] = $data[0];
    }

    fclose($file);
}

$sizeData = [];

foreach ($getData as $key => $value) {
    $asizeData = explode("\t", $value);

    if (!isset($asizeData[3]) || $asizeData[3] === '') {
        continue;
    }

    $sizeData[] = [
        "articlecode" => $asizeData[2],
        "size" => $asizeData[3],
        "glid" => $asizeData[0]
    ];
}


var_dump($sizeData);

die();
set_time_limit(0);

$inputPath = 'z:\FGergo\fromBigdb.csv';
$outputDir = 'z:\FGergo\chunks\\';
$chunkSize = 49999;

if (!file_exists($outputDir)) {
    mkdir($outputDir, 0777, true);
}

try {
    if (($inputFile = fopen($inputPath, "r")) === FALSE) {
        throw new Exception("Nem sikerült megnyitni a bemeneti fájlt: $inputPath");
    }

    $chunkNumber = 1;
    $rowCount = 0;
    $currentChunk = [];
    
    
    $header = fgetcsv($inputFile, 0, ";");
    
    
    while (($row = fgetcsv($inputFile, 0, ";")) !== FALSE) {
        $rowCount++;
        $currentChunk[] = $row;
        
       
        if ($rowCount % $chunkSize === 0) {
            writeChunkToFile($currentChunk, $outputDir, $chunkNumber);
            $currentChunk = [];
            $chunkNumber++;
        }
    }
    
    
    if (!empty($currentChunk)) {
        writeChunkToFile($currentChunk, $outputDir, $chunkNumber);
    }
    
    fclose($inputFile);
    echo "Feldolgozás kész! Létrehozott chunk-ok száma: " . $chunkNumber;
    
} catch (Exception $e) {
    echo "Hiba történt: " . $e->getMessage();
}


function writeChunkToFile($chunk, $outputDir, $chunkNumber) {
    $outputFile = $outputDir . 'chunk_' . $chunkNumber . '.csv';
    
    if (($outHandle = fopen($outputFile, 'w')) === FALSE) {
        throw new Exception("Nem sikerült létrehozni a kimeneti fájlt: $outputFile");
    }
    
    
    fwrite($outHandle, "\xEF\xBB\xBF");
    
    
    foreach ($chunk as $row) {
        fputcsv($outHandle, $row, ";");
    }
    
    fclose($outHandle);
    echo "Chunk $chunkNumber létrehozva: $outputFile\n";
}
?>
*/