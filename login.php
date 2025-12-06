<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

header("Location: order_page.php");


if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM customers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $customer = $result->fetch_assoc();

        // If password is NOT hashed
        if ($password == $customer['password']) {
            $_SESSION['customer_id'] = $customer['id'];
            $_SESSION['customer_name'] = $customer['name'];
            header("Location: myaccount.php");
            exit;
        }

        // If password is hashed
        if (password_verify($password, $customer['password'])) {
            $_SESSION['customer_id'] = $customer['id'];
            $_SESSION['customer_name'] = $customer['name'];
            header("Location: myaccount.php");
            exit;
        }

        echo "Wrong password bro!";
    } else {
        echo "Email not found!";
    }
}
?>
