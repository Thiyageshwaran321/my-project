<?php
// admin_connect.php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pkbuilders";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$adminid = $_POST['adminid'];
$password = $_POST['password'];

// FIXED: Select from correct admin table
$sql = "SELECT * FROM admin_login WHERE adminid = ? AND password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $adminid, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {

    // save admin login session
    $_SESSION['admin_id'] = $adminid;

    echo "<script>
            alert('Login Successful');
            window.location.href='admin_dashboard.php';
          </script>";
    exit;

} else {

    echo "<script>
            alert('Invalid Admin ID or Password');
            window.location.href='index.html';
          </script>";
}

$conn->close();
?>
 