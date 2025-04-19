<?php
session_start();
include 'db.php';

// Ensure only authorized users can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Update stock
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_stock'])) {
    $product_id = $_POST['product_id'];
    $new_quantity = $_POST['new_quantity'];

    if ($new_quantity < 0) {
        $error_message = "Quantity cannot be negative!";
    } else {
        $stmt = $conn->prepare("UPDATE products SET quantity = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_quantity, $product_id);
        
        if ($stmt->execute()) {
            $success_message = "Stock updated successfully!";
        } else {
            $error_message = "Error updating stock: " . $conn->error;
        }
        $stmt->close();
    }
}

// Fetch all products
$result = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Stock Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="card shadow-lg p-4">
            <h2 class="text-center text-primary">📦 Stock Management</h2>

            <!-- Display Messages -->
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success text-center"><?= $success_message; ?></div>
            <?php elseif (!empty($error_message)): ?>
                <div class="alert alert-danger text-center"><?= $error_message; ?></div>
            <?php endif; ?>

            <!-- Update Stock Form -->
            <div class="card p-3 mt-3">
                <h4 class="text-secondary">🔄 Update Stock</h4>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Select Product:</label>
                        <select name="product_id" class="form-control" required>
                            <?php while ($row = $result->fetch_assoc()) { ?>
                                <option value="<?= $row['id'] ?>"><?= $row['name'] ?> (Stock: <?= $row['quantity'] ?>)</option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Quantity:</label>
                        <input type="number" name="new_quantity" class="form-control" required>
                    </div>
                    <button type="submit" name="update_stock" class="btn btn-primary">Update Stock</button>
                </form>
            </div>

            <!-- Current Stock Table -->
            <div class="card p-3 mt-4">
                <h4 class="text-secondary">📊 Current Stock</h4>
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Product ID</th>
                            <th>Name</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("SELECT * FROM products");
                        while ($row = $result->fetch_assoc()) {
                        ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['quantity'] ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Back to Dashboard Button -->
            <div class="text-center mt-3">
                <a href="admin_dashboard.php" class="btn btn-secondary">⬅️ Back to Dashboard</a>
            </div>

        </div>
    </div>

</body>
</html>
