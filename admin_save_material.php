<?php
session_start();
include "db.php";

$id    = $_POST['product_id'];
$name  = $_POST['material_name'];
$price = $_POST['price'];
$stock = $_POST['stock'];
$unit  = $_POST['unit_type'];
$image = $_POST['image'];

if ($id) {
    // UPDATE
    $stmt = $conn->prepare("
      UPDATE products
      SET material_name=?, price=?, stock=?, unit_type=?, image=?
      WHERE product_id=?
    ");
    $stmt->bind_param("siissi", $name, $price, $stock, $unit, $image, $id);
} else {
    // INSERT
    $stmt = $conn->prepare("
      INSERT INTO products (material_name, price, stock, unit_type, image)
      VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("siiss", $name, $price, $stock, $unit, $image);
}

$stmt->execute();
header("Location: admin_dashboard.php");
exit;
