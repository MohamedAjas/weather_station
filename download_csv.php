<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'esp_data';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("DB Error");

$filter = $_GET['filter'] ?? 'today';

$where = "1";
if ($filter == 'today') {
    $where = "DATE(timestamp) = CURDATE()";
} elseif ($filter == 'week') {
    $where = "timestamp >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
}

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=weather_data.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Temperature (°C)', 'Humidity (%)', 'Timestamp']);

$result = $conn->query("SELECT * FROM weather_data WHERE $where ORDER BY timestamp DESC");
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}
fclose($output);
exit;
?>
