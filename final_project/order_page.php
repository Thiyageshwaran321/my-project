<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

// Login check
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

// If product is selected from material.html
if (isset($_GET['product_id'])) {

    $product_id = intval($_GET['product_id']);

    // Fetch product details
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if (!$product) {
        echo "<h3>Product not found!</h3>";
        exit();
    }
}

// When order button clicked
if (isset($_POST['place_order'])) {

    $customer_id = $_SESSION['customer_id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Fetch product for price
    $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if (!$product) {
        echo "<script>alert('Invalid product');</script>";
        exit();
    }

    $total_price = $product['price'] * $quantity;

    // Insert order
    $insert = $conn->prepare("INSERT INTO orders (customer_id, product_id, quantity, total_price, order_status) VALUES (?, ?, ?, ?, ?)");
    $status = "Pending";
    $insert->bind_param("iiids", $customer_id, $product_id, $quantity, $total_price, $status);

    if ($insert->execute()) {
        echo "<script>
                alert('Order placed successfully!');
                window.location.href='myaccount.php';
              </script>";
        exit();
    } else {
        echo "<script>alert('Order failed!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Place Order</title>
</head>

<body>

<?php if (isset($product)): ?>
    <h2>Order Material</h2>
<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

// GET from material.html
if (isset($_GET['material']) && isset($_GET['price'])) {
    $material = $_GET['material'];
    $price = $_GET['price'];
    $img = $_GET['img'];
}

// ORDER SUBMIT
if (isset($_POST['place_order'])) {

    $customer_id = $_SESSION['customer_id'];
    $material = $_POST['material'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    $total = $price * $quantity;

    $insert = $conn->prepare(
        "INSERT INTO orders (customer_id, material, quantity, total_price, order_status)
         VALUES (?, ?, ?, ?, 'Pending')"
    );
    $insert->bind_param("isid", $customer_id, $material, $quantity, $total);

    if ($insert->execute()) {
        echo "<script>alert('Order Placed Successfully!'); window.location='myaccount.php';</script>";
        exit();
    } else {
        echo "<script>alert('Order Failed');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Place Order</title>
</head>
<body>

<h2>Place Order</h2>

<img src="<?= $img ?>" width="200">

<h3><?= $material ?></h3>
<p>Price per unit: ₹<?= $price ?></p>

<form method="POST">
    <input type="hidden" name="material" value="<?= $material ?>">
    <input type="hidden" name="price" value="<?= $price ?>">

    Quantity:
    <input type="number" name="quantity" min="1" required><br><br>

    <button type="submit" name="place_order">Confirm Order</button>
</form>

</body>
</html>

    <h3>Material: <?= $product['name']; ?></h3>
    <p>Price: ₹ <?= $product['price']; ?></p>

    <form method="POST">
        <input type="hidden" name="product_id" value="<?= $product['id']; ?>">

        <label>Quantity:</label>
        <input type="number" name="quantity" min="1" required>

        <button type="submit" name="place_order">Place Order</button>
    </form>

<?php else: ?>

    <h3>No product selected!</h3>
    <p>Please go back to <a href="material.html">Material Page</a></p>

<?php endif; ?>

</body>
</html>
