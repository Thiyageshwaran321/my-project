<?php
include "db.php";

require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;

// Fetch all low stock products
$q = $conn->query("SELECT material_name, stock, low_stock_limit, unit_type 
                   FROM products 
                   WHERE stock <= low_stock_limit");

while ($row = $q->fetch_assoc()) {

    $productName = $row['material_name'];
    $stockLeft   = $row['stock'];
    $limit       = $row['low_stock_limit'];
    $unitType    = $row['unit_type'];

    sendLowStockMail($productName, $stockLeft, $limit, $unitType);
}

echo "Check completed";

function sendLowStockMail($productName, $stockLeft, $threshold, $unitType) {

    $mail = new PHPMailer(true);

    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'thiyageshhari5655@gmail.com';
    $mail->Password   = 'zopmcuwqdvfyoksi'; // app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('thiyageshhari5655@gmail.com', 'PKBUILDERS Alert');
    $mail->addAddress('thiyageshhari321@gmail.com');

    $mail->isHTML(true);
    $mail->Subject = 'Low Stock Alert: ' . $productName;
    $mail->Body = "
        <h2>⚠️ Low Stock Alert</h2>
        <p><b>Product:</b> {$productName}</p>
        <p><b>Stock:</b> {$stockLeft} {$unitType}</p>
        <p><b>Limit:</b> {$threshold} {$unitType}</p>
        <p>Please restock.</p>
    ";

    $mail->send();
}
