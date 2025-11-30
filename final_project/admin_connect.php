<?php
// admin_connect.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pkbuilders"; // your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get values from form
$adminid = $_POST['adminid'];
$password = $_POST['password'];

// Check credentials in admin table (you can change table name as needed)
$sql = "SELECT * FROM admin_login WHERE adminid='$adminid' AND password='$password'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // Successful login — redirect to admin dashboard
  header("Location: admin_dashboard.html");
  exit();
} else {
  echo "<script>
          alert('Invalid Admin ID or Password');
          window.location.href='index.html';
        </script>";
}

$conn->close();
?>
