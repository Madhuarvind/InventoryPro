<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch suppliers & products
$suppliers = $conn->query("SELECT * FROM suppliers");
$products = $conn->query("SELECT * FROM products");

// Handle new purchase order
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $supplier_id = $_POST['supplier_id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $sql = "INSERT INTO purchase_orders (supplier_id, product_id, quantity) 
            VALUES ('$supplier_id', '$product_id', '$quantity')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Purchase Order Created!'); window.location.href='purchase_orders.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Fetch purchase orders
$orders = $conn->query("SELECT po.id, s.name AS supplier, p.name AS product, po.quantity, po.status 
                        FROM purchase_orders po
                        JOIN suppliers s ON po.supplier_id = s.id
                        JOIN products p ON po.product_id = p.id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }
        /* Form styling */
        .g-3 > .col-md-4, .g-3 > .col-md-2 {
            margin-bottom: 10px;
        }
        /* Table styling */
        .table thead {
            background-color: #343a40;
            color: #fff;
        }
    </style>
</head>
<body class="bg-light">
<div class="container">
    <div class="card shadow-lg">
        <h2 class="text-center">📝 Purchase Orders</h2>

        <!-- Form to create purchase order -->
        <form method="POST" class="row g-3 mb-4">
            <div class="col-md-4">
                <select name="supplier_id" class="form-select" required>
                    <option value="">Select Supplier</option>
                    <?php while ($s = $suppliers->fetch_assoc()) { ?>
                        <option value="<?= $s['id']; ?>"><?= htmlspecialchars($s['name']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-4">
                <select name="product_id" class="form-select" required>
                    <option value="">Select Product</option>
                    <?php while ($p = $products->fetch_assoc()) { ?>
                        <option value="<?= $p['id']; ?>"><?= htmlspecialchars($p['name']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="quantity" class="form-control" placeholder="Quantity" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-success w-100">Create Order</button>
            </div>
        </form>

        <!-- Search Bar -->
        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="🔍 Search Purchase Orders...">
        </div>

        <!-- Purchase Orders Table -->
        <table class="table table-bordered table-striped text-center" id="ordersTable">
    <thead class="table-dark">
        <tr>
            <th>Supplier</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Status</th>
            <th>Action</th> <!-- Add this column -->
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $orders->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['supplier']); ?></td>
            <td><?php echo htmlspecialchars($row['product']); ?></td>
            <td><?php echo $row['quantity']; ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
            <td>
                <!-- Update Button with Purchase Order ID -->
                <a href="update_purchase_order.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Update</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

                <?php while ($row = $orders->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['supplier']); ?></td>
                    <td><?= htmlspecialchars($row['product']); ?></td>
                    <td><?= $row['quantity']; ?></td>
                    <td><?= htmlspecialchars($row['status']); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Back to Dashboard Button -->
        <div class="text-center mt-3">
            <a href="admin_dashboard.php" class="btn btn-secondary">⬅️ Back to Dashboard</a>
        </div>
    </div>
</div>

<!-- Search Functionality -->
<script>
    $(document).ready(function(){
        $("#searchInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#ordersTable tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
</script>
</body>
</html>
