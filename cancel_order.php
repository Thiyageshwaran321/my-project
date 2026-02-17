<?php
session_start();
include "db.php";

if (!isset($_SESSION['customer_id'])) {
    die("Unauthorized");
}

if (!isset($_POST['order_id'])) {
    die("Invalid request");
}

$customer_id = $_SESSION['customer_id'];
$order_id = (int)$_POST['order_id'];

/* CHECK ORDER OWNERSHIP + STATUS */
$stmt = $conn->prepare("
    SELECT status
    FROM orders
    WHERE order_id = ? AND customer_id = ?
");
$stmt->bind_param("ii", $order_id, $customer_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Order not found");
}

if (!in_array(strtolower($order['status']), ['pending', 'order placed'])) {
    die("Order cannot be cancelled");
}

/* CANCEL ORDER */
$upd = $conn->prepare("
    UPDATE orders
    SET status = 'Cancelled',
        order_status = 'Cancelled'
    WHERE order_id = ?
");
$upd->bind_param("i", $order_id);
$upd->execute();

echo "<script>
alert('Order cancelled successfully');
window.location='my_account.php';
</script>";
