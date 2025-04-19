<?php
session_start();
include 'db.php'; // Database connection

// Ensure only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch Top 5 Customers
$customerQuery = "SELECT user.username AS name, SUM(orders.total_price) AS total_spent 
                  FROM orders 
                  JOIN user ON orders.user_id = user.id 
                  GROUP BY user.id 
                  ORDER BY total_spent DESC LIMIT 5";
$customerResult = $conn->query($customerQuery);
$top_customers = $customerResult->fetch_all(MYSQLI_ASSOC);

// Fetch Sales by Product Category
$categoryQuery = "SELECT ANY_VALUE(products.category) AS name, SUM(order_items.quantity * order_items.price) AS revenue
                  FROM order_items
                  JOIN products ON order_items.product_id = products.id
                  GROUP BY products.category";
$categoryResult = $conn->query($categoryQuery);
$category_sales = $categoryResult->fetch_all(MYSQLI_ASSOC);

// Fetch Sales Trends (Daily Sales)
$salesTrendsQuery = "SELECT DATE(order_date) AS date, SUM(total_price) AS revenue 
                     FROM orders 
                     GROUP BY DATE(order_date) 
                     ORDER BY date ASC";
$salesTrendsResult = $conn->query($salesTrendsQuery);
$sales_trends = $salesTrendsResult->fetch_all(MYSQLI_ASSOC);

// Fetch Payment Method Distribution
$paymentQuery = "SELECT payment_method, COUNT(*) AS count 
                 FROM payments 
                 GROUP BY payment_method";
$paymentResult = $conn->query($paymentQuery);
$payment_methods = $paymentResult->fetch_all(MYSQLI_ASSOC);

// Fetch Stock Availability for Top 5 Products
$stockQuery = "SELECT name, quantity FROM products ORDER BY quantity DESC LIMIT 5";
$stockResult = $conn->query($stockQuery);
$stock_availability = $stockResult->fetch_all(MYSQLI_ASSOC);

// Fetch Top 5 Best Selling Products
$topSellingQuery = "SELECT products.name, SUM(order_items.quantity) AS total_sold 
                    FROM order_items 
                    JOIN products ON order_items.product_id = products.id 
                    GROUP BY products.id 
                    ORDER BY total_sold DESC 
                    LIMIT 5";
$topSellingResult = $conn->query($topSellingQuery);
$top_selling_products = $topSellingResult->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Analytics</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }
        .dark-mode {
            background-color: #121212;
            color: white;
        }
        .dark-mode .card {
            background-color: #1e1e1e;
            color: white;
            border-color: #333;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center mb-4">📊 Advanced Analytics Dashboard</h2>

    <button class="btn btn-dark mb-3" onclick="toggleDarkMode()">🌙 Toggle Dark Mode</button>

    <!-- Top Customers -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">🏆 Top 5 Customers</div>
        <div class="card-body">
            <canvas id="topCustomersChart"></canvas>
        </div>
    </div>

    <!-- Sales by Category -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">📦 Sales by Product Category</div>
        <div class="card-body">
            <canvas id="categorySalesChart"></canvas>
        </div>
    </div>

    <!-- Sales Trends -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">📈 Sales Trends Over Time</div>
        <div class="card-body">
            <canvas id="salesTrendsChart"></canvas>
        </div>
    </div>

    <!-- Payment Method Distribution -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">💳 Payment Methods Used</div>
        <div class="card-body">
            <canvas id="paymentChart"></canvas>
        </div>
    </div>

    <!-- Stock Availability -->
    <div class="card mb-4">
        <div class="card-header bg-danger text-white">📉 Stock Availability (Top Products)</div>
        <div class="card-body">
            <canvas id="stockChart"></canvas>
        </div>
    </div>

    <!-- Top Selling Products (Pie Chart) -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">🔥 Top 5 Selling Products</div>
        <div class="card-body">
            <canvas id="topSellingChart"></canvas>
        </div>
    </div>

</div>

<script>
function getRandomColors(length) {
    const colors = [];
    for (let i = 0; i < length; i++) {
        colors.push(`hsl(${Math.floor(Math.random() * 360)}, 70%, 60%)`);
    }
    return colors;
}

// Top Customers Chart
new Chart(document.getElementById('topCustomersChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($top_customers, 'name')); ?>,
        datasets: [{
            label: 'Total Spent (₹)',
            data: <?php echo json_encode(array_column($top_customers, 'total_spent')); ?>,
            backgroundColor: getRandomColors(5)
        }]
    }
});

// Sales by Category Chart
new Chart(document.getElementById('categorySalesChart'), {
    type: 'pie',
    data: {
        labels: <?php echo json_encode(array_column($category_sales, 'name')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($category_sales, 'revenue')); ?>,
            backgroundColor: getRandomColors(5)
        }]
    }
});

// Sales Trends Chart
new Chart(document.getElementById('salesTrendsChart'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($sales_trends, 'date')); ?>,
        datasets: [{
            label: 'Revenue (₹)',
            data: <?php echo json_encode(array_column($sales_trends, 'revenue')); ?>,
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            fill: true
        }]
    }
});

// Payment Method Distribution Chart
new Chart(document.getElementById('paymentChart'), {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_column($payment_methods, 'payment_method')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($payment_methods, 'count')); ?>,
            backgroundColor: getRandomColors(3)
        }]
    }
});

// Stock Availability Chart
new Chart(document.getElementById('stockChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($stock_availability, 'name')); ?>,
        datasets: [{
            label: 'Stock Available',
            data: <?php echo json_encode(array_column($stock_availability, 'quantity')); ?>,
            backgroundColor: getRandomColors(5)
        }]
    }
});

// Top Selling Products Chart (Pie Chart)
new Chart(document.getElementById('topSellingChart'), {
    type: 'pie',
    data: {
        labels: <?php echo json_encode(array_column($top_selling_products, 'name')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($top_selling_products, 'total_sold')); ?>,
            backgroundColor: getRandomColors(5)
        }]
    }
});

// Dark Mode Toggle
function toggleDarkMode() {
    document.body.classList.toggle("dark-mode");
}
</script>

</body>
</html>
