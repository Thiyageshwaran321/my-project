<?php
session_start();
include "db.php";

// If you want admin login protection add:
# if (!isset($_SESSION['admin'])) { header("Location: admin_login.php"); exit(); }

$result = $conn->query("SELECT * FROM orders ORDER BY order_id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Order Panel</title>
<style>
body { font-family: Arial; background:#f2e7d5; }
table { width:90%; margin:20px auto; border-collapse: collapse; background:#fff; }
th, td { padding:12px; border:1px solid #ccc; text-align:left; }
th { background:#d1b961; }
select { padding:5px; }
button { padding:6px 12px; background:#d1b961; border:none; cursor:pointer; }
button:hover { background:#b7953a; }
</style>
</head>

<body>
<h2 style="text-align:center;">Admin — Update Order Status</h2>

<table>
<tr>
  <th>Order ID</th>
  <th>Customer ID</th>
  <th>Material</th>
  <th>Units</th>
  <th>Total</th>
  <th>Current Status</th>
  <th>Update</th>
</tr>

<?php while($o = $result->fetch_assoc()): ?>
<tr>
  <td><?= $o['order_id']; ?></td>
  <td><?= $o['customer_id']; ?></td>
  <td><?= $o['material_name']; ?></td>
  <td><?= $o['quantity']; ?></td>
  <td>₹<?= $o['total_amount']; ?></td>

  <td><b><?= $o['order_status']; ?></b></td>

  <td>
    <form action="update_status.php" method="POST">
      <input type="hidden" name="order_id" value="<?= $o['order_id']; ?>">

      <select name="status">
        <option value="placed" <?= ($o['order_status']=="placed")?"selected":"" ?>>Order Placed</option>
        <option value="shipped" <?= ($o['order_status']=="shipped")?"selected":"" ?>>Shipped</option>
        <option value="out_for_delivery" <?= ($o['order_status']=="out_for_delivery")?"selected":"" ?>>Out For Delivery</option>
        <option value="delivered" <?= ($o['order_status']=="delivered")?"selected":"" ?>>Delivered</option>
      </select>

      <button type="submit">Update</button>
    </form>
  </td>
</tr>
<?php endwhile; ?>
</table>

</body>
</html>
