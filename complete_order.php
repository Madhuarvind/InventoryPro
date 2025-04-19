<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid order ID.";
    exit();
}

$order_id = $_GET['id'];

// Check if order exists
$check_sql = "SELECT id, status FROM orders WHERE id = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Order not found.";
    exit();
}

$order = $result->fetch_assoc();

// Prevent re-completing an already completed order
if ($order['status'] === 'Completed') {
    echo "This order is already completed.";
    exit();
}

// Update order status
$update_sql = "UPDATE orders SET status = 'Completed' WHERE id = ?";
$stmt = $conn->prepare($update_sql);
$stmt->bind_param("i", $order_id);

if ($stmt->execute()) {
    header("Location: view_orders.php?message=Order Completed Successfully!");
    exit();
} else {
    echo "Error updating order: " . $conn->error;
}
?>
