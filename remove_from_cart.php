<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header('Location: /index.php');
    exit;
}
$customer_id = (int)$_SESSION['customer_id'];
$cart_id = isset($_GET['cart_id']) ? (int)$_GET['cart_id'] : 0;
if ($cart_id <= 0) {
    header('Location: /myaccount/cart.php');
    exit;
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=your_db;charset=utf8mb4','db_user','db_pass',[
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND customer_id = ?");
    $stmt->execute([$cart_id, $customer_id]);
    header('Location: /myaccount/cart.php?msg=removed');
    exit;
} catch (PDOException $e) {
    error_log($e->getMessage());
    header('Location: /myaccount/cart.php?msg=error');
    exit;
}
