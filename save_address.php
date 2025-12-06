<?php
session_start();
include "db.php";

if (!isset($_SESSION['customer_id'])) {
    header("Location: index.html");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$full_name = $_POST['full_name'];
$phone = $_POST['phone'];
$address_line = $_POST['address_line'];
$city = $_POST['city'];
$pincode = $_POST['pincode'];
$state = $_POST['state'];

$stmt = $conn->prepare("INSERT INTO customer_addresses 
(full_name, phone, address_line, city, pincode, state, customer_id) 
VALUES (?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("ssssssi", $full_name, $phone, $address_line, $city, $pincode, $state, $customer_id);

if ($stmt->execute()) {
    header("Location: my_account.php?msg=address_saved");
} else {
    echo "Error saving address!";
}
?>
