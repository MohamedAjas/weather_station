<!DOCTYPE html>
<html>
<head>
    <title>ESP32 Live Dashboard with Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f4f4;
            padding: 20px;
            text-align: center;
        }

        .card {
            display: inline-block;
            padding: 20px;
            margin: 15px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .value {
            font-size: 2em;
            font-weight: bold;
        }

        canvas {
            max-width: 700px;
            margin: 40px auto;
            display: block;
        }

        #status {
            margin-top: 10px;
            font-size: 0.9em;
            color: green;
        }
    </style>
</head>
<body>

    <h2>ESP32 - Live Temperature & Humidity Dashboard</h2>

    <div class="card">
        <div>Temperature</div>
        <div class="value" id="tempValue">-- °C</div>
    </div>
    <div class="card">
        <div>Humidity</div>
        <div class="value" id="humidValue">-- %</div>
    </div>
    <div class="card">
        <div id="timeValue">Reading time: --</div>
    </div>

    <div id="status">Last updated: Never</div>

    <!-- Charts -->
    <canvas id="tempChart"></canvas>
    <canvas id="humidChart"></canvas>

    <script>
        let tempChart, humidChart;

        function initCharts() {
            const tempCtx = document.getElementById('tempChart').getContext('2d');
            const humidCtx = document.getElementById('humidChart').getContext('2d');

            tempChart = new Chart(tempCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Temperature (°C)',
                        data: [],
                        borderColor: '#e67e22',
                        backgroundColor: 'rgba(230, 126, 34, 0.2)',
                        fill: true,
                        tension: 0.3
                    }]
                }
            });

            humidChart = new Chart(humidCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Humidity (%)',
                        data: [],
                        borderColor: '#3498db',
                        backgroundColor: 'rgba(52, 152, 219, 0.2)',
                        fill: true,
                        tension: 0.3
                    }]
                }
            });
        }

        function updateLatest() {
            fetch('fetch_latest.php')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('tempValue').innerText = data.temperature + ' °C';
                    document.getElementById('humidValue').innerText = data.humidity + ' %';
                    document.getElementById('timeValue').innerText = 'Reading time: ' + data.time;
                    document.getElementById('status').innerText = 'Last updated: ' + new Date().toLocaleTimeString();
                });
        }

        function updateCharts() {
            fetch('fetch_chart_data.php')
                .then(res => res.json())
                .then(data => {
                    const labels = data.map(d => d.time);
                    const temp = data.map(d => d.temperature);
                    const humid = data.map(d => d.humidity);

                    tempChart.data.labels = labels;
                    tempChart.data.datasets[0].data = temp;
                    tempChart.update();

                    humidChart.data.labels = labels;
                    humidChart.data.datasets[0].data = humid;
                    humidChart.update();
                });
        }

        initCharts();
        updateLatest();
        updateCharts();

        setInterval(() => {
            updateLatest();
            updateCharts();
        }, 5000);
    </script>
</body>
</html>
