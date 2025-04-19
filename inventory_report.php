<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Secure SQL query to prevent injection
$sql = "SELECT * FROM products ORDER BY name ASC"; 
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Report</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        .buttons-top {
            margin-bottom: 20px;
        }

        .buttons-top a {
            text-decoration: none;
            padding: 10px 15px;
            margin: 5px;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
        }

        .btn-csv {
            background: #28a745;
            color: white;
        }

        .btn-pdf {
            background: #dc3545;
            color: white;
        }

        .btn-csv:hover {
            background: #218838;
        }

        .btn-pdf:hover {
            background: #c82333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background: #007bff;
            color: white;
            cursor: pointer;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        tr:hover {
            background: #f1f1f1;
        }

        .low-stock {
            color: red;
            font-weight: bold;
        }

        .sufficient-stock {
            color: green;
        }

        .btn-back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .btn-back:hover {
            background: #0056b3;
        }

    </style>
</head>
<body>

    <div class="container">
        <h2>📦 Inventory Report</h2>

        <!-- Export Buttons at the Top -->
        <div class="buttons-top">
            <a href="export_inventory_csv.php" class="btn-csv">📄 Export CSV</a>
            <a href="export_inventory_pdf.php" class="btn-pdf">📜 Export PDF</a>
        </div>

        <table id="inventoryTable">
            <thead>
                <tr>
                    <th onclick="sortTable(0)">ID 🔽</th>
                    <th onclick="sortTable(1)">Product Name 🔽</th>
                    <th onclick="sortTable(2)">Price (₹) 🔽</th>
                    <th onclick="sortTable(3)">Stock 🔽</th>
                    <th>Low Stock Alert</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td>₹<?php echo htmlspecialchars($row['price']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td class="<?php echo ($row['quantity'] < 5) ? 'low-stock' : 'sufficient-stock'; ?>">
                            <?php echo ($row['quantity'] < 5) ? '⚠️ Low Stock' : '✔️ Sufficient'; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Back to Admin Dashboard Button at the Bottom -->
        <a href="admin_dashboard.php" class="btn-back">⬅️ Back to Admin Dashboard</a>

    </div>

    <script>
        function sortTable(n) {
            let table = document.getElementById("inventoryTable");
            let rows, switching, i, x, y, shouldSwitch, dir = "asc", switchCount = 0;
            switching = true;

            while (switching) {
                switching = false;
                rows = table.rows;
                for (i = 1; i < rows.length - 1; i++) {
                    shouldSwitch = false;
                    x = rows[i].getElementsByTagName("TD")[n].innerHTML.toLowerCase();
                    y = rows[i + 1].getElementsByTagName("TD")[n].innerHTML.toLowerCase();

                    if ((dir === "asc" && x > y) || (dir === "desc" && x < y)) {
                        shouldSwitch = true;
                        break;
                    }
                }

                if (shouldSwitch) {
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                    switchCount++;
                } else if (switchCount === 0 && dir === "asc") {
                    dir = "desc";
                    switching = true;
                }
            }
        }
    </script>

</body>
</html>
