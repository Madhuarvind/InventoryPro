<?php
session_start(); // ✅ Keep only one session_start() at the top

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 900px;
            margin-top: 50px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #343a40;
        }
        table {
            margin-top: 20px;
        }
        .btn-remove {
            color: white;
            background-color: #dc3545;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-remove:hover {
            background-color: #c82333;
        }
        .total-price {
            text-align: right;
            font-size: 1.2rem;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🛒 Your Shopping Cart</h2>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="alert alert-danger text-center mt-4">
            ❌ Your cart is empty.
        </div>
    <?php else: ?>
        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            $total_price = 0;
            foreach ($_SESSION['cart'] as $id => $item): 
                $total = $item['price'] * $item['quantity'];
                $total_price += $total;
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>$<?php echo number_format($total, 2); ?></td>
                    <td>
                        <a href="remove_from_cart.php?id=<?php echo $id; ?>" class="btn-remove">❌ Remove</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-price">
            Total Price: $<?php echo number_format($total_price, 2); ?>
        </div>

        <div class="text-center mt-4">
            <a href="checkout.php" class="btn btn-success btn-lg">🛍️ Proceed to Checkout</a>
            <a href="browse_products.php" class="btn btn-primary btn-lg">🔍 Continue Shopping</a>
        </div>
    <?php endif; ?>
</div>

<!-- Bootstrap JS (Optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
