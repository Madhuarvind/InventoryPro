<?php
require_once 'db.php';

// Initialize response array
$stats = array(
    'productCount' => 0,
    'orderCount' => 0,
    'userCount' => 0
);

try {
    // Get total number of products
    $query = "SELECT COUNT(*) as count FROM products";
    $result = $conn->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['productCount'] = intval($row['count']);
    }

    // Get total number of orders
    $query = "SELECT COUNT(*) as count FROM orders";
    $result = $conn->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['orderCount'] = intval($row['count']);
    }

    // Get total number of active users
    $query = "SELECT COUNT(*) as count FROM users WHERE status = 'active'";
    $result = $conn->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['userCount'] = intval($row['count']);
    }

    // Set response headers
    header('Content-Type: application/json');
    echo json_encode($stats);

} catch (Exception $e) {
    // Set error response
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch statistics']);
} finally {
    // Close database connection
    $conn->close();
}