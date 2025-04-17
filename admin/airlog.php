<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "envira_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM sensor_readings ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

$card = '';
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $pm = $row['PM25'];
    $ppm = $row['air_quality'];
    $timestamp = $row['timestamp'];

    if ($ppm <= 50) {
        $quality = "Excellent";
        $bg = "bg-green-100";
        $text = "text-green-800";
        $message = "Air quality is excellent. No precautions needed.";
    } elseif ($ppm <= 100) {
        $quality = "Good";
        $bg = "bg-green-100";
        $text = "text-green-800";
        $message = "Air quality is good. Safe to enjoy outdoor activities.";
    } elseif ($ppm <= 150) {
        $quality = "Moderate";
        $bg = "bg-yellow-100";
        $text = "text-yellow-800";
        $message = "Moderate air quality. Sensitive groups should take care.";
    } elseif ($ppm <= 300) {
        $quality = "Poor";
        $bg = "bg-red-100";
        $text = "text-red-800";
        $message = "Air quality is poor. Limit outdoor activities.";
    } else {
        $quality = "Very Poor";
        $bg = "bg-red-200";
        $text = "text-red-900";
        $message = "Very bad air quality! Avoid outdoor exposure.";
    
        // Insert alert into DB only if ppm > 200
        $alertMessage = "Air quality alert! Dangerous levels detected (PPM: $ppm) at $timestamp.";
        $insertAlert = "INSERT INTO alerts (message) VALUES ('$alertMessage')";
        
        // Check if insert query is successful
        if ($conn->query($insertAlert) === TRUE) {
            // Alert inserted successfully
        } else {
            // Log or handle error in inserting
            error_log("Error inserting alert: " . $conn->error);
        }
    
        // Add JavaScript inline script for popup alert
        $card .= "<script>alert('$alertMessage');</script>";
    }

    $card = "
    <div class='rounded-lg p-6 $bg bg-opacity-70 shadow-lg mb-10'>
        <h2 class='text-2xl font-bold $text mb-4'>Current Air Quality: $quality</h2>
        <p class='mb-2 font-semibold'>PM2.5 Value: <span class='font-normal'>$pm µg/m³</span></p>
        <p class='mb-2 font-semibold'>MQ135 Air Quality: <span class='font-normal'>$ppm ppm</span></p>
        <p class='mb-2 font-semibold'>Last Updated: <span class='font-normal'>$timestamp</span></p>
        <div class='mt-4 p-4 border-l-4 border-gray-500 bg-white bg-opacity-60'>
            <p class='text-sm'>$message</p>
        </div>
    </div>";
} else {
    $card = "<p>No data available.</p>";
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Air Quality Monitor</title>
    <script src="https://cdn.tailwindcss.com"></script> 
    <link rel="stylesheet" href="style.css">

    <!-- <link rel="stylesheet" href="style.css"> -->
<!-- CDN's -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    /* .navbar {
  background-color:;
  padding: 1rem 5%;
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: fixed;
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

.nav-links li a:hover {
  background-color:lightgreen;
  border-radius: 4px;
}
 */
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
    background-color: var(--secondary-color);
    border-radius: 4px;
}
#card{
    margin-top: 20px;
}

</style>

</head>
<body>

<nav class="navbar">
      <div class="logo">Envira</div>
      <ul class="nav-links">
        <li><a href="home.html">Home</a></li>
        <li><a href="about.html">About</a></li>
        <li><a href="airlog.php">Pollution</a></li>
        <li><a href="../index.html">LOGOUT</a></li>
        <li><a href="notification.php"><i  class="bi bi-envelope-exclamation-fill" ></i></a></li>
      </ul>
    </nav>

    <div class="container mx-auto" id="card">
        <div id="live-card">
            <?= $card ?>
        </div>

       <!-- Rules & Regulations Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- High Pollution Level Rules -->
    <div class="rounded-lg p-6 bg-red-100 bg-opacity-70 shadow-lg">
        <h2 class="text-2xl font-bold text-red-800 mb-4">High Pollution Areas</h2>
        <div class="space-y-4">
            <div>
                <h3 class="font-semibold text-red-700">PM2.5 Range:</h3>
                <p>Above 150 µg/m³</p>
            </div>
            <div>
                <h3 class="font-semibold text-red-700">Regulatory Measures:</h3>
                <ul class="list-disc list-inside space-y-2">
                    <li>Restrict vehicular movement on peak days</li>
                    <li>Ban construction and industrial emissions temporarily</li>
                    <li>Deploy air purification towers in hotspots</li>
                    <li>Conduct pollution control audits frequently</li>
                    <li>Mandate working from home for offices</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="rounded-lg p-6 bg-yellow-100 bg-opacity-70 shadow-lg">
        <h2 class="text-2xl font-bold text-yellow-800 mb-4">Moderate Pollution Areas</h2>
        <div class="space-y-4">
            <div>
                <h3 class="font-semibold text-yellow-700">PM2.5 Range:</h3>
                <p>51-150 µg/m³</p>
            </div>
            <div>
                <h3 class="font-semibold text-yellow-700">Regulatory Measures:</h3>
                <ul class="list-disc list-inside space-y-2">
                    <li>Implement odd-even traffic rules</li>
                    <li>Limit emissions from factories with temporary caps</li>
                    <li>Promote use of public transport and EVs</li>
                    <li>Monitor pollution sources regularly</li>
                    <li>Increase green zone coverage</li>
                </ul>
            </div>
        </div>
    </div>

    
    <div class="rounded-lg p-6 bg-green-100 bg-opacity-70 shadow-lg">
        <h2 class="text-2xl font-bold text-green-800 mb-4">Low Pollution Areas</h2>
        <div class="space-y-4">
            <div>
                <h3 class="font-semibold text-green-700">PM2.5 Range:</h3>
                <p>0-50 µg/m³</p>
            </div>
            <div>
                <h3 class="font-semibold text-green-700">Proactive Measures:</h3>
                <ul class="list-disc list-inside space-y-2">
                    <li>Encourage sustainable practices</li>
                    <li>Expand green spaces and plantations</li>
                    <li>Promote solar and clean energy</li>
                    <li>Run awareness and education campaigns</li>
                    <li>Maintain regular AQI monitoring infrastructure</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Envira</h3>
                <p>Making Earth a better place</p>
            </div>
            <div class="footer-section">
                <h3>Contact</h3>
                <p>Email: info@envira.com</p>
                <p>Phone: (555) 123-4567</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 Envira. All rights reserved.</p>
        </div>
    </footer> -->

    <!-- Auto Refresh Script -->
    <script>
        setInterval(() => {
            fetch(window.location.href)
                .then(res => res.text())
                .then(html => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const newCard = doc.querySelector('#live-card');
                    document.querySelector('#live-card').innerHTML = newCard.innerHTML;
                });
        }, 5000);
    </script>

<script>
    let lastAlertId = null;

    setInterval(() => {
        fetch('get_alert.php')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    if (lastAlertId !== data.id) {
                        alert(data.message);
                        lastAlertId = data.id;
                    }
                }
            });
    }, 1000);
</script>

</body>
<!-- CDN's -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>

</html>