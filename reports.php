<?php
session_start();
require 'db.php'; // Ensure this file contains a valid database connection

if (!isset($_SESSION['user_id'])) {
    die("Error: You must be logged in to view reports. <a href='login.php'>Login</a>");
}

// Fetch order reports with correct column names
$sql = "SELECT o.id AS order_id, u.username AS customer_name, 
               COALESCE(o.total_price, 0) AS amount, 
               COALESCE(o.status, 'Pending') AS status, 
               COALESCE(o.order_date, 'N/A') AS order_date
        FROM orders o
        JOIN user u ON o.user_id = u.id"; 

$result = $conn->query($sql);

if (!$result) {
    die("Error fetching reports: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            text-align: center;
            padding: 20px;
        }
        .container {
            background: #fff;
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h2 { color: #333; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background: #28a745;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Order Reports</h2>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Amount (₹)</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['order_id']; ?></td>
            <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
            <td>₹<?php echo number_format($row['amount'], 2); ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
            <td><?php echo $row['order_date']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
