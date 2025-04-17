<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Water Quality Prediction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

<div class="container mt-5 d-flex justify-content-center">
    <div id="resultCard" style="
        width: 30rem;
        background-color: #fdecea;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        display: none;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    ">
        <div style="padding: 25px;">
            <h5 style="
                color: #b71c1c;
                font-weight: bold;
                margin-bottom: 20px;
                font-size: 20px;
            ">
                Water Quality Result: Poor
            </h5>
            <p id="predictionText" style="
                font-size: 16px;
                margin-bottom: 20px;
            ">
                Fetching data...
            </p>
            <div style="
                background-color: white;
                padding: 14px;
                border-left: 5px solid #b71c1c;
                border-radius: 5px;
                font-size: 15px;
            ">
                Water quality is poor. Avoid drinking or usage.
            </div>
        </div>
    </div>
</div>

<div id="futurePredictionCard" style="
    font-size: 16px;
    text-align: center;
    margin: 20px auto;
    padding: 15px;
    border: 2px solid #0d47a1;
    border-radius: 10px;
    background-color: #e3f2fd;
    display: none;
    max-width: 500px;
">
    Calculating future water quality...
</div>

<div class="container mt-4">
    <h5 class="text-center mb-3">Water Quality Visualization</h5>
    <div class="row justify-content-center">

        <div class="col-md-4 mb-4 d-flex justify-content-center">
            <div class="card shadow-sm p-3" style="width: 100%; max-width: 380px;">
                <div class="card-body d-flex justify-content-center">
                    <canvas id="lineChart" width="350" height="350" style="max-width: 350px; max-height: 350px;"></canvas>
                </div>
            </div>
        </div>

        <hr>

        <div class="col-md-4 mb-4 d-flex justify-content-center">
            <div class="card shadow-sm p-3" style="width: 100%; max-width: 380px;">
                <div class="card-body d-flex justify-content-center">
                    <canvas id="pieChart" width="350" height="350" style="max-width: 350px; max-height: 350px;"></canvas>
                </div>
            </div>
        </div>

        <hr>

        <div class="col-md-4 mb-4 d-flex justify-content-center">
            <div class="card shadow-sm p-3" style="width: 100%; max-width: 380px;">
                <div class="card-body d-flex justify-content-center">
                    <canvas id="barChart" width="350" height="350" style="max-width: 350px; max-height: 350px;"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>




<script>
function getPrediction() {
    fetch('http://localhost/envira/water/predict.php')
        .then(response => response.json())
        .then(data => {
            const card = document.getElementById("resultCard");
            const text = document.getElementById("predictionText");

            if (data.status === "success") {
                let message = `
                    <strong>Prediction:</strong> ${data.prediction}<br>
                    <strong>Temperature:</strong> ${data.data.Temp}°C<br>
                    <strong>DO:</strong> ${data.data.DO}<br>
                    <strong>PH:</strong> ${data.data.PH}<br>
                    <strong>Conductivity:</strong> ${data.data.Conductivity}
                `;
                text.innerHTML = message;
                card.style.display = "block";

                if (data.prediction === "Good") {
                    card.className = "card border-success shadow-sm";
                } else if (data.prediction === "Poor") {
                    card.className = "card border-warning shadow-sm";
                } else {
                    card.className = "card border-danger shadow-sm";
                }
            } else {
                text.innerHTML = `<strong>Error:</strong> ${data.message}`;
                card.style.display = "block";
                card.className = "card border-danger shadow-sm";
            }
        })
        .catch(err => {
            const card = document.getElementById("resultCard");
            const text = document.getElementById("predictionText");
            text.innerHTML = `<strong>Error:</strong> ${err}`;
            card.style.display = "block";
            card.className = "card border-danger shadow-sm";
        });
}

getPrediction();

function getFuturePrediction() {
    fetch('http://localhost/envira/water/predict_future.php')
        .then(response => response.json())
        .then(data => {
            const futureDiv = document.getElementById("futurePredictionCard");
            if (data.status === "success") {
                futureDiv.innerHTML = `
                    <strong>Predicted Future Values:</strong><br>
                    Temperature: ${data.future_prediction.Temp}°C<br>
                    DO: ${data.future_prediction.DO}<br>
                    Conductivity: ${data.future_prediction.PH}<br>
                    PH: ${data.future_prediction.Conductivity}
                `;
                futureDiv.style.display = "block";
            } else {
                futureDiv.innerText = `Error: ${data.message}`;
                futureDiv.style.display = "block";
            }
        })
        .catch(err => {
            const futureDiv = document.getElementById("futurePredictionCard");
            futureDiv.innerText = `Error: ${err}`;
            futureDiv.style.display = "block";
        });
}

getFuturePrediction();
setInterval(getFuturePrediction, 10000);

let lineChart, pieChart, barChart;

function renderCharts(data) {
    const labels = ["Record 1", "Record 2", "Record 3", "Record 4", "Record 5"];
    const tempData = data.map(item => item.Temp);
    const doData = data.map(item => item.DO);
    const condData = data.map(item => item.conductivity);

    if (lineChart) lineChart.destroy();
    if (pieChart) pieChart.destroy();
    if (barChart) barChart.destroy();

    lineChart = new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                { label: 'Temp (°C)', data: tempData, borderColor: 'rgb(255, 99, 132)', fill: false },
                { label: 'DO', data: doData, borderColor: 'rgb(54, 162, 235)', fill: false },
                { label: 'PH', data: condData, borderColor: 'rgb(75, 192, 192)', fill: false }
            ]
        }
    });

    let latest = data[data.length - 1];
    pieChart = new Chart(document.getElementById('pieChart'), {
        type: 'pie',
        data: {
            labels: ['Temp', 'DO', 'PH'],
            datasets: [{
                data: [latest.Temp, latest.DO, latest.conductivity],
                backgroundColor: ['#ff6384', '#36a2eb', '#4bc0c0']
            }]
        }
    });

    barChart = new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                { label: 'Temp (°C)', data: tempData, backgroundColor: 'rgba(255, 99, 132, 0.6)' },
                { label: 'DO', data: doData, backgroundColor: 'rgba(54, 162, 235, 0.6)' },
                { label: 'PH', data: condData, backgroundColor: 'rgba(75, 192, 192, 0.6)' }
            ]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });
}

function fetchChartData() {
    fetch('http://localhost/envira/water/fetch_chart_data.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                renderCharts(data.data);
            }
        })
        .catch(err => console.error("Chart Fetch Error:", err));
}

fetchChartData();
setInterval(fetchChartData, 10000);
</script>

</body>
</html>
