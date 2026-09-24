<?php
session_start();
include "db.php";

/* LOGIN CHECK */
if (!isset($_SESSION['customer_id'])) {
    header("Location: index.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

/* PRODUCT CHECK */
if (!isset($_GET['product_id'], $_GET['quantity'])) {
    die("Invalid Request");
}

$product_id = (int)$_GET['product_id'];
$quantity   = (int)$_GET['quantity'];

/* PRODUCT DETAILS */
$p = $conn->prepare(
  "SELECT material_name, price FROM products WHERE product_id=?"
);
$p->bind_param("i", $product_id);
$p->execute();
$product = $p->get_result()->fetch_assoc();

if (!$product) {
    die("Product not found");
}

$material_name = $product['material_name'];
$unit_price    = (float)$product['price'];
$material_cost = $unit_price * $quantity;

/* ADDRESS: GET DEFAULT FIRST */
$a = $conn->prepare("
  SELECT * FROM customer_addresses 
  WHERE customer_id=? AND is_default=1
");
$a->bind_param("i", $customer_id);
$a->execute();
$res = $a->get_result();

/* FALLBACK: ANY ADDRESS */
if ($res->num_rows === 0) {
    $a = $conn->prepare("
      SELECT * FROM customer_addresses 
      WHERE customer_id=? 
      ORDER BY id ASC 
      LIMIT 1
    ");
    $a->bind_param("i", $customer_id);
    $a->execute();
    $res = $a->get_result();

    if ($res->num_rows === 0) {
        die("<h3>Please add an address in My Account before ordering.</h3>");
    }
}

$address = $res->fetch_assoc();

/* FINAL TOTAL (NO DELIVERY CHARGE) */
$total_amount  = $material_cost;
$delivery_days = 4;
?>

<!DOCTYPE html>
<html>
<head>
<title>Select Delivery Address</title>

<style>
body { font-family:Segoe UI; background:#f3e9d7; }
.container { width:80%; margin:30px auto; }
.card {
  background:#fff;
  padding:20px;
  border-radius:12px;
  margin-bottom:20px;
}
.summary {
  background:#faf4e4;
  border-left:6px solid #8a6b36;
  padding:20px;
  border-radius:12px;
}
.btn {
  background:#8a6b36;
  color:#fff;
  padding:12px 25px;
  border:none;
  border-radius:8px;
  font-size:16px;
  cursor:pointer;
}
</style>
</head>

<body>

<div class="container">

<h2>Select Delivery Address</h2>

<div class="card">
  <b><?= htmlspecialchars($address['full_name']) ?></b>
  (<?= htmlspecialchars($address['phone']) ?>)<br>
  <?= htmlspecialchars($address['address_line']) ?>,
  <?= htmlspecialchars($address['city']) ?> –
  <?= htmlspecialchars($address['pincode']) ?><br>
  <?= htmlspecialchars($address['state']) ?>
  <br><br>
  <input type="radio" checked> <b>Cash On Delivery</b>
</div>

<div class="summary">
  <h3>Order Summary</h3>

  <p><b>Material:</b> <?= htmlspecialchars($material_name) ?></p>
  <p><b>Unit Price:</b> ₹<?= number_format($unit_price) ?></p>
  <p><b>Quantity:</b> <?= $quantity ?></p>
  <p><b>Material Cost:</b> ₹<?= number_format($material_cost) ?></p>

  <hr>

  <p><b>Expected Delivery:</b> <?= $delivery_days ?> days</p>
  <p style="font-size:18px;">
    <b>Total Amount:</b> ₹<?= number_format($total_amount) ?>
  </p>
</div>

<br>

<form action="place_order.php" method="POST">
  <input type="hidden" name="product_id" value="<?= $product_id ?>">
  <input type="hidden" name="quantity" value="<?= $quantity ?>">
  <input type="hidden" name="total_amount" value="<?= $total_amount ?>">
  <input type="hidden" name="expected_delivery_full" value="<?= date('Y-m-d H:i:s') ?>">

  <button class="btn">Confirm & Place Order</button>
</form>

</div>
</body>
</html>
