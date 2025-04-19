<?php
session_start();
require 'db.php'; // Ensure you have your database connection file

// Check if the user is logged in and has the correct role (admin or staff)
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'staff')) {
    die("❌ Access Denied. <a href='login.php'>Login</a>");
}

// Get order ID from request
$order_id = $_GET['order_id'] ?? '';

if (empty($order_id)) {
    die("⚠️ Invalid Order ID. <a href='orders.php'>Go back</a>");
}

// Check if the order exists
$sql = "SELECT * FROM orders WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ Order not found. <a href='orders.php'>Go back</a>");
}

$order = $result->fetch_assoc();

// Check if the order is already marked as paid
if ($order['status'] === 'Paid') {
    die("✅ This order has already been marked as Paid. <a href='orders.php'>Go back</a>");
}

// Mark order as Paid
$update_sql = "UPDATE orders SET status = 'Paid', updated_at = NOW() WHERE order_id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("s", $order_id);

if ($update_stmt->execute()) {
    $message = "✅ Order #$order_id has been marked as Paid successfully.";
} else {
    $message = "❌ Error updating order status. Please try again.";
}

// Close connections
$update_stmt->close();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
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
        .message {
            font-size: 18px;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
        }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .back-button {
            background: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .back-button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Payment Status</h2>
    <p class="message <?php echo isset($message) && strpos($message, '✅') !== false ? 'success' : 'error'; ?>">
        <?php echo $message; ?>
    </p>
    <a href="orders.php" class="back-button">⬅ Go Back to Orders</a>
</div>

</body>
</html>
