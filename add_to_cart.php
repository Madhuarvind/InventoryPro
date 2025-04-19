<?php
session_start();
include('db.php');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("❌ Invalid Request Method");
}

// Validate product ID
if (!isset($_POST['product_id']) || !is_numeric($_POST['product_id'])) {
    die("❌ Invalid Product ID");
}

$product_id = (int) $_POST['product_id'];

// Fetch product details securely
$stmt = $conn->prepare("SELECT id, name, price FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("❌ Product not found.");
}

$product = $result->fetch_assoc();

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// If product exists in the cart, increase quantity; otherwise, add new
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]['quantity'] += 1;
} else {
    $_SESSION['cart'][$product_id] = [
        'name' => $product['name'],
        'price' => $product['price'],
        'quantity' => 1
    ];
}

// Redirect after adding
header("Location: view_cart.php");
exit();
?>
