<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Material Cost Calculator</title>
  <link rel="stylesheet" href="calculator.css">

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

<body>

<header>
  <div class="logo" style="display:flex; align-items:center;">
    <img src="static/pklogo.png"
         style="height:130px;width:180px;margin-right:15px;margin-top:-15px;">
  </div>

  <nav>
    <a href="index.php" class="home-1">HOME</a>
    <a href="material.php" class="material">MATERIAL</a>
    <a href="calculator.php" class="calculator">CALCULATOR</a>
    <a href="about.php" class="about">ABOUT</a>

    <?php if(isset($_SESSION['customer_id'])): ?>
      <a href="my_account.php" class="contact">MY ACCOUNT</a>
      <a href="logout.php" class="login">LOGOUT</a>
    <?php else: ?>
      <a onclick="openLogin()" class="login">LOGIN</a>
    <?php endif; ?>
  </nav>
</header>

<!-- ================= LOGIN MODAL ================= -->

<div class="modal" id="loginModal">

  <!-- ROLE SELECT -->
  <div class="login-box" id="roleSelectBox">
    <div class="close-btn" onclick="closeLogin()">&times;</div>
    <h2>Login</h2>
    <p style="text-align:center;">Select your role</p>
    <button onclick="showCustomerLogin()">Customer</button>
    <button onclick="showAdminLogin()">Admin</button>
  </div>

  <!-- CUSTOMER LOGIN -->
  <div class="login-box" id="customerLoginBox" style="display:none;">
    <div class="close-btn" onclick="closeLogin()">&times;</div>
    <h2>Customer Login</h2>

    <form action="customer_connect.php" method="POST">
      <input type="text" name="username" placeholder="Username" required>

      <div class="password-box">
        <input type="password" name="password" id="customerPassword" placeholder="Password" required>
        <i class="fa-solid fa-eye"
           onclick="togglePassword('customerPassword', this)"></i>
      </div>

      <button type="submit">Login</button>
    </form>
  </div>

  <!-- ADMIN LOGIN -->
  <div class="login-box" id="adminLoginBox" style="display:none;">
    <div class="close-btn" onclick="closeLogin()">&times;</div>
    <h2>Admin Login</h2>

    <form action="admin_connect.php" method="POST">
      <input type="text" name="adminid" placeholder="Admin ID" required>

      <div class="password-box">
        <input type="password" name="password" id="adminPassword" placeholder="Password" required>
        <i class="fa-solid fa-eye"
           onclick="togglePassword('adminPassword', this)"></i>
      </div>

      <button type="submit">Login</button>
    </form>
  </div>

</div>

<!-- ================= CALCULATOR ================= -->

<div class="calculator1">
  <h2>Material Cost Calculator</h2>

  <label>Select Material:</label>
  <select id="material">
    <option value="">-- Choose --</option>
    <option value="5000">M-Sand (₹5000/unit)</option>
    <option value="4500">P-Sand (₹4500/unit)</option>
    <option value="400">Cement (₹400/unit)</option>
  </select>

  <br><br>

  <label>Quantity (units):</label>
  <input  style="width: 96%; margin-bottom: 50px;" type="number" id="quantity" placeholder="Enter quantity">

  <button class="btn" onclick="calculateCost()">Calculate Total Cost</button>
</div>

<div class="result" id="result"></div>

<!-- ================= SCRIPT ================= -->

<script>
/* PASSWORD TOGGLE */
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

/* LOGIN MODAL */
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

/* CALCULATOR */
function calculateCost() {
  const materialPrice = parseFloat(document.getElementById("material").value);
  const quantity = parseFloat(document.getElementById("quantity").value);
  const resultDiv = document.getElementById("result");

  if (isNaN(materialPrice) || isNaN(quantity) || quantity <= 0) {
    resultDiv.innerHTML = "<strong>Please select material and enter valid quantity.</strong>";
    return;
  }

  const totalCost = materialPrice * quantity;

  resultDiv.innerHTML = `
    <strong>RESULT:</strong><br><br>
    Price per Unit: ₹${materialPrice}<br>
    Quantity: ${quantity}<br>
    <strong>Total Cost: ₹${totalCost}</strong>
  `;
}

</script>

</body>
</html>
