<?php
include 'db.php';
?>

<h2>Admin Dashboard - Orders</h2>
<table border="1" cellpadding="10">
<tr>
    <th>Order ID</th>
    <th>Customer</th>
    <th>Product</th>
    <th>Quantity</th>
    <th>Total Price</th>
    <th>Status</th>
    <th>Date</th>
</tr>

<?php
$query = "
SELECT orders.id, customers.name AS customer, products.name AS product,
       orders.quantity, orders.total_price, orders.order_status, orders.order_date
FROM orders
JOIN customers ON orders.customer_id = customers.id
JOIN products ON orders.product_id = products.id
ORDER BY orders.id DESC
";

$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['customer']}</td>
        <td>{$row['product']}</td>
        <td>{$row['quantity']}</td>
        <td>₹{$row['total_price']}</td>
        <td>{$row['order_status']}</td>
        <td>{$row['order_date']}</td>
    </tr>";
}
?>
</table>
