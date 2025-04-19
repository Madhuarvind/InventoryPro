<?php
session_start();
include('db.php');

if (!isset($_GET['id'])) {
    die("Invalid Product ID");
}

$product_id = $_GET['id'];
$query = "SELECT * FROM products WHERE id = $product_id";
$result = $conn->query($query);

if ($result->num_rows == 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $product['name']; ?> - Product Details</title>
</head>
<body>
    <h2><?php echo $product['name']; ?></h2>
    <p><strong>Price:</strong> $<?php echo $product['price']; ?></p>
    <p><strong>Category:</strong> <?php echo $product['category']; ?></p>
    <p><strong>Supplier:</strong> <?php echo $product['supplier']; ?></p>
    <p><strong>Description:</strong> <?php echo isset($product['description']) ? $product['description'] : "No description available."; ?></p>

    <a href="add_to_cart.php?id=<?php echo $product['id']; ?>">Add to Cart</a>
    <br>
    <a href="browse_products.php">Back to Products</a>
</body>
</html>

