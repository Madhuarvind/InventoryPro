<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle supplier addition
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $sql = "INSERT INTO suppliers (name,  phone, email, address) 
            VALUES ('$name',  '$phone', '$email', '$address')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Supplier added successfully!'); window.location.href='suppliers.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Fetch suppliers
$result = $conn->query("SELECT * FROM suppliers");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Suppliers</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }
        .form-control, .btn {
            margin-bottom: 10px;
        }
        .table thead {
            background-color: #343a40;
            color: #fff;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h2 class="text-center">🚚 Manage Suppliers</h2>

        <!-- Supplier Form -->
        <form method="POST">
            <div class="mb-3">
                <input type="text" name="name" class="form-control" placeholder="Supplier Name" required>
            </div>
            
            <div class="mb-3">
                <input type="text" name="phone" class="form-control" placeholder="Phone">
            </div>
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email">
            </div>
            <div class="mb-3">
                <textarea name="address" class="form-control" placeholder="Address"></textarea>
            </div>
            <button type="submit" class="btn btn-success w-100">Add Supplier</button>
        </form>

        <!-- Search Bar for Supplier List -->
        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="🔍 Search for a supplier...">
        </div>

        <!-- Supplier List Table -->
        <h3 class="mt-4 text-center">🚚 Supplier List</h3>
        <table class="table table-bordered table-striped text-center" id="supplierTable">
            <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['address']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Search Functionality for Supplier Table
    $(document).ready(function(){
        $("#searchInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#supplierTable tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
</script>
</body>
</html>
