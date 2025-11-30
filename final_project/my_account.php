<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    // If not logged in, redirect to login page
    header("Location: login.html");
    exit();
}

$customer_id = $_SESSION['customer_id'];
?>

<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header('Location: /index.php');
    exit;
}
$customer_id = (int)$_SESSION['customer_id'];

try {
    $pdo = new PDO('mysql:host=localhost;dbname=your_db;charset=utf8mb4','db_user','db_pass',[
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $pdo->prepare("
        SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name, p.price, p.image
        FROM cart c
        JOIN products p ON p.id = c.product_id
        WHERE c.customer_id = ?
    ");
    $stmt->execute([$customer_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log($e->getMessage());
    $items = [];
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Your Cart</title></head>
<body>
<h1>My Cart</h1>
<?php if (empty($items)): ?>
  <p>Your cart is empty. <a href="/products.php">Browse products</a></p>
<?php else: ?>
  <form action="update_cart.php" method="post">
    <table border="1" cellpadding="8">
      <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th><th>Action</th></tr></thead>
      <tbody>
      <?php
      $total = 0;
      foreach ($items as $it):
        $subtotal = $it['price'] * $it['quantity'];
        $total += $subtotal;
      ?>
        <tr>
          <td>
            <img src="<?php echo htmlspecialchars($it['image']); ?>" width="60" alt="">
            <?php echo htmlspecialchars($it['name']); ?>
          </td>
          <td><?php echo number_format($it['price'],2); ?></td>
          <td>
            <input type="number" name="qty[<?php echo $it['cart_id']; ?>]" value="<?php echo $it['quantity']; ?>" min="1" style="width:60px;">
          </td>
          <td><?php echo number_format($subtotal,2); ?></td>
          <td>
            <a href="remove_from_cart.php?cart_id=<?php echo $it['cart_id']; ?>" onclick="return confirm('Remove item?')">Remove</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>

    <p><strong>Total: ₹<?php echo number_format($total,2); ?></strong></p>
    <button type="submit">Update Quantities</button>
    <a href="place_order.php">Place Order</a>
  </form>
<?php endif; ?>
</body>
</html>


<?php
session_start();
include 'db_connect.php';
$cid = $_SESSION['customer_id'];
$result = $conn->query("SELECT * FROM orders WHERE customer_id='$cid' ORDER BY order_id DESC");
?>

<h2>My Orders</h2>
<table border="1" cellpadding="10">
<tr>
  <th>Order ID</th>
  <th>Material</th>
  <th>Quantity</th>
  <th>Total</th>
  <th>Status</th>
  <th>Date</th>
</tr>
<?php while($row = $result->fetch_assoc()) { ?>
<tr>
  <td><?= $row['order_id'] ?></td>
  <td><?= $row['material_name'] ?></td>
  <td><?= $row['quantity'] ?></td>
  <td>₹<?= $row['total_price'] ?></td>
  <td><?= $row['status'] ?></td>
  <td><?= $row['order_date'] ?></td>
</tr>
<?php } ?>
</table>
<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: index.html"); // if not logged in, go to home/login
  exit();
}
?>

<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Cart - PK Builders</title>
  <link rel="stylesheet" href="material.css">
</head>
<body>
  <h1 style="text-align:center;">My Cart</h1>

  <?php if (!empty($_SESSION['cart'])): ?>
    <table border="1" style="width:80%;margin:auto;border-collapse:collapse;text-align:center;">
      <tr>
        <th>Material</th>
        <th>Unit</th>
        <th>Price (₹)</th>
        <th>Total (₹)</th>
      </tr>
      <?php 
      $grand_total = 0;
      foreach ($_SESSION['cart'] as $item):
        $grand_total += $item['total'];
      ?>
        <tr>
          <td><?= htmlspecialchars($item['material']) ?></td>
          <td><?= $item['unit'] ?></td>
          <td><?= $item['price'] ?></td>
          <td><?= $item['total'] ?></td>
        </tr>
      <?php endforeach; ?>
      <tr>
        <td colspan="3"><strong>Grand Total</strong></td>
        <td><strong>₹<?= $grand_total ?></strong></td>
      </tr>
    </table>
  <?php else: ?>
    <p style="text-align:center;">Your cart is empty!</p>
  <?php endif; ?>

  <div style="text-align:center;margin-top:20px;">
    <a href="material.php">← Continue Shopping</a>
  </div>

  <meta charset="UTF-8">
  <title>My Account</title>
  <link rel="stylesheet" href="style.css">
   <style>
  body {
  margin: 0;
  font-family: 'Segoe UI', sans-serif;
  background-color: #f3e9d7;
  color: #333;

}

header {
  margin-right:20px;
  background-color: #f3e9d7;
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.home-1{
      margin-left:20px;
        margin-right: 20px;
         color:#333;
           
    }
    .home-1:hover{
         color:#d1b961;
    }
    
    .material{
    
         margin-left:20px;
        margin-right: 20px;
    color:#333;
    }
    .material:hover{
         color: #d1b961;        
    }
    .calculator{
      margin-left:20px;
        margin-right: 20px;
         color:#333;
    }
    .calculator:hover{
        color:#d1b961;
    }
    .about{
         margin-left:20px;
        margin-right: 20px;
          color:#333;
    }
    .about:hover{
         color:#d1b961;
    }
    .contact{ 
        margin-left:20px;
        margin-right: 20px;
       color:#333;
}
    .contact:hover{ color:#d1b961;
      }
.headofpro{
      margin-right:438px;
    font-size:xx-large;
    color: #d1b961;
    margin-left:0;
  align-items: center;
    margin-bottom: 10px;
  
     

}

.logo img {
  height: 50px;
}

.logo {
  text-align: center;
}

nav ul {
  list-style: none;
  display: flex;
  gap: 30px;
}

nav a {
  text-decoration: none;
  color: #444;
  font-weight: bold;
}

 nav{
    padding-top:20px ;
    margin :20px;
   }

    nav a {
      color: #030202;;
      margin-left: 20px;
      text-decoration: none;
      font-weight: bold;
      cursor: pointer;
      margin-top: 20%;
    }




  

    /* Login Modal Box */
    .modal {
      display: none;
      position: fixed;
      z-index: 100;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      backdrop-filter: blur(5px);
      background-color: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
    }

    .login-box {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 15px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.37);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      padding: 30px;
      width: 300px;
      color: white;
      position: relative;
    }

    .login-box h2 {
      text-align: center;
      margin-bottom: 20px;
    }
      .login-box input[type="username"],
    .login-box input[type="PASSWORD"]

      {
      width: 100%;
      backdrop-filter: blur(10px);
      padding: 10px;
      margin: 10px 0;
      border: none;
      border-radius: 5px;
      background: rgba(255, 255, 255, 0.2);
      color: white;
    }

    .login-box input::placeholder {
      color: #ccc;
    }

    .login-box .options {
      display: flex;
      justify-content: space-between;
      font-size: 14px;
      margin: 10px 0;
    }

    .login-box .options input[type="checkbox"] {
      margin-right: 5px;
    }

    .login-box button {
      width: 100%;
      padding: 10px;
      border: none;
      background: #000;
      color:#d1b961;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
      margin-top: 10px;
    }

    .login-box .register-link {
      text-align: center;
      font-size: 13px;
      margin-top: 10px;
    }

    .close-btn {
      position: absolute;
      top: 10px;
      right: 15px;
      color:#d1b961;
      font-size: 18px;
      cursor: pointer;          
    }
    
.login{
  color:  #d1b961;
}
    

.login:hover{
  color:#333

}

.nav{
  align-items: center;
}
.img1{
  width:100%;
  height:100%;
}
.frontbui{
  padding: 12px 24px;
   background-color: black;
   color:#d1b961;
    border: 2px solid black;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold; 
    margin-right: 10px;
}
.frontbui:hover {
  background-color: #2e2e2e;
}

.frontcal{
  padding: 12px 24px;
   border: 2px solid black;
      color:#d1b961;
   border-radius: 5px; 
   text-decoration: none;
   font-weight: bold;
}

.frontcal:hover{
  background:rgb(243, 172, 8)
}

   /* Account page */
.account-container {
      display: flex;
      width: 95%;
      max-width: 1500px;
      margin: 10px auto;
      background: #f3e9d7;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    /* Sidebar */
    .sidebar {
      width: 250px;
      background: #f3e9d7;
      color: #333;
      padding: 20px;
    }

    .sidebar h2 {
      margin-bottom: 20px;
      font-size: 20px;
    }

    .sidebar ul {
      list-style: none;
    }

    .sidebar ul li {
      padding: 12px 10px;
      margin: 8px 0;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .sidebar ul li:hover,
    .sidebar ul li.active {
      background: #f7e49f
    }

    /* Main Content */
    .main-content {
      flex: 1;
      padding: 30px;
    }

    .section {
      display: none;
      animation: fadeIn 0.4s ease-in-out;
    }

    .section.active {
      display: block;
    }

    @keyframes fadeIn {
      from {opacity: 0;}
      to {opacity: 1;}
    }

    .section h3 {
      margin-bottom: 20px;
      font-size: 22px;
      border-bottom: 2px solid #f3e9d7;
      padding-bottom: 8px;
      display: inline-block;
    }

    .profile-info, .order-list, .address-list {
      background: #f1f3f6;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    .profile-info p, .order-list p, .address-list p {
      margin: 10px 0;
    }

    .logout-btn {
      background: #ff4757;
      border: none;
      padding: 10px 20px;
      color: #fff;
      font-size: 14px;
      border-radius: 6px;
      cursor: pointer;
      margin-top: 20px;
      transition: 0.3s;
    }

    .logout-btn:hover {
      background: #e84118;
    }
    

#roleSelectBox button {
  width: 100%;
  padding: 10px;
  margin-top: 10px;
  border: none;
  background: #000;
  color: #d1b961;
  border-radius: 5px;
  cursor: pointer;
  font-weight: bold;
}

#roleSelectBox button:hover {
  background: #222;
}

  </style>
</head>
<body>
<header>
  <div class="logo" style="display:flex;align-items:center;">
    <img src="static/pklogo.png" alt="Logo" style="height:130px;width:180px;margin-right:15px;margin-top:-15px;">
  </div>
  <nav>
    <a href="index.html" class="home-1">HOME</a> 
    <a href="material.html" class="material">MATERIAL</a>
    <a href="calculator.html" class="calculator">CALCULATOR</a>
    <a href="about.html" class="about">ABOUT</a>
    <a href="account.php" class="contact">MY ACCOUNT</a>
    <a href="logout.php" class="login">LOGOUT</a>
  </nav>
</header>

<main>
  <div class="account-container">
    <!-- Sidebar -->
    <div class="sidebar">
      <h2>My Account</h2>
      <ul>
        <li class="active" onclick="showSection('profile')">👤 Profile</li>
        <li onclick="showSection('orders')">📦 My Orders</li>
        <li onclick="showSection('addresses')">🏠 Saved Addresses</li>
        <li onclick="showSection('settings')">⚙️ Settings</li>
        <li onclick="window.location.href='logout.php'">🚪 Logout</li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <div id="profile" class="section active">
        <h3>Profile Information</h3>
        <div class="profile-info">
          <p><strong>Name:</strong> <?php echo $_SESSION['username']; ?></p>
          <p><strong>Email:</strong> <?php echo $_SESSION['email']; ?></p>
          <p><strong>Phone:</strong> <?php echo $_SESSION['mobilenum']; ?></p>
        </div>
      </div>

      <div id="orders" class="section">
        <h3>My Orders</h3>
        <div class="order-list">
          <p>No orders yet.</p>
        </div>
      </div>

      <div id="addresses" class="section">
        <h3>Saved Addresses</h3>
        <div class="address-list">
          <p>🏠 Add your address info soon...</p>
        </div>
      </div>

      <div id="settings" class="section">
        <h3>Account Settings</h3>
        <p>Coming soon: update your password and details.</p>
      </div>
    </div>
  </div>
</main>

<script>
function showSection(sectionId) {
  let sections = document.querySelectorAll('.section');
  sections.forEach(sec => sec.classList.remove('active'));
  document.getElementById(sectionId).classList.add('active');
}
</script>
</body>
</html>
