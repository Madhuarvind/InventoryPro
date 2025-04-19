<?php
include "db.php";
$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);
?>

<table border="1">
    <tr>
        <th>Product ID</th>
        <th>Product Name</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Barcode</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['price']; ?></td>
            <td><?php echo $row['quantity']; ?></td>
            <td><img src="<?php echo $row['barcode_image']; ?>" width="150"></td>
        </tr>
    <?php } ?>
</table>
