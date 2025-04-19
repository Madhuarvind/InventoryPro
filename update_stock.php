<?php
session_start();
include 'db.php';
include 'send_email.php'; // Include email function

// Ensure only admin users can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Validate product ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid product ID.");
}

$id = $_GET['id'];
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("Product not found.");
}

$success_message = $error_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_quantity = intval($_POST['quantity']);

    if ($new_quantity < 0) {
        $error_message = "❌ Quantity cannot be negative!";
    } else {
        $update_sql = "UPDATE products SET quantity = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ii", $new_quantity, $id);
        
        if ($stmt->execute()) {
            // Send Email Notification on Stock Update
            $to = "madhuaravind21@gmail.com"; // Change to actual admin email
            $subject = "✅ Stock Updated: " . $product['name'];
            $message = "The stock for <b>" . $product['name'] . "</b> has been updated to <b>$new_quantity</b> units.";

            sendEmail($to, $subject, $message);

            // Send Low Stock Alert if Below Threshold (10)
            if ($new_quantity < 10) {
                $low_stock_subject = "🚨 Low Stock Alert - " . $product['name'];
                $low_stock_message = "⚠️ Warning: The stock level for <b>" . $product['name'] . "</b> is critically low (<b>$new_quantity</b> remaining). Please restock soon!";
                sendEmail($to, $low_stock_subject, $low_stock_message);
            }

            $success_message = "✅ Stock updated successfully!";
        } else {
            $error_message = "❌ Error updating stock: " . $conn->error;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Update Stock</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow-lg p-4">
            <h2 class="text-center text-primary">📦 Update Stock</h2>

            <!-- Display Messages -->
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success text-center"><?= $success_message; ?></div>
            <?php elseif (!empty($error_message)): ?>
                <div class="alert alert-danger text-center"><?= $error_message; ?></div>
            <?php endif; ?>

            <!-- Update Stock Form -->
            <div class="card p-3 mt-3">
                <h4 class="text-secondary">🔄 Update Stock for <?= htmlspecialchars($product['name']); ?></h4>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">New Quantity:</label>
                        <input type="number" name="quantity" value="<?= htmlspecialchars($product['quantity']); ?>" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Stock</button>
                </form>
            </div>

            <!-- Back to Products Button -->
            <div class="text-center mt-3">
                <a href="products.php" class="btn btn-secondary">⬅️ Back to Products</a>
            </div>
        </div>
    </div>
</body>
</html>
