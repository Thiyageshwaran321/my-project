<?php
session_start();
include 'db.php';

/* ------------------------------------------------
   MUST BE LOGGED IN
---------------------------------------------------*/
if (!isset($_SESSION['customer_id'])) {
    echo "<script>alert('Please login to place an order'); window.location='index.html';</script>";
    exit;
}

$customer_id = $_SESSION['customer_id'];

/* ------------------------------------------------
   VALIDATE REQUIRED FIELDS
---------------------------------------------------*/
if (
    !isset($_POST['product_id']) ||
    !isset($_POST['quantity']) ||
    !isset($_POST['address_id'])
) {
    echo "Invalid request. Missing details.";
    exit;
}

$product_id = (int) $_POST['product_id'];
$quantity   = (int) $_POST['quantity'];
$address_id = (int) $_POST['address_id'];

// Validate quantity
if ($quantity <= 0) {
    echo "Invalid quantity.";
    exit;
}

/* ------------------------------------------------
   FETCH PRODUCT DETAILS
---------------------------------------------------*/
$stmt = $conn->prepare("SELECT material_name, price FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Product not found!";
    exit;
}

$product = $result->fetch_assoc();
$material = $product['material_name'];
$price = $product['price'];
$total_amount = $price * $quantity;

/* ------------------------------------------------
   VERIFY ADDRESS BELONGS TO THIS USER
---------------------------------------------------*/
$chk = $conn->prepare("SELECT id FROM customer_addresses WHERE id = ? AND customer_id = ?");
$chk->bind_param("ii", $address_id, $customer_id);
$chk->execute();
$addr_res = $chk->get_result();

if ($addr_res->num_rows === 0) {
    echo "Invalid address selection!";
    exit;
}

/* ------------------------------------------------
   INSERT ORDER INTO DATABASE
---------------------------------------------------*/
$stmt2 = $conn->prepare("
    INSERT INTO orders 
        (customer_id, product_id, material_name, quantity, total_amount, status, address_id, placed_time) 
    VALUES 
        (?, ?, ?, ?, ?, 'pending', ?, NOW())
");

$stmt2->bind_param(
    "iisidi",
    $customer_id,
    $product_id,
    $material,
    $quantity,
    $total_amount,
    $address_id
);

/* ------------------------------------------------
   EXECUTE AND REDIRECT
---------------------------------------------------*/
if ($stmt2->execute()) {
    echo "<script>
            alert('Order placed successfully!');
            window.location='my_account.php';
          </script>";
} else {
    echo "Order failed: " . $stmt2->error;
}

?>
