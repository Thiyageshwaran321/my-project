<?php
session_start();
include "db.php";

if (!isset($_SESSION['customer_id'])) {
    echo "<script>alert('Please login to add items to cart'); window.location='index.html';</script>";
    exit();
}

if (!isset($_POST['product_id']) || !isset($_POST['quantity'])) {
    echo "Invalid request";
    exit();
}

$product_id = $_POST['product_id'];
$quantity = $_POST['quantity'];
$customer_id = $_SESSION['customer_id'];

// Get product details
$stmt = $conn->prepare("SELECT material_name, price FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "Product not found!";
    exit();
}

$material_name = $product['material_name'];
$price = $product['price'];

// Insert into cart
$insert = $conn->prepare("INSERT INTO cart (customer_id, product_id, material_name, unit, price) VALUES (?, ?, ?, ?, ?)");
$insert->bind_param("iisid", $customer_id, $product_id, $material_name, $quantity, $price);

if ($insert->execute()) {
    echo "<script>alert('Added to cart successfully!'); window.location='my_account.php';</script>";
} else {
    echo "Failed to add to cart";
}
?>
