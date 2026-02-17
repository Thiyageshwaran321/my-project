<?php
session_start();
include "db.php";

header('Content-Type: application/json');

// Show errors (turn OFF in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include PHPMailer
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check DB
if (!$conn || $conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit();
}

// Only POST allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

// Validate inputs
if (!isset($_POST['product_id'], $_POST['stock'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit();
}

$product_id = (int)$_POST['product_id'];
$new_stock  = (int)$_POST['stock'];
$low_stock_limit = isset($_POST['low_stock_limit']) ? (int)$_POST['low_stock_limit'] : 0;

// Fetch product details
$productName = '';
$oldStock = 0;
$unitType = '';
$dbLowLimit = 0;

$getProduct = $conn->prepare("SELECT material_name, stock, unit_type, low_stock_limit FROM products WHERE product_id = ?");
$getProduct->bind_param("i", $product_id);
$getProduct->execute();
$result = $getProduct->get_result();

if ($row = $result->fetch_assoc()) {
    $productName = $row['material_name'];
    $oldStock    = (int)$row['stock'];
    $unitType    = $row['unit_type'];
    $dbLowLimit  = (int)$row['low_stock_limit'];
} else {
    echo json_encode(['status' => 'error', 'message' => 'Product not found']);
    exit();
}
$getProduct->close();

// Decide which low limit to use
if ($low_stock_limit <= 0) {
    $low_stock_limit = $dbLowLimit;
}

// Safety default
if ($low_stock_limit <= 0) {
    $low_stock_limit = 10;
}

// Update stock (and low stock limit)
if (isset($_POST['low_stock_limit']) && (int)$_POST['low_stock_limit'] > 0) {
    $stmt = $conn->prepare("UPDATE products SET stock = ?, low_stock_limit = ? WHERE product_id = ?");
    $stmt->bind_param("iii", $new_stock, $low_stock_limit, $product_id);
} else {
    $stmt = $conn->prepare("UPDATE products SET stock = ? WHERE product_id = ?");
    $stmt->bind_param("ii", $new_stock, $product_id);
}

if (!$stmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
    exit();
}
$stmt->close();
// After reducing stock, fetch new stock + limit
$p = $conn->prepare("SELECT material_name, stock, low_stock_limit, unit_type FROM products WHERE product_id = ?");
$p->bind_param("i", $product_id);
$p->execute();
$r = $p->get_result()->fetch_assoc();

if ($r) {
    $productName = $r['material_name'];
    $newStock    = (int)$r['stock'];
    $limit       = (int)$r['low_stock_limit'];
    $unitType    = $r['unit_type'];

    // If stock reached or went below reorder level → SEND MAIL
    if ($newStock <= $limit) {
        sendLowStockMail($productName, $newStock, $limit, $unitType);
    }
}

// ✅ SIMPLE & RELIABLE: If stock is LOW after update → send mail
$emailSent = false;

// Send mail ONLY when stock crosses from above limit to low/equal limit
if (
    (int)$oldStock > (int)$low_stock_limit &&
    (int)$new_stock <= (int)$low_stock_limit
) {
    $emailSent = sendLowStockMail($productName, $new_stock, $low_stock_limit, $unitType);
}



// Prepare message
$addedQuantity = $new_stock - $oldStock;
if ($addedQuantity > 0) {
    $message = "Successfully added {$addedQuantity} {$unitType} to {$productName}. New stock: {$new_stock} {$unitType}";
} else {
    $message = "Stock updated successfully";
}

// Final JSON response
echo json_encode([
    'status' => 'success',
    'message' => $message,
    'email_sent' => $emailSent
]);

$conn->close();
exit();


// =======================
// MAIL FUNCTION
// =======================
function sendLowStockMail($productName, $stockLeft, $threshold, $unitType) {

    $mail = new PHPMailer(true);

    try {
        // No debug in AJAX
        $mail->SMTPDebug = 0;

        // SMTP settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'thiyageshhari5655@gmail.com';
        $mail->Password   = 'zopmcuwqdvfyoksi'; // App password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender & Receiver
        $mail->setFrom('thiyageshhari5655@gmail.com', 'PKBUILDERS Alert');
        $mail->addAddress('thiyageshhari321@gmail.com');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Low Stock Alert: ' . $productName;
        $mail->Body = "
            <h2>⚠️ Low Stock Alert</h2>
            <p><strong>Product:</strong> {$productName}</p>
            <p><strong>Current Stock:</strong> {$stockLeft} {$unitType}</p>
            <p><strong>Threshold:</strong> {$threshold} {$unitType}</p>
            <p>Please restock immediately.</p>
        ";

        $mail->AltBody = "Low Stock Alert\nProduct: {$productName}\nStock: {$stockLeft} {$unitType}\nThreshold: {$threshold}";

        return $mail->send();

    } catch (Exception $e) {
        error_log("Mail Error: " . $mail->ErrorInfo);
        return false;
    }
}
?>
