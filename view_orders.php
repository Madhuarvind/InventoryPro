<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch orders with correct table references
$sql = "SELECT orders.id, products.name AS product_name, user.username AS customer_name, 
               order_items.quantity, orders.total_price, orders.order_date, orders.status
        FROM orders
        JOIN order_items ON orders.id = order_items.order_id
        JOIN products ON order_items.product_id = products.id
        JOIN user ON orders.user_id = user.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order History</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        .card {
            margin-top: 50px;
        }
        .card-header {
            background-color: #007bff;
            color: white;
            text-align: center;
        }
        .table thead {
            background-color: #343a40;
            color: white;
        }
        .status-pending {
            color: red;
            font-weight: bold;
        }
        .status-completed {
            color: green;
            font-weight: bold;
        }
        .status-cancelled {
            color: orange;
            font-weight: bold;
        }
        .btn-action {
            margin-bottom: 5px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="card shadow-lg">
            <div class="card-header">
                <h2>📋 Order History</h2>
            </div>
            <div class="card-body">
                <?php if ($result->num_rows > 0): ?>
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Customer</th>
                                <th>Quantity</th>
                                <th>Total Price</th>
                                <th>Order Date</th>
                                <th>Status</th>
                                <?php if ($_SESSION['role'] === 'admin'): ?>
                                    <th>Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id']); ?></td>
                                    <td><?= htmlspecialchars($row['product_name']); ?></td>
                                    <td><?= htmlspecialchars($row['customer_name']); ?></td>
                                    <td><?= htmlspecialchars($row['quantity']); ?></td>
                                    <td>₹<?= number_format($row['total_price'], 2); ?></td>
                                    <td><?= htmlspecialchars($row['order_date']); ?></td>
                                    <td>
                                        <span class="
                                            <?= $row['status'] == 'Pending' ? 'status-pending' : '' ?>
                                            <?= $row['status'] == 'Completed' ? 'status-completed' : '' ?>
                                            <?= $row['status'] == 'Cancelled' ? 'status-cancelled' : '' ?>">
                                            <?= htmlspecialchars($row['status']); ?>
                                        </span>
                                    </td>
                                    <?php if ($_SESSION['role'] === 'admin'): ?>
                                        <td>
                                            <a href="update_order.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm btn-action">✏️ Update</a>
                                            <a href="cancel_order.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm btn-action" onclick="return confirm('Are you sure you want to cancel this order?');">❌ Cancel</a>
                                            <?php if ($row['status'] !== 'Completed'): ?>
                                                <a href="complete_order.php?id=<?= $row['id']; ?>" class="btn btn-success btn-sm btn-action" onclick="return confirm('Mark this order as completed?');">✅ Complete</a>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info text-center">No orders found.</div>
                <?php endif; ?>
            </div>
            <div class="card-footer text-center">
                <a href="<?= ($_SESSION['role'] === 'admin') ? 'admin_dashboard.php' : 'customer_dashboard.php'; ?>" class="btn btn-secondary">🏠 Back to Dashboard</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
