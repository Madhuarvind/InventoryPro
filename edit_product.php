<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$id = $_GET['id'];

// Securely fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    echo "<div class='alert alert-danger text-center'>Product not found!</div>";
    exit();
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $quantity = trim($_POST['quantity']);
    $supplier = trim($_POST['supplier']);

    // Prepared statement to update product
    $update_stmt = $conn->prepare("UPDATE products SET name=?, price=?, quantity=?, supplier=? WHERE id=?");
    $update_stmt->bind_param("sdisi", $name, $price, $quantity, $supplier, $id);

    if ($update_stmt->execute()) {
        $success = "Product updated successfully!";
    } else {
        $error = "Error: " . $update_stmt->error;
    }
    $update_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 500px;
            margin-top: 50px;
        }
        .card {
            padding: 20px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card shadow">
            <h2 class="text-center text-primary">✏️ Edit Product</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price (₹)</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $product['price']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" class="form-control" value="<?php echo $product['quantity']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Supplier</label>
                    <input type="text" name="supplier" class="form-control" value="<?php echo htmlspecialchars($product['supplier']); ?>" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">💾 Update Product</button>
                <a href="products.php" class="btn btn-secondary w-100 mt-2">🔙 Back to Products</a>
            </form>
        </div>
    </div>
</body>
</html>
