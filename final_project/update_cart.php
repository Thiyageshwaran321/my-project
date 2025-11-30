<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header('Location: /index.php');
    exit;
}
$customer_id = (int)$_SESSION['customer_id'];

if (!isset($_POST['qty']) || !is_array($_POST['qty'])) {
    header('Location: /myaccount/cart.php');
    exit;
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=your_db;charset=utf8mb4','db_user','db_pass',[
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $pdo->beginTransaction();
    $stmtSelect = $pdo->prepare("SELECT customer_id FROM cart WHERE id = ?");
    $stmtUpdate = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    foreach ($_POST['qty'] as $cart_id => $quantity) {
        $cart_id = (int)$cart_id;
        $quantity = (int)$quantity;
        if ($cart_id <= 0) continue;
        if ($quantity <= 0) {
            // Optionally delete instead of setting to 0
            $pdo->prepare("DELETE FROM cart WHERE id = ? AND customer_id = ?")->execute([$cart_id, $customer_id]);
            continue;
        }
        // Ensure the cart row belongs to this user (extra safety)
        $stmtSelect->execute([$cart_id]);
        $owner = $stmtSelect->fetch(PDO::FETCH_ASSOC);
        if ($owner && $owner['customer_id'] == $customer_id) {
            $stmtUpdate->execute([$quantity, $cart_id]);
        }
    }
    $pdo->commit();
    header('Location: /myaccount/cart.php?msg=updated');
    exit;

} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log($e->getMessage());
    header('Location: /myaccount/cart.php?msg=error');
    exit;
}
