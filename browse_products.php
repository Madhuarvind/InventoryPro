<?php
session_start();
include 'db.php';

$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Products</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
    .product-card {
        transition: transform 0.2s ease-in-out;
        border-radius: 15px;
        overflow: hidden;
        border: 1px solid #ddd;
        padding: 25px; /* Increased padding */
        min-height: 450px; /* Increased card height */
    }

    .product-card:hover {
        transform: scale(1.08); /* Slightly bigger hover effect */
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.25);
    }

    .product-img {
        width: 100%;
        height: 350px; /* Increased image height */
        object-fit: cover;
        border-bottom: 1px solid #ddd;
    }

    .card-title {
        font-size: 1.6rem; /* Bigger title */
        font-weight: bold;
    }

    .card-text {
        font-size: 1.3rem; /* Bigger text */
    }

    .low-stock {
        border: 3px solid red;
    }
</style>

</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">🛍 Browse Products</h2>

        <!-- Search Bar -->
        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="🔍 Search for a product...">
        </div>

        <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php 
                    $imagePath = $row['image'];
                    if (empty($imagePath) || !file_exists(__DIR__ . "/" . $imagePath)) {
                        $imagePath = "uploads/default.jpg";
                    }
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card product-card <?php echo ($row['quantity'] < 10) ? 'low-stock' : ''; ?>">
                        <img src="<?php echo htmlspecialchars($imagePath); ?>" class="product-img" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                            <p class="card-text">💰 Price: ₹<?php echo number_format($row['price'], 2); ?></p>
                            <p class="card-text">📦 Stock: <strong><?php echo $row['quantity']; ?></strong></p>
                            <form method="POST" action="add_to_cart.php">
                                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                <div class="input-group mb-2">
                                    <input type="number" name="quantity" class="form-control" placeholder="Quantity" min="1" required>
                                    <button type="submit" class="btn btn-primary">➕ Add to Cart</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="text-center mt-3">
            <a href="customer_dashboard.php" class="btn btn-secondary">⬅️ Back to Dashboard</a>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $("#searchInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(".card").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
</script>

</body>
</html>