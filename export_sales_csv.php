<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=sales_report.csv');

// Open file pointer
$output = fopen('php://output', 'w');

// Write column headers
fputcsv($output, ['Product Name', 'Total Sold', 'Total Revenue (₹)']);

// Fetch Sales Data
$sales_sql = "SELECT p.name, SUM(o.quantity) AS total_sold, SUM(o.total_price) AS total_revenue 
              FROM orders o 
              JOIN products p ON o.product_id = p.id 
              WHERE o.status = 'Completed' 
              GROUP BY p.id";
$sales_result = $conn->query($sales_sql);

// Write rows to CSV
while ($row = $sales_result->fetch_assoc()) {
    fputcsv($output, [$row['name'], $row['total_sold'], number_format($row['total_revenue'], 2)]);
}

// Close output stream
fclose($output);
exit();
?>
