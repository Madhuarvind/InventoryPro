<?php
session_start();
include('db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("❌ Error: Please log in to view order details.");
}

// Check if the order_id is provided in the URL
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    die("❌ Error: Order ID not specified.");
}

$order_id = intval($_GET['order_id']);
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Fetch order details (Admin can see all, Customer can see their own orders)
if ($user_role === 'Admin') {
    $stmt = $conn->prepare("SELECT orders.id, orders.order_date, orders.total_price, orders.status, users.name AS customer_name 
                            FROM orders JOIN users ON orders.user_id = users.id WHERE orders.id = ?");
} else {
    $stmt = $conn->prepare("SELECT id, order_date, total_price, status FROM orders WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $order_id, $user_id);
}

$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("❌ Error: Order not found.");
}

// Fetch order items
$stmt = $conn->prepare("SELECT p.name, oi.quantity, oi.price 
                        FROM order_items oi 
                        JOIN products p ON oi.product_id = p.id 
                        WHERE oi.order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Details</title>
</head>
<body>
    <h2>Order Details (ID: <?= $order['id'] ?>)</h2>
    
    <?php if ($user_role === 'Admin'): ?>
        <p><strong>Customer:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
    <?php endif; ?>
    
    <p><strong>Order Date:</strong> <?= $order['order_date'] ?></p>
    <p><strong>Status:</strong> <?= $order['status'] ?></p>
    <p><strong>Total Price:</strong> ₹<?= number_format($order['total_price'], 2) ?></p>
    
    <h3>Items:</h3>
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price (₹)</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $items->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>₹<?= number_format($item['price'], 2) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <a href="order_history.php">🔙 Back to Order History</a>
</body>
</html>
