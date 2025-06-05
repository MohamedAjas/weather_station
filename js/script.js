function fetchData() {
    const filter = document.getElementById('filter').value;
    fetch('data_fetch.php?filter=' + filter)
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector("#dataTable tbody");
            tbody.innerHTML = "";

            let minTemp = Infinity, maxTemp = -Infinity;
            let minHum = Infinity, maxHum = -Infinity;

            data.forEach(row => {
                const tr = document.createElement("tr");

                const temp = parseFloat(row.temperature);
                const hum = parseFloat(row.humidity);

                minTemp = Math.min(minTemp, temp);
                maxTemp = Math.max(maxTemp, temp);
                minHum = Math.min(minHum, hum);
                maxHum = Math.max(maxHum, hum);

                tr.innerHTML = `
                    <td>${row.id}</td>
                    <td><span class="badge temp-badge">${temp.toFixed(1)} °C</span></td>
                    <td><span class="badge humidity-badge">${hum.toFixed(1)} %</span></td>
                    <td>${row.reading_time}</td>
                `;
                tbody.appendChild(tr);
            });

            document.getElementById("minmax").innerHTML = `
                <strong>Min Temp:</strong> ${minTemp.toFixed(1)} °C |
                <strong>Max Temp:</strong> ${maxTemp.toFixed(1)} °C |
                <strong>Min Hum:</strong> ${minHum.toFixed(1)} % |
                <strong>Max Hum:</strong> ${maxHum.toFixed(1)} %
            `;
        });
}

function downloadCSV() {
    const filter = document.getElementById('filter').value;
    window.location = 'data_export.php?filter=' + filter;
}

function generatePDF() {
    const doc = new jsPDF();
    doc.text("ESP32 Weather Data", 14, 16);
    doc.autoTable({ html: '#dataTable', startY: 20 });
    doc.save("weather_data.pdf");
}

setInterval(fetchData, 5000); // Auto-refresh every 5s
document.getElementById("filter").addEventListener("change", fetchData);
window.onload = fetchData;
