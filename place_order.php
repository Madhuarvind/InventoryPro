<?php
session_start();
include('db.php');

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['total_price'])) {
    // Get total price from the form
    $total_price = $_POST['total_price'];

    // Get user ID from the session (assuming the user is logged in)
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];  // Make sure this is set when the user logs in
    } else {
        die("You must be logged in to place an order.");
    }

    // Get the customer's name (optional, you can add this based on your DB structure)
    $stmt = $conn->prepare("SELECT username FROM user WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $customer_name = $user['username'];

    // Check if the cart exists and is not empty
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        // Insert the order into the database
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'Pending')");
        $stmt->bind_param("id", $user_id, $total_price);
        

        if ($stmt->execute()) {
            // Get the ID of the inserted order
            $order_id = $stmt->insert_id;

            // Insert each item in the cart into the order_items table and reduce quantity
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $update_quantity_stmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");

            foreach ($_SESSION['cart'] as $id => $item) {
                // Insert each item into order_items
                $stmt->bind_param("iiid", $order_id, $id, $item['quantity'], $item['price']);
                $stmt->execute();

                // Reduce the quantity by the quantity ordered
                $update_quantity_stmt->bind_param("ii", $item['quantity'], $id);
                $update_quantity_stmt->execute();
            }

            // Clear the cart after the order is placed
            unset($_SESSION['cart']);

            // Display success message
            echo "<div class='confirmation-message'>";
            echo "<h2>Your order has been placed successfully!</h2>";
            echo "<p>Thank you, <strong>$customer_name</strong>. Your order is being processed and will be shipped soon.</p>";
            echo "<p><strong>Total Price: </strong>$$total_price</p>";
            echo "<a href='index.php' class='btn'>Go to Home</a>";
            echo "</div>";

        } else {
            // Log the error for debugging
            error_log("Error placing the order: " . $stmt->error);

            echo "<div class='error-message'>";
            echo "<h3>Error placing the order. Please try again later.</h3>";
            echo "</div>";
        }
    } else {
        // Handle empty cart
        echo "<div class='error-message'>";
        echo "<h3>Your cart is empty. Please add items to your cart before placing an order.</h3>";
        echo "</div>";
    }
} else {
    echo "<div class='error-message'>";
    echo "<h3>Invalid request.</h3>";
    echo "</div>";
}
?>

<!-- Add some custom styles for better UI -->
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f8f8;
        margin: 0;
        padding: 0;
    }

    .confirmation-message, .error-message {
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .confirmation-message h2 {
        color: #28a745;
    }

    .error-message h3 {
        color: #dc3545;
    }

    .btn {
        display: inline-block;
        padding: 10px 20px;
        margin-top: 20px;
        background-color: #007bff;
        color: #ffffff;
        text-decoration: none;
        border-radius: 5px;
        font-size: 16px;
    }

    .btn:hover {
        background-color: #0056b3;
    }

    p {
        font-size: 16px;
        color: #333;
    }

    strong {
        color: #007bff;
    }
</style>
