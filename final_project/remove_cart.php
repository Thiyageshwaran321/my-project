<?php
session_start();
include "db.php";

if (!isset($_SESSION['customer_id'])) exit("Login required");

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM cart WHERE id=? AND customer_id=?");
$stmt->bind_param("ii", $id, $_SESSION['customer_id']);
$stmt->execute();

echo "<script>alert('Removed from cart'); window.location='my_account.php';</script>";
?>
