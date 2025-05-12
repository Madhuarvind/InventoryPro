<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Fetch total sales revenue (Admins Only)
$total_revenue_sql = "SELECT SUM(total_price) AS revenue FROM orders WHERE status = 'Completed'";
$total_revenue_result = $conn->query($total_revenue_sql);
$total_revenue = $total_revenue_result->fetch_assoc()['revenue'];

// Fetch daily sales data
$sales_sql = "SELECT DATE(order_date) as order_date, SUM(total_price) as total_revenue 
              FROM orders 
              WHERE status = 'Completed' 
              GROUP BY DATE(order_date)";
$sales_result = $conn->query($sales_sql);

$dates = [];
$sales = [];
while ($row = $sales_result->fetch_assoc()) {
    $dates[] = $row['order_date'];
    $sales[] = $row['total_revenue'];
}

// Fetch monthly sales data
$monthly_sales_sql = "SELECT DATE_FORMAT(order_date, '%Y-%m') as month, SUM(total_price) as monthly_revenue 
                      FROM orders 
                      WHERE status = 'Completed' 
                      GROUP BY DATE_FORMAT(order_date, '%Y-%m') 
                      ORDER BY month ASC";
$monthly_sales_result = $conn->query($monthly_sales_sql);

$months = [];
$monthly_revenues = [];
while ($row = $monthly_sales_result->fetch_assoc()) {
    $months[] = $row['month'];
    $monthly_revenues[] = $row['monthly_revenue'];
}

// Fetch stock levels
$stock_sql = "SELECT name, quantity FROM products";
$stock_result = $conn->query($stock_sql);

$product_names = [];
$stock_levels = [];
$low_stock = []; // For low stock alert card
while ($row = $stock_result->fetch_assoc()) {
    $product_names[] = $row['name'];
    $stock_levels[] = $row['quantity'];
    if ($row['quantity'] < 5) { // Threshold for low stock
        $low_stock[] = $row;
    }
}

// Fetch top selling products using orders table
// Assumes orders table has a 'product_id' column linking to products.id
$top_products_sql = "SELECT p.name, SUM(oi.quantity) AS total_sales
                     FROM order_items oi
                     JOIN products p ON oi.product_id = p.id
                     JOIN orders o ON oi.order_id = o.id
                     WHERE o.status = 'Completed'
                     GROUP BY p.name
                     ORDER BY total_sales DESC
                     LIMIT 5";

$top_products_result = $conn->query($top_products_sql);

$top_product_names = [];
$top_product_sales = [];
while ($row = $top_products_result->fetch_assoc()) {
    $top_product_names[] = $row['name'];
    $top_product_sales[] = $row['total_sales'];
}


