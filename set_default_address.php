<?php
session_start();
include "db.php";

if (!isset($_SESSION['customer_id'], $_POST['selected_address_id'])) {
    header("Location: my_account.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$address_id  = $_POST['selected_address_id'];

/* Reset all */
$reset = $conn->prepare(
  "UPDATE customer_addresses SET is_default = 0 WHERE customer_id = ?"
);
$reset->bind_param("i", $customer_id);
$reset->execute();

/* Set selected */
$set = $conn->prepare(
  "UPDATE customer_addresses SET is_default = 1 
   WHERE id = ? AND customer_id = ?"
);
$set->bind_param("ii", $address_id, $customer_id);
$set->execute();

header("Location: my_account.php");
exit();
