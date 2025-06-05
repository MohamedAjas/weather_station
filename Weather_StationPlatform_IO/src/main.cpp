#include <WiFi.h>
#include <WiFiClient.h> // Though HTTPClient handles client needs, it's fine to include
#include <HTTPClient.h>

#include "DHTesp.h"

const char* serverName = "http://192.168.8.170/weather_station/insert.php"; // REPLACE THIS if using ngrok or if your server IP/path is different

const char* SSID = "Wokwi-GUEST"; // Default for Wokwi online simulator
const char* PASSWORD = "";        // Default for Wokwi

const int DHT_PIN = 18; // GPIO pin for DHT22 data
DHTesp dhtSensor;

void setup() {
  Serial.begin(115200);
  while (!Serial) {
    delay(10); // wait for serial port to connect. Needed for native USB
  }
  Serial.println("\n\nESP32 Weather Station - Initializing...");
  Serial.println("======================================");

  Serial.println("Setting up DHT22 sensor...");
  dhtSensor.setup(DHT_PIN, DHTesp::DHT22);
  if (dhtSensor.getStatus() == DHTesp::ERROR_NONE) {
    Serial.println("DHT22 sensor initialized successfully.");
  } else {
    Serial.print("DHT22 sensor initialization FAILED. Error: ");
    Serial.println(dhtSensor.getStatusString());
    Serial.println("Please check wiring and DHT_PIN.");
    // You might want to halt or implement a retry here if sensor is critical
  }
  Serial.println("--------------------------------------");

  Serial.print("Connecting to WiFi SSID: ");
  Serial.println(SSID);
  WiFi.mode(WIFI_STA);
  WiFi.begin(SSID, PASSWORD);

  unsigned long startTime = millis();
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
    if (millis() - startTime > 20000) { // 20 second timeout for WiFi
        Serial.println("\nFailed to connect to WiFi. Please check SSID/Password or network availability.");
        // Consider a deep sleep and retry, or alternative action
        return; // Or ESP.restart();
    }
  }

  Serial.println("\n--------------------------------------");
  Serial.println("WiFi connected!");
  Serial.print("IP Address: ");
  Serial.println(WiFi.localIP());
  Serial.print("Target Server: ");
  Serial.println(serverName);
  Serial.println("======================================");
  Serial.println();
}

void loop() {
  Serial.println("--- New Loop Iteration ---");
  TempAndHumidity data = dhtSensor.getTempAndHumidity();

  // Check if sensor readings are valid
  if (isnan(data.temperature) || isnan(data.humidity)) {
    Serial.println("Failed to read from DHT sensor! Skipping data send.");
  } else {
    Serial.print("Temperature: ");
    Serial.print(data.temperature, 2); // Format to 2 decimal places
    Serial.println(" °C");
    Serial.print("Humidity: ");
    Serial.print(data.humidity, 1); // Format to 1 decimal place
    Serial.println(" %");

    // Proceed to send data if WiFi is connected
    if (WiFi.status() == WL_CONNECTED) {
      HTTPClient http;
      String serverPath = String(serverName) + "?temperature=" + String(data.temperature, 2) + "&humidity=" + String(data.humidity, 1);
      
      Serial.print("Constructed URL for sending data: ");
      Serial.println(serverPath);

      Serial.println("Initiating HTTP GET request...");
      http.begin(serverPath.c_str()); // Specify URL
      // http.setConnectTimeout(5000); // Optional: set connection timeout in ms
      // http.setTimeout(5000);       // Optional: set response timeout in ms

      int httpResponseCode = http.GET(); // Send HTTP GET request
      
      Serial.print("HTTP Response code: ");
      Serial.println(httpResponseCode);

      if (httpResponseCode > 0) { // Check if got a valid HTTP response
        String payload = http.getString(); // Get the server's response payload
        Serial.println("Server response payload:");
        Serial.println(payload);
        if (httpResponseCode != 200 || payload.indexOf("Error") != -1 || payload.indexOf("failed") != -1) {
            Serial.println("*** WARNING: Server indicated an issue or non-OK response. Check insert.php logs. ***");
        } else if (payload.indexOf("successfully") != -1) {
            Serial.println("Data appears to be sent successfully to server.");
        }
      } else {
        Serial.print("Error on sending GET request. HTTPC_ERROR: ");
        Serial.println(http.errorToString(httpResponseCode).c_str()); // More descriptive ESP32 HTTP client error
      }
      http.end(); // Free resources
    } else {
      Serial.println("WiFi Disconnected. Cannot send data. Attempting to reconnect...");
      WiFi.disconnect(); // Optional: force disconnect
      WiFi.begin(SSID, PASSWORD); // Attempt to reconnect
    }
  }
  
  Serial.println("--------------------------");
  // DHT22 recommendation is to wait at least 2 seconds between readings.
  // Sending data over WiFi also takes time.
  delay(5000); // Wait 5 seconds before next iteration. Adjust as needed.
}