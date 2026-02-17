<?php
session_start();
include "db.php";

if (!isset($_SESSION['customer_id'])) {
    die("Unauthorized access");
}

if (
    !isset($_POST['username']) ||
    !isset($_POST['email']) ||
    !isset($_POST['mobilenum'])
) {
    die("Invalid request");
}

$customer_id = $_SESSION['customer_id'];

$username  = trim($_POST['username']);
$email     = trim($_POST['email']);
$mobilenum = trim($_POST['mobilenum']);

/* BASIC VALIDATION */
if (strlen($mobilenum) < 8 || strlen($mobilenum) > 15) {
    echo "<script>alert('Invalid mobile number'); history.back();</script>";
    exit();
}

/* UPDATE CUSTOMER */
$stmt = $conn->prepare("
    UPDATE customers 
    SET username = ?, email = ?, mobilenum = ?
    WHERE customer_id = ?
");

$stmt->bind_param("sssi", $username, $email, $mobilenum, $customer_id);

if ($stmt->execute()) {
    echo "<script>
        alert('Profile updated successfully');
        window.location='my_account.php';
    </script>";
} else {
    echo "<script>
        alert('Update failed');
        history.back();
    </script>";
}
?>
