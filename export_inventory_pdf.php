<?php
require 'vendor/autoload.php';
include 'db.php';

use Dompdf\Dompdf;

// Fetch inventory data
$sql = "SELECT * FROM products ORDER BY name ASC";
$result = $conn->query($sql);

$html = '<h2 style="text-align:center;">📦 Inventory Report</h2>';
$html .= '<table border="1" cellspacing="0" cellpadding="5" style="width:100%; text-align:left;">';
$html .= '<tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>Price (₹)</th>
            <th>Stock</th>
            <th>Status</th>
          </tr>';

while ($row = $result->fetch_assoc()) {
    $status = ($row['quantity'] < 5) ? '⚠️ Low Stock' : '✔️ Sufficient';
    $html .= "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>₹{$row['price']}</td>
                <td>{$row['quantity']}</td>
                <td>{$status}</td>
              </tr>";
}

$html .= '</table>';

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("inventory_report.pdf", ["Attachment" => true]);
?>
