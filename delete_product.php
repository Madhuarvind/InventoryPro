<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Check if the product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$id = $_GET['id'];

// Secure deletion with prepared statements
$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: products.php?msg=deleted");
} else {
    echo "<div class='alert alert-danger text-center'>Error deleting product: " . $conn->error . "</div>";
}

$stmt->close();
$conn->close();
?>
