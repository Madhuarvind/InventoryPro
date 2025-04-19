<?php
require_once 'db.php';

// Check if user is logged in and has admin role
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Charts - Inventory System</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <h1>Dashboard Analytics</h1>
        
        <div class="charts-grid">
            <div class="chart-container">
                <h2>Sales Trend (Last 30 Days)</h2>
                <canvas id="salesChart"></canvas>
            </div>
            
            <div class="chart-container">
                <h2>Inventory Levels by Category</h2>
                <canvas id="inventoryChart"></canvas>
            </div>
            
            <div class="chart-container">
                <h2>Order Status Distribution</h2>
                <canvas id="orderChart"></canvas>
            </div>
        </div>
    </div>

    <style>
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin: 20px 0;
    }
    .chart-container {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    </style>

    <script>
    async function loadChartData() {
        try {
            const response = await fetch('api/chart_data.php');
            const data = await response.json();
            
            // Sales Trend Chart
            new Chart(document.getElementById('salesChart'), {
                type: 'line',
                data: {
                    labels: data.sales_trend.map(item => item.date),
                    datasets: [{
                        label: 'Daily Sales',
                        data: data.sales_trend.map(item => item.total_sales),
                        borderColor: '#2563eb',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Sales Amount'
                            }
                        }
                    }
                }
            });

            // Inventory Levels Chart
            new Chart(document.getElementById('inventoryChart'), {
                type: 'bar',
                data: {
                    labels: data.inventory_levels.map(item => item.category),
                    datasets: [{
                        label: 'Stock Quantity',
                        data: data.inventory_levels.map(item => item.quantity),
                        backgroundColor: '#10b981'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Quantity'
                            }
                        }
                    }
                }
            });

            // Order Status Chart
            new Chart(document.getElementById('orderChart'), {
                type: 'pie',
                data: {
                    labels: data.order_distribution.map(item => item.status),
                    datasets: [{
                        data: data.order_distribution.map(item => item.count),
                        backgroundColor: [
                            '#3b82f6',
                            '#10b981',
                            '#ef4444',
                            '#f59e0b'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

        } catch (error) {
            console.error('Error loading chart data:', error);
        }
    }

    // Load charts when page loads
    document.addEventListener('DOMContentLoaded', loadChartData);
    </script>
</body>
</html>