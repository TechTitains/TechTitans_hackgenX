<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "envira_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['status' => 'error']));
}

$sql = "SELECT * FROM alerts ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $alert = $result->fetch_assoc();
    echo json_encode([
        'status' => 'success',
        'id' => $alert['id'],
        'message' => $alert['message']
    ]);
} else {
    echo json_encode(['status' => 'no_alert']);
}
$conn->close();
?>