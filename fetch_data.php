<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "esp_data";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo "<p style='color:red; text-align:center;'>Connection failed: " . $conn->connect_error . "</p>";
    exit;
}

$sql = "SELECT id, temperature, humidity, reading_time FROM sensor_data ORDER BY id DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0):
?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Temperature (°C)</th>
                <th>Humidity (%)</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row["id"]); ?></td>
                <td><span class="badge temp-badge"><?php echo number_format(floatval($row["temperature"]), 1); ?> °C</span></td>
                <td><span class="badge humidity-badge"><?php echo number_format(floatval($row["humidity"]), 1); ?> %</span></td>
                <td class="timestamp"><?php echo date("d M Y, H:i:s", strtotime($row["reading_time"])); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p style='text-align:center; font-size:1.2em; color:#7f8c8d;'>No sensor data available.</p>
<?php endif;

$conn->close();
?>
