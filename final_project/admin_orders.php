<?php
session_start();
include("db_connect.php");

session_start();
if(!isset($_SESSION['adminid'])){
    header("Location: admin_dashboard.php");
    exit();
}


// Update order status (if admin changes it)
if (isset($_POST['update_status'])) {
  $order_id = $_POST['order_id'];
  $status = $_POST['status'];
  $conn->query("UPDATE orders SET status='$status' WHERE order_id=$order_id");
}

// Fetch all orders
$sql = "SELECT o.order_id, o.material_name, o.quantity, o.price_per_unit, 
               o.total_amount, o.status, o.order_date, 
               c.username AS customer_name
        FROM orders o
        JOIN customers c ON o.customer_id = c.customer_id
        ORDER BY o.order_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Orders | PK BUILDERS</title>
  <link rel="stylesheet" href="admin.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f7f2e8;
      padding: 20px;
    }
    h1 {
      text-align: center;
      color: #4a2f1a;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 25px;
      background-color: #fff;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: center;
    }
    th {
      background-color: #d8b58d;
      color: #fff;
    }
    form {
      display: inline;
    }
    select {
      padding: 5px;
      border-radius: 5px;
    }
    button {
      background-color: #4a2f1a;
      color: white;
      border: none;
      padding: 5px 10px;
      border-radius: 5px;
      cursor: pointer;
    }
    button:hover {
      background-color: #6b3d23;
    }
  </style>
</head>
<body>
  <h1>Admin Dashboard - All Orders</h1>

  <table>
    <tr>
      <th>Order ID</th>
      <th>Customer</th>
      <th>Material</th>
      <th>Quantity</th>
      <th>Price</th>
      <th>Total</th>
      <th>Date</th>
      <th>Status</th>
      <th>Action</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= $row['order_id'] ?></td>
      <td><?= htmlspecialchars($row['customer_name']) ?></td>
      <td><?= htmlspecialchars($row['material_name']) ?></td>
      <td><?= $row['quantity'] ?></td>
      <td>₹<?= $row['price_per_unit'] ?></td>
      <td>₹<?= $row['total_amount'] ?></td>
      <td><?= $row['order_date'] ?></td>
      <td><?= $row['status'] ?></td>
      <td>
        <form method="POST">
          <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
          <select name="status">
            <option <?= $row['status']=="Pending" ? "selected" : "" ?>>Pending</option>
            <option <?= $row['status']=="Shipped" ? "selected" : "" ?>>Shipped</option>
            <option <?= $row['status']=="Delivered" ? "selected" : "" ?>>Delivered</option>
          </select>
          <button type="submit" name="update_status">Update</button>
        </form>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>

</body>
</html>
