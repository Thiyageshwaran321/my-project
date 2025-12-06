<?php
include "db.php";

$sql = "
    SELECT o.*, c.username 
    FROM orders o
    JOIN customers c ON o.customer_id = c.customer_id
    ORDER BY o.order_id DESC
";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "<tr><td colspan='6' style='text-align:center;'>No orders found.</td></tr>";
    exit;
}

while ($row = $result->fetch_assoc()) {

    echo "
    <tr>
        <td>{$row['order_id']}</td>
        <td>{$row['username']}</td>
        <td>{$row['material_name']}</td>
        <td>{$row['quantity']}</td>
        <td>{$row['status']}</td>

        <td>
            <button class='status-btn' onclick=\"updateStatus({$row['order_id']}, 'shipped')\">Shipped</button>
            <button class='status-btn' onclick=\"updateStatus({$row['order_id']}, 'out_for_delivery')\">Out For Delivery</button>
            <button class='status-btn' onclick=\"updateStatus({$row['order_id']}, 'delivered')\">Delivered</button>
        </td>
    </tr>
    ";
}
?>
