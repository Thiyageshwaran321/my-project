<?php
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    echo "Testing PHPMailer...\n";
    
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'thiyageshhari5655@gmail.com';
    $mail->Password   = 'zopmcuwqdvfyoksi'; // Your app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;
    
    $mail->setFrom('thiyageshhari5655@gmail.com', 'Test');
    $mail->addAddress('thiyageshhari321@gmail.com');
    
    $mail->isHTML(true);
    $mail->Subject = 'Test Email from PKBUILDERS';
    $mail->Body    = 'This is a test email to verify PHPMailer is working.';
    
    $mail->send();
    echo '✅ Test email sent successfully!';
    
} catch (Exception $e) {
    echo "❌ Failed: {$mail->ErrorInfo}";
}
?>