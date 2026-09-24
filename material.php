<?php
session_start();
include "db.php";

/* FETCH PRODUCTS WITH STOCK */
$products = $conn->query("
  SELECT product_id, material_name, price, image, stock, unit_type
  FROM products
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>PK BUILDERS</title>

<link rel="stylesheet" href="material.css">
<link rel="stylesheet"
 href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<header>
  <div class="logo" style="display:flex;align-items:center;">
    <img src="static/pklogo.png"
         style="height:130px;width:180px;margin-right:15px;margin-top:-15px;">
  </div>

  <nav>
    <a href="index.php">HOME</a>
    <a href="material.php">MATERIAL</a>
    <a href="calculator.php">CALCULATOR</a>
    <a href="about.php">ABOUT</a>
    <a href="my_account.php">MY ACCOUNT</a>

    <?php if(isset($_SESSION['customer_id'])): ?>
      <a href="logout.php">LOGOUT</a>
    <?php else: ?>
      <a onclick="openLogin()">LOGIN</a>
    <?php endif; ?>
  </nav>
</header>

<div class="container">
<div class="cards">

<?php while($row = $products->fetch_assoc()): ?>
<div class="card">

  <!-- IMAGE -->
  <img src="<?= htmlspecialchars($row['image']) ?>" class="material-img">

  <!-- NAME -->
  <h3><?= htmlspecialchars($row['material_name']) ?></h3>

  <!-- PRICE -->
  <p class="price">₹<?= number_format($row['price']) ?> per unit</p>

  <!-- STOCK STATUS -->
  <?php if ($row['stock'] > 0): ?>
    <p style="color:green;font-weight:bold;align-items:center;display:flex;justify-content:center;">
      <i class="fas fa-check-circle" style="margin-right:5px;"></i>
    In Stock (<?= $row['stock'] ?> <?= $row['unit_type'] ?><?= $row['stock'] > 1 ? 's' : '' ?>)

    </p>
  <?php else: ?>
    <p style="color:red;font-weight:bold;">
      Out of Stock
    </p>
  <?php endif; ?>

  <!-- ORDER FORM -->
  <form>
    <label>
  Choose <?= ucfirst($row['unit_type']) ?>:
</label>

    <input type="number"
           name="quantity"
           class="unit"
           min="1"
           max="<?= $row['stock'] ?>"
           <?= $row['stock'] == 0 ? 'disabled' : 'required' ?>>

    <input type="hidden"
           name="product_id"
           value="<?= $row['product_id'] ?>">

    <div class="btn-group">

      <!-- ORDER NOW -->
      <button type="submit"
              formaction="select_address.php"
              formmethod="GET"
              class="order-btn"
              <?= $row['stock'] == 0 ? 'disabled style="background:#aaa;cursor:not-allowed;"' : '' ?>>
        Order Now
      </button>

      <!-- ADD TO CART -->
      <button type="submit"
              formaction="add_to_cart.php"
              formmethod="POST"
              class="calc-btn"
              <?= $row['stock'] == 0 ? 'disabled style="background:#aaa;cursor:not-allowed;"' : '' ?>>
        Add to cart
      </button>

    </div>
  </form>

</div>
<?php endwhile; ?>

</div>
</div>

</body>
</html>
