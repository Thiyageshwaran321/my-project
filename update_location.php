<?php
$phone = $_POST['phone'];
$lat = $_POST['lat'];
$lng = $_POST['lng'];
$type = $_POST['driver']; // 'driver' or 'customer'

// DB Connection
$conn = new mysqli("localhost", "root", "", "tracking_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$table = ($type == "driver") ? "drivers" : "customers";

$sql = "INSERT INTO $table (phone, latitude, longitude)
        VALUES ('$phone', $lat, $lng)
        ON DUPLICATE KEY UPDATE latitude=$lat, longitude=$lng";

if ($conn->query($sql) === TRUE) {
    echo "Location updated";
} else {
    echo "Error: " . $conn->error;
}
$conn->close();
?>
