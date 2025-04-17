<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "envira_db");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

$sql = "SELECT Temp, DO, PH, conductivity FROM water_data ORDER BY id DESC LIMIT 5";
$result = $conn->query($sql);

$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode(["status" => "success", "data" => array_reverse($data)]);
} else {
    echo json_encode(["status" => "error", "message" => "No data found"]);
}

$conn->close();
?>
