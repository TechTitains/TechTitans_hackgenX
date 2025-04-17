<?php
header('Content-Type: application/json');

// Database connection
$conn = new mysqli("localhost", "root", "", "envira_db");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

$sql = "SELECT Temp, DO, PH, conductivity FROM water_data ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $temp = $row['Temp'];
    $do = $row['DO'];
    $ph = $row['PH'];
    $cond = $row['conductivity'];

    // Prediction Logic
    $quality = "Unknown";

    if ($do >= 6 && $ph >= 6.5 && $ph <= 8.5 && $temp <= 30 && $cond <= 500) {
        $quality = "Good";
    } elseif ($do >= 4 && $ph >= 6 && $ph <= 9 && $temp <= 35 && $cond <= 800) {
        $quality = "Poor";
    } else {
        $quality = "Bad";
    }

    echo json_encode([
        "status" => "success",
        "prediction" => $quality,
        "data" => [
            "Temp" => $temp,
            "DO" => $do,
            "Conductivity" => $ph,
            "PH" => $cond
        ]
    ]);

} else {
    echo json_encode(["status" => "error", "message" => "No data found"]);
}

$conn->close();
?>
