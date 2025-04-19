<?php
session_start();
include('db.php');

if ($_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Define how many results per page
$results_per_page = 10;

// Find the total number of orders
$sql = "SELECT COUNT(*) AS total_orders FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        JOIN user u ON o.user_id = u.id
        LEFT JOIN payments pay ON o.id = pay.order_id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_orders = $row['total_orders'];

// Determine the current page number (if not set, default to 1)
$page_number = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_limit = ($page_number - 1) * $results_per_page;

// Handle Search
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Modify query with search condition
$query = "SELECT o.id, u.username AS customer_name, p.name AS product_name, 
                 oi.quantity, o.total_price, o.status, 
                 pay.payment_status, pay.payment_date  
          FROM orders o
          JOIN order_items oi ON o.id = oi.order_id  
          JOIN products p ON oi.product_id = p.id
          JOIN user u ON o.user_id = u.id  
          LEFT JOIN payments pay ON o.id = pay.order_id
          WHERE u.username LIKE ? OR p.name LIKE ?
          ORDER BY o.order_date DESC
          LIMIT ?, ?";
$stmt = $conn->prepare($query);
$search_term = "%$search%";
$stmt->bind_param("ssii", $search_term, $search_term, $start_limit, $results_per_page);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Bootstrap Icons (optional) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        .table th, .table td {
            text-align: center;
        }
        .action-buttons form {
            display: inline-block;
        }
        .pagination .page-link {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }
        .pagination .page-item.active .page-link {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .badge-success {
            background-color: #28a745;
        }
        .badge-warning {
            background-color: #ffc107;
        }
        .card-header {
            background-color: #f8f9fa;
        }
        .card {
            margin-bottom: 20px;
        }
        .input-group {
            max-width: 400px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2>Order Management</h2>

    <!-- Search Form -->
    <div class="card">
        <div class="card-header">
            <h5>Search Orders</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="manage_orders.php" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by Customer or Product" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Payment Status</th>
                    <th>Payment Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['customer_name']; ?></td>
                        <td><?php echo $row['product_name']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td>₹<?php echo number_format($row['total_price'], 2); ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td>
                            <span class="badge <?php echo ($row['payment_status'] == 'Paid') ? 'badge-success' : 'badge-warning'; ?>">
                                <?php echo $row['payment_status'] ?? 'Not Paid'; ?>
                            </span>
                        </td>
                        <td><?php echo $row['payment_date'] ? date("d-m-Y H:i:s", strtotime($row['payment_date'])) : '-'; ?></td>
                        <td class="action-buttons">
                            <form method="POST" action="update_order_status.php" class="d-inline">
                                <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                <select name="status" class="form-select form-select-sm status-select">
                                    <option value="Pending" <?php echo ($row['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Completed" <?php echo ($row['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                    <option value="Cancelled" <?php echo ($row['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                            </form>

                            <?php if ($row['status'] == 'Cancelled' && $row['payment_status'] == 'Paid') { ?>
                                <form method="POST" action="process_refund.php" class="d-inline">
                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Issue Refund</button>
                                </form>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Pagination Controls -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php
                $total_pages = ceil($total_orders / $results_per_page);
                if ($page_number > 1) {
                    echo '<li class="page-item"><a class="page-link" href="manage_orders.php?page=' . ($page_number - 1) . '">Previous</a></li>';
                }
                for ($i = 1; $i <= $total_pages; $i++) {
                    $active_class = ($i == $page_number) ? 'active' : '';
                    echo '<li class="page-item ' . $active_class . '"><a class="page-link" href="manage_orders.php?page=' . $i . '">' . $i . '</a></li>';
                }
                if ($page_number < $total_pages) {
                    echo '<li class="page-item"><a class="page-link" href="manage_orders.php?page=' . ($page_number + 1) . '">Next</a></li>';
                }
                ?>
            </ul>
        </nav>
    </div>
</div>

<!-- Include jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Enable tooltips for action buttons
    $(document).ready(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>

</body>
</html>
