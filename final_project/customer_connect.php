


<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pkbuilders";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$user = $_POST['username'];
$pass = $_POST['password'];

$sql = "SELECT * FROM customers WHERE username='$user'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  if (password_verify($pass, $row['password'])) {
    $_SESSION['username'] = $row['username'];
    $_SESSION['email'] = $row['email'];
    $_SESSION['mobilenum'] = $row['mobilenum'];
    header("Location: account.php");
    exit();
  } else {
    echo "<script>alert('Invalid password!'); window.history.back();</script>";
  }
} else {
  echo "<script>alert('No account found! Please register.'); window.location.href='login.html';</script>";
}
session_start();
$_SESSION['customer_id'] = $row['customer_id'];  // or your column name
header("Location: material.html");
exit();
$conn->close();
?>
