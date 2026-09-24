<?php
session_start();
include "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id']);
    $status = $conn->real_escape_string($_POST['status']);
    
    // Validate status
    $allowed_statuses = ['pending', 'shipped', 'out_for_delivery', 'delivered', 'cancelled'];
    if (!in_array($status, $allowed_statuses)) {
        die('Invalid status');
    }
    
    // Update only the status
    $sql = "UPDATE orders SET status = '$status' WHERE order_id = $order_id";
    
    if ($conn->query($sql)) {
        // Success - redirect back to dashboard
        header("Location: admin_dashboard.php?status_updated=1&order_id=" . $order_id . "&status=" . $status);
        exit();
    } else {
        echo "Error updating status: " . $conn->error;
    }
} else {
    echo "Invalid request";
}
?>