<?php
session_start();
include "db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && isset($_POST['reply'])) {
    
    $id = intval($_POST['id']);
    $reply = trim($_POST['reply']);
    
    if (empty($reply)) {
        header("Location: admin_dashboard.php?error=empty_reply");
        exit();
    }
    
    // Check if admin_reply column exists
    $check = $conn->query("SHOW COLUMNS FROM feedback LIKE 'admin_reply'");
    if ($check->num_rows == 0) {
        $conn->query("ALTER TABLE feedback ADD COLUMN admin_reply TEXT NULL");
    }
    
    // Update the feedback
    $query = "UPDATE feedback SET admin_reply = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $reply, $id);
    
    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?feedback_replied=1");
    } else {
        header("Location: admin_dashboard.php?error=db_error");
    }
    
    $stmt->close();
} else {
    header("Location: admin_dashboard.php");
}
$conn->close();
?>