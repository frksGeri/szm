<?php
$db = new mysqli("localhost", "root", "", "gerison_import_data");

if ($db->connect_error) {
    die("kapcsolodási hiba. üzenet: " . $db->connect_error);
}

$sql = "SELECT `name`, `value`, DATE(dateStamp) as dateStamp 
        FROM gerison_import_data.null_hiba 
        where name != 'polcarnagy'
        ORDER BY dateStamp DESC";

$select = $db->query($sql);

$data_by_supplier = [];
$all_dates = [];

if ($select->num_rows > 0) {
    while ($row = $select->fetch_assoc()) {
        $supplier = $row['name'];
        $date = $row['dateStamp'];
      
        if (!isset($data_by_supplier[$supplier])) {
            $data_by_supplier[$supplier] = [];
        }
        $data_by_supplier[$supplier][$date] = $row['value'];
        
        if (!in_array($date, $all_dates)) {
            $all_dates[] = $date;
        }
    }
}

sort($all_dates);

foreach ($data_by_supplier as $supplier => &$data) {
    $labels_for_table = [];
    $values_for_table = [];
    $labels_for_chart = [];
    $values_for_chart = [];

    foreach ($all_dates as $date) {
        $labels_for_chart[] = $date;
        $values_for_chart[] = isset($data[$date]) ? $data[$date] : null;

        if (isset($data[$date])) {
            $labels_for_table[] = $date;
            $values_for_table[] = $data[$date];
        }
    }

    $data = [
        'labels_for_table' => $labels_for_table,
        'values_for_table' => $values_for_table,
        'labels_for_chart' => $labels_for_chart,
        'values_for_chart' => $values_for_chart
    ];
}

$db->close();

$colors = [
    'rgba(0, 123, 255, 1)',
    'rgba(255, 99, 132, 1)',
    'rgba(54, 162, 235, 1)',
    'rgba(255, 206, 86, 1)',
    'rgba(75, 192, 192, 1)'
];

