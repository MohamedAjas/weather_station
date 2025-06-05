<!DOCTYPE html>
<html>
<head>
    <title>ESP32 Live Weather Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h2 { text-align: center; }
        .controls { text-align: center; margin-bottom: 20px; }
        .controls select, .controls button { margin: 0 5px; padding: 8px 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        .minmax { text-align: center; margin-top: 15px; font-weight: bold; }
        canvas { max-width: 100%; }
    </style>
</head>
<body>

<h2>ESP32 DHT22 Weather Dashboard</h2>
<div class="controls">
    <label for="filter">View:</label>
    <select id="filter">
        <option value="today">Today</option>
        <option value="week">This Week</option>
        <option value="all">All Time</option>
    </select>
    <button onclick="downloadCSV()">Download CSV</button>
    <button onclick="window.print()">Download PDF</button>
</div>
<div class="minmax" id="minmax"></div>
<canvas id="weatherChart" height="100"></canvas>
<table id="dataTable">
    <thead>
        <tr>
            <th>ID</th>
            <th>Temperature (°C)</th>
            <th>Humidity (%)</th>
            <th>Time</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
<script>
const chartCtx = document.getElementById('weatherChart').getContext('2d');
let weatherChart = new Chart(chartCtx, {
    type: 'line',
    data: { labels: [], datasets: [
        { label: 'Temperature (°C)', borderColor: 'orange', data: [], fill: false },
        { label: 'Humidity (%)', borderColor: 'blue', data: [], fill: false }
    ] },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});

function fetchData() {
    const filter = document.getElementById('filter').value;
    fetch(`data_fetch.php?filter=${filter}`)
        .then(response => response.json())
        .then(data => {
            updateChart(data);
            updateTable(data);
            updateMinMax(data);
        });
}

function updateChart(data) {
    const labels = data.map(row => row.reading_time);
    const temps = data.map(row => parseFloat(row.temperature));
    const hums = data.map(row => parseFloat(row.humidity));

    weatherChart.data.labels = labels;
    weatherChart.data.datasets[0].data = temps;
    weatherChart.data.datasets[1].data = hums;
    weatherChart.update();
}

function updateTable(data) {
    const tbody = document.querySelector('#dataTable tbody');
    tbody.innerHTML = '';
    data.forEach(row => {
        tbody.innerHTML += `<tr><td>${row.id}</td><td>${row.temperature}</td><td>${row.humidity}</td><td>${row.reading_time}</td></tr>`;
    });
}

function updateMinMax(data) {
    if (!data.length) return;
    const temps = data.map(row => parseFloat(row.temperature));
    const hums = data.map(row => parseFloat(row.humidity));
    const minmaxDiv = document.getElementById('minmax');
    minmaxDiv.innerHTML = `Min Temp: ${Math.min(...temps).toFixed(1)} °C, Max Temp: ${Math.max(...temps).toFixed(1)} °C | Min Humidity: ${Math.min(...hums).toFixed(1)} %, Max Humidity: ${Math.max(...hums).toFixed(1)} %`;
}

function downloadCSV() {
    const filter = document.getElementById('filter').value;
    window.location.href = `download_csv.php?filter=${filter}`;
}

setInterval(fetchData, 5000); // Update every 5 seconds
fetchData(); // Initial call
</script>

</body>
</html>