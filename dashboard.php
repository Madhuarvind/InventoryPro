<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch total sales revenue
$total_revenue_sql = "SELECT SUM(total_price) AS revenue FROM orders WHERE status = 'Completed'";
$total_revenue_result = $conn->query($total_revenue_sql);
$total_revenue = $total_revenue_result->fetch_assoc()['revenue'];

// Fetch daily sales data
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
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Dashboard</h2>
        <h3>Total Revenue: ₹<?php echo number_format($total_revenue, 2); ?></h3>

        <!-- Sales Trend Chart -->
        <h3>Sales Trend</h3>
        <canvas id="salesChart"></canvas>

        <!-- Stock Level Chart -->
        <h3>Stock Levels</h3>
        <canvas id="stockChart"></canvas>
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
