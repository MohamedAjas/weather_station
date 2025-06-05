<!DOCTYPE html>
<html>
<head>
    <title>ESP32 DHT22 Weather Dashboard</title>
    <style>
        /* Your CSS styles here */
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2 { text-align: center; }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .refresh-button { display: block; margin: 10px auto; padding: 10px 15px; }
        .badge { padding: 5px 10px; color: white; border-radius: 4px; font-weight: bold; }
        .temp-badge { background-color: #f39c12; }
        .humidity-badge { background-color: #3498db; }
        .timestamp { font-style: italic; color: #555; }
    </style>
</head>
<body>

    <h2>ESP32 DHT22 Weather Dashboard</h2>

    <button class="refresh-button" onclick="location.reload()"> Refresh Data</button>

    <?php
    // DATABASE CONNECTION AND QUERY (THIS WAS MISSING OR INCOMPLETE)
    $servername = "localhost";
    $username = "root"; // Your database username (default for XAMPP)
    $password = "";     // Your database password (default for XAMPP)
    $dbname = "esp_data"; // The database name you used

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("<p style='color:red; text-align:center;'>Connection failed: " . $conn->connect_error . "</p>");
    }

    // SQL query to select all data from sensor_data table, ordered by ID descending (latest first)
    $sql = "SELECT id, temperature, humidity, reading_time FROM sensor_data ORDER BY id DESC";
    $result = $conn->query($sql); // << THIS LINE DEFINES $result

    // NOW YOU CAN CHECK $result
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
                    <td data-label="ID"><?php echo htmlspecialchars($row["id"]); ?></td>
                    <td data-label="Temperature">
                        <span class="badge temp-badge"><?php echo number_format(floatval($row["temperature"]), 1); ?> °C</span>
                    </td>
                    <td data-label="Humidity">
                        <span class="badge humidity-badge"><?php echo number_format(floatval($row["humidity"]), 1); ?> %</span>
                    </td>
                    <td data-label="Time" class="timestamp"><?php echo date("d M Y, H:i:s", strtotime($row["reading_time"])); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align:center; font-size:1.2em; color:#7f8c8d;">
            <?php
            if ($result === false) {
                // Query itself failed
                echo "Error fetching data: " . htmlspecialchars($conn->error);
            } else {
                // Query was successful but returned no rows
                echo "No sensor data available.";
            }
            ?>
        </p>
    <?php endif; ?>

    <?php
    if (isset($conn)) {
        $conn->close(); // Close the database connection
    }
    ?>

</body>
</html>