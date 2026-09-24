<?php
session_start();
include "db.php";

header('Content-Type: application/json');

// Error reporting (turn OFF in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include PHPMailer - Adjust path if needed
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check database connection
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
if (!isset($_POST['product_id']) || !isset($_POST['stock'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit();
}

$product_id = (int)$_POST['product_id'];
$new_stock = (int)$_POST['stock'];
$reorder_level = isset($_POST['low_stock_limit']) ? (int)$_POST['low_stock_limit'] : 0;

// Fetch current product details BEFORE update
$productName = '';
$old_stock = 0;
$unitType = '';
$current_reorder = 0;

$getProduct = $conn->prepare("SELECT material_name, stock, unit_type, low_stock_limit FROM products WHERE product_id = ?");
$getProduct->bind_param("i", $product_id);
$getProduct->execute();
$result = $getProduct->get_result();

if ($row = $result->fetch_assoc()) {
    $productName = $row['material_name'];
    $old_stock = (int)$row['stock'];
    $unitType = $row['unit_type'];
    $current_reorder = (int)$row['low_stock_limit'];
} else {
    echo json_encode(['status' => 'error', 'message' => 'Product not found']);
    exit();
}
$getProduct->close();

// Use provided reorder level or keep existing
if ($reorder_level <= 0) {
    $reorder_level = $current_reorder;
}

// Safety default
if ($reorder_level <= 0) {
    $reorder_level = 10;
}

// Update stock and reorder level
$stmt = $conn->prepare("UPDATE products SET stock = ?, low_stock_limit = ? WHERE product_id = ?");
$stmt->bind_param("iii", $new_stock, $reorder_level, $product_id);

if (!$stmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
    exit();
}
$stmt->close();

// Check if stock is BELOW reorder level and send email
$email_sent = false;
$email_status = 'not_sent';
$email_message = '';

// Send email ONLY when stock goes BELOW the reorder level
if ($new_stock < $reorder_level) {
    // Calculate critical level (20% of reorder level)
    $criticalLevel = max(1, floor($reorder_level * 0.2));
    
    $email_sent = sendLowStockMail(
        $productName, 
        $new_stock, 
        $reorder_level, 
        $unitType,
        $criticalLevel,
        $old_stock
    );
    
    if ($email_sent) {
        $email_status = 'sent';
        $email_message = "Low stock alert email sent for {$productName}";
        error_log("✅ LOW STOCK EMAIL SENT: {$productName} - Stock: {$new_stock}{$unitType}, Limit: {$reorder_level}{$unitType}");
    } else {
        $email_status = 'failed';
        $email_message = "Failed to send low stock alert email";
        error_log("❌ EMAIL FAILED: {$productName} - Check PHPMailer settings");
    }
}

// Prepare user message
$addedQuantity = $new_stock - $old_stock;
if ($addedQuantity > 0) {
    $message = "✅ Successfully added {$addedQuantity} {$unitType} to {$productName}";
} elseif ($addedQuantity < 0) {
    $message = "✅ Successfully reduced stock by " . abs($addedQuantity) . " {$unitType}";
} else {
    $message = "✅ Stock updated successfully (no change)";
}

$message .= ". New stock: {$new_stock} {$unitType}";

// Add warning if stock is below reorder level
if ($new_stock < $reorder_level) {
    $message .= " ⚠️ Stock is BELOW reorder level ({$reorder_level} {$unitType})";
}

// Return JSON response
echo json_encode([
    'status' => 'success',
    'message' => $message,
    'email_sent' => $email_sent,
    'email_status' => $email_status,
    'email_message' => $email_message,
    'data' => [
        'product_id' => $product_id,
        'product_name' => $productName,
        'old_stock' => $old_stock,
        'new_stock' => $new_stock,
        'added_quantity' => $addedQuantity,
        'unit_type' => $unitType,
        'reorder_level' => $reorder_level,
        'is_below_reorder' => ($new_stock < $reorder_level)
    ]
]);

$conn->close();
exit();

// ============================================
// ENHANCED EMAIL FUNCTION
// ============================================
function sendLowStockMail($productName, $stockLeft, $reorderLevel, $unitType, $criticalLevel, $oldStock) {
    
    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'thiyageshhari5655@gmail.com'; // Your Gmail
        $mail->Password   = 'zopmcuwqdvfyoksi'; // Your App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->Timeout    = 30;
        
        // Disable SSL verification for testing (remove in production)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Sender & Recipient
        $mail->setFrom('thiyageshhari5655@gmail.com', 'PKBUILDERS Inventory');
        $mail->addAddress('thiyageshhari321@gmail.com', 'Admin');
        $mail->addReplyTo('thiyageshhari5655@gmail.com', 'Support');

        // Calculate urgency
        $belowBy = $reorderLevel - $stockLeft;
        $percentage = ($stockLeft / $reorderLevel) * 100;
        
        if ($stockLeft <= $criticalLevel) {
            $urgency = 'CRITICAL';
            $urgencyColor = '#c53030';
        } elseif ($percentage <= 50) {
            $urgency = 'URGENT';
            $urgencyColor = '#ecc94b';
        } else {
            $urgency = 'WARNING';
            $urgencyColor = '#4299e1';
        }

        // Email Subject
        $mail->Subject = "🚨 {$urgency} STOCK ALERT: {$productName}";

        // HTML Email Body
        $mail->isHTML(true);
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: 'Segoe UI', Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; }
                .header { background: {$urgencyColor}; color: white; padding: 20px; text-align: center; }
                .content { padding: 25px; background: #ffffff; }
                .warning-box { background: #fff5f5; border-left: 4px solid #e53e3e; padding: 15px; margin: 20px 0; }
                .product-name { font-size: 22px; font-weight: bold; color: #2d3748; }
                .stock-value { font-size: 32px; font-weight: bold; color: #e53e3e; }
                .details { background: #f7fafc; padding: 15px; border-radius: 8px; margin: 20px 0; }
                .details table { width: 100%; }
                .details td { padding: 8px; }
                .label { font-weight: 600; color: #4a5568; }
                .footer { background: #f7fafc; padding: 15px; text-align: center; font-size: 12px; color: #718096; }
                .button { display: inline-block; background: #4299e1; color: white; padding: 12px 25px; text-decoration: none; border-radius: 6px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>⚠️ {$urgency} STOCK ALERT</h2>
                    <p>Action Required Immediately</p>
                </div>
                <div class='content'>
                    <div class='product-name'>{$productName}</div>
                    
                    <div class='warning-box'>
                        <p style='margin:0; font-size:16px;'>
                            <strong>Stock has fallen below reorder level!</strong>
                        </p>
                    </div>
                    
                    <div class='details'>
                        <table>
                            <tr>
                                <td class='label'>Current Stock:</td>
                                <td><span class='stock-value'>{$stockLeft} {$unitType}</span></td>
                            </tr>
                            <tr>
                                <td class='label'>Reorder Level:</td>
                                <td><strong>{$reorderLevel} {$unitType}</strong></td>
                            </tr>
                            <tr>
                                <td class='label'>Deficit:</td>
                                <td><strong style='color:#e53e3e;'>{$belowBy} {$unitType} below limit</strong></td>
                            </tr>
                            <tr>
                                <td class='label'>Previous Stock:</td>
                                <td>{$oldStock} {$unitType}</td>
                            </tr>
                            <tr>
                                <td class='label'>Critical Level:</td>
                                <td>{$criticalLevel} {$unitType}</td>
                            </tr>
                            <tr>
                                <td class='label'>Alert Time:</td>
                                <td>" . date('d-m-Y H:i:s') . "</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div style='text-align: center; margin-top: 25px;'>
                        <p><strong>Immediate action required to prevent stockout!</strong></p>
                        <a href='http://" . $_SERVER['HTTP_HOST'] . "/admindashboard.php' class='button'>Go to Admin Panel</a>
                    </div>
                </div>
                <div class='footer'>
                    <p>This is an automated message from PKBUILDERS Inventory System</p>
                    <p style='font-size:11px;'>Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        // Plain text version
        $mail->AltBody = "{$urgency} STOCK ALERT\n\n" .
                        "Product: {$productName}\n" .
                        "Current Stock: {$stockLeft} {$unitType}\n" .
                        "Reorder Level: {$reorderLevel} {$unitType}\n" .
                        "Deficit: {$belowBy} {$unitType} below limit\n" .
                        "Critical Level: {$criticalLevel} {$unitType}\n" .
                        "Previous Stock: {$oldStock} {$unitType}\n" .
                        "Alert Time: " . date('d-m-Y H:i:s') . "\n\n" .
                        "ACTION REQUIRED: Please restock immediately!\n" .
                        "Login to admin panel to update stock.";

        // Send email
        if($mail->send()) {
            return true;
        } else {
            error_log("PHPMailer Error: " . $mail->ErrorInfo);
            return false;
        }

    } catch (Exception $e) {
        error_log("PHPMailer Exception: " . $mail->ErrorInfo);
        return false;
    }
}
?>