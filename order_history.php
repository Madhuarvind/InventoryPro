<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    die("❌ Error: Please log in to view order history.");
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role']; // Assume role is stored in session (Admin or Customer)

if ($user_role === 'Admin') {
    $stmt = $conn->prepare("SELECT orders.id, orders.order_date, orders.total_price, orders.status, users.name AS customer_name FROM orders JOIN users ON orders.user_id = users.id ORDER BY orders.order_date DESC");
} else {
    $stmt = $conn->prepare("SELECT id, order_date, total_price, status FROM orders WHERE user_id = ? ORDER BY order_date DESC");
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Order History</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 30px;
        }
        .card {
            border-radius: 12px;
        }
        .table th, .table td {
            text-align: center;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 10px;
            font-size: 14px;
        }
        .pending { background-color: #ffc107; color: black; }
        .completed { background-color: #28a745; color: white; }
        .cancelled { background-color: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white text-center">
                <h3><i class="fas fa-history"></i> Order History</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <a href="browse_products.php" class="btn btn-success"><i class="fas fa-shopping-cart"></i> Continue Shopping</a>
                    <a href="customer_dashboard.php" class="btn btn-dark"><i class="fas fa-home"></i> Dashboard</a>
                </div>

                <?php if ($result->num_rows > 0): ?>
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Order ID</th>
                                <th>Order Date</th>
                                <?php if ($user_role === 'Admin') echo "<th>Customer</th>"; ?>
                                <th>Total Price (₹)</th>
                                <th>Status</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?= $row['id'] ?></td>
                                    <td><?= date("d M Y", strtotime($row['order_date'])) ?></td>
                                    <?php if ($user_role === 'Admin') echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>"; ?>
                                    <td><strong>₹<?= number_format($row['total_price'], 2) ?></strong></td>
                                    <td>
                                        <span class="status-badge 
                                            <?= strtolower($row['status']) == 'completed' ? 'completed' : (strtolower($row['status']) == 'pending' ? 'pending' : 'cancelled') ?>">
                                            <?= ucfirst($row['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="order_details.php?order_id=<?= $row['id'] ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted text-center">No orders found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
