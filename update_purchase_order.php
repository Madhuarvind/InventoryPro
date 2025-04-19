<?php
session_start();
include 'db.php';

// Ensure only logged-in admin users can access this page.
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Validate Purchase Order ID passed via GET.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Purchase Order ID. Please provide a valid 'id' parameter in the URL.");
}

$order_id = intval($_GET['id']);

// Fetch the current purchase order details using a prepared statement.
$sql = "SELECT po.id, po.quantity, po.status, s.name AS supplier, p.name AS product, p.id AS product_id 
        FROM purchase_orders po 
        JOIN suppliers s ON po.supplier_id = s.id 
        JOIN products p ON po.product_id = p.id 
        WHERE po.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Purchase Order not found.");
}

$order = $result->fetch_assoc();
$stmt->close();

$error = $success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_status = $_POST['status'];
    
    // Validate the new status.
    $allowed_statuses = ['Pending', 'Completed', 'Cancelled'];
    if (!in_array($new_status, $allowed_statuses)) {
        $error = "Invalid status selected.";
    } else {
        // If the new status is "Completed" and the order was not previously completed,
        // update the product stock by adding the order quantity.
        if ($new_status === "Completed" && $order['status'] !== "Completed") {
            $update_stock_sql = "UPDATE products SET quantity = quantity + ? WHERE id = ?";
            $stmt_stock = $conn->prepare($update_stock_sql);
            $stmt_stock->bind_param("ii", $order['quantity'], $order['product_id']);
            $stmt_stock->execute();
            $stmt_stock->close();
        }
        
        // Update the purchase order status.
        $update_sql = "UPDATE purchase_orders SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("si", $new_status, $order_id);
        
        if ($stmt->execute()) {
            $success = "Purchase order status updated successfully!";
            // Redirect back after a short delay.
            header("Refresh: 2; URL=purchase_orders.php");
        } else {
            $error = "Error updating status: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Purchase Order</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            padding: 20px;
            border-radius: 10px;
        }
    </style>
</head>
<body class="bg-light">
<div class="container">
    <div class="card shadow-lg">
        <h2 class="text-center text-primary">Update Purchase Order</h2>
        <p class="text-center">
            <strong>Supplier:</strong> <?php echo htmlspecialchars($order['supplier']); ?> &nbsp; | &nbsp;
            <strong>Product:</strong> <?php echo htmlspecialchars($order['product']); ?> &nbsp; | &nbsp;
            <strong>Quantity:</strong> <?php echo $order['quantity']; ?>
        </p>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success text-center"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="status" class="form-label">Update Order Status:</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="Pending" <?php if ($order['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                    <option value="Completed" <?php if ($order['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                    <option value="Cancelled" <?php if ($order['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Update Status</button>
        </form>

        <div class="text-center mt-3">
            <a href="purchase_orders.php" class="btn btn-secondary">⬅️ Back to Purchase Orders</a>
        </div>
    </div>
</div>
</body>
</html>
