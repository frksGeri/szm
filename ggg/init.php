<?php

require '../phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

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

$szakalManufacturers = array();

function gyartoGyujtes($conn)
{
    $szakalManufacturers = array();
    $sql = "SELECT * FROM gyartok";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            array_push($szakalManufacturers, new SzakalGyarto($row["nev"], $row["name_short"], $row["gy_kod"], $row["tecdoy_id"], $row["utotag"], $row["matched_strings"]));
        }
    }

    return $szakalManufacturers;
}

function findManufacturer($searchFor, $conn)
{
    global $szakalManufacturers;
    if (count($szakalManufacturers) == 0) 
        $szakalManufacturers = gyartoGyujtes($conn);

    foreach ($szakalManufacturers as $manuf)
    {
        if (in_array($searchFor, $manuf->matchedStrings))
        {
            return $manuf->utotag;
        }
    }

    return "ERROR";
}

function loadSizeData($manufacturer)
{
    $path = 'z:\szerző peti\\' . $manufacturer . '.csv';

    $getData = [];
    if (($file = fopen($path, "r")) != FALSE) {
        $header = fgetcsv($file, 1000, ",");
        while (($data = fgetcsv($file, 1000, ",")) !== false) {

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
    return $sizeData;
}

function debugSheetContent($sheet, $maxRows = 10)
{
    echo "Ellenőrzés fájlba írás előtt:\n";
    $counter = 0;

    foreach ($sheet->getRowIterator() as $row) {
        if ($counter >= $maxRows) break; // Korlátozás a maxRows-ra

        $rowIndex = $row->getRowIndex();
        $cellValues = [];

        foreach ($row->getCellIterator() as $cell) {
            $cellValues[] = $cell->getValue();
        }

        var_dump($cellValues);
        $counter++;
    }
}


function szamolas($newSheet, $counter = 1)
{

    foreach ($newSheet as $key => $value) {
        var_dump($value);

        $counter++;
        if ($counter <= 10) {
            break;
        }
    }
}
function filterSpreadsheetColumns(string $inputFile, array $columnsToKeep, $rowNeed, string $outputSheetName = 'todb'): Spreadsheet
{
    $reader = IOFactory::createReaderForFile($inputFile);
    $reader->setReadDataOnly(true);

    $readFilter = new class($columnsToKeep, $rowNeed) implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {
        private array $columns;
        private int $startRow;

        public function __construct(array $columns, $startRow)
        {
            $this->columns = $columns;
            $this->startRow = $startRow;
        }

        public function readCell($columnAddress, $row, $worksheetName = ''): bool
        {

            if ($row < $this->startRow) {
                return false;
            }
            return in_array($columnAddress, $this->columns);
        }
    };

    $reader->setReadFilter($readFilter);
    $spreadsheet = $reader->load($inputFile);

    $oldSheet = $spreadsheet->getSheet(0);

    $newSpreadsheet = new Spreadsheet();
    $newSheet = $newSpreadsheet->getActiveSheet();
    $newSheet->setTitle($outputSheetName);

    $newRow = 1;

    foreach ($oldSheet->getRowIterator() as $oldRow) {
        $cellIterator = $oldRow->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);

        $hasData = false;

        foreach ($cellIterator as $cell) {
            $column = $cell->getColumn();

            if (in_array($column, $columnsToKeep)) {
                $cellValue = $cell->getValue();
                $newSheet->setCellValueExplicit(
                    $column . $newRow,
                    $cellValue,
                    DataType::TYPE_STRING
                );
                $hasData = true;
            }
        }

        if ($hasData) {
            $newRow++;
        }
    }

    return $newSpreadsheet;
}


function saveSpreadsheetToCsv(
    Spreadsheet $spreadsheet,
    string $outputFileName,
    string $delimiter = ';',
    string $lineEnding = "\r\n",
    int $sheetIndex = 0
): void {
    $writer = new Csv($spreadsheet);
    $writer->setDelimiter($delimiter);
    $writer->setLineEnding($lineEnding);
    $writer->setUseBOM(true);
    $writer->setSheetIndex($sheetIndex);

    $writer->save($outputFileName);

    echo 'kesz a mentes';
}


function connectToDatabase()
{
    $host = '131.0.0.199';
    $username = 'geri';
    $password = '';
    $dbname = 'gerisondump';

    $conn = new mysqli($host, $username, $password, $dbname, 3307);

    if ($conn->connect_error) {
        die("Kapcsolódási hiba: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
    return $conn;
}

$db = connectToDatabase();


function run($query, $params = [])
{
    global $db;

    $stmt = $db->prepare($query);
    if (!$stmt) {
        die("Lekérdezési hiba: " . $db->error);
    }

    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...array_values($params));
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    $stmt->close();
    return true;
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

function convertToNumber1($value)
{
    if (is_numeric($value)) {
        return (float)$value;
    }
    $value = str_replace(',', '.', $value);
    $value = preg_replace('/[^0-9.]/', '', $value);
    return is_numeric($value) ? (float)$value : 0;
}
