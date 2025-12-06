<?php
session_start();
include 'db.php';

if (!isset($_SESSION['customer_id'])) {
    echo "<script>alert('Please login'); window.location='index.html';</script>";
    exit;
}

if (!isset($_GET['order_id'])) {
    echo "Invalid Order ID";
    exit;
}

$order_id = $_GET['order_id'];

$stmt = $conn->prepare("
    SELECT placed_time, shipped_time, out_time, delivered_time 
    FROM orders 
    WHERE order_id = ? AND customer_id = ?
");
$stmt->bind_param("ii", $order_id, $_SESSION['customer_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo "Order not found.";
    exit;
}

function statusDot($value) {
    return (!empty($value)) ? "🟢" : "⚪";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Track Order</title>
<style>
body {
    font-family: Segoe UI;
    background: #f5ebd7;
    padding: 20px;
}
.timeline { width: 80%; margin: auto; }
.stage { display: flex; margin: 25px 0; align-items: start; }
.icon { font-size: 24px; margin-right: 10px; }
.text { font-size: 18px; }
.date { font-size: 13px; color: gray; }
</style>
</head>

<body>

<h2>Tracking Order #<?= $order_id ?></h2>

<div class="timeline">

    <!-- Order Placed -->
    <div class="stage">
        <div class="icon">🟢</div>
        <div class="text">
            <strong>Order Placed</strong>
            <div class="date"><?= $order['placed_time'] ?></div>
        </div>
    </div>

    <!-- Shipped -->
    <div class="stage">
        <div class="icon"><?= statusDot($order['shipped_time']) ?></div>
        <div class="text">
            <strong>Shipped</strong>
            <div class="date"><?= $order['shipped_time'] ?: "Awaiting update..." ?></div>
        </div>
    </div>

    <!-- Out For Delivery -->
    <div class="stage">
        <div class="icon"><?= statusDot($order['out_time']) ?></div>
        <div class="text">
            <strong>Out For Delivery</strong>
            <div class="date"><?= $order['out_time'] ?: "Awaiting update..." ?></div>
        </div>
    </div>

    <!-- Delivered -->
    <div class="stage">
        <div class="icon"><?= statusDot($order['delivered_time']) ?></div>
        <div class="text">
            <strong>Delivered</strong>
            <div class="date"><?= $order['delivered_time'] ?: "Not delivered yet" ?></div>
        </div>
    </div>

</div>

</body>
</html>
