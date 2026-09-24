<?php
session_start();
include "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id']);
    $delivery_date = !empty($_POST['expected_delivery_date']) ? $_POST['expected_delivery_date'] : null;
    $delivery_time = !empty($_POST['expected_delivery_time']) ? $_POST['expected_delivery_time'] : null;
    $status = isset($_POST['status']) ? $_POST['status'] : null;
    
    // Format time properly
    if ($delivery_time && strlen($delivery_time) <= 5) {
        $delivery_time .= ':00';
    }
    
    // Build update query
    $updates = [];
    if ($delivery_date) {
        $updates[] = "expected_delivery_date = '$delivery_date'";
    } else {
        $updates[] = "expected_delivery_date = NULL";
    }
    
    if ($delivery_time) {
        $updates[] = "expected_delivery_time = '$delivery_time'";
    } else {
        $updates[] = "expected_delivery_time = NULL";
    }
    
    if ($status) {
        $allowed_statuses = ['pending', 'shipped', 'out_for_delivery', 'delivered', 'cancelled'];
        if (in_array($status, $allowed_statuses)) {
            $updates[] = "status = '$status'";
        }
    }
    
    if (!empty($updates)) {
        $sql = "UPDATE orders SET " . implode(", ", $updates) . " WHERE order_id = $order_id";
        
        if ($conn->query($sql)) {
            header("Location: admin_dashboard.php?updated=1&order_id=" . $order_id);
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Nothing to update";
    }
} else {
    echo "Invalid request method";
}
?>  