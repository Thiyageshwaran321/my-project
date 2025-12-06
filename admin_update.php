<?php
include "db.php";

if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
    echo "error";
    exit;
}

$order_id = $_POST['order_id'];
$status = $_POST['status'];

if ($status == "shipped") {
    $sql = "UPDATE orders SET status='shipped', shipped_time = NOW() WHERE order_id = ?";
}
else if ($status == "out_for_delivery") {
    $sql = "UPDATE orders SET status='out for delivery', out_time = NOW() WHERE order_id = ?";
}
else if ($status == "delivered") {
    $sql = "UPDATE orders SET status='delivered', delivered_time = NOW() WHERE order_id = ?";
}
else {
    echo "error";
    exit;
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);

echo ($stmt->execute()) ? "success" : "error";
?>
