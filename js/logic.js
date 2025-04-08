import * as XLSX from 'xlsx';

async function generateExcelWithChart() {
    
    const fileContent = await window.fs.readFile('stat.csv', { encoding: 'utf8' });
    const Papa = await import('papaparse');
    
    const parsedData = Papa.default.parse(fileContent, {
        header: true,
        delimiter: ";",
        dynamicTyping: true,
        skipEmptyLines: true
    });

    
    const sortedData = parsedData.data.sort((a, b) => b.value - a.value);

    
    const top20Data = sortedData.slice(0, 20);

    
    const ws = XLSX.utils.json_to_sheet(top20Data);

    
    ws['!cols'] = [
        { wch: 20 },
        { wch: 12 },
        { wch: 20 } 
    ];

    
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Data");

    
    const chartSheet = XLSX.utils.aoa_to_sheet([
        ['Bar Chart'],
        ['Name', 'Value'],
        ...top20Data.map(row => [row.name, row.value])
    ]);

    
    XLSX.utils.book_append_sheet(wb, chartSheet, "Chart");

    
    if (!wb.Workbook) wb.Workbook = {};
    if (!wb.Workbook.Views) wb.Workbook.Views = [];
    if (!wb.Workbook.Charts) wb.Workbook.Charts = [];

    
    wb.Workbook.Charts.push({
        name: "Top 20 Values",
        fromSheet: "Chart",
        toSheet: "Chart",
        type: "bar",
        series: [
            {
                name: "Values",
                categories: "Chart!A3:A22",
                values: "Chart!B3:B22"
            }
        ],
        title: {
            text: "Top 20 Values by Name"
        },
        legend: {
            position: "bottom"
        }
    });

    
    const wbout = XLSX.write(wb, {
        bookType: 'xlsx',
        type: 'binary',
        bookSST: false,
        compression: true
    });

    
    const buf = new ArrayBuffer(wbout.length);
    const view = new Uint8Array(buf);
    for (let i = 0; i < wbout.length; i++) view[i] = wbout.charCodeAt(i) & 0xFF;
    
    return buf;
}


generateExcelWithChart();