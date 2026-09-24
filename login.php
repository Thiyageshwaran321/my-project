<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

// PHPMailer files
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM customers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $customer = $result->fetch_assoc();

        // Password check (plain OR hashed)
        if ($password == $customer['password'] || password_verify($password, $customer['password'])) {

            $_SESSION['customer_id']   = $customer['id'];
            $_SESSION['customer_name'] = $customer['name'];

            // =======================
            // SEND LOGIN MAIL
            // =======================
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'thiyageshhari5655@gmail.com'; // YOUR EMAIL
                $mail->Password   = 'xitahoaqthkkfqtr';   // APP PASSWORD
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom('yourgmail@gmail.com', 'DIGISAND');
                $mail->addAddress($customer['email'], $customer['name']);

                $mail->isHTML(true);
                $mail->Subject = 'Login Alert';
                $mail->Body    = "
                    <h3>Hello {$customer['name']},</h3>
                    <p>You have successfully logged in to <b>DIGISAND</b>.</p>
                    <p>If this wasn’t you, please contact support immediately.</p>
                    <br>
                    <p>Thank you,<br>DIGISAND Team</p>
                ";

                $mail->send();
            } catch (Exception $e) {
                // Optional: log error
                // echo "Mail Error: {$mail->ErrorInfo}";
            }

            header("Location: myaccount.php");
            exit;
        } else {
            echo "Wrong password bro!";
        }

    } else {
        echo "Email not found!";
    }
}
?>
