<?php
session_start();
include "db.php";

// OPTIONAL: If you add admin login later
// if (!isset($_SESSION['admin_id'])) {
//     echo "unauthorized";
//     exit;
// }

if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
    echo "invalid";
    exit;
}

$order_id = $_POST['order_id'];
$status   = $_POST['status'];

$dateColumn = "";

// Match button → column
if ($status == "shipped") {
    $dateColumn = "shipped_time";
} 
else if ($status == "out_for_delivery") {
    $dateColumn = "out_time";
} 
else if ($status == "delivered") {
    $dateColumn = "delivered_time";
}

// Validate status
if ($dateColumn == "") {
    echo "invalid-status";
    exit;
}

// Update order
$query = "UPDATE orders SET status = ?, $dateColumn = NOW() WHERE order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $status, $order_id);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "error";
}
?>
