<?php
session_start();
include "db.php";

// Redirect if user not logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: index.html");
    exit();
}

$customer_id = $_SESSION['customer_id'];

/* ---------------- FETCH CUSTOMER DETAILS ---------------- */
$stmt = $conn->prepare("SELECT username, email, mobilenum FROM customers WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* ---------------- FETCH ORDERS ---------------- */
$order_q = $conn->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY order_id DESC");
$order_q->bind_param("i", $customer_id);
$order_q->execute();
$orders = $order_q->get_result();
$order_q->close();

/* ---------------- FETCH CART ---------------- */
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

<style>
/* ===================== GENERAL STYLES ===================== */
body {
  margin: 0;
  font-family: 'Segoe UI', sans-serif;
  background-color: #f3e9d7;
  color: #333;
}

header {
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

nav a {
  margin-left: 20px;
  text-decoration: none;
  font-weight: bold;
  color: #000;
}
nav a:hover { color: #d1b961; }

/* ===================== ACCOUNT LAYOUT ===================== */
.account-container {
  display: flex;
  width: 95%;
  margin: 20px auto;
}

.sidebar {
  width: 250px;
  padding: 20px;
}

.sidebar ul li {
  padding: 12px;
  margin: 8px 0;
  cursor: pointer;
  border-radius: 6px;
}
.sidebar ul li.active,
.sidebar ul li:hover { background: #f7e49f; }

/* MAIN CONTENT AREA */
.main-content {
  flex: 1;
  padding: 30px;
}

.section { display: none; }
.section.active { display: block; }

/* BOX STYLES */
.box {
  background: #f1f3f6;
  padding: 20px;
  border-radius: 10px;
}

/* FULL WIDTH CART BOX */
.cart-item {
  width: 100%;
  background: #fff;
  padding: 18px;
  border-radius: 10px;
  margin-bottom: 12px;
  display: flex;
  justify-content: space-between;
  box-shadow: 0px 2px 10px rgba(0,0,0,0.08);
}

.track-btn {
  background: #d6a84c;
  padding: 5px 10px;
  color: black;
  border-radius: 5px;
  text-decoration: none;
  font-weight: bold;
}
.track-btn:hover { background: #b68a2f; }
</style>

</head>
<body>

<header>
  <div class="logo">
    <img src="static/pklogo.png" style="height:120px;">
  </div>

  <nav>
    <a href="index.html">HOME</a>
    <a href="material.html">MATERIAL</a>
    <a href="calculator.html">CALCULATOR</a>
    <a href="about.html">ABOUT</a>
    <a href="my_account.php">MY ACCOUNT</a>
    <a href="logout.php" style="color:#d1b961;">LOGOUT</a>
  </nav>
</header>

<div class="account-container">

  <!-- SIDEBAR -->
  <div class="sidebar">
    <h2>My Account</h2>
    <ul>
      <li class="active" onclick="showSection('profile')">👤 Profile</li>
      <li onclick="showSection('orders')">📦 My Orders</li>
      <li onclick="showSection('addresses')">🏠 Saved Addresses</li>
      <li onclick="showSection('cart')">🛒 Cart</li>
      <li onclick="showSection('feedback')">💬 Feedback</li>
      <li onclick="window.location.href='logout.php'">🚪 Logout</li>
    </ul>
  </div>

  <!-- MAIN CONTENT WRAPPER -->
  <div class="main-content">

    <!-- PROFILE SECTION -->
    <div id="profile" class="section active">
      <h3>Profile Information</h3>
      <div class="box">
        <p><strong>Name:</strong> <?= $customer['username']; ?></p>
        <p><strong>Email:</strong> <?= $customer['email']; ?></p>
        <p><strong>Phone:</strong> <?= $customer['mobilenum']; ?></p>
      </div>
    </div>

    <!-- ORDERS SECTION -->
    <div id="orders" class="section">
      <h3>My Orders</h3>
      <div class="box">

        <?php if ($orders->num_rows > 0): ?>
          <?php while ($o = $orders->fetch_assoc()): ?>
            <p>
              📦 <?= $o['material_name']; ?> — 
              <?= $o['quantity']; ?> units — 
              ₹<?= $o['total_amount']; ?> — 
              <b><?= $o['status']; ?></b>

              <a href="track_order.php?order_id=<?= $o['order_id']; ?>" class="track-btn">Track Order</a>
            </p>
          <?php endwhile; ?>
        <?php else: ?>
          <p>No orders yet.</p>
        <?php endif; ?>

      </div>
    </div>

    <!-- SAVED ADDRESSES -->
    <div id="addresses" class="section">
      <h3>Saved Addresses</h3>
      
      <div class="box">
        <h4>Add New Address</h4>
        <form action="save_address.php" method="POST">
          <input type="text" name="full_name" placeholder="Full Name" required><br><br>
          <input type="text" name="phone" placeholder="Phone Number" required><br><br>
          <input type="text" name="address_line" placeholder="Address Line" required><br><br>
          <input type="text" name="city" placeholder="City" required><br><br>
          <input type="text" name="pincode" placeholder="Pincode" required><br><br>
          <input type="text" name="state" placeholder="State" required><br><br>

          <button type="submit" style="padding:10px 20px; background:#9c7742; color:white; border:none; border-radius:5px;">
            Save Address
          </button>
        </form>

        <hr><br>
        <h4>Your Saved Addresses</h4>

        <?php
        $addr_q = $conn->prepare("SELECT * FROM customer_addresses WHERE customer_id = ?");
        $addr_q->bind_param("i", $customer_id);
        $addr_q->execute();
        $list = $addr_q->get_result();

        if ($list->num_rows > 0):
          while ($a = $list->fetch_assoc()):
        ?>

        <div style="background:#fff; padding:15px; border-radius:5px; margin-bottom:10px;">
          <b><?= $a['full_name']; ?></b> (<?= $a['phone']; ?>)<br>
          <?= $a['address_line']; ?><br>
          <?= $a['city']; ?> - <?= $a['pincode']; ?><br>
          <?= $a['state']; ?><br><br>

          <a href="delete_address.php?id=<?= $a['id']; ?>" style="color:red;">Delete</a>
        </div>

        <?php endwhile; else: ?>
          <p>No addresses saved.</p>
        <?php endif; ?>

      </div>
    </div>

    <!-- CART SECTION (FULL WIDTH) -->
    <div id="cart" class="section">
      <h3>My Cart</h3>

      <?php if ($cart_items->num_rows > 0): ?>
        <?php while ($c = $cart_items->fetch_assoc()): ?>

        <div class="cart-item">
          
          <div>
            <p><b>🛒 <?= $c['material_name']; ?></b></p>
            <p>Price: ₹<?= $c['price']; ?></p>

            <form action="update_cart.php" method="POST">
              <input type="hidden" name="cart_id" value="<?= $c['id']; ?>">
              Qty: <input type="number" name="unit" value="<?= $c['unit']; ?>" min="1" style="width:60px;">
              <button style="background:#9c7742; color:white; padding:5px 12px; border:none;">Update</button>
            </form>
          </div>

          <div>
            <a href="remove_cart.php?id=<?= $c['id']; ?>" style="color:red; margin-right:10px;">Remove</a>

            <form action="select_address.php" method="GET">
              <input type="hidden" name="product_id" value="<?= $c['product_id']; ?>">
              <input type="hidden" name="quantity" value="<?= $c['unit']; ?>">
              <button style="background:#28a745; color:white; padding:8px 15px; border:none; border-radius:5px;">
                Buy Now
              </button>
            </form>
          </div>

        </div>

        <?php endwhile; ?>
      <?php else: ?>
        <p>Your cart is empty.</p>
      <?php endif; ?>

    </div>
    <div id="feedback" class="section">
    <h3>My Feedback & Replies</h3>

    <?php
    $fb = $conn->prepare("SELECT * FROM feedback WHERE customer_id = ? ORDER BY id DESC");
    $fb->bind_param("i", $customer_id);
    $fb->execute();
    $feedbacks = $fb->get_result();

    if ($feedbacks->num_rows > 0) {
        while ($f = $feedbacks->fetch_assoc()) {
            echo "
            <div style='background:#fff; padding:15px; border-radius:6px; margin-bottom:10px;'>
                <p><b>Your Feedback:</b> {$f['message']}</p>
                <p><b>Submitted on:</b> {$f['created_at']}</p>
                <hr>
                <p><b>Admin Reply:</b><br> " .
                    ($f['admin_reply'] ? "<span style='color:green;'>{$f['admin_reply']}</span>" : "<i>No reply yet</i>") .
                "</p>
            </div>
            ";
        }
    } else {
        echo "<p>No feedback submitted yet.</p>";
    }
    ?>
</div>


   <div id="feedback" class="section">
  <h3>Feedback</h3>
  <div class="order-list">

    <form action="save_feedback.php" method="POST">
        <textarea name="message" placeholder="Write your feedback..." 
                  required style="width:100%; height:120px; padding:10px;"></textarea>
        <br><br>
        <button type="submit" 
                style="background:#9c7742; color:white; padding:10px 20px; border:none; border-radius:5px;">
            Submit Feedback
        </button>
    </form>

    <hr><br>

    <h4>Your Previous Feedback</h4>

    <?php
      $fb = $conn->prepare("SELECT * FROM feedback WHERE customer_id = ? ORDER BY id DESC");
      $fb->bind_param("i", $customer_id);
      $fb->execute();
      $fb_res = $fb->get_result();

      if ($fb_res->num_rows > 0) {
          while ($f = $fb_res->fetch_assoc()) {
              echo "<div style='background:#fff;padding:10px;border-radius:5px;margin-bottom:10px;'>
                      {$f['message']}<br>
                      <span style='font-size:12px;color:gray;'>{$f['created_at']}</span>
                    </div>";
          }
      } else {
          echo "<p>No feedback submitted yet.</p>";
      }
    ?>
  </div>
</div>


  </div> <!-- END MAIN CONTENT -->

</div> <!-- END ACCOUNT CONTAINER -->

<script>
function showSection(id) {
  document.querySelectorAll(".section").forEach(div => div.classList.remove("active"));
  document.querySelectorAll(".sidebar li").forEach(li => li.classList.remove("active"));

  document.getElementById(id).classList.add("active");
  event.target.classList.add("active");
}
</script>

</body>
</html>
