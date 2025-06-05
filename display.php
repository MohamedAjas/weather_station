<?php
// database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'esp_data';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Live Weather Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <style>
        body { font-family: Arial; text-align: center; margin: 30px; }
        canvas { max-width: 100%; }
        .controls { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        th, td { border: 1px solid #ccc; padding: 10px; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Weather Station Dashboard</h2>

    <div class="controls">
        <label for="filter">View:</label>
        <select id="filter">
            <option value="today">Today</option>
            <option value="week">This Week</option>
            <option value="all">All</option>
        </select>
        <button onclick="downloadCSV()">Download CSV</button>
        <button onclick="downloadPDF()">Download PDF</button>
    </div>

    <canvas id="weatherChart" height="100"></canvas>
    <div id="minMaxDisplay" style="margin-top:20px; font-weight:bold;"></div>

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
        let weatherChart;

        function fetchData() {
            const filter = document.getElementById('filter').value;

            $.ajax({
                url: 'get_data.php',
                method: 'GET',
                data: { filter },
                dataType: 'json',
                success: function (response) {
                    const labels = response.data.map(row => row.timestamp);
                    const temps = response.data.map(row => parseFloat(row.temperature));
                    const hums  = response.data.map(row => parseFloat(row.humidity));

                    updateChart(labels, temps, hums);
                    updateTable(response.data);
                    updateMinMax(temps, hums);
                }
            });
        }

        function updateChart(labels, tempData, humData) {
            const ctx = document.getElementById('weatherChart').getContext('2d');
            if (weatherChart) weatherChart.destroy();
            weatherChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Temperature (°C)',
                            data: tempData,
                            borderColor: 'orange',
                            fill: false
                        },
                        {
                            label: 'Humidity (%)',
                            data: humData,
                            borderColor: 'blue',
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        function updateTable(data) {
            const tbody = document.querySelector("#dataTable tbody");
            tbody.innerHTML = "";
            data.forEach(row => {
                tbody.innerHTML += `
                    <tr>
                        <td>${row.id}</td>
                        <td>${row.temperature}</td>
                        <td>${row.humidity}</td>
                        <td>${row.timestamp}</td>
                    </tr>`;
            });
        }

        function updateMinMax(temps, hums) {
            const minT = Math.min(...temps).toFixed(1);
            const maxT = Math.max(...temps).toFixed(1);
            const minH = Math.min(...hums).toFixed(1);
            const maxH = Math.max(...hums).toFixed(1);

            document.getElementById("minMaxDisplay").innerText =
                `Min Temp: ${minT}°C | Max Temp: ${maxT}°C | Min Humidity: ${minH}% | Max Humidity: ${maxH}%`;
        }

        function downloadCSV() {
            const filter = document.getElementById('filter').value;
            window.open(`download_csv.php?filter=${filter}`, '_blank');
        }

        function downloadPDF() {
            const doc = new jspdf.jsPDF();
            doc.text("Weather Station Report", 20, 20);
            doc.autoTable({ html: '#dataTable' });
            doc.save("weather_report.pdf");
        }

        document.getElementById('filter').addEventListener('change', fetchData);
        fetchData();
        setInterval(fetchData, 5000); // auto-refresh every 5 seconds
    </script>
</body>
</html>
