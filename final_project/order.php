<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "construction";

// Connect to DB
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
  

// Check if form sent values
if (isset($_POST['material']) && isset($_POST['price']) && isset($_POST['unit'])) {
  $material = $_POST['material'];
  $price = $_POST['price'];
  $unit = $_POST['unit'];
  $total = $price * $unit;

  $sql = "INSERT INTO orders (material, price, unit, total_price)
          VALUES ('$material', '$price', '$unit', '$total')";

  if ($conn->query($sql) === TRUE) {
    echo "<br>✅ Order placed successfully!";
  } else {
    echo "<br>❌ Error: " . $conn->error;
  }
} else {
  echo "<br>⚠️ Form data not received properly!";
}

$conn->close();
?>
