<?php
$phone = $_GET['phone'];
$type = $_GET['customer']; // 'driver' or 'customer'

$conn = new mysqli("localhost", "root", "", "tracking_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$table = ($type == "driver") ? "drivers" : "customers";
$sql = "SELECT latitude, longitude FROM $table WHERE phone = '$phone'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(["lat" => 0, "lng" => 0]);
}
$conn->close();
?>
