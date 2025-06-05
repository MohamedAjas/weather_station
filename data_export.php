<?php
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="weather_data.csv"');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "esp_data";
$conn = new mysqli($servername, $username, $password, $dbname);

$filter = $_GET['filter'] ?? 'all';

$where = "";
if ($filter == 'today') {
    $where = "WHERE DATE(reading_time) = CURDATE()";
} elseif ($filter == 'week') {
    $where = "WHERE YEARWEEK(reading_time, 1) = YEARWEEK(CURDATE(), 1)";
}

$sql = "SELECT id, temperature, humidity, reading_time FROM sensor_data $where ORDER BY id DESC";
$result = $conn->query($sql);

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Temperature (°C)', 'Humidity (%)', 'Timestamp']);
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}
fclose($output);
$conn->close();
?>
