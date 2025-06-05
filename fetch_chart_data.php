<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "esp_data";

$conn = new mysqli($servername, $username, $password, $dbname);

header('Content-Type: application/json');

if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed"]);
    exit;
}

// Get the last 10 records
$sql = "SELECT temperature, humidity, reading_time FROM sensor_data ORDER BY id DESC LIMIT 10";
$result = $conn->query($sql);

$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            "time" => date("H:i:s", strtotime($row["reading_time"])),
            "temperature" => floatval($row["temperature"]),
            "humidity" => floatval($row["humidity"])
        ];
    }
    // Reverse to get oldest to newest
    $data = array_reverse($data);
}

echo json_encode($data);
$conn->close();
?>
