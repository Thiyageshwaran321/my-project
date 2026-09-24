<?php
session_start();

if (!isset($_POST['address_id'])) {
    die("No address selected");
}

$_SESSION['selected_address_id'] = (int)$_POST['address_id'];

header("Location: my_account.php");
exit();
