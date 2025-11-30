<?php
session_start();
include 'db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['order'])) {
    $customer_id = $_SESSION['customer_id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Get price
    $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    $total_price = $product['price'] * $quantity;

    // Insert into orders
    $insert = $conn->prepare("INSERT INTO orders (customer_id, product_id, quantity, total_price, order_status) VALUES (?, ?, ?, ?, 'Pending')");
    $insert->bind_param("iiid", $customer_id, $product_id, $quantity, $total_price);
    $insert->execute();

    echo "<script>alert('Order placed successfully!');</script>";
}
?>

<form method="POST">
    <select name="product_id">
        <?php
        $products = $conn->query("SELECT * FROM products");
        while ($row = $products->fetch_assoc()) {
            echo "<option value='{$row['id']}'>{$row['name']} - ₹{$row['price']}</option>";
        }
        ?>
    </select>
    <input type="number" name="quantity" min="1" required>
    <button type="submit" name="order">Place Order</button>
</form>
