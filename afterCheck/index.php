<?php

header('Content-Type: text/html; charset=utf-8');


ob_implicit_flush(true);
ob_end_flush();

set_time_limit(0);
ini_set('memory_limit', '500000M');

$forrasMappa = 'Z:\shodan_vevo_arlistak\magyar';
$celMappa = 'C:\Users\LP-GERGO\Desktop\afterCheck';
$fajlNevek = ['cikkek_hun_all.csv.zip', 'E330_arlista_nagyker_all.csv.zip'];

masolFajlok($forrasMappa, $celMappa, $fajlNevek);

sleep(6);

//top150
$alkatreszek1 = [
    "Levegőszűrő" => [50, 135000],
    "Lengőkar" => [300, 500000],
    "Első fékbetét" => [100, 310000],
    "Első féktárcsa" => [1000, 500000],
    "Pollenszűrő" => [50, 62000],
    "Féknyereg" => [100, 710000],
    "Stabilizátor kar" => [50, 110000],
    "Kormányösszekötő gömbfej" => [50, 60000],
    "Hosszbordásszíj" => [50, 50000],
    "Motortartó bak" => [400, 500000],
    "Hátsó féktárcsa" => [1000, 500000],
    "Kerékcsapágy készlet" => [100, 500000],
    "Vízhűtő" => [1000, 700000],
    "Gumifékcső" => [50, 50000],
    "Kézifék bowden" => [100, 100000],
    "ABS jeladó" => [650, 500000],
    "Hátsó fékbetét" => [100, 100000],
    "Hengerfej töm." => [100, 220000],
    "Első Lengéscsillapító" => [600, 500000],
    "Stabilizátor szilent" => [50, 140000],
    "Üzemanyagszűrő" => [100, 180000],
    "Gázteleszkóp, csomagtér fedél" => [100, 120000],
    "Vízcső" => [100, 100000],
    "Fényszóró" => [650, 2000000],
    "Szilentblokk" => [50, 110000],
    "Féknyereg javítókészlet" => [30, 150000],
    "Vízpumpa" => [200, 500000],
    "Kormányösszekötő belső" => [100, 100000],
    "Szelepfedél tömítés" => [40, 100000],
    "Hátsó Lengéscsillapító" => [1000, 500000],
    "Lengőkar gömbfej" => [50, 130000],
    "Ablaktörlő lapát" => [50, 66000],
    "Hátsó lámpa" => [400, 500000],
    "Töltőlevegő hűtő cső" => [300, 500000],
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
    "Fékpofa" => [100, 125000],
    "Generátor" => [150, 580000],
    "Féltengely gumiharang készlet" => [50, 59000],
    "Szivósor tömítés" => [10, 100000],
    "Kuplungszett (3db)" => [1000, 1000000],
    "Lengéscsillapító" => [1000, 500000],
    "Gyújtótrafó" => [100, 100000],
    "Hátsó Spirálrugó" => [100, 100000],
    "Visszapillantó tükör, külső" => [1000, 500000],
    "Vezérműszíj készlet" => [1000, 500000],
    "Dugattyúgyűrű" => [50, 145000],
    "Spirálrugó" => [80, 300000],
    "Felső tömítéskészlet" => [800, 600000],
    "Jeladó, kipufogógáz hőmérséklet" => [100, 152000],
    "Önindító" => [1000, 500000],
    "Hajtókarcsapágy" => [50, 135000],
    "Lengéscsillapító porvédő+ütköz" => [50, 500000],
    "Fékkopás jelző" => [50, 90000],
    "Klímakompresszor" => [1000, 600000],
    "Féltengelycsukló készlet" => [100, 170000],
    "Kerékcsapágy aggyal" => [400, 500000],
    "Szelep" => [50, 230000],
    "Vezetőgörgő, hosszbordás szíj" => [100, 100000],
    "Jeladó, főtengely (holtpont jeladó)" => [100, 110000],
    "Üzemanyagszivattyú" => [1000, 500000],
    "Tükörlap" => [50, 190000],
    "Toronyszilent" => [50, 66000],
    "Vezérműszíjkészlet vízpumpával" => [1000, 500000],
    "EGR szelep" => [800, 850000],
    "Szimmering, főtengely" => [50, 110000],
    "Termosztát" => [500, 200000],
    "Toronycsapágy+szilent" => [100, 100000],
    "Ablakemelő" => [250, 210000],
    "Vezérműlánc hajtás készlet" => [1000, 500000],
    "Tartozékkészlet, tárcsafékbetét" => [100, 100000],
    "Kipufogósor töm." => [50, 50000],
    "Kipufogódob, hátsó" => [100, 750000],
    "Terelőlemez / féktárcsa" => [50, 50000],
    "Főtengelycsapágy" => [100, 221000],
    "Fékmunkahenger" => [50, 375000],
    "Kuplungszett" => [1000, 1500000],
    "Irányjelző lámpa, első" => [100, 230000],
    "Töltőlevegő hűtő" => [1000, 500000],
    "Dugattyú" => [100, 580000],
    "Ventilátor, vízhűtő" => [1000, 500000],
    "Vezérműszíj" => [50, 70000],
    "Ventilátor, utastér" => [100, 170000],
    "Izzítógyertya" => [100, 162000],
    "Feszítőgörgő, hosszbordás szíj" => [100, 100000],
    "Ködfényszóró" => [1000, 500000],
    "Üzemanyagszűrő betét" => [50, 150000],
    "Szimmering" => [50, 100000],
    "Gyújtókábel készlet" => [100, 160000],
    "Ajtózár" => [100, 500000],
    "Kuplung főhenger" => [100, 100000],
    "Generátor szabadonfutó" => [50, 87000],
    "Hűtőfolyadék kiegyenlítő tartály" => [50, 150000],
    "Lökhárító" => [3000, 2000000],
    "Termosztát házzal" => [100, 150000],
    "Szíjtárcsa, főtengely" => [100, 220000],
    "Tömítőgyűrű" => [4, 110000],
    "Légtömegmérő házzal" => [1000, 540000],
    "Hengerfej csavar" => [50, 150000],
    "Szervószivattyú, kormányzás" => [1000, 800000],
    "Szelepszár szimmering" => [50, 61000],
    "Olajhűtő" => [100, 475000],
    "Kipufogó tömítés" => [50, 50000],
    "Jeladó, hűtőfolyadék hőm." => [100, 100000],
    "Lengéscsillapító ütköző" => [50, 55000],
    "Feszítőgörgő, vezérműszíj" => [100, 125000],
    "Feszültség szabályzó" => [100, 100000],
    "Féltengelycsukló készlet külső" => [100, 100000],
    "Ékszíj" => [50, 50000],
    "Hosszbordásszíj készlet" => [1000, 500000],
    "Jeladó, vezérműtengely" => [50, 50000],
    "Tartozékkészlet, fékpofa" => [50, 110000],
    "Sárvédő" => [1000, 500000],
    "Fűtőradiátor" => [1000, 500000],
    "Kipufogódob, középső" => [1000, 500000],
    "Kipufogócső" => [100, 700000],
    "Kuplungszett (2db)" => [1000, 500000],
    "Kettős tömegű lendkerék (DMF)" => [1000, 1100000],
    "Kipufogó tartó" => [50, 50000],
    "Hátsótengely lengőkar" => [1000, 500000],
    "Patent, karosszéria" => [20, 50000],
    "Olajteknő tömítés" => [10, 70000],
    "Gázteleszkóp, motortér fedél" => [100, 100000],
    "Feszítőkar, hosszbordásszíj" => [100, 150000],
    "Hátsótengely híd szilent" => [50, 50000],
    "Féknyereg dugattyú" => [50, 50000],
    "Szűrő, automataváltó" => [100, 190000],
    "Díszrács, lökhárító" => [400, 580000],
    "Lökhárító tartó" => [300, 500000],
    "Fékdob" => [100, 130000],
    "Kapcsoló, kormányoszlop" => [1000, 500000],
    "Kapcsoló, ablakemelő" => [600, 500000],
    "Kuplung munkahenger" => [1000, 500000],
    "Ablakemelő, motor nélkül" => [1000, 120000],
    "Főfékhenger" => [1000, 500000],
    "Üzemanyagcső" => [100, 500000],
    "Csavar" => [10, 50000],
    "Jeladó, szívócsonk nyomás (MAP)" => [100, 116000],
    "Ablaktörlő motor" => [700, 500000],
    "Ablaktörlő kar" => [100, 100000],
    "Kinyomócsapágy, hidraulikus" => [700, 500000],
    "Kormánymű porvédő készlet" => [50, 70000],
];

