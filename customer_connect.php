<?php
session_start();
include 'db.php'; // Make sure this file defines $conn correctly

// START — customer login
if (isset($_POST['username']) && isset($_POST['password'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    // FETCH USER USING CORRECT COLUMN NAME
    $stmt = $conn->prepare("SELECT * FROM customers WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $customer = $result->fetch_assoc();

        // password match check
        if ($password == $customer['password']) {

            // SET CORRECT SESSION VARIABLES
            $_SESSION['customer_id'] = $customer['customer_id'];  // correct column
            $_SESSION['username'] = $customer['username'];        // correct column
            $_SESSION['email'] = $customer['email'];              // correct column
            $_SESSION['mobilenum'] = $customer['mobilenum'];      // correct column

            header("Location: material.html");
            exit;
        } else {
            echo "<script>alert('Wrong Password'); window.location='index.html';</script>";
        }

    } else {
        echo "<script>alert('User Not Found'); window.location='index.html';</script>";
    }
}
?>
