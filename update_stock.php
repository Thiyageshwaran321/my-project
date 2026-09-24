
<?php
session_start();
include "db.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Check if connection exists
if (!$conn) {
    die("error: Database connection failed");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Validate inputs
    if (!isset($_POST['product_id']) || !isset($_POST['stock'])) {
        echo "error: Missing required fields";
        exit();
    }
    
    $product_id = intval($_POST['product_id']);
    $stock = intval($_POST['stock']);
    
    // Get product name before update for email
    $productName = '';
    $getProduct = $conn->prepare("SELECT material_name FROM products WHERE product_id = ?");
    $getProduct->bind_param("i", $product_id);
    $getProduct->execute();
    $result = $getProduct->get_result();
    if ($row = $result->fetch_assoc()) {
        $productName = $row['material_name'];
    }
    $getProduct->close();
    
    // Check if low_stock_limit is provided
    if (isset($_POST['low_stock_limit']) && !empty($_POST['low_stock_limit'])) {
        $low_stock_limit = intval($_POST['low_stock_limit']);
        $query = "UPDATE products SET stock = ?, low_stock_limit = ? WHERE product_id = ?";
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            echo "error: Prepare failed - " . $conn->error;
            exit();
        }
        
        $stmt->bind_param("iii", $stock, $low_stock_limit, $product_id);
    } else {
        // Backward compatibility - only update stock
        $query = "UPDATE products SET stock = ? WHERE product_id = ?";
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            echo "error: Prepare failed - " . $conn->error;
            exit();
        }
        
        $stmt->bind_param("ii", $stock, $product_id);
    }
    
    if ($stmt->execute()) {
        
        // ===== LOW STOCK CHECK =====
        $LOW_STOCK_LIMIT = 10; // Default threshold
        
        // Check if stock is low (using either default or product-specific limit)
        $checkLimit = isset($low_stock_limit) ? $low_stock_limit : $LOW_STOCK_LIMIT;
        
        if ($stock <= $checkLimit && !empty($productName)) {
            // Send email alert
            $emailSent = sendLowStockMail($productName, $stock, $checkLimit);
            if ($emailSent) {
                error_log("Low stock email sent for: $productName");
            }
        }
        
        echo "success";
    } else {
        echo "error: " . $stmt->error;
    }
    
    $stmt->close();
} else {
    echo "error: Invalid request method";
}

// ===== FUNCTION TO SEND MAIL =====
function sendLowStockMail($productName, $stockLeft, $threshold) {
    
    $mail = new PHPMailer(true);
    
    try {
        // ===== SMTP SETTINGS =====
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'thiyageshhari5655@gmail.com';   // your Gmail
        $mail->Password   = 'zopmcuwqdvfyoksi';        // REPLACE WITH YOUR APP PASSWORD
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;      // SSL
        $mail->Port       = 465;
        
        // ===== DEBUG (TURN OFF IN PRODUCTION) =====
        $mail->SMTPDebug = 0;       // Set to 0 in production, 2 for debugging
        $mail->Debugoutput = 'html';
        
        // ===== SENDER & RECEIVER =====
        $mail->setFrom('thiyageshhari5655@gmail.com', 'PKBUILDERS System');
        $mail->addAddress('thiyageshhari321@gmail.com'); // admin email
        $mail->addReplyTo('thiyageshhari5655@gmail.com', 'PKBUILDERS System');
        
        // ===== EMAIL CONTENT =====
        $mail->isHTML(true);
        $mail->Subject = '🚨 Low Stock Alert - PKBUILDERS';
        
        // Current date and time
        $dateTime = date('d-m-Y H:i:s');
        
        $mail->Body    = "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #e53e3e; color: white; padding: 15px; text-align: center; border-radius: 5px 5px 0 0; }
                    .content { background: #f7fafc; padding: 20px; border: 1px solid #e2e8f0; }
                    .product-name { font-size: 18px; font-weight: bold; color: #2d3748; }
                    .stock-value { font-size: 24px; color: #e53e3e; font-weight: bold; }
                    .threshold { background: #fed7d7; padding: 10px; border-radius: 5px; margin: 15px 0; }
                    .footer { background: #edf2f7; padding: 10px; text-align: center; font-size: 12px; color: #718096; border-radius: 0 0 5px 5px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>⚠️ LOW STOCK ALERT</h2>
                    </div>
                    <div class='content'>
                        <p>The following product is running low on stock:</p>
                        
                        <div class='threshold'>
                            <p><span class='product-name'>Product:</span> {$productName}</p>
                            <p><span class='product-name'>Current Stock:</span> <span class='stock-value'>{$stockLeft}</span></p>
                            <p><span class='product-name'>Threshold Limit:</span> {$threshold}</p>
                        </div>
                        
                        <p><strong>Action Required:</strong> Please restock this product as soon as possible to avoid stockouts.</p>
                        <p><small>Alert generated on: {$dateTime}</small></p>
                    </div>
                    <div class='footer'>
                        <p>This is an automated message from PKBUILDERS Inventory System</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        // Plain text alternative for non-HTML email clients
        $mail->AltBody = "LOW STOCK ALERT\n\nProduct: {$productName}\nCurrent Stock: {$stockLeft}\nThreshold: {$threshold}\n\nPlease restock this product immediately.\n\nAlert generated on: {$dateTime}";
        
        // ===== SEND MAIL =====
        if($mail->send()) {
            return true;
        } else {
            error_log("Mailer Error: " . $mail->ErrorInfo);
            return false;
        }
        
    } catch (Exception $e) {
        error_log("Mail Exception: " . $mail->ErrorInfo);
        return false;
    }
}

$conn->close();
?>