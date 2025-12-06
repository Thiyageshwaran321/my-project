<?php
include "db.php";

if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
    echo "Invalid request";
    exit();
}

$order_id = $_POST['order_id'];
$status = $_POST['status'];

// Save timestamp also
$time_field = "";
if ($status == "placed") $time_field = "placed_time";
if ($status == "shipped") $time_field = "shipped_time";
if ($status == "out_for_delivery") $time_field = "out_time";
if ($status == "delivered") $time_field = "delivered_time";

// Update order table
$stmt = $conn->prepare("UPDATE orders SET order_status=?, $time_field=NOW() WHERE order_id=?");
$stmt->bind_param("si", $status, $order_id);

if ($stmt->execute()) {
    echo "<script>alert('Status updated!'); window.location='admin_orders.php';</script>";
} else {
    echo "Error updating status";
}
?>
