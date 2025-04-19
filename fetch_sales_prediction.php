<?php
// Call Flask API
$api_url = "http://localhost:5000/predict";
$response = file_get_contents($api_url);
$data = json_decode($response, true);

if (!$data || isset($data["error"])) {
    echo "<h2 class='text-center text-danger'>Error fetching sales predictions</h2>";
    echo "<p class='text-center'>" . ($data["error"] ?? "Unknown error") . "</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Predictions</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .container-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        h2 {
            color: #333;
        }
        .table thead {
            background: #ffc107 !important;
            color: white;
        }
        .table tbody tr:hover {
            background: #ffe082;
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
            padding: 12px;
        }
        .chart-container {
            height: 350px;
        }
        .btn-custom {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-custom:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="container-box">
        <h2 class="text-center mb-4">📊 Sales Predictions for Next 7 Days</h2>

        <!-- Sales Prediction Table -->
        <div class="table-responsive">
            <table class="table table-bordered shadow-sm">
                <thead>
                    <tr>
                        <th>📅 Day</th>
                        <th>💰 Predicted Sales (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $day => $sales) { ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($day); ?></strong></td>
                            <td><span class="badge bg-success fs-6">₹<?php echo number_format($sales, 2); ?></span></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Sales Prediction Chart -->
        <div class="chart-container mt-4">
            <canvas id="salesChart"></canvas>
        </div>

        <!-- Back to Dashboard Button -->
        <div class="text-center mt-4">
            <a href="admin_dashboard.php" class="btn-custom">⬅ Back to Dashboard</a>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script>
    var ctx = document.getElementById('salesChart').getContext('2d');
    var salesChart = new Chart(ctx, {
        type: 'line', 
        data: {
            labels: <?php echo json_encode(array_keys($data)); ?>,
            datasets: [{
                label: 'Predicted Sales (₹)',
                data: <?php echo json_encode(array_values($data), JSON_NUMERIC_CHECK); ?>,
                backgroundColor: 'rgba(255, 193, 7, 0.2)',
                borderColor: '#ffc107',
                borderWidth: 3,
                pointBackgroundColor: '#e67e22',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true, position: 'top' }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    title: { display: true, text: 'Sales Amount (₹)' }
                },
                x: { title: { display: true, text: 'Days' } }
            }
        }
    });
</script>

</body>
</html>
