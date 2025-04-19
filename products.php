<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root {
            --primary-color: #6366f1;
            --hover-color: #4f46e5;
        }

        body {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
            min-height: 500px; /* Increased card height */
        }

        .product-img {
            height: 300px; /* Increased image height */
            width: 100%;
            object-fit: contain; /* Changed to contain for full image visibility */
            padding: 15px;
            transition: transform 0.3s ease;
        }

        /* Increased card content sizing */
        .card-content {
            padding: 20px;
            font-size: 1.1rem;
        }

        .card-title {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .btn-custom {
            padding: 10px 20px;
            font-size: 1rem;
        }

        .search-container {
            max-width: 600px;
            margin: 30px auto;
        }

        .search-input {
            font-size: 1.2rem;
            padding: 15px 50px;
        }

        /* Larger grid columns */
        .col-xl-4 {
            flex: 0 0 auto;
            width: 33.33333333%;
            padding: 15px;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h1 class="h1 fw-bold">📦 Manage Products</h1>
        <div class="d-flex align-items-center gap-3">
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="add_product.php" class="btn btn-success btn-lg">
                    <i class="fas fa-plus"></i> Add Product
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Enhanced Search Bar -->
    <div class="search-container">
        <div class="input-group">
            <input type="text" id="searchInput" class="form-control search-input" 
                   placeholder="Search by name, supplier, or SKU...">
            <button class="btn btn-primary" disabled>
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>

    <div class="row g-4">
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php 
                $imagePath = $row['image'];
                if (empty($imagePath) || !file_exists(__DIR__ . "/" . $imagePath)) {
                    $imagePath = "uploads/default.jpg";
                }
            ?>
            <div class="col-12 col-md-6 col-xl-4">
                <div class="glass-card position-relative p-4 <?php echo ($row['quantity'] < 10) ? 'low-stock' : ''; ?>">
                    <?php if ($row['quantity'] < 10): ?>
                        <span class="badge bg-danger position-absolute top-0 start-0 m-3">Low Stock</span>
                    <?php endif; ?>
                    <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                         class="product-img w-100" 
                         alt="<?php echo htmlspecialchars($row['name']); ?>"
                         onerror="this.src='uploads/default.jpg'">
                    <div class="card-content">
                        <h3 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h3>
                        <div class="product-details">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="price-tag">₹<?php echo number_format($row['price'], 2); ?></span>
                                <span class="text-muted">SKU: <?php echo htmlspecialchars($row['sku'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="stock-info">
                                    <i class="fas fa-cubes"></i>
                                    Stock: <strong><?php echo $row['quantity']; ?></strong>
                                </div>
                                <div class="supplier-info">
                                    <i class="fas fa-truck"></i>
                                    <?php echo htmlspecialchars($row['supplier']); ?>
                                </div>
                            </div>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <div class="d-flex gap-2 justify-content-between">
                                    <a href="edit_product.php?id=<?php echo $row['id']; ?>" 
                                       class="btn btn-primary btn-custom">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="update_stock.php?id=<?php echo $row['id']; ?>" 
                                       class="btn btn-success btn-custom">
                                        <i class="fas fa-arrow-up"></i> Stock
                                    </a>
                                    <a href="delete_product.php?id=<?php echo $row['id']; ?>" 
                                       class="btn btn-danger btn-custom"
                                       onclick="return confirm('Are you sure you want to delete this product?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script>
    $(document).ready(function(){
        // Enhanced Search Functionality
        $('#searchInput').on('input', function(){
            const searchTerm = $(this).val().toLowerCase().trim();
            $('.glass-card').each(function(){
                const card = $(this);
                const text = card.text().toLowerCase();
                const match = text.includes(searchTerm);
                card.closest('.col-xl-4').toggle(match);
            });
        });

        // Debounce search input
        let timeout;
        $('#searchInput').on('keyup', function(){
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                $(this).trigger('input');
            }, 300);
        });
    });
</script>
</body>
</html>