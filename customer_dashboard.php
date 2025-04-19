<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Fetch customer details
$customer_id = $_SESSION['user_id'];
$customer_sql = "SELECT username, email FROM user WHERE id = ?";
$stmt = $conn->prepare($customer_sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$customer_result = $stmt->get_result();
$customer = $customer_result->fetch_assoc();

// Fetch order history
$order_sql = "SELECT id, order_date, total_price, status FROM orders WHERE user_id = ? ORDER BY order_date DESC";
$stmt = $conn->prepare($order_sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$order_result = $stmt->get_result();

// Fetch cart items
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Fetch sales trends
$sales_sql = "SELECT DATE(order_date) as order_date, SUM(total_price) as total_revenue 
              FROM orders WHERE status = 'Completed' 
              GROUP BY DATE(order_date)";
$sales_result = $conn->query($sales_sql);
$dates = [];
$sales = [];
while ($row = $sales_result->fetch_assoc()) {
    $dates[] = $row['order_date'];
    $sales[] = $row['total_revenue'];
}

// Fetch total orders & amount spent
$order_summary_sql = "SELECT COUNT(id) AS total_orders, SUM(total_price) AS total_spent FROM orders WHERE user_id = ?";
$stmt = $conn->prepare($order_summary_sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$total_orders = $result['total_orders'] ?? 0;
$total_spent = $result['total_spent'] ?? 0.00;

// Fetch most purchased product
$product_sql = "SELECT products.name, COUNT(order_items.product_id) AS count 
                FROM order_items 
                JOIN products ON order_items.product_id = products.id 
                JOIN orders ON order_items.order_id = orders.id 
                WHERE orders.user_id = ? 
                GROUP BY order_items.product_id 
                ORDER BY count DESC LIMIT 1";
$stmt = $conn->prepare($product_sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$product_result = $stmt->get_result()->fetch_assoc();
$most_purchased_product = $product_result['name'] ?? 'N/A';

// Next Offer (Static for now, but can be based on DB logic)
$next_discount_offer = "Get 10% off on your next purchase!";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Customer Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { background-color: #343a40; height: 100vh; padding-top: 20px; position: fixed; width: 250px; }
        .sidebar a { color: #fff; font-size: 18px; padding: 12px; display: block; text-decoration: none; }
        .sidebar a:hover { background-color: #495057; }
        .main-content { margin-left: 250px; padding: 20px; }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar position-fixed">
        <h3 class="text-center text-white">Customer Panel</h3>
        <a href="browse_products.php">🛍️ Shop Products</a>
        <a href="order_history.php">📜 Orders History</a>
        <a href="customer_orders.php">📜 my Orders</a>
        <a href="view_cart.php">🛒 View Cart</a>
        <a href="wishlist.php">❤️ My Wishlist</a>
        <a href="profile.php">👤 My Profile</a>
        <a href="logout.php" class="text-danger">🚪 Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container mt-4">
            <!-- Customer Insights Card -->
            <div class="card mt-4 shadow-lg">
                <div class="card-header text-center bg-success text-white">
                    <h5>📊 Your Purchase Insights</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">🛒 <strong>Total Orders Placed:</strong> <?php echo $total_orders; ?></li>
                        <li class="list-group-item">💰 <strong>Total Amount Spent:</strong> ₹<?php echo number_format($total_spent, 2); ?></li>
                        <li class="list-group-item">📦 <strong>Most Purchased Product:</strong> <?php echo $most_purchased_product; ?></li>
                        <li class="list-group-item">🎉 <strong>Next Offer:</strong> <?php echo $next_discount_offer; ?></li>
                    </ul>
                </div>
            </div>

            <!-- Order History -->
            <div class="card mt-4 shadow-lg">
                <div class="card-header text-center bg-primary text-white">
                    <h5>📜 My Order History</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Total Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $order_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo $row['order_date']; ?></td>
                                    <td>₹<?php echo number_format($row['total_price'], 2); ?></td>
                                    <td><?php echo $row['status']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Cart Items -->
            <div class="card mt-4 shadow-lg">
                <div class="card-header text-center bg-warning text-white">
                    <h5>🛒 Your Cart</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($cart_items)): ?>
                        <p class="text-danger">Your cart is empty.</p>
                    <?php else: ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total_price = 0;
                                foreach ($cart_items as $id => $item): 
                                    $total = $item['price'] * $item['quantity'];
                                    $total_price += $total;
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                                        <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td>₹<?php echo number_format($total, 2); ?></td>
                                        <td><a href="remove_from_cart.php?id=<?php echo $id; ?>" class="text-danger">Remove</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <h5>Total Price: ₹<?php echo number_format($total_price, 2); ?></h5>
                        <a href="checkout.php" class="btn btn-success">🛒 Proceed to Checkout</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Wishlist -->
            <div class="card mt-4 shadow-lg">
                <div class="card-header text-center bg-danger text-white">
                    <h5>❤️ My Wishlist</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Feature coming soon...</p>
                </div>
            </div>

            <!-- Profile -->
            <div class="card mt-4 shadow-lg">
                <div class="card-header text-center bg-info text-white">
                    <h5>👤 My Profile</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($customer['username']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>
                    <a href="profile.php" class="btn btn-primary">Edit Profile</a>
                </div>
            </div>

    <script>
        var ctx = document.getElementById('salesChart').getContext('2d');
        var salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dates); ?>,
                datasets: [{
                    label: 'Total Revenue',
                    data: <?php echo json_encode($sales); ?>,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>

</body>
</html>
