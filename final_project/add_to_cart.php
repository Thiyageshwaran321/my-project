<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    // Not logged in — either redirect to login or return error
    header('Location: /index.php?msg=login_required');
    exit;
}

$customer_id = (int)$_SESSION['customer_id'];
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$qty = isset($_POST['quantity']) ? (int)$_POST['quantity'] : (isset($_GET['qty']) ? (int)$_GET['qty'] : 1);
if ($product_id <= 0 || $qty <= 0) {
    header('Location: /products.php?msg=invalid_input');
    exit;
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=your_db;charset=utf8mb4','db_user','db_pass',[
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // If product already in cart, increase quantity
    $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE customer_id = ? AND product_id = ?");
    $stmt->execute([$customer_id, $product_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $newQty = $row['quantity'] + $qty;
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE customer_id = ? AND product_id = ?");
        $stmt->execute([$newQty, $customer_id, $product_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO cart (customer_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$customer_id, $product_id, $qty]);
    }

    header('Location: /myaccount/cart.php?msg=added');
    exit;

} catch (PDOException $e) {
    // Log the real error on server; show friendly message
    error_log($e->getMessage());
    header('Location: /products.php?msg=error');
    exit;
}
