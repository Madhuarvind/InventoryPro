<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include PHPMailer

function sendEmail($to, $subject, $message) {
    $mail = new PHPMailer(true);

    try {
        // SMTP Server Settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Change for Outlook, Yahoo, etc.
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com'; // Your Email
        $mail->Password = 'your-app-password'; // Use App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Email Settings
        $mail->setFrom('your-email@gmail.com', 'Admin Notification');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        // Send Email
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
