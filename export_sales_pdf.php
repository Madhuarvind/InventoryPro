<?php
session_start();
require __DIR__ . '/vendor/autoload.php'; // Ensure correct path
include 'db.php';

use Dompdf\Dompdf;

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch Sales Data
$sales_sql = "SELECT p.name, SUM(o.quantity) AS total_sold, SUM(o.total_price) AS total_revenue 
              FROM orders o 
              JOIN products p ON o.product_id = p.id 
              WHERE o.status = 'Completed' 
              GROUP BY p.id";
$sales_result = $conn->query($sales_sql);

// Generate HTML for PDF
$html = '<h2 style="text-align: center;">📊 Sales Report</h2>';
$html .= '<table border="1" cellspacing="0" cellpadding="5" style="width: 100%;">';
$html .= '<thead><tr><th>Product Name</th><th>Total Sold</th><th>Total Revenue (₹)</th></tr></thead>';
$html .= '<tbody>';

while ($row = $sales_result->fetch_assoc()) {
    $html .= "<tr>
                <td>{$row['name']}</td>
                <td>{$row['total_sold']}</td>
                <td>₹" . number_format($row['total_revenue'], 2) . "</td>
              </tr>";
}

$html .= '</tbody></table>';

// Initialize Dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output PDF
$dompdf->stream('sales_report.pdf', ['Attachment' => true]);
?>
