<?php
session_start();
include "db.php";

if (!isset($_SESSION['customer_id'])) {
    exit("Unauthorized access");
}

$id = $_GET['id'];
$customer = $_SESSION['customer_id'];

$stmt = $conn->prepare("DELETE FROM customer_addresses WHERE id=? AND customer_id=?");
$stmt->bind_param("ii", $id, $customer);
$stmt->execute();

header("Location: my_account.php?msg=deleted");
?>
