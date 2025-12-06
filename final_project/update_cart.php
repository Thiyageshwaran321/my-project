<?php
session_start();
include "db.php";

if (!isset($_SESSION['customer_id'])) {
    echo "<script>alert('Please login first'); window.location='index.html';</script>";
    exit;
}

$customer_id = $_SESSION['customer_id'];

// Validate form data
if (!isset($_POST['cart_id']) || !isset($_POST['unit'])) {
    echo "<script>alert('Invalid update request'); window.location='my_account.php';</script>";
    exit;
}

$cart_id = (int)$_POST['cart_id'];
$unit = (int)$_POST['unit'];

// If quantity is zero → Remove item from cart
if ($unit <= 0) {
    $del = $conn->prepare("DELETE FROM cart WHERE id=? AND customer_id=?");
    $del->bind_param("ii", $cart_id, $customer_id);
    if ($del->execute()) {
        echo "<script>alert('Item removed from cart'); window.location='my_account.php';</script>";
        exit;
    }
}

// Update quantity
$stmt = $conn->prepare("UPDATE cart SET unit=? WHERE id=? AND customer_id=?");
$stmt->bind_param("iii", $unit, $cart_id, $customer_id);

if ($stmt->execute()) {
    echo "<script>alert('Quantity updated successfully'); window.location='my_account.php';</script>";
} else {
    echo "<script>alert('Failed to update'); window.location='my_account.php';</script>";
}
?>
