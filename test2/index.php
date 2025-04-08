<?php

$outputPath = "Z:\Farkas Gergo\jm\\newJMkaucio_" . date("Y.m.d") . ".csv";

$finalFile = file_get_contents($outputPath);

$rows = explode("\n", $finalFile);

$counter = 0;

foreach ($rows as $key => $row) {

    if ($key == 0) {
        continue;
    }

    $row = explode(";", $row);

    var_dump($row);

    if ($counter > 10) {
        break;
    }
    $counter++;
}



/*
$alkatreszek = [
    "Levegőszűrő" => [50, 50000],
    "Lengőkar" => [1000, 500000],
    "Első fékbetét" => [100, 100000],
    "Első féktárcsa" => [1000, 500000],
    "Pollenszűrő" => [50, 50000],
    "Féknyereg" => [100, 100000],
    "Stabilizátor kar" => [50, 50000],
    "Kormányösszekötő gömbfej" => [50, 50000],
    "Hosszbordásszíj" => [50, 50000],
    "Motortartó bak" => [1000, 500000],
    "Hátsó féktárcsa" => [1000, 500000],
    "Kerékcsapágy készlet" => [1000, 500000],
    "Vízhűtő" => [1000, 500000],
    "Gumifékcső" => [50, 50000],
    "Kézifék bowden" => [100, 100000],
    "ABS jeladó" => [1000, 500000],
    "Hátsó fékbetét" => [100, 100000],
    "Hengerfej töm." => [100, 100000],
    "Első Lengéscsillapító" => [1000, 500000],
    "Stabilizátor szilent" => [50, 50000],
    "Üzemanyagszűrő" => [100, 100000],
    "Gázteleszkóp, csomagtér fedél" => [100, 100000],
    "Vízcső" => [100, 100000],
    "Fényszóró" => [4000, 2000000],
    "Szilentblokk" => [50, 50000],
    "Féknyereg javítókészlet" => [50, 50000],
    "Vízpumpa" => [1000, 500000],
    "Kormányösszekötő belső" => [100, 100000],
    "Szelepfedél tömítés" => [100, 100000],
    "Hátsó Lengéscsillapító" => [1000, 500000],
    "Lengőkar gömbfej" => [50, 50000],
    "Ablaktörlő lapát" => [50, 50000],
    "Hátsó lámpa" => [1000, 500000],
    "Töltőlevegő hűtő cső" => [1000, 500000],
    "Klímahűtő" => [1000, 500000],
    "Féktárcsa" => [1000, 500000],
    "Féltengely, komplett" => [1000, 500000],
    "Lengőkar szilent" => [100, 100000],
    "Első Spirálrugó" => [100, 100000],
    "Lambdaszonda" => [1000, 500000],
    "Fékbetét" => [1000, 500000],
    "Gyújtógyertya" => [50, 50000],
    "Olajszűrő" => [50, 50000],
    "Olajszűrő betét" => [50, 50000],
    "Fékpofa" => [100, 100000],
    "Generátor" => [1000, 500000],
    "Féltengely gumiharang készlet" => [50, 50000],
    "Szivósor tömítés" => [50, 50000],
    "Kuplungszett (3db)" => [1000, 500000],
    "Lengéscsillapító" => [1000, 500000],
    "Gyújtótrafó" => [100, 100000],
    "Hátsó Spirálrugó" => [100, 100000],
    "Visszapillantó tükör, külső" => [1000, 500000],
    "Vezérműszíj készlet" => [1000, 500000],
    "Dugattyúgyűrű" => [50, 50000],
    "Spirálrugó" => [100, 100000],
    "Felső tömítéskészlet" => [1000, 500000],
    "Jeladó, kipufogógáz hőmérséklet" => [100, 100000],
    "Önindító" => [1000, 500000],
    "Hajtókarcsapágy" => [50, 50000],
    "Lengéscsillapító porvédő+ütköz" => [50, 50000],
    "Fékkopás jelző" => [50, 50000],
    "Klímakompresszor" => [1000, 500000],
    "Féltengelycsukló készlet" => [100, 100000],
    "Kerékcsapágy aggyal" => [1000, 500000],
    "Szelep" => [50, 50000],
    "Vezetőgörgő, hosszbordás szíj" => [100, 100000],
    "Jeladó, főtengely (holtpont jeladó)" => [100, 100000],
    "Üzemanyagszivattyú" => [1000, 500000],
    "Tükörlap" => [50, 50000],
    "Toronyszilent" => [50, 50000],
    "Vezérműszíjkészlet vízpumpával" => [1000, 500000],
    "EGR szelep" => [1000, 500000],
    "Szimmering, főtengely" => [50, 50000],
    "Termosztát" => [100, 100000],
    "Toronycsapágy+szilent" => [100, 100000],
    "Ablakemelő" => [100, 100000],
    "Vezérműlánc hajtás készlet" => [1000, 500000],
    "Tartozékkészlet, tárcsafékbetét" => [100, 100000],
    "Kipufogósor töm." => [50, 50000],
    "Kipufogódob, hátsó" => [100, 100000],
    "Terelőlemez / féktárcsa" => [50, 50000],
    "Főtengelycsapágy" => [100, 100000],
    "Fékmunkahenger" => [50, 50000],
    "Kuplungszett" => [1000, 500000],
    "Irányjelző lámpa, első" => [100, 100000],
    "Töltőlevegő hűtő" => [1000, 500000],
    "Dugattyú" => [100, 100000],
    "Ventilátor, vízhűtő" => [1000, 500000],
    "Vezérműszíj" => [50, 50000],
    "Ventilátor, utastér" => [100, 100000],
    "Izzítógyertya" => [100, 100000],
    "Feszítőgörgő, hosszbordás szíj" => [100, 100000],
    "Ködfényszóró" => [1000, 500000],
    "Üzemanyagszűrő betét" => [50, 50000],
    "Szimmering" => [50, 50000],
    "Gyújtókábel készlet" => [100, 100000],
    "Ajtózár" => [50, 50000],
    "Kuplung főhenger" => [100, 100000],
    "Generátor szabadonfutó" => [50, 50000],
    "Hűtőfolyadék kiegyenlítő tartály" => [50, 50000],
    "Lökhárító" => [10000, 2000000],
    "Termosztát házzal" => [100, 100000],
    "Szíjtárcsa, főtengely" => [100, 100000],
    "Tömítőgyűrű" => [50, 50000],
    "Légtömegmérő házzal" => [1000, 500000],
    "Hengerfej csavar" => [50, 50000],
    "Szervószivattyú, kormányzás" => [1000, 500000],
    "Szelepszár szimmering" => [50, 50000],
    "Olajhűtő" => [100, 100000],
    "Kipufogó tömítés" => [50, 50000],
    "Jeladó, hűtőfolyadék hőm." => [100, 100000],
    "Lengéscsillapító ütköző" => [50, 50000],
    "Feszítőgörgő, vezérműszíj" => [100, 100000],
    "Feszültség szabályzó" => [100, 100000],
    "Féltengelycsukló készlet külső" => [100, 100000],
    "Ékszíj" => [50, 50000],
    "Hosszbordásszíj készlet" => [1000, 500000],
    "Jeladó, vezérműtengely" => [50, 50000],
    "Tartozékkészlet, fékpofa" => [50, 50000],
    "Sárvédő" => [1000, 500000],
    "Fűtőradiátor" => [1000, 500000],
    "Kipufogódob, középső" => [1000, 500000],
    "Kipufogócső" => [100, 100000],
    "Kuplungszett (2db)" => [1000, 500000],
    "Kettős tömegű lendkerék (DMF)" => [1000, 500000],
    "Kipufogó tartó" => [50, 50000],
    "Hátsótengely lengőkar" => [1000, 500000],
    "Patent, karosszéria" => [50, 50000],
    "Olajteknő tömítés" => [50, 50000],
    "Gázteleszkóp, motortér fedél" => [100, 100000],
    "Feszítőkar, hosszbordásszíj" => [100, 100000],
    "Hátsótengely híd szilent" => [50, 50000],
    "Féknyereg dugattyú" => [50, 50000],
    "Szűrő, automataváltó" => [100, 100000],
    "Díszrács, lökhárító" => [1000, 500000],
    "Lökhárító tartó" => [1000, 500000],
    "Fékdob" => [100, 100000],
    "Kapcsoló, kormányoszlop" => [1000, 500000],
    "Kapcsoló, ablakemelő" => [1000, 500000],
    "Kuplung munkahenger" => [1000, 500000],
    "Ablakemelő, motor nélkül" => [100, 100000],
    "Főfékhenger" => [1000, 500000],
    "Üzemanyagcső" => [1000, 500000],
    "Csavar" => [50, 50000],
    "Jeladó, szívócsonk nyomás (MAP)" => [100, 100000],
    "Ablaktörlő motor" => [1000, 500000],
    "Ablaktörlő kar" => [100, 100000],
    "Kinyomócsapágy, hidraulikus" => [1000, 500000],
    "Kormánymű porvédő készlet" => [50, 50000],
];

$companies = [
    "22LUBRICANTS HUNGARY Kft." => "STOCK226",
    "Abakus Sp. z o.o." => "STOCK13",
    "ABR Autómax Kft." => "STOCK39",
    "Abroncs Kereskedőház Kft." => "STOCK98",
    "ABS_NL" => "STOCK61NL",
    "ABS_PL" => "STOCK61",
    "ACI-Auto Components International s.r.o" => "STOCK1",
    "Advanta d.o.o" => "STOCK25",
    "Agrokémia Sellye Zrt" => "STOCK217",
    "AIRTOP ITALIA" => "STOCK193",
    "AJS Parts" => "STOCK91",
    "AkkuPoll EURO Kft." => "STOCK70eur",
    "AkkuPoll Kft." => "STOCK70huf",
    "ALCAR KFT." => "STOCK150",
    "Alfa" => "STOCK24",
    "ALGO SPA" => "STOCK201",
    "AMC CYL" => "STOCK171",
    "Amero-R Kft." => "STOCK11",
    "Armafilt Zrt" => "STOCK3",
    "ARVDOO" => "STOCK88",
    "AS-PL sp. zoo" => "STOCK138",
    "ASMETCENTER" => "STOCK102",
    "ASMET_" => "STOCK100",
    "ATH Autoteile Handel GmbH." => "STOCK122",
    "AUTO KELLY a.s." => "STOCK2",
    "Auto Service Tools LTD" => "STOCK83",
    "Autó Triplex Kft." => "STOCK154",
    "Auto Wallis Renault" => "STOCK310",
    "AUTO-MOTO RS s.r.o." => "STOCK141",
    "AUTO-PARTNER BLUE" => "STOCK228",
    "AUTOFOR" => "",
    "Autogalant_" => "STOCK18",
    "AUTOGROUP HUNGARY KFT." => "STOCK143",
    "Autonet Import Mo. Kft." => "STOCK4",
    "AUTONOVA EXPORT-IMPORT KFT" => "STOCK185",
    "Autopartner" => "STOCK104",
    "AutopartnerHUF" => "STOCK104HU",
    "Autopartner_2" => "STOCK104ro",
    "Autopartner_3" => "STOCK104cr",
    "AVP Autoland GMBH." => "STOCK72",
    "AVP Autoland Skoda" => "STOCK72SK",
    "BANKOSAN OTOMOTİV TİC.VE SAN.LTD.STİ." => "STOCK212",
    "Banner Batterien Hungária Kft." => "STOCK227",
    "Basbug" => "STOCK210",
    "Baumgartner Autócentrum Kft." => "STOCK120",
    "Bepco_" => "STOCK132",
    "Bepco_PL" => "STOCK199",
    "BHMD" => "STOCK108",
    "Bilstein BluePrint" => "STOCK49",
    "BILSTEIN SWAG" => "STOCK43",
    "Bimida" => "STOCK47",
    "Birner Hungaria Kft." => "STOCK34",
    "Boda" => "STOCK232",
    "BORGW" => "STOCK224",
    "BOSAL CZ" => "STOCK148",
    "Bottari_" => "STOCK58",
    "BTS gmbh" => "STOCK103",
    "CEMPOL" => "STOCK10",
    "CERMotor" => "STOCK109",
    "citycarparts" => "STOCK209",
    "Complex.net Sp. z o.o." => "STOCK15",
    "Conex" => "STOCK216",
    "ConexRo" => "STOCK216RO",
    "Continental Hungaria Kft." => "STOCK40",
    "Continental-Ate" => "STOCK208",
    "Corteco GmbH" => "STOCK172A",
    "CSERGŐ BP" => "STOCK153",
    "CSERGŐ Rapid" => "STOCK153R",
    "Dayco Europe S.r.l." => "STOCK170",
    "DELPHI TEC" => "STOCK187",
    "DF Tuning Kft" => "",
    "Di-Fer Kft" => "STOCK145",
    "DIEDERICHS Karosserieteile GmbH" => "STOCK205",
    "Diesel Czesci" => "STOCK156",
    "Direct Fit Hungary KFT" => "STOCK168",
    "Dr.Motor" => "STOCK164",
    "DTP MOTORTEILE GmbH" => "STOCK165",
    "ElringKringel AG" => "STOCK136",
    "Emil Frey Magyarország Kft." => "STOCK92",
    "Emil Frey uj" => "STOCK92_EF",
    "ENEOS Europe Limited" => "",
    "ESEN SKF" => "STOCK160",
    "EURO CARPARTS" => "STOCK211",
    "EURO07" => "STOCK206",
    "Eurotexaduo" => "STOCK220",
    "Euroton" => "STOCK106",
    "Falcon Car" => "STOCK78",
    "FEBEST Europe Distribution" => "STOCK151L",
    "FEBEST Europe Distribution OÜ-X" => "STOCK151X",
    "FEBEST Europe Distribution2 Bolgár" => "STOCK151D",
    "FEBEST Europe Distribution3" => "STOCK151B",
    "FEBI RS. HU" => "STOCK87HU",
    "FebiRs" => "STOCK87",
    "Federal Mogul GMBH." => "STOCK36",
    "Federal Mogul Gmbh/FPDIESEL" => "STOCK36FP",
    "Federal-Mogul Global Aftermarket EMEA BV _ PL" => "STOCK97",
    "FIS COM" => "STOCK133",
    "Fischer Automotive" => "STOCK198",
    "Forex Kft." => "STOCK35",
    "FRECCIA S.R.L." => "STOCK172",
    "Frytech Kft" => "STOCK123",
    "GABLINI/HYU-DE" => "STOCK188HYU-DE",
    "GABLINI/HYU-ÉRD" => "STOCK188HYU-ÉRD",
    "GABLINI/KIA-DE" => "STOCK188KIA-DE",
    "GABLINI/KIA-ÉRD" => "STOCK188KIA-ÉRD",
    "GABLINI/NIS-INF-AMS" => "STOCK188NAMS",
    "GABLINI/NIS-INF-GYŐR" => "STOCK188NGY",
    "GABLINI/PSA-FRA" => "STOCK188PSA-FRA",
    "GABLINI/PSA-SLO" => "STOCK188PSA-SL",
    "Gall-ker Kft" => "STOCK21",
    "GATES_" => "STOCK121",
    "Gazela" => "STOCK89",
    "Gazela Belgrad" => "STOCK89BE",
    "Gazela Cacak" => "STOCK89CA",
    "Gazela Ujvidek" => "STOCK89UJ",
    "GBG" => "STOCK127",
    "GMT d.o.o." => "STOCK300",
    "GrupaTopex" => "STOCK221",
    "GrupaTopex H" => "STOCK221 HETI1",
    "GRUPAUTO S.A." => "STOCK42",
    "HAHN+KOLB Hungária" => "STOCK86",
    "HART OP" => "STOCK180-2",
    "HART PL" => "STOCK180-3",
    "HART SK" => "STOCK180",
    "Hegyalja KFT BP" => "STOCK155",
    "Hegyalja KFT Rapid" => "STOCK155R",
    "HellaEUR" => "STOCK20eur",
    "HellaHungaria" => "STOCK20",
    "HellaRO" => "STOCK20ro",
    "HERTH" => "STOCK223",
    "Hipol HC" => "STOCK57HC",
    "HIPOL HK" => "STOCK57HK",
    "Hipol HM" => "STOCK57HM",
    "Hipol HO" => "STOCK57HO",
    "Hipol HW" => "STOCK57HW",
    "Hipol HZ" => "STOCK57BB",
    "HIRSCHVOGEL Service GmbH." => "STOCK116",
    "IPSA Autoteile Großhandelsgeselschaft mbH" => "STOCK173",
    "Ivanics FORD Külső" => "STOCK115FK",
    "Ivanics HYUNDAI Külső" => "STOCK115HK",
    "Ivanics Kft." => "STOCK115",
    "Ivanics Kft. /szfvar" => "STOCK115SZF",
    "Ivanics Volvo Külső" => "STOCK115VK",
    "J.B.M. CAMPLLONG S.L" => "STOCK41",
    "Japanparts S.r.l." => "STOCK77",
    "Jász-Plasztik Autócentrum Kft." => "STOCK74",
    "JMAUTODILY" => "STOCK190",
    "JMAUTODILY STAV" => "STOCK190ST",
    "Jonnesway Rom srl" => "STOCK85",
    "K MOTORSHOP" => "STOCK157",
    "KÁKOS" => "STOCK159",
    "Kamoka_" => "STOCK131",
    "Kar-Parts Kft." => "STOCK50",
    "Kar-Parts Kft. japan" => "STOCK50JP",
    "KAVO" => "STOCK183",
    "KAYABA" => "STOCK113",
    "KB Autóteam Kft." => "STOCK79",
    "King_" => "STOCK45",
    "Kocsis Imi" => "STOCK12",
    "Kokavecz" => "STOCK189",
    "KSTools Werkzeuge" => "STOCK65",
    "KUPLUNG VIAWEB Kft." => "STOCK203",
    "Lang EUR" => "STOCK84",
    "Láng Kereskedelmi Kft." => "STOCK5",
    "Lang Kulso" => "STOCK233",
    "M&D GROUP" => "STOCK158",
    "M-TECH POLAND SPÓŁKA Z OGRANICZONĄ ODPOWIEDZIALNOŚCIĄ" => "STOCK207",
    "M6 Kft" => "STOCK142",
    "Magneti Marelli Aft." => "STOCK55",
    "MAHER DIŞ TİCARET VE SANAYİ A.Ş" => "STOCK213",
    "Mahle Aftermarket" => "STOCK126",
    "Mahle Aftermarket PL" => "STOCK126PL",
    "Martex" => "STOCK90",
    "Martex_r" => "STOCK90_R",
    "MASTERSPORT" => "STOCK152",
    "Maxiforce MIami" => "STOCK230",
    "MB GARAGE" => "STOCK128",
    "MB GARAGE Rapid" => "STOCK128R",
    "MBK OTOMOTİV SAN.TİC. A.Ş" => "STOCK215",
    "Metelli-Graf" => "STOCK179",
    "Meyle_" => "STOCK71",
    "MiNiMa" => "STOCK118",
    "MiNiMa AU" => "STOCK118AU",
    "Misfat_" => "STOCK16",
    "Moto-Profil SP z.o.o." => "STOCK6",
    "Motogama" => "STOCK96",
    "Motorion Kft." => "STOCK7",
    "MOTOROL" => "STOCK139",
    "MSI" => "STOCK37",
    "MTE-THOMSON EUROPE GmbH" => "STOCK218",
    "NASA PONUDA" => "STOCK137",
    "Nefelejts kft." => "STOCK46",
    "NEKO OTOMOTIV LTD. STI" => "STOCK146",
    "NGK Spark Plug Europe GmbH" => "STOCK175",
    "Nimfas-Corporation Bt." => "STOCK229",
    "Nissens A/S" => "Stock22_PL",
    "Nissens_" => "STOCK22",
    "Novopolma" => "STOCK23",
    "Npr Europe" => "STOCK167",
    "NRF_" => "STOCK59",
    "OCAP Hungária Kft." => "STOCK33",
    "OCAP OC" => "STOCK33a",
    "OE Germany (beszállító)" => "STOCK186",
    "Optibelt_" => "STOCK82",
    "Originalnediely" => "STOCK161",
    "OSRAM, a.s." => "STOCK111",
    "OSVAT SRL" => "STOCK169",
    "Otokoç Genel Müdürlük" => "STOCK214",
    "Partner Autóalkatrész Piac Kft." => "STOCK9",
    "Parts4France" => "STOCK204",
    "Peace" => "STOCK147",
    "PEARLfect Kft" => "STOCK162",
    "Pet-Roll EUR Parts Kft." => "STOCK19eur",
    "Pet-Roll Parts Kft." => "STOCK19",
    "PPH POLCAR" => "STOCK32",
    "PPH POLCAR Kettes" => "STOCK32_NAGY",
    "PPH POLCAR/RS" => "STOCK32_RS",
    "Q4Y S.A" => "STOCK17",
    "REÁLSZISZTÉMA AUTÓKERESKEDELMI Kft./Bécs" => "STOCK184W",
    "REÁLSZISZTÉMA AUTÓKERESKEDELMI Kft./Brüsszel" => "STOCK184BR",
    "REÁLSZISZTÉMA AUTÓKERESKEDELMI Kft./Gyártás" => "STOCK184GY",
    "Reanult Hun. Rapid" => "STOCK107R",
    "REINZ Rapid" => "STOCK144",
    "Renault Hun." => "STOCK107",
    "Robert Bosch Kft." => "STOCK26",
    "Robert Bosch/Karlsruhe" => "STOCK28",
    "Rotinger pl" => "STOCK181",
    "S.C. AUTO GROUP C.M.B. S.R.L." => "STOCK48",
    "S.C. EUROEST CAR S.R.L._" => "STOCK117",
    "S.C. EUROEST CAR S.R.L._ RO" => "STOCK117RO",
    "S.C. L Intesa Promotive SRL" => "STOCK62",
    "S.M.Power Kft. Bp" => "STOCK124",
    "S.M.Power Kft. Szmiklós" => "STOCK125",
    "SAMPA BG" => "STOCK231",
    "Sanel BG" => "STOCK225BG",
    "Sanel CA" => "STOCK225CA",
    "Sanel NS" => "STOCK225NS",
    "Sanel O" => "STOCK225O",
    "SANGSIN BRAKE EUROPE Sp. z o.o." => "",
    "SASIC_" => "STOCK14",
    "SC Materom SRL" => "STOCK219",
    "Schäferbarthold GmbH." => "STOCK114",
    "Schäferbarthold GmbH. +1" => "STOCK114+1",
    "Schäferbarthold GmbH. +2" => "STOCK114+2",
    "Schäferbarthold GmbH. +3" => "STOCK114+3",
    "SEGURIDAD INDUSTRIAL S.A." => "STOCK191",
    "SHIPMAN" => "STOCK194",
    "SKF Svéd Golyóscsapágy ZRT" => "STOCK76",
    "Stahlgruber" => "STOCK29",
    "Stahlgruber cz." => "STOCK93",
    "Stahlgruber cz.2" => "STOCK93-2",
    "Stellox_" => "STOCK130",
    "SUDER & SUDER" => "STOCK135",
    "Suzuking Kft." => "STOCK75",
    "SW-Stahl GmbH" => "STOCK80",
    "Szabó-Autóművek Kft." => "STOCK51",
    "Szakál-Met-Al KFT" => "STOCK30",
    "Szakál-Met-Al KFT (bulgár külső)" => "STOCK0_bg",
    "Szakál-Met-Al KFT (horvát külső)" => "STOCK0_cr",
    "Szakál-Met-Al KFT (román külső)" => "STOCK0_ro",
    "Szatuna Kft." => "STOCK63",
    "Sziklai Kft." => "STOCK31",
    "Tafex Trade" => "STOCK73",
    "TECHNOFIL KFT" => "STOCK95",
    "TECHNOGAMA" => "STOCK134",
    "TED-GUM" => "STOCK197",
    "Teszt" => "STOCK_TEST",
    "THro" => "STOCK38_ro",
    "TH_" => "STOCK38",
    "Tiki vent" => "STOCK94",
    "TOKIC" => "STOCK195",
    "TokicRo" => "STOCK195RO",
    "TRISCAN DK" => "STOCK129DK",
    "Triscan GMBH" => "STOCK129DE",
    "Trost Auto Service Technik Kft" => "STOCK8",
    "TRUCKPOINT Kft." => "STOCK81",
    "TRUCKPOINT rapid" => "STOCK81r",
    "Trucktec_" => "STOCK64",
    "Turborail_" => "STOCK105",
    "Unitrak" => "STOCK140",
    "Valeo_" => "STOCK27",
    "Vanstar PL" => "STOCK44",
    "VariensBP" => "STOCK99_B",
    "VariensSV" => "STOCK99_S",
    "Vema Srl" => "STOCK200",
    "Vierol" => "STOCK196",
    "Walker_" => "STOCK101",
    "Wallis Motor Pest Kft." => "STOCK119",
    "WERNER METZGER GMBH" => "STOCK52",
    "Wilminkgroup" => "STOCK166",
    "WONH Europe GmbH" => "STOCK192",
    "WP BRAKE" => "STOCK202",
    "ZS+UAN" => "STOCK222",
    "ZSUAN2" => "STOCK222 B"
];

function readResultCsv($filePath)
{
    $data = [];

    if (file_exists($filePath)) {
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            while (($row = fgetcsv($handle, 0, "\t")) !== FALSE) {
                $data[] = $row;
            }
            fclose($handle);
        } else {
            echo "Hiba: Nem sikerült megnyitni a CSV fájlt!";
        }
    } else {
        echo "Hiba: A CSV fájl nem létezik az adott elérési úton: $filePath";
    }

    return $data;
}

$resultFile = "c:\Users\LP-GERGO\Desktop\\afterCheck\\result.csv";
echo "CSV fájl beolvasása...<br>";

$csvData = readResultCsv($resultFile);


$nemMegfeleloSorok = [];

$companies = array_flip($companies);

for ($i = 1; $i < count($csvData); $i++) {
    $sor = $csvData[$i];
    $termekNev = $sor[10]; 
    $osszeg = (float)$sor[3]; 
    $stockFromSor = trim($sor[1]);

    $beszallito = isset($companies[$stockFromSor]) ? $companies[$stockFromSor] : $stockFromSor;
 
    if (isset($alkatreszek[$termekNev])) {
        $tartomany = $alkatreszek[$termekNev];
        $minAr = $tartomany[0];
        $maxAr = $tartomany[1];


        if ($osszeg < $minAr) {
            $nemMegfeleloSorok[] = [
                'glid' => $sor[0],
                'cikkszam' => $sor[6],
                'gyarto' => $sor[9],
                'tcs' => $sor[10],
                'beszallito_stock' => $sor[1],
                'beszallito' => $beszallito,
                'ar' => $sor[3],
                'hiba' => 'Túl alacsony ár'
            ];
        } elseif ($osszeg > $maxAr) {
            $nemMegfeleloSorok[] = [
                'glid' => $sor[0],
                'cikkszam' => $sor[6],
                'gyarto' => $sor[9],
                'tcs' => $sor[10],
                'beszallito_stock' => $sor[1],
                'beszallito' => $beszallito,
                'ar' => $sor[3],
                'hiba' => 'Túl magas ár'
            ];
        }
    }
        
}



/*
$string = 'AA';

$string = str_pad($string,10,"C",STR_PAD_LEFT);
var_dump($string);

/*
$resultFile = "C:\\Users\\LP-GERGO\\Desktop\\afterCheck\\result.csv";
echo "CSV fájl beolvasása...\n" . "<br>";

$csvData = readResultCsv($resultFile);

$alkatreszek = [
    "Levegőszűrő" => [50, 50000],
    "Lengőkar" => [1000, 500000],
    "Első fékbetét" => [100, 100000],
    "Első féktárcsa" => [1000, 500000],
    "Pollenszűrő" => [50, 50000],
    "Féknyereg" => [100, 100000],
    "Stabilizátor kar" => [50, 50000],
    "Kormányösszekötő gömbfej" => [50, 50000],
    "Hosszbordásszíj" => [50, 50000],
    "Motortartó bak" => [1000, 500000],
    "Hátsó féktárcsa" => [1000, 500000],
    "Kerékcsapágy készlet" => [1000, 500000],
    "Vízhűtő" => [1000, 500000],
    "Gumifékcső" => [50, 50000],
    "Kézifék bowden" => [100, 100000],
    "ABS jeladó" => [1000, 500000],
    "Hátsó fékbetét" => [100, 100000],
    "Hengerfej töm." => [100, 100000],
    "Első Lengéscsillapító" => [1000, 500000],
    "Stabilizátor szilent" => [50, 50000],
    "Üzemanyagszűrő" => [100, 100000],
    "Gázteleszkóp, csomagtér fedél" => [100, 100000],
    "Vízcső" => [100, 100000],
    "Fényszóró" => [4000, 2000000],
    "Szilentblokk" => [50, 50000],
    "Féknyereg javítókészlet" => [50, 50000],
    "Vízpumpa" => [1000, 500000],
    "Kormányösszekötő belső" => [100, 100000],
    "Szelepfedél tömítés" => [100, 100000],
    "Hátsó Lengéscsillapító" => [1000, 500000],
    "Lengőkar gömbfej" => [50, 50000],
    "Ablaktörlő lapát" => [50, 50000],
    "Hátsó lámpa" => [1000, 500000],
    "Töltőlevegő hűtő cső" => [1000, 500000],
    "Klímahűtő" => [1000, 500000],
    "Féktárcsa" => [1000, 500000],
    "Féltengely, komplett" => [1000, 500000],
    "Lengőkar szilent" => [100, 100000],
    "Első Spirálrugó" => [100, 100000],
    "Lambdaszonda" => [1000, 500000],
    "Fékbetét" => [1000, 500000],
    "Gyújtógyertya" => [50, 50000],
    "Olajszűrő" => [50, 50000],
    "Olajszűrő betét" => [50, 50000],
    "Fékpofa" => [100, 100000],
    "Generátor" => [1000, 500000],
    "Féltengely gumiharang készlet" => [50, 50000],
    "Szivósor tömítés" => [50, 50000],
    "Kuplungszett (3db)" => [1000, 500000],
    "Lengéscsillapító" => [1000, 500000],
    "Gyújtótrafó" => [100, 100000],
    "Hátsó Spirálrugó" => [100, 100000],
    "Visszapillantó tükör, külső" => [1000, 500000],
    "Vezérműszíj készlet" => [1000, 500000],
    "Dugattyúgyűrű" => [50, 50000],
    "Spirálrugó" => [100, 100000],
    "Felső tömítéskészlet" => [1000, 500000],
    "Jeladó, kipufogógáz hőmérséklet" => [100, 100000],
    "Önindító" => [1000, 500000],
    "Hajtókarcsapágy" => [50, 50000],
    "Lengéscsillapító porvédő+ütköz" => [50, 50000],
    "Fékkopás jelző" => [50, 50000],
    "Klímakompresszor" => [1000, 500000],
    "Féltengelycsukló készlet" => [100, 100000],
    "Kerékcsapágy aggyal" => [1000, 500000],
    "Szelep" => [50, 50000],
    "Vezetőgörgő, hosszbordás szíj" => [100, 100000],
    "Jeladó, főtengely (holtpont jeladó)" => [100, 100000],
    "Üzemanyagszivattyú" => [1000, 500000],
    "Tükörlap" => [50, 50000],
    "Toronyszilent" => [50, 50000],
    "Vezérműszíjkészlet vízpumpával" => [1000, 500000],
    "EGR szelep" => [1000, 500000],
    "Szimmering, főtengely" => [50, 50000],
    "Termosztát" => [100, 100000],
    "Toronycsapágy+szilent" => [100, 100000],
    "Ablakemelő" => [100, 100000],
    "Vezérműlánc hajtás készlet" => [1000, 500000],
    "Tartozékkészlet, tárcsafékbetét" => [100, 100000],
    "Kipufogósor töm." => [50, 50000],
    "Kipufogódob, hátsó" => [100, 100000],
    "Terelőlemez / féktárcsa" => [50, 50000],
    "Főtengelycsapágy" => [100, 100000],
    "Fékmunkahenger" => [50, 50000],
    "Kuplungszett" => [1000, 500000],
    "Irányjelző lámpa, első" => [100, 100000],
    "Töltőlevegő hűtő" => [1000, 500000],
    "Dugattyú" => [100, 100000],
    "Ventilátor, vízhűtő" => [1000, 500000],
    "Vezérműszíj" => [50, 50000],
    "Ventilátor, utastér" => [100, 100000],
    "Izzítógyertya" => [100, 100000],
    "Feszítőgörgő, hosszbordás szíj" => [100, 100000],
    "Ködfényszóró" => [1000, 500000],
    "Üzemanyagszűrő betét" => [50, 50000],
    "Szimmering" => [50, 50000],
    "Gyújtókábel készlet" => [100, 100000],
    "Ajtózár" => [50, 50000],
    "Kuplung főhenger" => [100, 100000],
    "Generátor szabadonfutó" => [50, 50000],
    "Hűtőfolyadék kiegyenlítő tartály" => [50, 50000],
    "Lökhárító" => [10000, 2000000],
    "Termosztát házzal" => [100, 100000],
    "Szíjtárcsa, főtengely" => [100, 100000],
    "Tömítőgyűrű" => [50, 50000],
    "Légtömegmérő házzal" => [1000, 500000],
    "Hengerfej csavar" => [50, 50000],
    "Szervószivattyú, kormányzás" => [1000, 500000],
    "Szelepszár szimmering" => [50, 50000],
    "Olajhűtő" => [100, 100000],
    "Kipufogó tömítés" => [50, 50000],
    "Jeladó, hűtőfolyadék hőm." => [100, 100000],
    "Lengéscsillapító ütköző" => [50, 50000],
    "Feszítőgörgő, vezérműszíj" => [100, 100000],
    "Feszültség szabályzó" => [100, 100000],
    "Féltengelycsukló készlet külső" => [100, 100000],
    "Ékszíj" => [50, 50000],
    "Hosszbordásszíj készlet" => [1000, 500000],
    "Jeladó, vezérműtengely" => [50, 50000],
    "Tartozékkészlet, fékpofa" => [50, 50000],
    "Sárvédő" => [1000, 500000],
    "Fűtőradiátor" => [1000, 500000],
    "Kipufogódob, középső" => [1000, 500000],
    "Kipufogócső" => [100, 100000],
    "Kuplungszett (2db)" => [1000, 500000],
    "Kettős tömegű lendkerék (DMF)" => [1000, 500000],
    "Kipufogó tartó" => [50, 50000],
    "Hátsótengely lengőkar" => [1000, 500000],
    "Patent, karosszéria" => [50, 50000],
    "Olajteknő tömítés" => [50, 50000],
    "Gázteleszkóp, motortér fedél" => [100, 100000],
    "Feszítőkar, hosszbordásszíj" => [100, 100000],
    "Hátsótengely híd szilent" => [50, 50000],
    "Féknyereg dugattyú" => [50, 50000],
    "Szűrő, automataváltó" => [100, 100000],
    "Díszrács, lökhárító" => [1000, 500000],
    "Lökhárító tartó" => [1000, 500000],
    "Fékdob" => [100, 100000],
    "Kapcsoló, kormányoszlop" => [1000, 500000],
    "Kapcsoló, ablakemelő" => [1000, 500000],
    "Kuplung munkahenger" => [1000, 500000],
    "Ablakemelő, motor nélkül" => [100, 100000],
    "Főfékhenger" => [1000, 500000],
    "Üzemanyagcső" => [1000, 500000],
    "Csavar" => [50, 50000],
    "Jeladó, szívócsonk nyomás (MAP)" => [100, 100000],
    "Ablaktörlő motor" => [1000, 500000],
    "Ablaktörlő kar" => [100, 100000],
    "Kinyomócsapágy, hidraulikus" => [1000, 500000],
    "Kormánymű porvédő készlet" => [50, 50000],
];

$nemMegfeleloSorok = [];


for ($i = 1; $i < count($csvData); $i++) {
    $sor = $csvData[$i];
    $termekNev = $sor[10]; // Termék név
    $osszeg = (float)$sor[3]; // Összeg, számként


    if (isset($alkatreszek[$termekNev])) {
        $tartomany = $alkatreszek[$termekNev];
        $minAr = $tartomany[0];
        $maxAr = $tartomany[1];


        if ($osszeg < $minAr) {
            $nemMegfeleloSorok[] = [
                'glid' => $sor[0],
                'cikkszam' => $sor[6],
                'gyarto' => $sor[9],
                'tcs' => $sor[10],
                'beszallito_stock' => $sor[1],
                'ar' => $sor[3],
                'hiba' => 'Túl alacsony ár'
            ];
        } elseif ($osszeg > $maxAr) {
            $nemMegfeleloSorok[] = [
                'glid' => $sor[0],
                'cikkszam' => $sor[6],
                'gyarto' => $sor[9],
                'tcs' => $sor[10],
                'beszallito_stock' => $sor[1],
                'ar' => $sor[3],
                'hiba' => 'Túl magas ár'
            ];
        }
    }
}


if (count($nemMegfeleloSorok) > 0) {

    echo "Nem megfelelő sorok:<br><br>";
    echo "Ennyi darab: " . count($nemMegfeleloSorok) . "<br><br>";
    foreach ($nemMegfeleloSorok as $sor) {

        if ($sor['ar'] == 0 || $sor['ar'] == '0') {
            echo "<div style='background-color: #ff0000; font-weight:bold'> (Hiba: " . $sor['hiba'] . ") - glid: " . $sor['glid'] . ", articlecode: " . $sor['cikkszam'] . ", gyarto: " . $sor['gyarto'] . ", tcs: " . $sor['tcs'] .
                ", beszallito stock: " . $sor['beszallito_stock'] . ", ar: " . $sor['ar'];
            echo "</div><br>";
        } else {
            echo "(Hiba: " . $sor['hiba'] . ") - glid: " . $sor['glid'] . ", articlecode: " . $sor['cikkszam'] . ", gyarto: " . $sor['gyarto'] . ", tcs: " . $sor['tcs'] .
                ", beszallito stock: " . $sor['beszallito_stock'] . ", ar: " . $sor['ar'];
            echo "<br>";
        }
    }
} else {
    echo "Minden összeg az ártartományon belül van.\n";
}

function readResultCsv($filePath)
{
    $data = [];

    if (file_exists($filePath)) {
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            while (($row = fgetcsv($handle, 0, "\t")) !== FALSE) {
                $data[] = $row;
            }
            fclose($handle);
        } else {
            echo "Hiba: Nem sikerült megnyitni a CSV fájlt!";
        }
    } else {
        echo "Hiba: A CSV fájl nem létezik az adott elérési úton: $filePath";
    }

    return $data;
}
/*

function masolFajlok($forrasMappa, $celMappa, $fajlNevek) {
    // Ellenőrizzük, hogy a célmappa létezik-e, ha nem, hozzuk létre
    if (!is_dir($celMappa)) {
        mkdir($celMappa, 0777, true);
    }
    
    foreach ($fajlNevek as $fajlNev) {
        $forrasFajl = rtrim($forrasMappa, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fajlNev;
        $celFajl = rtrim($celMappa, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fajlNev;
        
        if (file_exists($forrasFajl)) {
            if (copy($forrasFajl, $celFajl)) {
                echo "A(z) {$fajlNev} sikeresen átmásolva!\n";
            } else {
                echo "Hiba történt a(z) {$fajlNev} másolása közben!\n";
            }
        } else {
            echo "A(z) {$fajlNev} nem található a forrásmappában!\n";
        }
    }
}

// Használat
$forrasMappa = 'Z:\shodan_vevo_arlistak\magyar'; // A forrásmappa neve
$celMappa = 'C:\Users\LP-GERGO\Desktop\afterCheck'; // A célmappa neve
$fajlNevek = ['cikkek_hun_all.csv.zip', 'E330_arlista_nagyker_all.csv.zip']; // Az átmásolandó fájlok nevei

masolFajlok($forrasMappa, $celMappa, $fajlNevek);


/*
$resultFile = 'c:\Users\LP-GERGO\Desktop\afterCheck\result.csv';

$csvData = readResultCsv($resultFile);



foreach($csvData as $key => $datas){
    var_dump($datas);

    $counter++;

    if($counter >= 5){
        break;
    }
}


$alkatreszek = [
    "Levegőszűrő" => [50, 70000],
    "Lengőkar" => [1000, 500000],
    "Első fékbetét" => [100, 100000],
    "Első féktárcsa" => [1000, 500000],
    "Pollenszűrő" => [50, 50000],
    "Féknyereg" => [100, 100000],
    "Stabilizátor kar" => [50, 50000],
    "Kormányösszekötő gömbfej" => [50, 50000],
    "Hosszbordásszíj" => [50, 50000],
    "Motortartó bak" => [1000, 500000],
    "Hátsó féktárcsa" => [1000, 500000],
    "Kerékcsapágy készlet" => [1000, 500000],
    "Vízhűtő" => [1000, 500000],
    "Gumifékcső" => [50, 50000],
    "Kézifék bowden" => [100, 100000],
    "ABS jeladó" => [1000, 500000],
    "Hátsó fékbetét" => [100, 100000],
    "Hengerfej töm." => [100, 100000],
    "Első Lengéscsillapító" => [1000, 500000],
    "Stabilizátor szilent" => [50, 50000],
    "Üzemanyagszűrő" => [100, 100000],
    "Gázteleszkóp, csomagtér fedél" => [100, 100000],
    "Vízcső" => [100, 100000],
    "Fényszóró" => [10000, 2000000],
    "Szilentblokk" => [50, 50000],
    "Féknyereg javítókészlet" => [50, 50000],
    "Vízpumpa" => [1000, 500000],
    "Kormányösszekötő belső" => [100, 100000],
    "Szelepfedél tömítés" => [100, 100000],
    "Hátsó Lengéscsillapító" => [1000, 500000],
    "Lengőkar gömbfej" => [50, 50000],
    "Ablaktörlő lapát" => [50, 50000],
    "Hátsó lámpa" => [1000, 500000],
    "Töltőlevegő hűtő cső" => [1000, 500000],
    "Klímahűtő" => [1000, 500000],
    "Féktárcsa" => [1000, 500000],
    "Féltengely, komplett" => [1000, 500000],
    "Lengőkar szilent" => [100, 100000],
    "Első Spirálrugó" => [100, 100000],
    "Lambdaszonda" => [1000, 500000],
    "Fékbetét" => [1000, 500000],
    "Gyújtógyertya" => [50, 50000],
    "Olajszűrő" => [50, 50000],
    "Olajszűrő betét" => [50, 50000],
    "Fékpofa" => [100, 100000],
    "Generátor" => [1000, 500000],
    "Féltengely gumiharang készlet" => [50, 50000],
    "Szivósor tömítés" => [50, 50000],
    "Kuplungszett (3db)" => [1000, 500000],
    "Lengéscsillapító" => [1000, 500000],
    "Gyújtótrafó" => [100, 100000],
    "Hátsó Spirálrugó" => [100, 100000],
    "Visszapillantó tükör, külső" => [1000, 500000],
    "Vezérműszíj készlet" => [1000, 500000],
    "Dugattyúgyűrű" => [50, 50000],
    "Spirálrugó" => [100, 100000],
    "Felső tömítéskészlet" => [1000, 500000],
    "Jeladó, kipufogógáz hőmérséklet" => [100, 100000],
    "Önindító" => [1000, 500000],
    "Hajtókarcsapágy" => [50, 50000],
    "Lengéscsillapító porvédő+ütköz" => [50, 50000],
    "Fékkopás jelző" => [50, 50000],
    "Klímakompresszor" => [1000, 500000],
    "Féltengelycsukló készlet" => [100, 100000],
    "Kerékcsapágy aggyal" => [1000, 500000],
    "Szelep" => [50, 50000],
    "Vezetőgörgő, hosszbordás szíj" => [100, 100000],
    "Jeladó, főtengely (holtpont jeladó)" => [100, 100000],
    "Üzemanyagszivattyú" => [1000, 500000],
    "Tükörlap" => [50, 50000],
    "Toronyszilent" => [50, 50000],
    "Vezérműszíjkészlet vízpumpával" => [1000, 500000],
    "EGR szelep" => [1000, 500000],
    "Szimmering, főtengely" => [50, 50000],
    "Termosztát" => [100, 100000],
    "Toronycsapágy+szilent" => [100, 100000],
    "Ablakemelő" => [100, 100000],
    "Vezérműlánc hajtás készlet" => [1000, 500000],
    "Tartozékkészlet, tárcsafékbetét" => [100, 100000],
    "Kipufogósor töm." => [50, 50000],
    "Kipufogódob, hátsó" => [100, 100000],
    "Terelőlemez / féktárcsa" => [50, 50000],
    "Főtengelycsapágy" => [100, 100000],
    "Fékmunkahenger" => [50, 50000],
    "Kuplungszett" => [1000, 500000],
    "Irányjelző lámpa, első" => [100, 100000],
    "Töltőlevegő hűtő" => [1000, 500000],
    "Dugattyú" => [100, 200000],
    "Ventilátor, vízhűtő" => [1000, 500000],
    "Vezérműszíj" => [50, 50000],
    "Ventilátor, utastér" => [100, 100000],
    "Izzítógyertya" => [100, 100000],
    "Feszítőgörgő, hosszbordás szíj" => [100, 100000],
    "Ködfényszóró" => [1000, 500000],
    "Üzemanyagszűrő betét" => [50, 50000],
    "Szimmering" => [50, 50000],
    "Gyújtókábel készlet" => [100, 100000],
    "Ajtózár" => [50, 50000],
    "Kuplung főhenger" => [100, 100000],
    "Generátor szabadonfutó" => [50, 50000],
    "Hűtőfolyadék kiegyenlítő tartály" => [50, 50000],
    "Lökhárító" => [10000, 2000000],
    "Termosztát házzal" => [100, 100000],
    "Szíjtárcsa, főtengely" => [100, 100000],
    "Tömítőgyűrű" => [50, 50000],
    "Légtömegmérő házzal" => [1000, 500000],
    "Hengerfej csavar" => [50, 50000],
    "Szervószivattyú, kormányzás" => [1000, 500000],
    "Szelepszár szimmering" => [50, 50000],
    "Olajhűtő" => [100, 100000],
    "Kipufogó tömítés" => [50, 50000],
    "Jeladó, hűtőfolyadék hőm." => [100, 100000],
    "Lengéscsillapító ütköző" => [50, 50000],
    "Feszítőgörgő, vezérműszíj" => [100, 100000],
    "Feszültség szabályzó" => [100, 100000],
    "Féltengelycsukló készlet külső" => [100, 100000],
    "Ékszíj" => [50, 50000],
    "Hosszbordásszíj készlet" => [1000, 500000],
    "Jeladó, vezérműtengely" => [50, 50000],
    "Tartozékkészlet, fékpofa" => [50, 50000],
    "Sárvédő" => [1000, 500000],
    "Fűtőradiátor" => [1000, 500000],
    "Kipufogódob, középső" => [1000, 500000],
    "Kipufogócső" => [100, 100000],
    "Kuplungszett (2db)" => [1000, 500000],
    "Kettős tömegű lendkerék (DMF)" => [1000, 500000],
    "Kipufogó tartó" => [50, 50000],
    "Hátsótengely lengőkar" => [1000, 500000],
    "Patent, karosszéria" => [50, 50000],
    "Olajteknő tömítés" => [50, 50000],
    "Gázteleszkóp, motortér fedél" => [100, 100000],
    "Feszítőkar, hosszbordásszíj" => [100, 100000],
    "Hátsótengely híd szilent" => [50, 50000],
    "Féknyereg dugattyú" => [50, 50000],
    "Szűrő, automataváltó" => [100, 100000],
    "Díszrács, lökhárító" => [1000, 500000],
    "Lökhárító tartó" => [1000, 500000],
    "Fékdob" => [100, 100000],
    "Kapcsoló, kormányoszlop" => [1000, 500000],
    "Kapcsoló, ablakemelő" => [1000, 500000],
    "Kuplung munkahenger" => [1000, 500000],
    "Ablakemelő, motor nélkül" => [100, 100000],
    "Főfékhenger" => [1000, 500000],
    "Üzemanyagcső" => [1000, 500000],
    "Csavar" => [50, 50000],
    "Jeladó, szívócsonk nyomás (MAP)" => [100, 100000],
    "Ablaktörlő motor" => [1000, 500000],
    "Ablaktörlő kar" => [100, 100000],
    "Kinyomócsapágy, hidraulikus" => [1000, 500000],
    "Kormánymű porvédő készlet" => [50, 50000],
];


$nemMegfeleloSorok = [];


for ($i = 1; $i < min(100000,count($csvData)); $i++) {
    $sor = $csvData[$i];
    

    $termekNev = $sor[10]; // terméknév
    $osszeg = (float)$sor[3]; // ar
    
    
    if (isset($alkatreszek[$termekNev])) {
        $tartomany = $alkatreszek[$termekNev];
        $minAr = $tartomany[0];
        $maxAr = $tartomany[1];
        

        if ($osszeg < $minAr) {
            $nemMegfeleloSorok[] = [
                'glid' => $sor[0],
                'cikkszam' => $sor[6],
                'hiba' => 'Túl alacsony ár'
            ];
        } elseif ($osszeg > $maxAr) {
            $nemMegfeleloSorok[] = [
                'glid' => $sor[0],
                'cikkszam' => $sor[6],
                'hiba' => 'Túl magas ár'
            ];
        }
        
    } 
}


if (count($nemMegfeleloSorok) > 0) {
    echo "Amik kiesnek a tartományból\n";
    foreach ($nemMegfeleloSorok as $sor) {
        echo "glid: " . $sor['glid'] . ", articlecode: " . $sor['cikkszam'];
        if (isset($sor['hiba'])) {
            echo " (Hiba: " . $sor['hiba'] . ")";
        }
        echo "\n";
    }
} else {
    echo "Minden összeg az ártartományon belül van.\n";
}





function readResultCsv($filePath) {
    $data = [];
    
    if (file_exists($filePath)) {
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            while (($row = fgetcsv($handle, 0, "\t")) !== FALSE) {
                $data[] = $row; 
            }
            fclose($handle);
        } else {
            echo "Hiba: Nem sikerült megnyitni a CSV fájlt!";
        }
    } else {
        echo "Hiba: A CSV fájl nem létezik az adott elérési úton: $filePath";
    }
    
    return $data;
}



/*
$nums = [1,2,3,4,5];

function szorzas($szamok){
    return $szamok*$szamok;
};

$szamokSzamlalasa = array_map('szorzas',$nums);

print_r($szamokSzamlalasa);

/*
$brands = ['BMW','AUDI','VW','VOLVO'];

function writeCarsName($cars,$c = 'autó'){

    foreach($cars as $car){
        echo "$car márkáju $c" . PHP_EOL;
    };

};

writeCarsName($brands);
writeCarsName($brands, 'kocsi');



/*



*/