<?php
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;

$mail = new PHPMailer(true);

$mail->SMTPDebug = 2;
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'thiyageshhari5655@gmail.com';
$mail->Password = 'zopmcuwqdvfyoksi'; // Your Gmail App Password
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;

$mail->setFrom('thiyageshhari5655@gmail.com', 'Test');
$mail->addAddress('thiyageshhari321@gmail.com');

$mail->Subject = 'Test Mail';
$mail->Body = 'If you got this, PHPMailer works!';

$mail->send();
echo "Mail sent!";
