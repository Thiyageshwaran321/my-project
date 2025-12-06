<?php
session_start();
include "db.php";

if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
    echo "Invalid request";
    exit;
}

$order_id = $_POST['order_id'];
$status = $_POST['status'];

$dateColumn = "";

if ($status == "shipped")              $dateColumn = "shipped_at";
if ($status == "out_for_delivery")     $dateColumn = "out_for_delivery_at";
if ($status == "delivered")            $dateColumn = "delivered_at";

if ($dateColumn == "") {
    echo "Invalid status";
    exit;
}

$stmt = $conn->prepare("UPDATE orders SET status=?, $dateColumn = NOW() WHERE order_id=?");
$stmt->bind_param("si", $status, $order_id);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "error";
}
?>
