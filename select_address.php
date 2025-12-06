<?php
session_start();
include "db.php";

if (!isset($_SESSION['customer_id'])) {
    header("Location: index.html");
    exit();
}

$customer_id = $_SESSION['customer_id'];

if (!isset($_GET['product_id']) || !isset($_GET['quantity'])) {
    echo "Invalid request";
    exit;
}

$product_id = (int) $_GET['product_id'];
$quantity   = (int) $_GET['quantity'];

// Fetch saved addresses
$stmt = $conn->prepare("SELECT * FROM customer_addresses WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$addresses = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
<title>Select Delivery Address</title>

<style>
body {
    font-family: 'Segoe UI';
    background: #f3e9d7;
    margin: 0;
    padding: 0;
}

/* Wrapper */
.container {
    width: 60%;
    margin: 50px auto;
    background: white;
    padding: 25px;
    border-radius: 12px;
    border: 1px solid #ddd;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Heading */
h2 {
    color: #8a6b36;
    margin-bottom: 20px;
    font-size: 28px;
}

/* Address box */
.address-box {
    border: 2px solid #ddd;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    transition: 0.3s;
}

.address-box:hover {
    border-color: #8a6b36;
    background: #faf4e4;
}

/* Radio button spacing */
.address-box input[type="radio"] {
    transform: scale(1.3);
    margin-right: 10px;
}

/* Submit button */
.btn {
    background: #8a6b36;
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 17px;
    font-weight: bold;
}

.btn:hover {
    background: #6f5428;
}

/* No address text */
.no-address {
    color: red;
    font-size: 18px;
    font-weight: bold;
}
</style>

</head>
<body>

<div class="container">

<h2>Select Delivery Address</h2>

<form action="place_order.php" method="POST">

    <!-- Hidden values -->
    <input type="hidden" name="product_id" value="<?= $product_id ?>">
    <input type="hidden" name="quantity" value="<?= $quantity ?>">

    <?php if ($addresses->num_rows > 0): ?>

        <?php while ($a = $addresses->fetch_assoc()): ?>
            <label>
                <div class="address-box">
                    <input type="radio" name="address_id" value="<?= $a['id'] ?>" required>

                    <b><?= $a['full_name'] ?></b> (<?= $a['phone'] ?>) <br>
                    <?= $a['address_line'] ?> <br>
                    <?= $a['city'] ?> - <?= $a['pincode'] ?> <br>
                    <?= $a['state'] ?>
                </div>
            </label>
        <?php endwhile; ?>

    <?php else: ?>
        <p class="no-address">No saved addresses found!</p>
        <p>Please add an address in My Account → Saved Addresses</p>
    <?php endif; ?>
<div class="address-box">
    <label>
        <input type="radio" name="payment_method" value="COD" checked>
        <b>Cash On Delivery</b> (Only available)
    </label>
</div>
    <br>
    <button type="submit" class="btn">Confirm & Place Order</button>
    

</form>

</div>

</body>
</html>