//top150 utáni 150
$alkatreszek2 = [
    "Klímaszárító patron" => [100, 100000],
    "Kormányösszekötő külső+belső" => [100, 100000],
    "Alsó tömítéskészlet" => [1000, 500000],        
    "Fojtószelep" => [500, 500000],                 
    "Turbófeltöltő" => [10000, 2000000],            
    "Váltótartó bak" => [100, 100000],              
    "Szellőzőventilátor ellenálás" => [100, 100000],
    "Teljes tömítéskészlet" => [250, 1000000],      
    "Kerékagy" => [10, 500000],                     
    "Kipufogóbilincs" => [50, 50000],                   
    "Vízcsőcsonk" => [100, 100000],                     
    "Toronycsapágy" => [100, 100000],                   
    "Fényszórómosó fúvóka" => [100, 100000],            
    "Parkolószenzor" => [10, 500000],                   
    "Kuplungbowden" => [100, 100000],                   
    "Porlasztócsúcs" => [1000, 500000],                 
    "Tükör borítás, külső" => [10, 500000],             
    "Szelepvezető" => [100, 100000],                    
    "Önindító bendix" => [10, 500000],                  
    "Befecskendező" => [10, 500000],                    
    "Szerszám" => [50, 1000000],                        
    "Vezetőgörgő, vezérműszíj" => [100, 100000],        
    "Hátsótengely stabilizátor" => [100, 100000],       
    "Olajteknő" => [10, 500000],                        
    "Féklámpa kapcsoló" => [50, 58000],                 
    "Kardánfelfüggesztő csapágy" => [10, 500000],       
    "Olajszivattyú" => [250, 1000000],                  
    "Váltóbowden" => [1000, 500000],                    
    "Kormánymű porvédő" => [50, 50000],                 
    "Szelepfedél" => [1000, 500000],                    
    "Szíjfeszítő, hosszbordás szíj" => [1000, 500000],  
    "Légrugó" => [250, 1000000],                        
    "Kuplungszett (4db) DMF" => [250, 1000000],         
    "Összekötőrúd" => [10, 500000],                     
    "Kipufogócsonk tömítés" => [50, 50000],             
    "Vezérműtengely" => [100, 350000],                  
    "Diódahíd" => [50, 50000],                          
    "Olajleengedő csavar" => [50, 50000],               
    "Fényszórómagasság állító" => [10, 500000],         
    "Olajnyomás kapcsoló" => [50, 50000],               
    "Toronyszilent készlet" => [10, 500000],            
    "Szimmering, differenciálmű" => [50, 50000],        
    "Kartergázcső, motorblokk" => [100, 100000],        
    "Tömítéskészlet, turbofeltöltő" => [10, 500000],    
    "Kormánymű" => [50, 1000000],                       
    "Izzó, halogén" => [100, 100000],                   
    "Kinyomócsapágy, mechanikus" => [10, 500000],       
    "Ablaktörlő mechanika" => [10, 500000],             
    "Vezérműlánc" => [10, 500000],                      
    "Hidrotőke" => [100, 100000],                       
    "Jeladó, kipufogógáz-nyomás" => [1000, 500000],     
    "Hűtőventillátor kuplung" => [1000, 500000],        
    "Rendszámtábla világítás" => [10, 500000],          
    "Katalizátor" => [250, 1000000],                    
    "Stabilizátor rúd csapágyazás" => [50, 50000],      
    "Díszrács" => [10, 500000],                         
    "Szolenoid szelep, bütyköstengely állítás" => [250, 1000000],
    "Turbó beszerelési készlet" => [1000, 500000],      
    "Hátsótengely lengőkarszilent" => [100, 100000],    
    "Turbó tömítés" => [10, 500000],                    
    "Kerékcsavar" => [50, 50000],                       
    "Tömítés, olajhűtő" => [50, 1000000],              
    "Szelephimba" => [10, 500000],                     
    "Tolóajtó görgő" => [100, 100000],                 
    "Ablakmosó motor" => [10, 500000],                 
    "Turbó olajcső" => [100, 100000],                  
    "Féknyereg vezető készlet" => [100, 100000],       
    "Ajtóhatároló" => [100, 100000],                   
    "Légtömegmérő ház nélkül" => [1000, 500000],       
    "Laprugó szilent" => [100, 100000],                
    "Flexibilis kipufogócső" => [10, 500000],          
    "Szívócső, légszűrő" => [100, 100000],             
    "Tolatólampa kapcsoló" => [10, 500000],            
    "Küszöbborítás" => [1000, 500000],                 
    "Hardy tárcsa, kardántengely" => [10, 500000],     
    "Nyomás jelátalakító" => [10, 500000],             
    "Lengéscsillapító porvédő" => [10, 500000],        
    "Mágneskapcsoló alkatrészek" => [10, 500000],      
    "Kerékcsapágy" => [10, 500000],                    
    "Kuplungtárcsa" => [1000, 500000],                 
    "Hengerpersely" => [10, 500000],                   
    "Izzó, egyéb" => [100, 100000],                    
    "Ajtó kilincs" => [50, 50000],                     
    "Csomagtér ajtózár" => [800, 500000],             
    "Gömbfej" => [100, 100000],                        
    "Ventilátor kapcsoló" => [50, 50000],              
    "Szimmering, vezérműtengely" => [100, 100000],     
    "Kiegyenlítő tartály sapka" => [50, 50000],        
    "Homlokfal és alkatrészei" => [10, 500000],        
    "Burkolat, motor alatti" => [10, 500000],          
    "Tömítés, egyéb" => [10, 500000],                  
    "Csavar anya" => [10, 50000],                      
    "Termosztát+O gyűrű" => [50, 50000],               
    "Csonkállvány" => [10, 500000],                    
    "Fékszerelék" => [50, 50000],                      
    "Üzemanyagszivattyú, komplett" => [1000, 500000],  
    "Tágulószelep, klímaberendezés" => [10, 500000],   
    "Vákuumpumpa" => [1000, 500000],                    
    "Tömítés, EGR szelep" => [100, 100000],             
    "Nívópálca" => [50, 50000],                         
    "Féltengelycsukló készlet belső" => [100, 130000],  
    "Generator burkolat" => [50, 50000],                
    "Sárvédő jav. ív" => [50, 50000],                   
    "Nyomáskapcsoló, klíma" => [100, 100000],           
    "Féknyereg tartó" => [100, 100000],                 
    "Kipufogógáz hűtő" => [250, 1000000],               
    "Jeladó, üzemanyag nyomás" => [1000, 500000],       
    "Szívósor" => [100, 100000],                        
    "Váltógomb" => [10, 500000],                        
    "Féltengely gumiharang" => [50, 50000],             
    "Fémfékcső" => [100, 100000],                       
    "Tömítőgyűrű, befecskendező" => [10, 500000],       
    "Xenon ballaszt" => [1000, 500000],                 
    "Akkumulátor, szgk" => [1000, 500000],              
    "Mágneskapcsoló, önindító" => [100, 100000],        
    "Szimmering, sebességváltó" => [50, 50000],         
    "Izzó, irányjelző" => [50, 50000],                  
    "Relé" => [10, 500000],                             
    "Önindító javítókészlet" => [10, 500000],           
    "Lendkerék" => [250, 1000000],                      
    "Tengelycsonk, felfüggesztés" => [10, 500000],      
    "Elektromos teleszkóp" => [1000, 500000],           
    "Motorházfedél" => [250, 1000000],                  
    "Önindító szénkefe tartó" => [10, 500000],          
    "Pótféklámpa" => [100, 100000],                     
    "O-Gyűrű" => [10, 500000],                          
    "Önindító szabadonfutó" => [100, 100000],           
    "Olajleengedő csavaralátét" => [20, 50000],         
    "Ellenállás, szellőzés" => [100, 100000],           
    "Rugótányér" => [100, 100000],                      
    "Jeladó, kopogás" => [100, 100000],                 
    "Vízpumpa tömítés" => [100, 100000],                
    "Stabilizátor javító készlet" => [100, 100000],     
    "Főfékhenger felújító készlet" => [100, 100000],    
    "Bordáskerék, vezérműtengely" => [10, 500000],      
    "Irányjelző lámpa, oldalsó" => [50, 50000],         
    "Fényszóró, univerzális" => [50, 1000000],          
    "Generátor forgórész" => [10, 500000],              
    "Részecskeszűrő, kipufogó" => [50, 1000000],        
    "Stabilizátor kar készlet" => [50, 50000],          
    "Vezérműlánc feszítő" => [10, 500000],              
    "Dugattyú gyűrű nélkül" => [550, 500000],          
    "Támcsapágy" => [50, 50000],                       
    "Kábel készlet" => [50, 1000000],                  
    "Jeladó, motorolajszint" => [100, 100000],         
    "Vonóhorog" => [10, 500000],                       
    "Javítókészlet, generátor" => [50, 50000],         
    "Javítókészlet, felfüggesztés" => [250, 1000000],  
    "Féltengelycsukló" => [1000, 500000],              
    "Olajszűrőház tömítés" => [50, 50000],             
];

