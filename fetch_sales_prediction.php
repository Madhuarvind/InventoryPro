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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(120deg, #f9f9f9 0%, #e0e7ff 100%);
            font-family: 'Poppins', sans-serif;
            color: #333;
        }
        .container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container-box {
            background: linear-gradient(120deg, #f8fafc 0%, #e0e7ff 100%);
            padding: 56px 44px 40px 44px;
            border-radius: 32px;
            box-shadow: 0 10px 36px rgba(80, 112, 255, 0.14), 0 2px 10px rgba(0,0,0,0.07);
            max-width: 900px;
            margin: 0 auto;
        }
        h2 {
            font-weight: 800;
            color: #2b2b4c;
            letter-spacing: 0.5px;
            background: linear-gradient(90deg, #0056b3 0%, #00c6ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 2.2rem;
        }
        .table thead {
            background: #fff !important;
            color: #007bff;
            font-weight: 700;
            border-bottom: 2px solid #e0e7ff;
        }
        .table tbody tr {
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .table tbody tr:hover {
            background: #f0f7ff;
            box-shadow: 0 2px 12px rgba(0,123,255,0.10);
            transform: scale(1.01);
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
            padding: 16px 10px;
            font-size: 1.13rem;
            border: none;
        }
        .chart-container {
            height: 420px;
            min-width: 320px;
            max-width: 100%;
            background: #f9fbff;
            border-radius: 20px;
            padding: 32px 18px 18px 18px;
            box-shadow: 0 4px 18px rgba(0,123,255,0.08);
            margin-top: 24px;
        }
        .btn-custom {
            background: linear-gradient(90deg, #0056b3 0%, #00c6ff 100%);
            color: white;
            padding: 13px 32px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            border: none;
            font-size: 1.13rem;
            box-shadow: 0 2px 10px rgba(0,123,255,0.10);
            transition: all 0.3s cubic-bezier(.4,2,.6,1);
            letter-spacing: 0.5px;
        }
        .btn-custom:hover {
            background: linear-gradient(90deg, #003366 0%, #0096c7 100%);
            transform: translateY(-2px) scale(1.04);
            box-shadow: 0 8px 24px rgba(0,123,255,0.15);
        }
        .badge.bg-success {
            background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%) !important;
            color: #222 !important;
            font-weight: 700;
            font-size: 1.13rem;
            border-radius: 10px;
            padding: 10px 22px;
            box-shadow: 0 2px 10px rgba(67,233,123,0.10);
        }
        @media (max-width: 600px) {
            .container, body {
                min-height: unset;
                display: block;
            }
            .container-box {
                padding: 10px 2px;
                margin-top: 18px;
                border-radius: 16px;
                max-width: 98vw;
            }
            .chart-container {
                padding: 6px 1px;
                height: 180px;
                min-width: unset;
                max-width: 100vw;
            }
            .btn-custom {
                padding: 10px 12px;
                font-size: 1rem;
            }
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

