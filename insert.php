<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "esp_data";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check if the request method is GET and if temperature and humidity are set
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        // Check if the GET parameters are set before trying to access them
        if (isset($_GET["temperature"]) && isset($_GET["humidity"])) {
            $temperature = $_GET["temperature"];
            $humidity = $_GET["humidity"];

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // It's good practice to also add a timestamp for when the data was inserted
            // For example, if your table has a 'reading_time' column:
            // $sql = "INSERT INTO sensor_data (temperature, humidity, reading_time) VALUES ('$temperature', '$humidity', NOW())";
            // The original code only inserts temperature and humidity:
            $sql = "INSERT INTO sensor_data (temperature, humidity) VALUES ('$temperature', '$humidity')";

            if ($conn->query($sql) === TRUE) {
                echo "New record created successfully";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Error: Temperature or humidity not set in GET request.";
        }
    } else {
        echo "Error: Invalid request method. Please use GET.";
    }

    $conn->close();
?>