<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "esp_data";

$conn = new mysqli($servername, $username, $password, $dbname);

header('Content-Type: application/json');

if ($conn->connect_error) {
    echo json_encode(["error" => "DB connection failed"]);
    exit;
}

$sql = "SELECT temperature, humidity, reading_time FROM sensor_data ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
        "temperature" => number_format(floatval($row["temperature"]), 1),
        "humidity" => number_format(floatval($row["humidity"]), 1),
        "time" => date("d M Y, H:i:s", strtotime($row["reading_time"]))
    ]);
} else {
    echo json_encode(["temperature" => "--", "humidity" => "--", "time" => "No data"]);
}

$conn->close();
?>
