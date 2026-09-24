<?php
session_start();
include "db.php";

if (!isset($_SESSION['customer_id'])) {
    header("Location: index.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

/* CUSTOMER DETAILS */
$stmt = $conn->prepare("SELECT username, email, mobilenum FROM customers WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* ORDERS */
$order_q = $conn->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY order_id DESC");
$order_q->bind_param("i", $customer_id);
$order_q->execute();
$orders = $order_q->get_result();
$order_q->close();

/* CART */
$cart_q = $conn->prepare("SELECT * FROM cart WHERE customer_id = ?");
$cart_q->bind_param("i", $customer_id);
$cart_q->execute();
$cart_items = $cart_q->get_result();
$cart_q->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Account</title>


</head>

<body>
  <link rel="stylesheet" href="my_account.css">
<header>
  <img src="static/pklogo.png" style="height:120px;">
  <nav>
    <a href="index.php">HOME</a>
    <a href="material.php">MATERIAL</a>
    <a href="calculator.php">CALCULATOR</a>
    <a href="about.php">ABOUT</a>
    <a href="my_account.php">MY ACCOUNT</a>
    <a href="logout.php" style="color:#d1b961;">LOGOUT</a>
  </nav>
</header>

<div class="account-container">

<!-- SIDEBAR -->
<div class="sidebar">
  <h2>My Account</h2>
  <ul>
    <li class="active" onclick="showSection('profile',this)">👤 Profile</li>
    <li onclick="showSection('orders',this)">📦 My Orders</li>
    <li onclick="showSection('addresses',this)">🏠 Saved Addresses</li>
    <li onclick="showSection('cart',this)">🛒 Cart</li>
    <li onclick="showSection('feedback',this)">💬 Feedback</li>
    <li onclick="window.location='logout.php'">🚪 Logout</li>
  </ul>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">

<!-- PROFILE -->
<div id="profile" class="section active">
<h3>Profile</h3>
<div class="box">
<p><b>Name:</b> <?= htmlspecialchars($customer['username']) ?></p>
<p><b>Email:</b> <?= htmlspecialchars($customer['email']) ?></p>
<p><b>Phone:</b> <?= htmlspecialchars($customer['mobilenum']) ?></p>

<button onclick="openUpdateProfile()"
style="background:#8a6b36;color:#fff;padding:10px 18px;border:none;border-radius:6px;">
Update Details
</button>
</div>
</div>

<!-- UPDATE MODAL -->
<div id="updateProfileModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;
background:rgba(0,0,0,0.5);z-index:2000;">
<div style="background:#fff;width:400px;margin:130px auto;padding:25px;border-radius:10px;">
<h3>Update Profile</h3>
<form action="update_profile_final.php" method="POST">
<input type="text" name="username" value="<?= $customer['username'] ?>" required><br><br>
<input type="email" name="email" value="<?= $customer['email'] ?>" required><br><br>
<input type="text" name="mobilenum" value="<?= $customer['mobilenum'] ?>" required><br><br>
<button style="background:#28a745;color:#fff;padding:8px 15px;border:none;">Confirm</button>
<button type="button" onclick="closeUpdateProfile()">Cancel</button>
</form>
</div>
</div>




<!-- ORDERS -->
<div id="orders" class="section">
  <h3>My Orders</h3>
  <?php if($orders->num_rows): while($o=$orders->fetch_assoc()): ?>
    <div class="order-item">
      <?= $o['material_name'] ?> |
      <?= $o['quantity'] ?> units |
      ₹<?= $o['total_amount'] ?> |
      <b><?= $o['status'] ?></b>

      <a class="track-btn"
         href="track_order.php?order_id=<?= $o['order_id'] ?>">
        Track
      </a>

      <?php if (!empty($o['expected_delivery_date'])): ?>
        <div class="expected-box">
          <b>Expected</b>
          <?= date("d.m.Y", strtotime($o['expected_delivery_date'])) ?>
          <?php if (!empty($o['expected_delivery_time'])): ?>
            at <?= date("g.i A", strtotime($o['expected_delivery_time'])) ?>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <!-- 🔴 CANCEL ORDER BUTTON -->
      <?php
        $canCancel = in_array(
          strtolower($o['status']),
          ['pending', 'order placed']
        );
      ?>

      <?php if ($canCancel): ?>
        <form action="cancel_order.php"
              method="POST"
              onsubmit="return confirm('Are you sure you want to cancel this order?');"
              style="margin-top:10px;">

          <input type="hidden"
                 name="order_id"
                 value="<?= $o['order_id'] ?>">

          <button type="submit"
                  style="
                    background:#dc3545;
                    color:white;
                    padding:6px 14px;
                    border:none;
                    border-radius:5px;
                    cursor:pointer;">
            Cancel Order
          </button>
        </form>
      <?php endif; ?>

    </div>
  <?php endwhile; else: ?>
    <p>No orders yet.</p>
  <?php endif; ?>
</div>


<!-- ADDRESSES -->
<div id="addresses" class="section">
<h3>Saved Addresses</h3>

<div class="box">
<form action="save_address.php" method="POST">
  <input name="full_name" placeholder="Full Name" required><br><br>
  <input name="phone" placeholder="Phone" required><br><br>
  <input name="address_line" placeholder="Address" required><br><br>
  <input name="city" placeholder="City" required><br><br>
  <input name="pincode" placeholder="Pincode" required><br><br>
  <input name="state" placeholder="State" required><br><br>
  <button>Save Address</button>
</form>
</div>

<div class="box">
<h4>Your Saved Addresses</h4>

<form action="set_default_address.php" method="POST">

<?php
$addr = $conn->prepare(
  "SELECT * FROM customer_addresses WHERE customer_id = ?"
);
$addr->bind_param("i", $customer_id);
$addr->execute();
$res = $addr->get_result();

if ($res->num_rows > 0):
while ($a = $res->fetch_assoc()):
  $checked = ($a['is_default'] == 1) ? "checked" : "";
?>

<label style="
  display:block;
  background:white;
  padding:15px;
  border-radius:8px;
  margin-bottom:10px;
  cursor:pointer;
  <?= $checked ? 'border:2px solid #28a745;' : '' ?>
">
  <input 
    type="radio" 
    name="selected_address_id" 
    value="<?= $a['id'] ?>" 
    <?= $checked ?> 
    required
    style="margin-right:10px;"
  >
  <b><?= htmlspecialchars($a['full_name']) ?></b><br>
  <?= htmlspecialchars($a['address_line']) ?><br>
  <?= htmlspecialchars($a['city']) ?> - <?= htmlspecialchars($a['pincode']) ?><br>
  <?= htmlspecialchars($a['state']) ?>
</label>

<?php endwhile; ?>

<button style="background:#28a745;color:white;padding:8px 15px;border:none;border-radius:5px;">
  Use Selected Address
</button>

<?php else: ?>
<p>No saved addresses.</p>
<?php endif; ?>

</form>
</div>
</div>

<style>
/* BASE */


/* NAV */

/* MAIN WRAPPER */



/* SECTIONS */
.section {
  display: none;
}
.section.active {
  display: block;
}

/* BOXES */
.box {
  background: #f1f3f6;
  padding: 20px;
  border-radius: 10px;
  margin-bottom: 40px;
  margin-top:20px;
  
}

/* ORDER & CART */
.order-item,
.cart-item {
  background: white;
  padding: 15px;
  border-radius: 10px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  margin-bottom: 12px;
}

.cart-item {
  display: flex;
  justify-content: space-between;
}

/* TRACK BUTTON */
.track-btn {
  background: #d6a84c;
  padding: 5px 10px;
  border-radius: 5px;
  text-decoration: none;
  font-weight: bold;
  color: black;
}

/* EXPECTED DELIVERY */
.expected-box {
  background: #fdf4d7;
  padding: 10px;
  border-radius: 8px;
  margin-top: 5px;
}

</style>
<!-- CART -->
<div id="cart" class="section">
<h3>My Cart</h3>
<?php if($cart_items->num_rows): while($c=$cart_items->fetch_assoc()): ?>
<div class="cart-item">
<div>
  <p><b><?= $c['material_name'] ?></b></p>
  <p>Price: ₹<?= $c['price'] ?></p>
  <p>Total: ₹<?= $c['price']*$c['unit'] ?></p>
  <form action="update_cart.php" method="POST">
    <input type="hidden" name="cart_id" value="<?= $c['id'] ?>">
    Qty: <input type="number" name="unit" min="1" value="<?= $c['unit'] ?>" required>
    <button>Update</button>
  </form>
</div>
<div>
  <a href="remove_cart.php?id=<?= $c['id'] ?>" style="color:red;">Remove</a><br><br>
  <form action="select_address.php" method="GET">
    <input type="hidden" name="product_id" value="<?= $c['product_id'] ?>">
    <input type="hidden" name="quantity" value="<?= $c['unit'] ?>">
    <button>Buy Now</button>
  </form>
</div>
</div>
<?php endwhile; else: ?>
<p>Your cart is empty.</p>
<?php endif; ?>
</div>

<!-- FEEDBACK -->
<div id="feedback" class="section">
<h3>Feedback</h3>

<div class="box">
<form action="save_feedback.php" method="POST">
<textarea name="message" minlength="5" required style="width:100%;height:120px;"></textarea><br><br>
<button>Submit</button>
</form>
</div>

<?php
$fb=$conn->prepare("SELECT * FROM feedback WHERE customer_id=? ORDER BY id DESC");
$fb->bind_param("i",$customer_id);
$fb->execute();
$fres=$fb->get_result();
while($f=$fres->fetch_assoc()):
?>
<div class="box">
<b>Your Feedback:</b> <?= htmlspecialchars($f['message']) ?><br>
<b>Admin Reply:</b>

<?= !empty($f['admin_reply']) ? "<span style='color:green'>{$f['admin_reply']}</span>" : "<i>No reply yet</i>" ?>
</div>
<?php endwhile; ?>
</div>

</div>
</div>

<script>
function showSection(id,el){
  document.querySelectorAll('.section').forEach(s=>s.classList.remove('active'));
  document.querySelectorAll('.sidebar li').forEach(li=>li.classList.remove('active'));
  document.getElementById(id).classList.add('active');
  el.classList.add('active');
}

function openUpdateProfile(){
  document.getElementById("updateProfileModal").style.display="block";
}
function closeUpdateProfile(){
  document.getElementById("updateProfileModal").style.display="none";
}
</script>


</body>
</html>
