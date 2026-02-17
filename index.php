<?php
session_start();
include "db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>PK Building Material Suppliers</title>

  <!-- Main CSS -->
  <link rel="stylesheet" href="style.css" />

  <!-- Font Awesome -->
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    .password-box{
      position: relative;
      width: 100%;
    }
    .password-box input{
      width: 100%;
      padding: 10px 40px 10px 10px;
    }
    .password-box i{
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #555;
    }
  </style>
</head>

<body style="background-image: url('back.jpg'); background-size: cover;">

<!-- ================= HEADER ================= -->
<header>
  <div class="logo" style="display:flex; align-items:center;">
    <img src="static/pklogo.png"
         style="height:130px;width:180px;margin-top:-15px;margin-right:15px;">
  </div>

  <!-- ================= NAVBAR ================= -->
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

<!-- ================= HERO SECTION ================= -->
<main>
<section class="hero">

  <div class="hero-text">
    <h2>Your Trusted Construction Material Partner</h2>
    <h1>Get High Quality Construction Materials</h1>
  </div>

  <div class="hero-image">
    <img src="static/pkneed.png" alt="Construction Illustration" />
  </div>

  <!-- ================= LOGIN MODAL ================= -->
  <div class="modal" id="loginModal">

    <!-- Role Selection -->
    <div class="login-box" id="roleSelectBox">
      <div class="close-btn" onclick="closeLogin()">&times;</div>
      <h2>Login</h2>
      <p style="text-align:center;">Select Your Role</p>
      <button onclick="showCustomerLogin()">Customer</button>
      <button onclick="showAdminLogin()">Admin</button>
    </div>

    <!-- CUSTOMER LOGIN -->
    <div class="login-box" id="customerLoginBox" style="display:none;">
      <div class="close-btn" onclick="closeLogin()">&times;</div>
      <h2>Customer Login</h2>

      <form action="customer_connect.php" method="POST">
        <input type="text" name="username" placeholder="Username" required />

        <div class="password-box">
          <input type="password" name="password" id="customerPassword" placeholder="Password" required>
          <i class="fa-solid fa-eye" onclick="togglePassword('customerPassword', this)"></i>
        </div>

        <div class="options">
          <label><input type="checkbox"> Remember me</label>
          <a href="#">Forgot Password?</a>
        </div>

        <button type="submit" name="login">Login</button>
      </form>

      <div class="register-link">
        Don’t have an account? <a href="login.html">Sign Up</a>
      </div>
    </div>

    <!-- ADMIN LOGIN -->
    <div class="login-box" id="adminLoginBox" style="display:none;">
      <div class="close-btn" onclick="closeLogin()">&times;</div>
      <h2>Admin Login</h2>

      <form action="admin_connect.php" method="POST">
        <input type="text" name="adminid" placeholder="Admin ID" required />

        <div class="password-box">
          <input type="password" name="password" id="adminPassword" placeholder="Password" required>
          <i class="fa-solid fa-eye" onclick="togglePassword('adminPassword', this)"></i>
        </div>

        <button type="submit">Login</button>
      </form>
    </div>

  </div>
</section>
</main>

<!-- ================= FOOTER ================= -->
<footer>
  <p>© 2025 PK Building Material Suppliers. All rights reserved.</p>
</footer>

<!-- ================= SCRIPTS ================= -->
<script>
function togglePassword(id, icon){
  const input = document.getElementById(id);
  if(input.type === "password"){
    input.type = "text";
    icon.classList.replace("fa-eye","fa-eye-slash");
  }else{
    input.type = "password";
    icon.classList.replace("fa-eye-slash","fa-eye");
  }
}

function openLogin() {
  document.getElementById('loginModal').style.display = 'flex';
  document.getElementById('roleSelectBox').style.display = 'block';
  document.getElementById('customerLoginBox').style.display = 'none';
  document.getElementById('adminLoginBox').style.display = 'none';
}

function closeLogin() {
  document.getElementById('loginModal').style.display = 'none';
}

function showCustomerLogin() {
  document.getElementById('roleSelectBox').style.display = 'none';
  document.getElementById('customerLoginBox').style.display = 'block';
}

function showAdminLogin() {
  document.getElementById('roleSelectBox').style.display = 'none';
  document.getElementById('adminLoginBox').style.display = 'block';
}

window.onclick = function(event) {
  const modal = document.getElementById('loginModal');
  if (event.target === modal) {
    modal.style.display = 'none';
  }
}
</script>

</body>
</html>
