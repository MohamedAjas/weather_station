<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "esp_data";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$filter = $_GET['filter'] ?? 'today';
$where = "";
if ($filter === 'today') {
    $where = "WHERE DATE(reading_time) = CURDATE()";
} elseif ($filter === 'week') {
    $where = "WHERE YEARWEEK(reading_time, 1) = YEARWEEK(CURDATE(), 1)";
}

$sql = "SELECT id, temperature, humidity, reading_time FROM sensor_data $where ORDER BY id DESC";
$result = $conn->query($sql);

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="weather_data.csv"');

$output = fopen("php://output", "w");
fputcsv($output, ['ID', 'Temperature (°C)', 'Humidity (%)', 'Time']);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [$row['id'], $row['temperature'], $row['humidity'], $row['reading_time']]);
    }
}

fclose($output);
$conn->close();
?>