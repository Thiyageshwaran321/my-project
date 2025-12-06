<?php
session_start();
include "db.php";

if (!isset($_SESSION['customer_id'])) {
    header("Location: index.html");
    exit;
}

$customer_id = $_SESSION['customer_id'];
$message = trim($_POST['message']);

if ($message === "") {
    echo "<script>alert('Enter your feedback!'); window.history.back();</script>";
    exit;
}

$stmt = $conn->prepare("INSERT INTO feedback (customer_id, message) VALUES (?, ?)");
$stmt->bind_param("is", $customer_id, $message);

if ($stmt->execute()) {
    echo "<script>alert('Feedback submitted successfully!'); window.location='my_account.php';</script>";
} else {
    echo "Error: " . $stmt->error;
}
?>