$suppliers = array_keys($data_by_supplier);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kimutatás - Több Beszállító</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        h2 {
            color: #555;
            margin-top: 30px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .control-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin: 20px 0;
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .filter-section {
            flex: 1;
        }
        .filter-section label {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            display: block;
            margin-bottom: 10px;
        }
        .filter-section .filter-inputs {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        .filter-section select {
            width: 100%;
            max-width: 300px;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
            cursor: pointer;
        }
        .filter-section select:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }
        .filter-section input[type="text"],
        .filter-section input[type="date"] {
            width: 100%;
            max-width: 300px;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .filter-section input[type="text"]:focus,
        .filter-section input[type="date"]:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }
        .error-message {
            color: #d32f2f;
            font-size: 14px;
            margin-top: 5px;
            min-height: 20px;
        }
       
        .sort-button {
            margin: 10px 0;
            text-align: right;
        }
        .sort-button button {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .sort-button button:hover {
            background-color: #45a049;
        }
        .no-data {
            text-align: center;
            color: #555;
            font-style: italic;
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table, td, th {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        td:nth-child(2) {
            font-weight: bold;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        canvas {
            max-width: 100%;
            margin: 20px 0;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container" id="content">
        <h1>Nálunk van 0 Kimutatás - Több Beszállító</h1>

        <div class="control-section">
            <div class="filter-section">
                <label>Beszállító és dátum szűrés:</label>
                <div class="filter-inputs">
                    <select id="supplierFilter" onchange="filterSuppliers()">
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?php echo htmlspecialchars($supplier); ?>">
                                <?php echo htmlspecialchars($supplier); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input list="suppliersList" id="supplierSearch" placeholder="Keresés..." oninput="filterSuppliers()">
                    <datalist id="suppliersList">
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?php echo htmlspecialchars($supplier); ?>">
                        <?php endforeach; ?>
                    </datalist>
                    <input type="date" id="startDate" onchange="filterSuppliers()">
                    <input type="date" id="endDate" onchange="filterSuppliers()">
                </div>
                <div id="errorMessage" class="error-message"></div>
            </div>
          
        </div>

        
        <?php foreach ($data_by_supplier as $supplier => $data): ?>
            <div class="supplier-section" data-supplier="<?php echo htmlspecialchars($supplier); ?>">
                <h2><?php echo htmlspecialchars($supplier); ?></h2>
                <div class="sort-button">
                    <button onclick="toggleSortOrder('<?php echo htmlspecialchars($supplier); ?>')">Rendezés váltása</button>
                </div>
                <div class="table-container" data-supplier="<?php echo htmlspecialchars($supplier); ?>">
                    <?php if (count($data['labels_for_table']) > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Dátum</th>
                                    <th>Nálunk van 0 db szám</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                for ($i = 0; $i < count($data['labels_for_table']); $i++) {
                                    echo "<tr>";
                                    echo "<td>" . $data['labels_for_table'][$i] . "</td>";
                                    echo "<td>" . $data['values_for_table'][$i] . "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="no-data">Nincs adat ehhez a beszállítóhoz.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <canvas id="nullHibaChart"></canvas>
    </div>

    <script>
        const allData = <?php echo json_encode($data_by_supplier); ?>;
        const allDates = <?php echo json_encode($all_dates); ?>;
        const suppliersList = <?php echo json_encode($suppliers); ?>;
        
        const chartData = {};
        <?php foreach ($data_by_supplier as $supplier => $data): ?>
            chartData["<?php echo htmlspecialchars($supplier); ?>"] = <?php echo json_encode($data['values_for_chart']); ?>;
        <?php endforeach; ?>
        
        const colors = <?php echo json_encode($colors); ?>;
        const ctx = document.getElementById('nullHibaChart').getContext('2d');
        let nullHibaChart;
        
        function initChart() {
            const datasets = [];
            
            suppliersList.forEach((supplier, index) => {
                const color = colors[index % colors.length];
                datasets.push({
                    label: 'Nálunk van 0 (db) - ' + supplier,
                    data: chartData[supplier],
                    borderColor: color,
                    backgroundColor: color.replace('1)', '0.1)'),
                    pointBackgroundColor: color,
                    pointBorderColor: '#fff',
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    fill: true,
                    tension: 0.4,
                    hidden: true
                });
            });
            
            if (nullHibaChart) {
                nullHibaChart.destroy();
            }
            
            nullHibaChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: allDates,
                    datasets: datasets
                },
                options: {
                    animation: {
                        duration: 2000,
                        easing: 'easeInOutQuad'
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Nálunk van 0 (db)',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                font: {
                                    size: 12
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Dátum',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 12
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false,
                            position: 'top'
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: {
                                size: 14
                            },
                            bodyFont: {
                                size: 12
                            }
                        }
                    }
                }
            });
        }

        let sortOrders = {};

        function filterSuppliers() {
            const supplierFilter = document.getElementById('supplierFilter');
            const supplierSearch = document.getElementById('supplierSearch');
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const errorMessage = document.getElementById('errorMessage');
            let selectedSupplier = supplierSearch.value.trim().toLowerCase();

            if (!selectedSupplier) {
                selectedSupplier = supplierFilter.value.toLowerCase();
            }

            let matchedSupplier = null;
            const allSuppliers = suppliersList.map(s => s.toLowerCase());
            for (let supplier of allSuppliers) {
                if (supplier.includes(selectedSupplier)) {
                    matchedSupplier = supplier;
                    break;
                }
            }

            if (!matchedSupplier) {
                errorMessage.textContent = "Nincs ilyen beszállító.";
                document.querySelectorAll('.supplier-section').forEach(section => {
                    section.classList.add('hidden');
                });
                
                if (nullHibaChart) {
                    nullHibaChart.data.datasets.forEach(dataset => {
                        dataset.hidden = true;
                    });
                    nullHibaChart.update();
                }
                return;
            } else {
                errorMessage.textContent = "";
            }

            const start = startDate ? new Date(startDate) : null;
            const end = endDate ? new Date(endDate) : null;

            document.querySelectorAll('.supplier-section').forEach(section => {
                const supplier = section.dataset.supplier.toLowerCase();
                if (supplier === matchedSupplier) {
                    section.classList.remove('hidden');

                    const tableContainer = section.querySelector('.table-container');
                    const table = tableContainer.querySelector('table');
                    if (!table) return;

                    const tbody = table.querySelector('tbody');
                    const rows = Array.from(tbody.querySelectorAll('tr'));
                    const filteredRows = rows.filter(row => {
                        const dateCell = row.cells[0].textContent;
                        const rowDate = new Date(dateCell);
                        if (start && rowDate < start) return false;
                        if (end && rowDate > end) return false;
                        return true;
                    });

                    const sortOrder = sortOrders[supplier] || 'asc';
                    filteredRows.sort((a, b) => {
                        const dateA = new Date(a.cells[0].textContent);
                        const dateB = new Date(b.cells[0].textContent);
                        return sortOrder === 'asc' ? dateA - dateB : dateB - dateA;
                    });

                    tbody.innerHTML = '';
                    filteredRows.forEach(row => tbody.appendChild(row));
                } else {
                    section.classList.add('hidden');
                }
            });

            if (nullHibaChart) {
                const actualSupplierName = suppliersList.find(s => s.toLowerCase() === matchedSupplier);
                
                nullHibaChart.data.datasets.forEach((dataset, index) => {
                    const datasetSupplier = dataset.label.split(' - ')[1];
                    
                    if (datasetSupplier.toLowerCase() === matchedSupplier) {
                        dataset.hidden = false;
                        
                        if (start || end) {
                            const filteredData = [...chartData[datasetSupplier]].map((value, i) => {
                                const date = new Date(allDates[i]);
                                if (start && date < start) return null;
                                if (end && date > end) return null;
                                return value;
                            });
                            dataset.data = filteredData;
                        } else {
                            dataset.data = chartData[datasetSupplier];
                        }
                    } else {
                        dataset.hidden = true;
                    }
                });
                
                nullHibaChart.update();
            }

            if (supplierSearch.value.trim()) {
                const options = Array.from(supplierFilter.options);
                const matchingOption = options.find(opt => opt.value.toLowerCase() === matchedSupplier);
                if (matchingOption) {
                    supplierFilter.value = matchingOption.value;
                }
            }
        }

        function toggleSortOrder(supplier) {
            sortOrders[supplier] = sortOrders[supplier] === 'asc' ? 'desc' : 'asc';
            filterSuppliers();
        }
        
        window.onload = function() {
            initChart();
            filterSuppliers();
            
            if (nullHibaChart && nullHibaChart.data.datasets.length > 0) {
                nullHibaChart.data.datasets[0].hidden = false;
                nullHibaChart.update();
            }
        };
    </script>
</body>
</html>