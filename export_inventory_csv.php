<?php
include 'db.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=inventory_report.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Product Name', 'Price (₹)', 'Stock', 'Status']);

$sql = "SELECT * FROM products ORDER BY name ASC";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id'], 
        $row['name'], 
        $row['price'], 
        $row['quantity'], 
        ($row['quantity'] < 5) ? 'Low Stock' : 'Sufficient Stock'
    ]);
}

fclose($output);
?>
