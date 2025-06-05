<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "esp_data";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode([]));
}

$filter = $_GET['filter'] ?? 'today';
$where = "";
if ($filter === 'today') {
    $where = "WHERE DATE(reading_time) = CURDATE()";
} elseif ($filter === 'week') {
    $where = "WHERE YEARWEEK(reading_time, 1) = YEARWEEK(CURDATE(), 1)";
} // else all time (no filter)

$sql = "SELECT id, temperature, humidity, reading_time FROM sensor_data $where ORDER BY id DESC";
$result = $conn->query($sql);
$data = [];

if ($result) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($data);
$conn->close();
?>