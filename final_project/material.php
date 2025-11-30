

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PK BUILDERS</title>
  <link rel="stylesheet" href="material.css">
</head>
<body>
   <header>
  <?php if (isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
  <script>
    alert("Successfully added to cart!");
  </script>
<?php endif; ?>

 <div class="logo" style="display: flex; align-items: center;  ;">
    <img src="static/pklogo.png" alt="Logo" style="height: 130px; width: 180px; margin-right: 15px;margin-top:-15px;">
    <div>
       
    </div>
</div>  
    <nav>
      <a href="index.html" class="home-1">HOME</a> 
      <a href="material.html" class="material">MATERIAL</a>
      <a href="calculator.html" class="calculator">CALCULATOR</a>
      <a href="about.html" class="about">ABOUT</a>
      <a href="account.html" class="contact">MY ACCOUNT</a>
      <a onclick="openLogin()" class="login">LOGIN</a>
   
   </nav>
   </header>

     <main>
    <section class="hero">
      <!-- Login Modal -->
<div class="modal" id="loginModal">
  <div class="login-box" id="roleSelectBox">
    <div class="close-btn" onclick="closeLogin()">&times;</div>
    <h2>Login</h2>
    <p style="text-align:center;">Select your role</p>
    <button onclick="showCustomerLogin()">Customer</button>
    <button onclick="showAdminLogin()">Admin</button>
  </div>

  <!-- Customer Login -->
  <div class="login-box" id="customerLoginBox" style="display:none;">
    <div class="close-btn" onclick="closeLogin()">&times;</div>
    <h2>Customer Login</h2>
    <form action="customer_connect.php" method="POST">
      <input type="username" name="username" placeholder="Username" required />
      <input type="password" name="password" placeholder="Password" required />
      <div class="options">
        <label><input type="checkbox"> Remember me</label>
        <a href="#">Forgot Password?</a>
      </div>
      <button type="submit">Login</button>
      <div class="register-link">
        Don’t have an account? <a href="signup.html">SignUp</a>
      </div>
    </form>
  </div>

<!-- Admin Login -->
<div class="login-box" id="adminLoginBox" style="display:none;">
  <div class="close-btn" onclick="closeLogin()">&times;</div>
  <h2>Admin Login</h2>
  <form action="admin_connect.php" method="POST">
    <input type="text" name="adminid" placeholder="Admin ID" required />
    <input type="password" name="password" placeholder="Password" required />
    <button type="submit">Login</button>
  </form>
</div>


<script>
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

    </section>
  </main>

  <div class="container">
   
    <div class="cards">
       <form action="place_order.php" method="POST">
     <div class="card">
          <img src="static/2m-sand.jpg" alt="Construction" class="material-img">
          <h3>MSand</h3>
          <p class="price">₹4500 per unit</p>
          <label>Choose unit:   </label>
        <form action="place_order.php" method="POST">
             <input type="hidden" name="material" value="MSand">
             <input type="hidden" name="price" value="4500">
             <input type="number" name="unit" required>
             <button type="submit" name="order_btn">Order Now</button>
              <div class="btn-group">
       
          <button class="calc-btn">Add to cart</button> 
        </form>
         
        </div>
  </div>  
</form>

<form action="place_order.php" method="POST">
     <div class="card">
          <img src="static/psand.avif" alt="Construction" class="material-img">
          <h3>PSand</h3>
          <p class="price">₹9000 per unit</p>
          <label>Choose unit:   </label>
          <input type="number" class="unit"name="unit" required>
          <input type="hidden" name="material" value="MSand">
          <input type="hidden" name="price" value="4500">
          <div class="btn-group">
          <button class="order-btn">Order Now</button>
          <button class="calc-btn">Add to cart</button> 
        </div>
  </div>  
</form>
<?php if (isset($_SESSION['customer_id'])): ?>
  <form action="add_to_cart.php?product_id=<?php echo $row['id']; ?>" method="post" style="display:inline;">
    <input type="hidden" name="quantity" value="1" />
    <button type="submit">Add to Cart</button>
  </form>
<?php else: ?>
  <a href="index.php">Login to Order</a>
<?php endif; ?>
<form action="place_order.php" method="POST">
     <div class="card">
          <img src="static/dust.webp" alt="Construction" class="material-img">
          <h3>Dust</h3>
          <p class="price">₹9000 per unit</p>
          <label>Choose unit:   </label>
          <input type="number" class="unit"name="unit" required>
          <input type="hidden" name="material" value="MSand">
          <input type="hidden" name="price" value="4500">
          <div class="btn-group">
          <button class="order-btn">Order Now</button>
          <button class="calc-btn">Add to cart</button> 
        </div>
  </div>  
</form>

      <form action="place_order.php" method="POST">
     <div class="card">
          <img src="static/sand.jpg" alt="Construction" class="material-img">
          <h3>Sand</h3>
          <p class="price">₹9000 per unit</p>
          <label>Choose unit:   </label>
          <input type="number" class="unit"name="unit" required>
          <input type="hidden" name="material" value="MSand">
          <input type="hidden" name="price" value="4500">
          <div class="btn-group">
          <button class="order-btn">Order Now</button>
          <button class="calc-btn">Add to cart</button> 
        </div>
  </div>  
</form>
      <form action="place_order.php" method="POST">
     <div class="card">
          <img src="static/ramco.jpg" alt="Construction" class="material-img">
          <h3>Ramco cement</h3>
          <p class="price">₹350 per pack</p>
          <label>Choose pack:   </label>
          <input type="number" class="unit"name="unit" required>
          <input type="hidden" name="material" value="MSand">
          <input type="hidden" name="price" value="4500">
          <div class="btn-group">
          <button class="order-btn">Order Now</button>
          <button class="calc-btn">Add to cart</button> 
        </div>
  </div>  
</form>
     <form action="place_order.php" method="POST">
     <div class="card">
          <img src="static/cement.png" alt="Construction" class="material-img">
          <h3>chettinad cement</h3>
          <p class="price">₹400 per pack</p>
          <label>Choose pack:   </label>
          <input type="number" class="unit"name="unit" required>
          <input type="hidden" name="material" value="MSand">
          <input type="hidden" name="price" value="4500">
          <div class="btn-group">
          <button class="order-btn">Order Now</button>
          <button class="calc-btn">Add to cart</button> 
        </div>
  </div>  
</form>
 <form action="place_order.php" method="POST">
     <div class="card">
          <img src="static/tech.png" alt="Construction" class="material-img">
          <h3>Ultratech cement</h3>
          <p class="price">₹450 per pack</p>
          <label>Choose pack:   </label>
          <input type="number" class="unit"name="unit" required>
          <input type="hidden" name="material" value="MSand">
          <input type="hidden" name="price" value="4500">
          <div class="btn-group">
          <button class="order-btn">Order Now</button>
          <button class="calc-btn">Add to cart</button> 
        </div>
  </div>  
</form>
<form action="place_order.php" method="POST">
     <div class="card">
          <img src="static/redbricks.jpg" alt="Construction" class="material-img">
          <h3>Red Brick</h3>
          <p class="price">₹10 per piece</p>
          <label>Choose piece:   </label>
          <input type="number" class="unit"name="unit" required>
          <input type="hidden" name="material" value="MSand">
          <input type="hidden" name="price" value="4500">
          <div class="btn-group">
          <button class="order-btn">Order Now</button>
          <button class="calc-btn">Add to cart</button> 
        </div>
  </div>  
</form>
     
      <form action="place_order.php" method="POST">
     <div class="card">
          <img src="static/gravel.webp" alt="Construction" class="material-img">
          <h3>Gravel</h3>
          <p class="price">₹5000 per unit</p>
          <label>Choose unit:   </label>
          <input type="number" class="unit"name="unit" required>
          <input type="hidden" name="material" value="MSand">
          <input type="hidden" name="price" value="4500">
          <div class="btn-group">
          <button class="order-btn">Order Now</button>
          <button class="calc-btn">Add to cart</button> 
        </div>
  </div>  
</form>
    <form action="place_order.php" method="POST">
     <div class="card">
          <img src="static/waste.jpg" alt="Construction" class="material-img">
          <h3>Waste Sand</h3>
          <p class="price">₹3000 per unit</p>
          <label>Choose unit:   </label>
          <input type="number" class="unit"name="unit" required>
          <input type="hidden" name="material" value="MSand">
          <input type="hidden" name="price" value="4500">
          <div class="btn-group">
          <button class="order-btn">Order Now</button>
          <button class="calc-btn">Add to cart</button> 
        </div>
  </div>  
</form>
<form action="place_order.php" method="POST">
     <div class="card">
          <img src="static/2m-sand.jpg" alt="Construction" class="material-img">
          <h3>PSand</h3>
          <p class="price">₹9000 per unit</p>
          <label>Choose unit:   </label>
          <input type="number" class="unit"name="unit" required>
          <input type="hidden" name="material" value="MSand">
          <input type="hidden" name="price" value="4500">
          <div class="btn-group">
          <button class="order-btn">Order Now</button>
          <button class="calc-btn">Add to cart</button> 
        </div>
  </div>  
</form>
<form action="place_order.php" method="POST">
     <div class="card">
          <img src="C:\xampp\htdocs\final_project\images\2m-sand.jpg" alt="Construction" class="material-img">
          <h3>PSand</h3>
          <p class="price">₹9000 per unit</p>
          <label>Choose unit:   </label>
          <input type="number" class="unit"name="unit" required>
          <input type="hidden" name="material" value="MSand">
          <input type="hidden" name="price" value="4500">
          <div class="btn-group">
          <button class="order-btn">Order Now</button>
          <button class="calc-btn">Add to cart</button> 
        </div>
  </div>  
</form>
<form action="place_order.php" method="POST">
     <div class="card">
          <img src="C:\xampp\htdocs\final_project\images\2m-sand.jpg" alt="Construction" class="material-img">
          <h3>PSand</h3>
          <p class="price">₹9000 per unit</p>
          <label>Choose unit:   </label>
          <input type="number" class="unit"name="unit" required>
          <input type="hidden" name="material" value="MSand">
          <input type="hidden" name="price" value="4500">
          <div class="btn-group">
          <button class="order-btn">Order Now</button>
          <button class="calc-btn">Add to cart</button> 
        </div>
  </div>  
</form>
<form action="place_order.php" method="POST">
     <div class="card">
          <img src="C:\xampp\htdocs\final_project\images\2m-sand.jpg" alt="Construction" class="material-img">
          <h3>PSand</h3>
          <p class="price">₹9000 per unit</p>
          <label>Choose unit:   </label>
          <input type="number" class="unit"name="unit" required>
          <input type="hidden" name="material" value="MSand">
          <input type="hidden" name="price" value="4500">
          <div class="btn-group">
          <button class="order-btn">Order Now</button>
          <button class="calc-btn">Add to cart</button> 
        </div>
  </div>  
</form>
<form action="place_order.php" method="POST">
     <div class="card">
          <img src="C:\xampp\htdocs\final_project\images\2m-sand.jpg" alt="Construction" class="material-img">
          <h3>PSand</h3>
          <p class="price">₹9000 per unit</p>
          <label>Choose unit:   </label>
          <input type="number" class="unit"name="unit" required>
          <input type="hidden" name="material" value="MSand">
          <input type="hidden" name="price" value="4500">
          <div class="btn-group">
          <button class="order-btn">Order Now</button>
          <button class="calc-btn">Add to cart</button> 
        </div>
  </div>  
</form>
<form action="add_to_cart.php" method="POST">
  <div class="card">
    <img src="static/2m-sand.jpg" alt="MSand" class="material-img">
    <h3>MSand</h3>
    <p class="price">₹4500 per unit</p>
    <label>Choose unit:</label>
    <input type="number" name="unit" required>
    <input type="hidden" name="material" value="MSand">
    <input type="hidden" name="price" value="4500">
    <div class="btn-group">
      <button type="submit" name="add_to_cart" class="calc-btn">Add to cart</button>
      <button type="submit" formaction="place_order.php" class="order-btn">Order Now</button>
    </div>
  </div>
</form>



      <!-- You can duplicate the card blocks and just change the names/prices accordingly -->
    </div>
  </div>
</body>
