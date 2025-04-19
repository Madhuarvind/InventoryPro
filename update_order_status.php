<?php
session_start();
include('db.php');

// Check if the user has admin privileges
if ($_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Check if order_id and status are set in the POST request
if (isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    // Fetch the order details from the database using the order_id
    $query = "SELECT * FROM orders WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // The order exists, now update its status
        $query_update = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt_update = $conn->prepare($query_update);
        $stmt_update->bind_param("si", $status, $order_id);

        if ($stmt_update->execute()) {
            echo "Order status updated successfully!";
            // Redirect back to the order management page after success
            header('Location: manage_orders.php');
            exit();
        } else {
            echo "Error updating order status.";
        }
    } else {
        echo "❌ Error: Order not found.";
    }
} else {
    echo "❌ Error: Order ID or Status not specified.";
}
?>
