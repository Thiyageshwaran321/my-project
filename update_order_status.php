<?php
session_start();
include "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    
    // Validate status
    $allowed_statuses = ['pending', 'shipped', 'out_for_delivery', 'delivered', 'cancelled'];
    if (!in_array($status, $allowed_statuses)) {
        die('Invalid status');
    }
    
    try {
        // Update status only (removed updated_at if column doesn't exist)
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $stmt->bind_param("si", $status, $order_id);
        
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Error: " . $stmt->error;
        }
        
        $stmt->close();
    } catch (Exception $e) {
        // If updated_at column error, try without it
        try {
            $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
            $stmt->bind_param("si", $status, $order_id);
            
            if ($stmt->execute()) {
                echo "success";
            } else {
                echo "Error: " . $stmt->error;
            }
            
            $stmt->close();
        } catch (Exception $e2) {
            echo "Database error: " . $e2->getMessage();
        }
    }
} else {
    echo "Invalid request";
}
?>