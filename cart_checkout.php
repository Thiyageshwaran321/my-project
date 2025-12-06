<?php
session_start();
include "db.php";

if (!isset($_SESSION['customer_id'])) {
    header("Location: index.html");
    exit;
}

$customer_id = $_SESSION['customer_id'];

if (!isset($_POST['cart_id'])) {
    echo "Invalid Request!";
    exit;
}

$cart_id = (int)$_POST['cart_id'];

// Fetch cart item
$stmt = $conn->prepare("SELECT * FROM cart WHERE id = ? AND customer_id = ?");
$stmt->bind_param("ii", $cart_id, $customer_id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if (!$item) {
    echo "Cart item not found.";
    exit;
}

// Redirect to select address page
header("Location: select_address.php?product_id={$item['product_id']}&quantity={$item['unit']}");
exit;
?>