$alkatreszek = array_merge($alkatreszek1,$alkatreszek2);

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


echo "Python szkript futtatása kezdődik...\n";
runPythonScript();

sleep(70);

$resultFile = "c:\Users\LP-GERGO\Desktop\\afterCheck\\result.csv";
echo "CSV fájl beolvasása...<br>";

if(!isset($csvData)){
    sleep(50);
    $csvData = readResultCsv($resultFile);
}

$csvData = readResultCsv($resultFile);


$nemMegfeleloSorok = [];

$companies = array_flip($companies);

$kivetelek = [
    '80/75',
    '80/75B',
    'EZC-TY-093_NTY',
    'AMP1502_MM',
    'J3272000_HTB',
    '00342918_TED',
    'AC2038_VW',
    '31430316_VOLV',
    '1835102020_WABCO',
    'KBL19602.0-1517_BERAL',
    '837.640_ELR',
    'FP-1149437',
    '54472_NRF',
    '4N1903028A_VOLK',
    



];

for ($i = 1; $i < count($csvData); $i++) {
    $sor = $csvData[$i];
    $termekNev = $sor[10]; 
    $osszeg = (float)$sor[3]; 
    $stockFromSor = trim($sor[1]);

    if (in_array($sor[6], $kivetelek)) {
        continue;
    }

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
$date = date('Y-m-d');

if (count($nemMegfeleloSorok) > 0) {
    $exportDir = "z:\GZS\utoellenorzes\\";
    if (!is_dir($exportDir)) {
        mkdir($exportDir, 0777, true);
    }
   // $exportFilePath = "$exportDir\\not_good_prices_products_TTTT_" . $date . ".csv";
    $exportFilePath = "$exportDir\\not_good_prices_products_TTTT_top300_3" . $date . ".csv";
    $exportHandle = fopen($exportFilePath, "w");
    
    if ($exportHandle === false) {
        die("Hiba: Nem sikerült megnyitni az export fájlt: $exportFilePath");
    }

    echo "Nem megfelelő sorok: <br><br>";
    echo "Ennyi darab: " . count($nemMegfeleloSorok) . "<br><br>";
    fprintf($exportHandle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

    fputcsv($exportHandle, ["Összesen " . count($nemMegfeleloSorok) . " db feltételezhetően nem megfelelő árazású termék van."], ';');
    fputcsv($exportHandle, ['GLID', 'Cikkszám', 'Gyártó', 'Termékcsoport', 'STOCK', 'Beszállító', 'Ár', 'Hiba'], ';');

    foreach ($nemMegfeleloSorok as $sor) {
        if ($sor['ar'] == 0 || $sor['ar'] == '0' || $sor['ar'] == 1 || $sor['ar'] == '1') {
            echo "<div style='background-color: #ff0000; font-weight:bold'> (Hiba: " . $sor['hiba'] . ") - glid: " . $sor['glid'] . ", articlecode: " . $sor['cikkszam'] . ", gyarto: " . $sor['gyarto'] . ", tcs: " . $sor['tcs'] .
                ", beszallito stock: " . $sor['beszallito_stock'] ." beszallito: ". $sor['beszallito'] . ", ar: " . $sor['ar'];
            echo "</div><br>";
        } else {
            echo "(Hiba: " . $sor['hiba'] . ") - glid: " . $sor['glid'] . ", articlecode: " . $sor['cikkszam'] . ", gyarto: " . $sor['gyarto'] . ", tcs: " . $sor['tcs'] .
                ", beszallito stock: " . $sor['beszallito_stock'] ." beszallito: ". $sor['beszallito'] . ", ar: " . $sor['ar'];
            echo "<br>";
        }
        fputcsv($exportHandle, [
            $sor['glid'],
            $sor['cikkszam'],
            $sor['gyarto'],
            $sor['tcs'],
            $sor['beszallito_stock'],
            $sor['beszallito'],
            $sor['ar'],
            $sor['hiba']
        ], ';');
    }
    fclose($exportHandle);
} else {
    echo "Minden összeg az ártartományon belül van.\n";
}


function masolFajlok($forrasMappa, $celMappa, $fajlNevek)
{

    if (!is_dir($celMappa)) {
        mkdir($celMappa, 0777, true);
    }

    foreach ($fajlNevek as $fajlNev) {
        $forrasFajl = rtrim($forrasMappa, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fajlNev;
        $celFajl = rtrim($celMappa, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fajlNev;

        if (file_exists($forrasFajl)) {
            if (copy($forrasFajl, $celFajl)) {
                echo "A(z) {$fajlNev} sikeresen átmásolva!<br><br>" . PHP_EOL;
            } else {
                echo "Hiba történt a(z) {$fajlNev} másolása közben!\n";
            }
        } else {
            echo "A(z) {$fajlNev} nem található a forrásmappában!\n";
        }
    }
}

function runPythonScript()
{
    $pythonPath = "python";
    $scriptPath = "c:\\Users\\LP-GERGO\\Desktop\\afterCheck\\arlista_kiegeszites.py";

    $command = escapeshellcmd("$pythonPath $scriptPath");
    $output = shell_exec($command);

    echo "<pre>Python kimenet:\n";
    echo $output ? $output : "Nincs kimenet a Python szkripttől";
    echo "</pre>";

    return $output;
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
