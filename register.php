<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pkbuilders";  // your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $email = $_POST['email'];
  $mobilenum = $_POST['mobilenum'];

  $sql = "INSERT INTO customers (username, password, email, mobilenum)
          VALUES ('$username', '$password', '$email', '$mobilenum')";

  if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Account created successfully!'); window.location.href='index.html';</script>";
  } else {
    echo "Error: " . $conn->error;
  }
}

$conn->close();
?>