// New Chart: Orders Count by Day of Week
$orders_dow_sql = "SELECT DAYNAME(order_date) as day, COUNT(*) as total_orders 
                   FROM orders 
                   WHERE status = 'Completed'
                   GROUP BY DAYNAME(order_date)
                   ORDER BY FIELD(day, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')";
$orders_dow_result = $conn->query($orders_dow_sql);
$days_of_week = [];
$dow_orders = [];
while ($row = $orders_dow_result->fetch_assoc()) {
    $days_of_week[] = $row['day'];
    $dow_orders[] = $row['total_orders'];
}

// New Chart: Average Order Value Trend
$avg_order_sql = "SELECT DATE(order_date) as day, AVG(total_price) as avg_order_value 
                  FROM orders 
                  WHERE status = 'Completed'
                  GROUP BY DATE(order_date)
                  ORDER BY day ASC";
$avg_order_result = $conn->query($avg_order_sql);
$avg_order_days = [];
$avg_order_values = [];
while ($row = $avg_order_result->fetch_assoc()) {
    $avg_order_days[] = $row['day'];
    $avg_order_values[] = $row['avg_order_value'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | InventoryPro</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- ApexCharts for advanced charts -->
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <!-- DataTables CSS -->
  <link href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <!-- Favicon -->
  <link rel="icon" href="logo.png" type="image/png">
  
  <style>
      :root {
          --primary: #4361ee;
          --secondary: #3f37c9;
          --accent: #4895ef;
          --success: #2ecc71;
          --warning: #f39c12;
          --danger: #e74c3c;
          --info: #3498db;
          --dark: #1b263b;
          --light: #f8f9fa;
          --gradient-start: #4361ee;
          --gradient-end: #7209b7;
      }
      
      body {
          font-family: 'Poppins', sans-serif;
          background-color: #f0f2f5;
          overflow-x: hidden;
      }
      
      /* Sidebar Styling */
      .sidebar {
          width: 280px;
          height: 100vh;
          background: linear-gradient(to bottom, var(--dark), #2c3e50);
          color: white;
          padding-top: 20px;
          transition: all 0.3s ease;
          position: fixed;
          left: 0;
          top: 0;
          z-index: 1000;
          box-shadow: 3px 0 10px rgba(0,0,0,0.1);
      }
      
      .sidebar-header {
          padding: 0 20px 20px;
          border-bottom: 1px solid rgba(255,255,255,0.1);
          margin-bottom: 15px;
      }
      
      .sidebar h3 {
          font-size: 22px;
          font-weight: 600;
          padding: 15px 0;
          text-transform: uppercase;
          text-align: center;
          letter-spacing: 1px;
      }
      
      .sidebar-menu {
          list-style: none;
          padding: 0;
          margin: 0;
      }
      
      .sidebar-menu li {
          padding: 0;
          position: relative;
          margin-bottom: 5px;
      }
      
      .sidebar-menu a {
          color: rgba(255,255,255,0.8);
          text-decoration: none;
          display: flex;
          align-items: center;
          font-size: 15px;
          padding: 12px 20px;
          transition: all 0.3s ease;
          border-left: 4px solid transparent;
      }
      
      .sidebar-menu a i {
          margin-right: 10px;
          font-size: 18px;
          width: 25px;
          text-align: center;
      }
      
      .sidebar-menu a:hover {
          background: rgba(255,255,255,0.1);
          color: white;
          border-left-color: var(--accent);
      }
      
      .sidebar-menu a.active {
          background: rgba(255,255,255,0.1);
          color: white;
          border-left-color: var(--accent);
      }
      
      /* Dropdown Styling */
      .submenu .submenu-content {
          display: none;
          list-style: none;
          padding-left: 20px;
          background: rgba(0,0,0,0.15);
          border-left: 4px solid rgba(255,255,255,0.1);
      }
      
      .submenu:hover .submenu-content {
          display: block;
      }
      
      .submenu a {
          cursor: pointer;
      }
      
      /* Responsive Sidebar */
      @media (max-width: 992px) {
          .sidebar {
              width: 70px;
              overflow: hidden;
          }
          
          .sidebar:hover {
              width: 280px;
          }
          
          .sidebar h3, .sidebar .sidebar-header p {
              display: none;
          }
          
          .sidebar:hover h3, .sidebar:hover .sidebar-header p {
              display: block;
          }
          
          .sidebar-menu a span {
              display: none;
          }
          
          .sidebar:hover .sidebar-menu a span {
              display: inline;
          }
          
          .main-content {
              margin-left: 70px;
          }
      }
      
      /* Main Content Styling */
      .main-content {
          margin-left: 280px;
          padding: 20px;
          transition: all 0.3s ease;
      }
      
      /* Card Styling */
      .card {
          border: none;
          border-radius: 15px;
          box-shadow: 0 5px 15px rgba(0,0,0,0.05);
          margin-bottom: 25px;
          transition: transform 0.3s ease, box-shadow 0.3s ease;
      }
      
      .card:hover {
          transform: translateY(-5px);
          box-shadow: 0 10px 20px rgba(0,0,0,0.1);
      }
      
      .card-header {
          border-radius: 15px 15px 0 0 !important;
          border-bottom: none;
          padding: 20px 25px;
      }
      
      .card-body {
          padding: 25px;
      }
      
      /* Dashboard Stats Cards */
      .stat-card {
          border-radius: 15px;
          padding: 25px;
          display: flex;
          align-items: center;
          background: white;
          box-shadow: 0 5px 15px rgba(0,0,0,0.05);
          transition: all 0.3s ease;
      }
      
      .stat-card:hover {
          transform: translateY(-5px);
          box-shadow: 0 10px 20px rgba(0,0,0,0.1);
      }
      
      .stat-card .icon {
          width: 60px;
          height: 60px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 24px;
          margin-right: 15px;
          color: white;
      }
      
      .stat-card .content h4 {
          font-size: 24px;
          font-weight: 700;
          margin-bottom: 5px;
      }
      
      .stat-card .content p {
          font-size: 14px;
          margin-bottom: 0;
          color: #6c757d;
      }
      
      /* Chart Containers */
      .chart-container {
          position: relative;
          height: 300px;
          width: 100%;
      }
      
      /* Low Stock Alert Styling */
      .low-stock {
          color: var(--danger);
          font-weight: 500;
      }
      
      .low-stock-badge {
          position: absolute;
          top: -8px;
          right: -8px;
          background: var(--danger);
          color: white;
          border-radius: 50%;
          width: 20px;
          height: 20px;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 12px;
          font-weight: bold;
      }
      
      /* Quick Action Buttons */
      .quick-action {
          text-align: center;
          padding: 15px;
          border-radius: 10px;
          background: white;
          box-shadow: 0 3px 10px rgba(0,0,0,0.05);
          transition: all 0.3s ease;
      }
      
      .quick-action:hover {
          transform: translateY(-5px);
          box-shadow: 0 8px 15px rgba(0,0,0,0.1);
      }
      
      .quick-action i {
          font-size: 24px;
          margin-bottom: 10px;
          display: block;
      }
      
      /* Notification Badge */
      .notification-badge {
          position: relative;
      }
      
      .notification-badge .badge {
          position: absolute;
          top: -5px;
          right: -5px;
      }
      
      /* Responsive Adjustments */
      @media (max-width: 768px) {
          .stat-card {
              margin-bottom: 15px;
          }
          
          .chart-container {
              height: 250px;
          }
      }
  </style>
</head>
<body>

<!-- Sidebar -->
<!-- Sidebar -->
<div class="sidebar position-fixed">
    <!-- Logo Section -->
    <div class="logo text-center mb-4">
        <img src="logo.png" alt="Logo" width="40" height="40" class="d-inline-block align-top me-2">
        <span class="text-white" style="font-size: 24px; font-weight: bold;">InventoryPro</span> <!-- Increase font size -->
    </div>
    <!-- Admin Panel Heading -->
    <h3 class="text-center text-white">InventoryPro - Admin Panel</h3>

    <ul class="sidebar-menu">
        <li><a href="dashboard.php"><i class="fas fa-chart-bar" style="color: #f39c12;"></i> Dashboard</a></li>

        <li class="submenu">
            <a href="#"><i class="fas fa-box" style="color: #3498db;"></i> Products ▼</a>
            <ul class="submenu-content">
                <li><a href="products.php">📦 Manage Products</a></li>
                <li><a href="add_product.php">➕ Add Product</a></li>
                <li><a href="edit_product.php">✏️ Edit Product</a></li>
            </ul>
        </li>

        <li class="submenu">
            <a href="#"><i class="fas fa-truck" style="color: #2ecc71;"></i> Suppliers ▼</a>
            <ul class="submenu-content">
                <li><a href="suppliers.php">🚚 Manage Suppliers</a></li>
                <li><a href="add_supplier.php">➕ Add Supplier</a></li>
            </ul>
        </li>

        <li class="submenu">
            <a href="#"><i class="fas fa-shopping-cart" style="color: #e74c3c;"></i> Orders ▼</a>
            <ul class="submenu-content">
                <li><a href="manage_orders.php">🛒 Manage Orders</a></li>
                <li><a href="view_orders.php">📜 View Orders</a></li>
                <li><a href="admin_orders.php">📝  Order</a></li>
            </ul>
        </li>

        <li class="submenu">
            <a href="#"><i class="fas fa-database" style="color: #9b59b6;"></i> Stock ▼</a>
            <ul class="submenu-content">
                <li><a href="stock.php">📊 Manage Stock</a></li>
                <li><a href="inventory_report.php">📄 Inventory Report</a></li>
            </ul>
        </li>

        <li class="submenu">
            <a href="#"><i class="fas fa-file-alt" style="color: #1abc9c;"></i> Reports ▼</a>
            <ul class="submenu-content">
                <li><a href="reports.php">📑 View Reports</a></li>
                <li><a href="sales_report.php">📈 Sales Report</a></li>
            </ul>
        </li>
        <li class="submenu">
    <a href="#"><i class="fas fa-file-invoice-dollar" style="color: #d35400;"></i> Purchase Orders ▼</a>
    <ul class="submenu-content">
        <li><a href="purchase_orders.php">📜 Manage Purchase Orders</a></li>
    </ul>
</li>
<li>
    <a href="fetch_sales_prediction.php">
        <i class="fas fa-chart-line" style="color: #e67e22;"></i> Sales Prediction
    </a>
</li>
<li><a href="analytics.php"><i class="fas fa-chart-line" style="color: #e74c3c;"></i> Advanced Analytics</a></li>

        <li><a href="scan_barcode.php"><i class="fas fa-barcode" style="color: #e67e22;"></i> Barcode Scan</a></li>
        <li><a href="user.php"><i class="fas fa-user" style="color: #16a085;"></i> Manage Users</a></li>
        <li><a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt" style="color: #c0392b;"></i> Logout</a></li>
    </ul>
</div>

<!-- FontAwesome Icons (Include in <head>) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


  <!-- Main Content -->
  <div class="main-content">
      <div class="container mt-4">
          <div class="card shadow-lg">
              <div class="card-header text-center bg-primary text-white">
                  <h3>Admin Dashboard</h3>
                  <p>Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</p>
              </div>
              <div class="card-body">
                  <!-- Dashboard Info Section -->
                  <div class="row mb-4">
                      <!-- Total Revenue Card -->
                      <div class="col-md-4">
                          <div class="card shadow-sm p-3">
                              <h5 class="text-secondary">Total Revenue</h5>
                              <p class="text-primary">₹<?php echo number_format($total_revenue, 2); ?></p>
                          </div>
                      </div>
                      <!-- Daily Sales Trend Chart -->
                      <div class="col-md-4">
                          <div class="card shadow-sm p-3">
                              <h5 class="text-secondary">Daily Sales Trend</h5>
                              <canvas id="salesChart"></canvas>
                          </div>
                      </div>
                      <!-- Monthly Sales Trend Chart -->
                      <div class="col-md-4">
                          <div class="card shadow-sm p-3">
                              <h5 class="text-secondary">Monthly Sales Trend</h5>
                              <canvas id="monthlySalesChart"></canvas>
                          </div>
                      </div>
                  </div>

                  <!-- Additional Charts Section -->
                  <div class="row mb-4">
                      <!-- Stock Levels Chart -->
                      <div class="col-md-6">
                          <div class="card shadow-sm p-3">
                              <h5 class="text-secondary">Stock Levels</h5>
                              <canvas id="stockChart"></canvas>
                          </div>
                      </div>
                      <!-- Top Selling Products Pie Chart -->
                      <div class="col-md-6">
                          <div class="card shadow-sm p-3">
                              <h5 class="text-secondary">Top Selling Products</h5>
                              <canvas id="topProductsPieChart"></canvas>
                          </div>
                      </div>
                  </div>

                  <!-- Additional Row for New Charts -->
                  <div class="row mb-4">
                      <!-- Orders Count by Day of Week Chart -->
                      <div class="col-md-6">
                          <div class="card shadow-sm p-3">
                              <h5 class="text-secondary">Orders by Day of Week</h5>
                              <canvas id="dowChart"></canvas>
                          </div>
                      </div>
                      <!-- Average Order Value Trend Chart -->
                      <div class="col-md-6">
                          <div class="card shadow-sm p-3">
                              <h5 class="text-secondary">Average Order Value Trend</h5>
                              <canvas id="avgOrderValueChart"></canvas>
                          </div>
                      </div>
                  </div>

                  <!-- Low Stock Alerts Card -->
                  <div class="row mb-4">
                      <div class="col-md-6">
                          <div class="card shadow-sm p-3">
                              <h5 class="text-secondary">Low Stock Alerts</h5>
                              <?php if(count($low_stock) > 0): ?>
                                  <ul class="list-group">
                                      <?php foreach($low_stock as $item): ?>
                                          <li class="list-group-item low-stock">
                                              <?php echo htmlspecialchars($item['name']); ?> - Qty: <?php echo $item['quantity']; ?>
                                          </li>
                                      <?php endforeach; ?>
                                  </ul>
                              <?php else: ?>
                                  <p class="text-success">All products are sufficiently stocked.</p>
                              <?php endif; ?>
                          </div>
                      </div>
                  </div>

              </div>
          </div>
      </div>
  </div>

  <script>
      // Daily Sales Trend Chart
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

      // Monthly Sales Trend Chart
      var ctx2 = document.getElementById('monthlySalesChart').getContext('2d');
      var monthlySalesChart = new Chart(ctx2, {
          type: 'bar',
          data: {
              labels: <?php echo json_encode($months); ?>,
              datasets: [{
                  label: 'Monthly Revenue (₹)',
                  data: <?php echo json_encode($monthly_revenues); ?>,
                  backgroundColor: 'orange'
              }]
          }
      });

      // Stock Levels Chart
      var ctx3 = document.getElementById('stockChart').getContext('2d');
      var stockChart = new Chart(ctx3, {
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

      // Top Selling Products Pie Chart
      var ctx4 = document.getElementById('topProductsPieChart').getContext('2d');
      var topProductsPieChart = new Chart(ctx4, {
          type: 'pie',
          data: {
              labels: <?php echo json_encode($top_product_names); ?>,
              datasets: [{
                  data: <?php echo json_encode($top_product_sales); ?>,
                  backgroundColor: ['purple', 'blue', 'green', 'orange', 'red']
              }]
          }
      });

      // Orders Count by Day of Week Chart
      var ctx5 = document.getElementById('dowChart').getContext('2d');
      var dowChart = new Chart(ctx5, {
          type: 'bar',
          data: {
              labels: <?php echo json_encode($days_of_week); ?>,
              datasets: [{
                  label: 'Orders',
                  data: <?php echo json_encode($dow_orders); ?>,
                  backgroundColor: 'teal'
              }]
          },
          options: {
              scales: {
                  y: {
                      beginAtZero: true
                  }
              }
          }
      });

      // Average Order Value Trend Chart
      var ctx6 = document.getElementById('avgOrderValueChart').getContext('2d');
      var avgOrderValueChart = new Chart(ctx6, {
          type: 'line',
          data: {
              labels: <?php echo json_encode($avg_order_days); ?>,
              datasets: [{
                  label: 'Avg Order Value (₹)',
                  data: <?php echo json_encode($avg_order_values); ?>,
                  borderColor: 'magenta',
                  fill: false
              }]
          },
          options: {
              scales: {
                  y: {
                      beginAtZero: true
                  }
              }
          }
      });
  </script>
</body>
</html>