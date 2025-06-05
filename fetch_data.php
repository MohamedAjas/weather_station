<?php
$filter = $_GET['filter'] ?? 'today';

$conn = new mysqli("localhost", "root", "", "weather_station");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$where = "";
if ($filter == "today") {
    $where = "WHERE DATE(timestamp) = CURDATE()";
} elseif ($filter == "week") {
    $where = "WHERE YEARWEEK(timestamp, 1) = YEARWEEK(CURDATE(), 1)";
}

$sql = "SELECT * FROM weather_data $where ORDER BY timestamp DESC LIMIT 100";
$result = $conn->query($sql);

$data = [];
while($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode(array_reverse($data));
$conn->close();
?>
