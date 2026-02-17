<?php
session_start();
include "db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = !empty($_POST['product_id']) ? intval($_POST['product_id']) : null;
    $material_name = $_POST['material_name'];
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $unit_type = $_POST['unit_type'];
    $image = $_POST['image'];
    $low_stock_limit = isset($_POST['low_stock_limit']) ? intval($_POST['low_stock_limit']) : 10;
    
    if ($product_id) {
        // Update existing product
        $query = "UPDATE products SET material_name=?, price=?, stock=?, unit_type=?, image=?, low_stock_limit=? WHERE product_id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sdisssi", $material_name, $price, $stock, $unit_type, $image, $low_stock_limit, $product_id);
    } else {
        // Insert new product
        $query = "INSERT INTO products (material_name, price, stock, unit_type, image, low_stock_limit) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sdisss", $material_name, $price, $stock, $unit_type, $image, $low_stock_limit);
    }
    
    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?product_saved=1");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}
$conn->close();
?>