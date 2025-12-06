<?php
include "db.php";

if (!isset($_POST['id']) || !isset($_POST['reply'])) {
    echo "<script>alert('Missing data'); window.location='admin_dashboard.php';</script>";
    exit;
}

$id = $_POST['id'];
$reply = $_POST['reply'];

$stmt = $conn->prepare("
    UPDATE feedback 
    SET admin_reply = ?, reply_at = NOW()
    WHERE id = ?
");

$stmt->bind_param("si", $reply, $id);

if ($stmt->execute()) {
    echo "<script>
            alert('Reply Sent Successfully!');
            window.location='admin_dashboard.php';
          </script>";
} else {
    echo "<script>
            alert('Failed to reply!');
            window.location='admin_dashboard.php';
          </script>";
}
?>
