<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

// Fetch daily sales data
include 'db.php';
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

// Fetch stock levels
$stock_sql = "SELECT name, quantity FROM products";
$stock_result = $conn->query($stock_sql);

$product_names = [];
$stock_levels = [];
while ($row = $stock_result->fetch_assoc()) {
    $product_names[] = $row['name'];
    $stock_levels[] = $row['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Staff Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            background-color: #343a40;
            height: 100vh;
            padding-top: 20px;
            position: fixed;
            width: 250px;
        }
        .sidebar a {
            color: #fff;
            font-size: 18px;
            padding: 12px;
            display: block;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .card-body {
            padding: 1.5rem;
        }
        .card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar position-fixed">
        <h3 class="text-center text-white">Staff Panel</h3>
        <a href="products.php">📦 View Products</a>
        <a href="stock.php">📊 Update Stock</a>
        <a href="orders.php">🛒 Place Orders</a>
        <a href="logout.php" class="text-danger">🚪 Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container mt-4">
            <div class="card shadow-lg">
                <div class="card-header text-center bg-primary text-white">
                    <h3>Staff Dashboard</h3>
                    <p>Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</p>
                </div>
                <div class="card-body">
                    <!-- Sales Trend Chart -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card shadow-sm p-3">
                                <h5 class="text-secondary">Sales Trend</h5>
                                <canvas id="salesChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Levels Chart -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card shadow-sm p-3">
                                <h5 class="text-secondary">Stock Levels</h5>
                                <canvas id="stockChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Staff Operations Section -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h4 class="text-secondary">⚙️ Operations</h4>
                            <ul class="list-group mb-3">
                                <li class="list-group-item"><a href="products.php" class="text-decoration-none">📦 View Products</a></li>
                                <li class="list-group-item"><a href="stock.php" class="text-decoration-none">📊 Update Stock</a></li>
                                <li class="list-group-item"><a href="orders.php" class="text-decoration-none">🛒 Place Orders</a></li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        // Sales Trend Chart
        var ctx1 = document.getElementById('salesChart').getContext('2d');
        var salesChart = new Chart(ctx1, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dates); ?>,
                datasets: [{
                    label: 'Daily Sales (₹)',
                    data: <?php echo json_encode($sales); ?>,
                    borderColor: 'blue',
                    fill: false
                }]
            }
        });

        // Stock Levels Chart
        var ctx2 = document.getElementById('stockChart').getContext('2d');
        var stockChart = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($product_names); ?>,
                datasets: [{
                    label: 'Stock Quantity',
                    data: <?php echo json_encode($stock_levels); ?>,
                    backgroundColor: 'green'
                }]
            }
        });
    </script>
</body>
</html>
