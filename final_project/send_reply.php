<?php
include "db.php";

$id = $_POST['id'];
$reply = $_POST['admin_reply'];

$stmt = $conn->prepare("
    UPDATE feedback 
    SET admin_reply = ?, reply_at = NOW()
    WHERE id = ?
");
$stmt->bind_param("si", $reply, $id);

if ($stmt->execute()) {
    echo "<script>alert('Reply sent successfully'); window.location='admin_dashboard.php';</script>";
} else {
    echo "Error updating reply!";
}
?>
