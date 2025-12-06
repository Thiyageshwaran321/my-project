<?php
session_start();
include "db.php";

if (!isset($_SESSION['customer_id'])) {
    echo "<script>alert('Please login first'); window.location='index.html';</script>";
    exit();
}

$customer_id = $_SESSION['customer_id'];

if (!isset($_POST['product_id']) || !isset($_POST['unit'])) {
    echo "Invalid request";
    exit();
}

$product_id = (int) $_POST['product_id'];
$unit = (int) $_POST['unit'];

$stmt = $conn->prepare("SELECT material_name, price FROM products WHERE product_id=?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows == 0) {
    die("Product not found!");
}

$row = $res->fetch_assoc();
$material = $row['material_name'];
$price = $row['price'];

$stmt2 = $conn->prepare("INSERT INTO cart (customer_id, product_id, material_name, price, unit) VALUES (?, ?, ?, ?, ?)");
$stmt2->bind_param("iisii", $customer_id, $product_id, $material, $price, $unit);
$stmt2->execute();

echo "<script>alert('Item added to cart'); window.location='material.html';</script>";
?>
