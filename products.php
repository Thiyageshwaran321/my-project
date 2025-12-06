<?php
session_start();
// require the DB connection file from the same directory – use __DIR__ for a reliable path
require_once __DIR__ . '/db.php';

// If db.php failed to create $conn, stop early with a helpful message instead of calling methods on null
if (!isset($conn) || !($conn instanceof mysqli)) {
  // Log or show a friendly message. In production you might log this instead.
  echo "<h2>Database connection error</h2><p>Could not connect to the database. Please check <code>db.php</code> and the MySQL server.</p>";
  exit();
}

// If you want only logged-in customers to view products, uncomment the block below
// if (!isset($_SESSION['customer_id'])) {
//     header('Location: login.html');
//     exit();
// }

// Fetch products from the `products` table
$products = [];
$sql = "SELECT id, name, description, price, image_path FROM products";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Products - PK BUILDERS</title>
  <link rel="stylesheet" href="material.css">
  <style>
    /* Minimal layout tweaks so this page looks like the other material pages */
    .products-container {max-width:1100px;margin:30px auto;padding:0 16px}
    .cards {display:flex;flex-wrap:wrap;gap:18px}
    .card {border:1px solid #ddd;border-radius:6px;padding:12px;width:230px;box-shadow:0 1px 3px rgba(0,0,0,.06)}
    .material-img{width:100%;height:140px;object-fit:cover;border-radius:4px}
    .price{font-weight:700;color:#111;margin:6px 0}
    .card form {display:flex;gap:8px;align-items:center}
    .card input[type="number"]{width:64px;padding:6px}
    .order-btn{background:#007bff;color:#fff;border:none;padding:8px 10px;border-radius:4px;cursor:pointer}
  </style>
</head>
<body>
  <header>
    <div class="logo" style="display:flex;align-items:center;">
      <img src="static/pklogo.png" alt="Logo" style="height:90px;width:130px;margin-right:12px;">
    </div>
    <nav>
      <a href="index.html">HOME</a>
      <a href="material.html">MATERIAL</a>
      <a href="product.php">PRODUCTS</a>
      <a href="my_account.php">MY ACCOUNT</a>
    </nav>
  </header>

  <main class="products-container">
    <h1>Products</h1>
    <div class="cards">
      <?php if (empty($products)): ?>
        <p>No products found.</p>
      <?php else: ?>
        <?php foreach ($products as $p): ?>
          <div class="card">
            <?php $img = !empty($p['image_path']) ? $p['image_path'] : 'static/psand.avif'; ?>
            <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="material-img">
            <h3><?php echo htmlspecialchars($p['name']); ?></h3>
            <p class="price">₹<?php echo number_format($p['price'], 2); ?> per unit</p>
            <p><?php echo htmlspecialchars($p['description']); ?></p>

            <form action="place_order.php" method="GET">
              <input type="hidden" name="product_id" value="<?php echo (int)$p['id']; ?>">
              <label>Qty</label>
              <input type="number" name="quantity" value="1" min="1" required>
              <button type="submit" class="order-btn">Order Now</button>
            </form>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>
