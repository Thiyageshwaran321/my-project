<?php
session_start();
include "db.php";

// If needed, add admin login check
// if (!isset($_SESSION['admin'])) { header("Location: admin_login.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $days = intval($_POST['delivery_days']);

    $update = $conn->prepare("UPDATE settings SET delivery_days = ? WHERE id = 1");
    $update->bind_param("i", $days);
    if ($update->execute()) {
        $msg = "Updated Successfully!";
    } else {
        $msg = "Failed to update!";
    }
}

// Fetch current delivery days
$res = $conn->query("SELECT delivery_days FROM settings WHERE id = 1")->fetch_assoc();
$current_days = $res ? $res['delivery_days'] : 2;

?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Settings</title>

<style>
body {
    margin: 0;
    padding: 0;
    background: #f3e9d7;
    font-family: 'Segoe UI';
}

.container {
    width: 40%;
    margin: 80px auto;
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0px 4px 18px rgba(0,0,0,0.15);
    border-left: 6px solid #8a6b36;
}

h2 {
    margin-top: 0;
    color: #8a6b36;
    font-size: 26px;
}

input[type="number"] {
    padding: 10px;
    width: 120px;
    font-size: 16px;
    border: 2px solid #ccc;
    border-radius: 6px;
}

input[type="number"]:focus {
    border-color: #8a6b36;
    outline: none;
}

.btn {
    background: #8a6b36;
    color: white;
    padding: 10px 16px;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
    margin-left: 10px;
}

.btn:hover {
    background: #6f5428;
}

.msg {
    margin-top: 15px;
    font-size: 16px;
    padding: 10px;
    background: #e7f7e7;
    color: #2d7a2d;
    border-left: 5px solid #2d7a2d;
    border-radius: 5px;
}

.error {
    background: #fde4e4;
    color: #d23b3b;
    border-left: 5px solid #d23b3b;
}
</style>

</head>
<body>

<div class="container">

    <h2>Update Delivery Days</h2>

    <form method="POST">
        <label><b>Expected Delivery Days:</b></label>
        <input type="number" name="delivery_days" min="1" value="<?= $current_days ?>" required>
        <button type="submit" class="btn">Update</button>
    </form>

    <?php if (isset($msg)): ?>
        <div class="msg"><?= $msg ?></div>
    <?php endif; ?>

</div>

</body>
</html>
