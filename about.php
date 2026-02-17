<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About | PK Building Material</title>
   <link rel="stylesheet" href="about.css">
 
</head>

<body>

<header>
  <div class="logo">
    <img src="static/pklogo.png" style="height:130px;width:180px;margin-top:-15px;">
  </div>

  <nav>
    <a href="index.php">HOME</a>
    <a href="material.php">MATERIAL</a>
    <a href="calculator.php">CALCULATOR</a>
    <a href="about.php">ABOUT</a>

    <?php if(isset($_SESSION['customer_id'])): ?>
      <a href="my_account.php">MY ACCOUNT</a>
      <a href="logout.php" style="color:#d1b961;">LOGOUT</a>
    <?php else: ?>
      <a href="#" onclick="openLogin()">LOGIN</a>
    <?php endif; ?>
  </nav>
</header>

<!-- LOGIN MODAL -->
<div class="modal" id="loginModal">

  <div class="login-box" id="roleSelectBox">
    <div class="close-btn" onclick="closeLogin()">&times;</div>
    <h2>Login</h2>
    <button onclick="showCustomerLogin()">Customer</button>
    <button onclick="showAdminLogin()">Admin</button>
  </div>

  <div class="login-box" id="customerLoginBox" style="display:none;">
    <div class="close-btn" onclick="closeLogin()">&times;</div>
    <h2>Customer Login</h2>
    <form action="customer_connect.php" method="POST">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
  </div>

  <div class="login-box" id="adminLoginBox" style="display:none;">
    <div class="close-btn" onclick="closeLogin()">&times;</div>
    <h2>Admin Login</h2>
    <form action="admin_connect.php" method="POST">
      <input type="text" name="adminid" placeholder="Admin ID" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
  </div>
</div>

<script>
function openLogin() {
  document.getElementById("loginModal").style.display = "flex";
}
function closeLogin() {
  document.getElementById("loginModal").style.display = "none";
}
function showCustomerLogin() {
  document.getElementById("roleSelectBox").style.display = "none";
  document.getElementById("customerLoginBox").style.display = "block";
}
function showAdminLogin() {
  document.getElementById("roleSelectBox").style.display = "none";
  document.getElementById("adminLoginBox").style.display = "block";
}
</script>
<main>
  <section class="about-section">
    <div class="about-intro">
      <h1>About <span>PK Building Material</span></h1>
      <p>
        At <strong>PK Building Material</strong>, we believe that building your dream shouldn’t be complicated. 
        Our mission is to make high-quality construction materials easily accessible, affordable, and delivered right to your site — on time, every time.
      </p>
    </div>

    <div class="about-grid">
      <div class="about-card">
        <h2>🏗️ Our Vision</h2>
        <p>
          To revolutionize the way construction materials are ordered and delivered by blending technology, 
          reliability, and customer-centric service.
        </p>
      </div>

      <div class="about-card">
        <h2>🚚 What We Offer</h2>
        <ul>
          <li>Premium-quality cement, sand, steel, and bricks</li>
          <li>Vehicle rental for delivery (Tippers, Trucks, Loaders)</li>
          <li>Smart order tracking with real-time GPS updates</li>
          <li>Flexible payment and feedback system</li>
        </ul>
      </div>

      <div class="about-card">
        <h2>💡 Why Choose Us?</h2>
        <ul>
          <li>✔️ Trusted suppliers and verified materials</li>
          <li>✔️ Transparent pricing and easy ordering</li>
          <li>✔️ Fast delivery and active customer support</li>
          <li>✔️ Track your orders anytime, anywhere</li>
        </ul>
      </div>
    </div>

    <div class="about-mission">
      <h2>Our Commitment</h2>
      <p>
        We’re committed to being more than just a supplier — we’re your <strong>construction partner</strong>. 
        Whether it’s a small home project or a large-scale development, our team ensures smooth, efficient, and reliable material delivery.
      </p>
      <a href="material.html" class="frontbui">Explore Materials</a>
      <a href="calculator.html" class="frontcal">Estimate Cost</a>
    </div>
  </section>
</main>

    <div class="footer">
      &copy; 2025 PK Building Material. All rights reserved.
    </div>
  </div>

</body>
</html>
