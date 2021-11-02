$(document).ready(function () {

    let expenseDataChart = JSON.parse(expensesData);
    let incomesDataChart = JSON.parse(incomesData);

    let expenseDataChartTable = arrayMaker(expenseDataChart);
    let incomesDataChartTable = arrayMaker(incomesDataChart);

    drawChartInView(expenseDataChartTable, document.getElementById('chartExpensesContainer'));
    drawChartInView(incomesDataChartTable, document.getElementById('chartIncomesContainer'));

    $(window).resize(function () {
        drawChartInView(expenseDataChartTable, document.getElementById('chartExpensesContainer'));
        drawChartInView(incomesDataChartTable, document.getElementById('chartIncomesContainer'));
    });
});

let comparator = function compareElement(firstElement, secondElement) {
    if (firstElement[1] < secondElement[1]) return 1;
    if (firstElement[1] > secondElement[1]) return -1;
    return 0;
}

let arrayMaker = function (arrayToFetchData) {
    let table = [
        ['Kategoria', 'Wartość']
    ];
    arrayToFetchData.forEach(element => {
        let isReapeted = false;
        for (let i = 0; i < table.length; i++) {
            if (table.length == 1) {
                table.push([element['name'], 0]);
                isReapeted = true;
                break;
            } else if (element['name'] == table[i][0]) {
                isReapeted = true;
                break;
            }
        }
        if (!isReapeted) {
            table.push([element['name'], 0]);
            isReapeted = false;
        }
    });
    table.forEach(element => {
        for (let i = 0; i < arrayToFetchData.length; i++) {
            if (element[0] == arrayToFetchData[i]['name']) {
                element[1] = parseFloat(element[1]) + parseFloat(arrayToFetchData[i]['amount']);
            }
        }
    });
    table.sort(comparator);

    return table;
};

let drawChartInView = function (table, element) {
    google.charts.load('current', {
        'packages': ['corechart']
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        let data = new google.visualization.arrayToDataTable(table);
        let options = {
            title: '',
            width: '10%',
            height: '420px',
            is3D: true,
            chartArea: {
                backgroundColor: {
                    fill: '#333333',
                    fillOpacity: 0.1
                },
                width: '95%',
                height: 'auto'
            },
        };
        // Instantiate and draw the chart 
        let chart = new google.visualization.PieChart(element);
        chart.draw(data, options);
    }
}