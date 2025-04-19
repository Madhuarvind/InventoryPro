<?php
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


if (!isset($_GET['id'])) {
    die("Invalid Product ID");
}

$product_id = $_GET['id'];

if (isset($_SESSION['cart'][$product_id])) {
    unset($_SESSION['cart'][$product_id]);  // Remove the product
}

header("Location: view_cart.php");
exit();
?>