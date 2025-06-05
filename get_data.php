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

$sql = "SELECT * FROM weather_data WHERE $where ORDER BY timestamp DESC";
$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode(['data' => $data]);
?>
