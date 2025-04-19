<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Validate order ID
if (!isset($_POST['order_id']) || !is_numeric($_POST['order_id'])) {
    echo "Invalid order ID.";
    exit();
}

$order_id = $_POST['order_id'];

// Fetch the order details
$query = "SELECT * FROM orders WHERE id = ? AND status = 'Pending'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Order cannot be cancelled.";
    exit();
}

$order = $result->fetch_assoc();

// Restore stock if applicable
if (isset($order['product_id']) && isset($order['quantity'])) {
    $update_stock_sql = "UPDATE products SET quantity = quantity + ? WHERE id = ?";
    $stmt = $conn->prepare($update_stock_sql);
    $stmt->bind_param("ii", $order['quantity'], $order['product_id']);
    $stmt->execute();
}

// Update order status to 'Cancelled'
$update_order_sql = "UPDATE orders SET status = 'Cancelled' WHERE id = ?";
$stmt = $conn->prepare($update_order_sql);
$stmt->bind_param("i", $order_id);

if ($stmt->execute()) {
    header("Location: customer_orders.php?message=Order Cancelled Successfully!");
    exit();
} else {
    echo "Error cancelling order: " . $conn->error;
}
?>
