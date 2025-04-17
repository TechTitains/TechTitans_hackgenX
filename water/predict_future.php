<?php
header('Content-Type: application/json');

// Database connection
$conn = new mysqli("localhost", "root", "", "envira_db");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

// Get last 5 entries
$sql = "SELECT Temp, DO, PH, conductivity FROM water_data ORDER BY id DESC LIMIT 5";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $totalTemp = 0;
    $totalDO = 0;
    $totalPH = 0;
    $totalConductivity = 0;
    $count = 0;

    while ($row = $result->fetch_assoc()) {
        $totalTemp += $row['Temp'];
        $totalDO += $row['DO'];
        $totalPH += $row['PH'];
        $totalConductivity += $row['conductivity'];
        $count++;
    }

    // Calculate averages
    $predictedTemp = round($totalTemp / $count, 2);
    $predictedDO = round($totalDO / $count, 2);
    $predictedPH = round($totalPH / $count, 2);
    $predictedConductivity = round($totalConductivity / $count, 2);

    echo json_encode([
        "status" => "success",
        "future_prediction" => [
            "Temp" => $predictedTemp,
            "DO" => $predictedDO,
            "PH" => $predictedPH,
            "Conductivity" => $predictedConductivity
        ]
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "No recent data found"]);
}

$conn->close();
?>
