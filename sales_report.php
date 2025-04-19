<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch Sales Data
$sales_sql = "SELECT p.name, SUM(oi.quantity) AS total_sold, SUM(oi.quantity * oi.price) AS total_revenue 
              FROM order_items oi 
              JOIN products p ON oi.product_id = p.id 
              JOIN orders o ON oi.order_id = o.id 
              WHERE o.status = 'Completed' 
              GROUP BY p.id";
$sales_result = $conn->query($sales_sql);

// Fetch Total Revenue
$total_revenue_sql = "SELECT SUM(oi.quantity * oi.price) AS revenue 
                      FROM order_items oi 
                      JOIN orders o ON oi.order_id = o.id 
                      WHERE o.status = 'Completed'";
$total_revenue_result = $conn->query($total_revenue_sql);
$total_revenue = $total_revenue_result->fetch_assoc()['revenue'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">📊 Sales Report</h2>

        <!-- Total Revenue Summary -->
        <div class="mb-3 text-center">
            <h4 class="text-success">💰 Total Revenue: ₹<?php echo number_format($total_revenue, 2); ?></h4>
        </div>
        <div class="text-center mt-3">
    <a href="export_sales_csv.php" class="btn btn-success">📥 Export as CSV</a>
    <a href="export_sales_pdf.php" class="btn btn-danger">📄 Export as PDF</a>
</div>

        <!-- Search Bar -->
        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="🔍 Search for a product...">
        </div>

        <table class="table table-bordered table-striped text-center" id="salesTable">
            <thead class="table-dark">
                <tr>
                    <th>Product Name</th>
                    <th>Total Sold</th>
                    <th>Total Revenue (₹)</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($sales_result->num_rows > 0) { ?>
                    <?php while ($row = $sales_result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo $row['total_sold']; ?></td>
                            <td>₹<?php echo number_format($row['total_revenue'], 2); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="3" class="text-center text-danger">No sales records found.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="text-center mt-3">
            <a href="admin_dashboard.php" class="btn btn-secondary">⬅️ Back to Dashboard</a>
        </div>
    </div>
</div>

<script>
    // Search Functionality
    $(document).ready(function(){
        $("#searchInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#salesTable tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
</script>

</body>
</html>
