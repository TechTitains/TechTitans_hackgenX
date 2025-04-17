<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "envira_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM alerts ORDER BY id DESC LIMIT 5";
$result = $conn->query($sql);

$alerts = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $alerts[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Latest Alerts</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">

<style>

body{
    background:#dff0ec;
    /* margin-top : 20px; */
}
#top{
    margin-top : 20px;
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
        <!-- <li><a href="notification.php"  ><i  class="bi bi-envelope-exclamation-fill" ></i></a></li> -->
      </ul>
    </nav>

    <div class="max-w-3xl mx-auto" >
        <h1 id="top" class="text-3xl font-bold mb-6 text-center text-red-600">Latest Air Quality Alerts</h1>

        <?php if (count($alerts) > 0): ?>
            <div  class="space-y-4">
                <?php foreach ($alerts as $alert): ?>
                    <div  class="bg-white shadow-md rounded-lg p-4 border-l-4 border-red-500">
                        <p class="text-gray-800"><?= htmlspecialchars($alert['message']) ?></p>
                        <p class="text-sm text-gray-500 mt-2"><?= htmlspecialchars($alert['created_at']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-600">No alerts found.</p>
        <?php endif; ?>
    </div>
</body>
</html>