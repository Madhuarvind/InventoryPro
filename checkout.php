<?php
session_start();
include('db.php');

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    die("Your cart is empty. <a href='browse_products.php'>Go back</a>");
}

$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 16px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        td {
            background-color: #f9f9f9;
        }

        td, th {
            padding: 12px;
            border: 1px solid #ddd;
        }

        .total-price {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-top: 20px;
            text-align: right;
        }

        .button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin-top: 20px;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
        }

        .button:hover {
            background-color: #45a049;
        }

        .back-link {
            display: block;
            margin-top: 15px;
            text-align: center;
        }

        .back-link a {
            color: #4CAF50;
            text-decoration: none;
            font-size: 16px;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Checkout</h2>

        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['cart'] as $id => $item): 
                    $total = $item['price'] * $item['quantity'];
                    $total_price += $total;
                ?>
                    <tr>
                        <td><?php echo $item['name']; ?></td>
                        <td>$<?php echo $item['price']; ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>$<?php echo $total; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-price">
            Total Price: $<?php echo $total_price; ?>
        </div>

        <form action="place_order.php" method="post">
            <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">
            <button type="submit" class="button">Confirm Order</button>
        </form>

        <div class="back-link">
            <a href="view_cart.php">Back to Cart</a>
        </div>
    </div>

</body>
</html>