<?php
session_start();
include "db.php";

/* LOGIN CHECK */
if (!isset($_SESSION['customer_id'])) {
    die("Unauthorized");
}

$customer_id = $_SESSION['customer_id'];

/* POST CHECK */
if (
    !isset(
        $_POST['product_id'],
        $_POST['quantity'],
        $_POST['total_amount'],
        $_POST['expected_delivery_full']
    )
) {
    die("Invalid Request");
}

$product_id   = (int)$_POST['product_id'];
$quantity     = (int)$_POST['quantity'];
$total_amount = (float)$_POST['total_amount'];

$expected_full = $_POST['expected_delivery_full'];
$expected_date = date("Y-m-d", strtotime($expected_full));
$expected_time = date("H:i:s", strtotime($expected_full));

/* GET DEFAULT ADDRESS */
$a = $conn->prepare("
  SELECT * FROM customer_addresses 
  WHERE customer_id = ? AND is_default = 1
");
$a->bind_param("i", $customer_id);
$a->execute();
$res = $a->get_result();

/* FALLBACK */
if ($res->num_rows === 0) {
    $a = $conn->prepare("
      SELECT * FROM customer_addresses 
      WHERE customer_id = ?
      ORDER BY id ASC
      LIMIT 1
    ");
    $a->bind_param("i", $customer_id);
    $a->execute();
    $res = $a->get_result();

    if ($res->num_rows === 0) {
        die("No address found");
    }
}

$address = $res->fetch_assoc();
$address_id = $address['id'];

/* PRODUCT */
$p = $conn->prepare("SELECT material_name, price FROM products WHERE product_id = ?");
$p->bind_param("i", $product_id);
$p->execute();
$product = $p->get_result()->fetch_assoc();

if (!$product) {
    die("Invalid product");
}

$material_name = $product['material_name'];
$material_cost = $product['price'] * $quantity;

/* SAFETY CHECK */
if ($material_cost != $total_amount) {
    $total_amount = $material_cost;
}

/* ORDER DATA */
$payment_method = "COD";
$status = "pending";
$order_status = "Order Placed";
$est_days = 4;

$check = $conn->prepare("
  SELECT stock FROM products WHERE product_id = ?
");
$check->bind_param("i", $product_id);
$check->execute();
$res = $check->get_result()->fetch_assoc();

if ($res['stock'] < $quantity) {
    echo "<script>
      alert('Stock not available');
      window.location='material.php';
    </script>";
    exit();
}

$updateStock = $conn->prepare("
  UPDATE products 
  SET stock = stock - ? 
  WHERE product_id = ?
");
$updateStock->bind_param("ii", $quantity, $product_id);
$updateStock->execute();

/* INSERT ORDER (FIXED) */
$ins = $conn->prepare("
INSERT INTO orders
(
  customer_id, product_id, quantity,
  material_name, material_cost,
  total_amount,
  payment_method, address_id,
  status, order_status, est_days,
  expected_delivery_date, expected_delivery_time
)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)
");

$ins->bind_param(
  "iiisidssissss",
  $customer_id,
  $product_id,
  $quantity,
  $material_name,
  $material_cost,
  $total_amount,
  $payment_method,
  $address_id,
  $status,
  $order_status,
  $est_days,
  $expected_date,
  $expected_time
);

if ($ins->execute()) {
    echo "<script>
      alert('Order placed successfully (Cash on Delivery)');
      window.location='my_account.php';
    </script>";
} else {
    die('Order failed. Please try again.');
}
// After reducing stock, fetch new stock + limit
$p = $conn->prepare("SELECT material_name, stock, low_stock_limit, unit_type FROM products WHERE product_id = ?");
$p->bind_param("i", $product_id);
$p->execute();
$r = $p->get_result()->fetch_assoc();

if ($r) {
    $productName = $r['material_name'];
    $newStock    = (int)$r['stock'];
    $limit       = (int)$r['low_stock_limit'];
    $unitType    = $r['unit_type'];

    // If stock reached or went below reorder level → SEND MAIL
    if ($newStock <= $limit) {
        sendLowStockMail($productName, $newStock, $limit, $unitType);
    }
}
