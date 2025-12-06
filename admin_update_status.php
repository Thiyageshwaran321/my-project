<?php
include "db.php";

if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
    echo "error";
    exit;
}

$order_id = $_POST['order_id'];
$status = $_POST['status'];

$column = "";

if ($status == "shipped") {
    $column = "shipped_time";
} elseif ($status == "out_for_delivery") {
    $column = "out_time";
} elseif ($status == "delivered") {
    $column = "delivered_time";
} else {
    echo "error";
    exit;
}

$stmt = $conn->prepare("
    UPDATE orders 
    SET status=?, $column = NOW() 
    WHERE order_id = ?
");

$stmt->bind_param("si", $status, $order_id);

echo ($stmt->execute()) ? "success" : "error";
