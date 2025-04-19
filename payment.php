<?php
session_start();
require 'vendor/autoload.php'; // Ensure this file exists and is correctly loaded

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

if (!isset($_SESSION['user_id'])) {
    die("Error: You must be logged in to make a payment. <a href='login.php'>Login</a>");
}

$order_id = $_GET['order_id'] ?? '';
$amount = $_GET['amount'] ?? 0;

if (empty($order_id) || $amount <= 0) {
    die("Invalid order details. <a href='checkout.php'>Go back</a>");
}

// UPI Payment Link
$upi_id = "madhu790@cnrb";
$upi_link = "upi://pay?pa=$upi_id&pn=Madhu&tr=$order_id&tn=Payment%20for%20$order_id&am=$amount&cu=INR";

// QR Code Options
$options = new QROptions([
    'version'      => 10, // Increased version to support more data
    'outputType'   => QRCode::OUTPUT_IMAGE_PNG,
    'eccLevel'     => QRCode::ECC_H,
    'scale'        => 10,
    'imageBase64'  => false,
]);


$qrcode = new QRCode($options);

// Generate and save QR code as a file
$qr_image_path = 'qrcodes/order_' . $order_id . '.png';
$qrcode->render($upi_link, $qr_image_path);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPI Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            text-align: center;
            padding: 20px;
        }
        .container {
            background: #fff;
            max-width: 400px;
            margin: auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h2 { color: #333; }
        .qr-code img {
            width: 100%;
            max-width: 250px;
            margin: 15px 0;
        }
        .pay-button {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .pay-button:hover {
            background: #218838;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Scan to Pay</h2>
    <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order_id); ?></p>
    <p><strong>Amount:</strong> ₹<?php echo number_format($amount, 2); ?></p>

    <div class="qr-code">
        <?php if (file_exists($qr_image_path)) { ?>
            <img src="<?php echo $qr_image_path; ?>" alt="UPI QR Code">
        <?php } else { ?>
            <p style="color:red;">Failed to generate QR Code.</p>
        <?php } ?>
    </div>

    <a href="<?php echo $upi_link; ?>" class="pay-button">Pay via UPI</a>
</div>

</body>
</html>
