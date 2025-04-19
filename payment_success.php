<?php
session_start();
include('db.php');

if (!isset($_GET['payment_id']) || !isset($_SESSION['order_id'])) {
    die("Invalid payment.");
}

$order_id = $_SESSION['order_id'];
$payment_id = $_GET['payment_id'];

// Insert payment record
$query = "INSERT INTO payments (order_id, payment_id, amount, status) VALUES ($order_id, '$payment_id', {$_SESSION['total_price']}, 'Success')";
$conn->query($query);

// Update order status to Completed
$conn->query("UPDATE orders SET status='Completed' WHERE id=$order_id");

// Clear session variables
unset($_SESSION['order_id']);
unset($_SESSION['total_price']);

echo "Payment successful! <a href='browse_products.php'>Continue Shopping</a>";
?>
