<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$customer_id = $_SESSION['user_id']; // Logged-in user ID

$query = "SELECT o.id, p.name AS product_name, oi.quantity, o.total_price, o.status, 
                 pay.payment_status, pay.payment_date 
          FROM orders o
          JOIN order_items oi ON o.id = oi.order_id
          JOIN products p ON oi.product_id = p.id
          LEFT JOIN payments pay ON o.id = pay.order_id
          WHERE o.user_id = $customer_id
          ORDER BY o.order_date DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Orders</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f9fa; text-align: center; }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color: #343a40; color: white; }
        .btn-cancel { background-color: red; color: white; padding: 5px 10px; border: none; cursor: pointer; }
        .btn-cancel:hover { background-color: darkred; }
        .btn-shop { text-decoration: none; background-color: #007bff; color: white; padding: 10px 15px; display: inline-block; margin-top: 10px; }
    </style>
</head>
<body>

    <h2>🛒 My Orders</h2>

    <?php if ($result->num_rows > 0) { ?>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Payment Status</th>
                <th>Payment Date</th>
                <th>Action</th>
            </tr>

            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']); ?></td>
                    <td><?= htmlspecialchars($row['product_name']); ?></td>
                    <td><?= htmlspecialchars($row['quantity']); ?></td>
                    <td>₹<?= number_format($row['total_price'], 2); ?></td>
                    <td><?= htmlspecialchars($row['status']); ?></td>
                    <td><?= isset($row['payment_status']) ? htmlspecialchars($row['payment_status']) : 'Not Paid'; ?></td>
                    <td><?= isset($row['payment_date']) ? htmlspecialchars($row['payment_date']) : '-'; ?></td>
                    <td>
                        <?php if ($row['status'] == 'Pending') { ?>
                            <form method="POST" action="cancel_order.php">
                                <input type="hidden" name="order_id" value="<?= htmlspecialchars($row['id']); ?>">
                                <button type="submit" class="btn-cancel" onclick="return confirm('Are you sure you want to cancel this order?');">Cancel</button>
                            </form>
                        <?php } else { ?>
                            -
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p>No orders found.</p>
    <?php } ?>

    <br>
    <a href="browse_products.php" class="btn-shop">🛍️ Continue Shopping</a>

</body>
</html>
