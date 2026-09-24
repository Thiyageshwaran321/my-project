<?php
session_start();
include "db.php";

$customer_id = $_SESSION['customer_id'];

$cart = $conn->query("SELECT * FROM cart WHERE customer_id=$customer_id");

while ($item = $cart->fetch_assoc()) {

    $product_id = $item['product_id'];
    $qty = $item['unit'];
    $price = $item['price'];
    $material = $item['material_name'];

    $total = $qty * $price;

    $stmt = $conn->prepare("
        INSERT INTO orders (customer_id, product_id, material_name, quantity, total_amount, status)
        VALUES (?, ?, ?, ?, ?, 'pending')
    ");
    
    $stmt->bind_param("iisid", $customer_id, $product_id, $material, $qty, $total);
    $stmt->execute();
}

// Empty cart after placing all orders
$conn->query("DELETE FROM cart WHERE customer_id=$customer_id");

echo "<script>alert('All items ordered successfully!'); window.location='my_account.php';</script>";
?>

