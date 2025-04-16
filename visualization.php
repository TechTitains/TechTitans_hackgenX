<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "envira_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$timestamps = [];
$pm25_values = [];
$aq_values = [];

$sql = "SELECT timestamp, PM25, air_quality FROM sensor_readings ORDER BY timestamp DESC LIMIT 20";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $rows = array_reverse($result->fetch_all(MYSQLI_ASSOC));
    foreach ($rows as $row) {
        $timestamps[] = $row['timestamp'];
        $pm25_values[] = $row['PM25'];
        $aq_values[] = $row['air_quality'];
    }
}

// Count categories for pie chart
$excellent = $good = $moderate = $poor = $very_bad = 0;
foreach ($aq_values as $value) {
    if ($value <= 50) $excellent++;
    elseif ($value <= 100) $good++;
    elseif ($value <= 150) $moderate++;
    elseif ($value <= 200) $poor++;
    else $very_bad++;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Air Quality Visualization</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }
        canvas {
            margin-bottom: 50px;
        }
        
    body {
        font-family: Arial, sans-serif;
        background: #dff0ec;
        padding: 20px;
    }
    canvas {
        margin-bottom: 50px;
    }
    #pieChart {
        max-width: 500px;
        max-height: 500px;
    }
    .navbar {
  background-color:#1d9952;
  padding: 1rem 5%;
  display: flex;
  justify-content: space-between;
  align-items: center;
  /* position: fixed; */
  width: 100%;
  top: 0;
  z-index: 1000;
}

.logo {
  color: white;
  font-size: 1.5rem;
  font-weight: bold;
}

.nav-links {
  display: flex;
  list-style: none;
}

.nav-links li a {
  color: white;
  text-decoration: none;
  padding: 0.5rem 1rem;
  transition: 0.3s;
}

.nav-links li a:hover {
  background-color:lightgreen;
  border-radius: 4px;
}



    </style>
</head>
<body>
<nav class="navbar">
      <div class="logo">Envira</div>
      <ul class="nav-links">
        <li><a href="#home">Home</a></li>
        <li><a href="about.html">About</a></li>
        <li><a href="#pollution">Pollution</a></li>
        <li><a href="contact.html">Contact</a></li>
        <li><a href="notification.php"  ><i  class="bi bi-envelope-exclamation-fill" ></i></a></li>
      </ul>
    </nav>

    <h1>Air Quality Dashboard</h1>

    <div id="charts">
        <canvas id="lineChart" height="100"></canvas>
        <canvas id="barChart" height="100"></canvas>
        <canvas id="pieChart" height="100"></canvas>
    </div>

    <script>
        const labels = <?= json_encode($timestamps) ?>;
        const pm25Data = <?= json_encode($pm25_values) ?>;
        const aqData = <?= json_encode($aq_values) ?>;

        const pieCounts = {
            excellent: <?= $excellent ?>,
            good: <?= $good ?>,
            moderate: <?= $moderate ?>,
            poor: <?= $poor ?>,
            very_bad: <?= $very_bad ?>
        };

        let lineChart, barChart, pieChart;

        function renderCharts() {
            const lineCtx = document.getElementById('lineChart').getContext('2d');
            const barCtx = document.getElementById('barChart').getContext('2d');
            const pieCtx = document.getElementById('pieChart').getContext('2d');

            if (lineChart) lineChart.destroy();
            if (barChart) barChart.destroy();
            if (pieChart) pieChart.destroy();

            lineChart = new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'PM2.5 (µg/m³)',
                            data: pm25Data,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            fill: false
                        },
                        {
                            label: 'Air Quality (PPM)',
                            data: aqData,
                            borderColor: 'rgba(54, 162, 235, 1)',
                            fill: false
                        }
                    ]
                },
                options: { responsive: true }
            });

            barChart = new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'PM2.5 (µg/m³)',
                            data: pm25Data,
                            backgroundColor: 'rgba(255, 99, 132, 0.6)'
                        },
                        {
                            label: 'Air Quality (PPM)',
                            data: aqData,
                            backgroundColor: 'rgba(54, 162, 235, 0.6)'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            pieChart = new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: ['Excellent', 'Good', 'Moderate', 'Poor', 'Very Bad'],
                    datasets: [{
                        data: [
                            pieCounts.excellent,
                            pieCounts.good,
                            pieCounts.moderate,
                            pieCounts.poor,
                            pieCounts.very_bad
                        ],
                        backgroundColor: [
                            'rgba(34,197,94,0.6)',
                            'rgba(132,204,22,0.6)',
                            'rgba(253,224,71,0.6)',
                            'rgba(251,191,36,0.6)',
                            'rgba(239,68,68,0.6)'
                        ]
                    }]
                },
                options: { responsive: true }
            });
        }

        renderCharts();

        // Auto-refresh data every 5 seconds
        setInterval(() => {
            fetch('visualization.php')
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    eval(doc.querySelector('script').textContent); // Re-evaluate chart JS
                });
        }, 5000);
    </script>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
</body>
</html>