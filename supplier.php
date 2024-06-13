<?php
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

function fetchLowStockProducts($conn, $supplierId)
{
    $query = "SELECT * FROM products WHERE supplier_id = $supplierId AND stock < restock_threshold";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        echo '<section>';
        echo '<h2>Products needing restocking:</h2>';
        echo '<ul>';
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<li>';
            echo 'Product Name: ' . $row['product_name'] . '<br>';
            echo 'Current Stock: ' . $row['stock'] . '<br>';
            echo 'Restock Threshold: ' . $row['restock_threshold'] . '<br>';
            echo '</li>';
        }
        echo '</ul>';
        echo '</section>';

        // Notify the supplier about low stock
        echo '<script>alert("Some products need restocking!");</script>';
    } else {
        echo '<p>No products need restocking at the moment.</p>';
    }
}

function fetchPendingOrders($conn, $supplierId)
{
    $query = "SELECT orders.*, products.product_name, users.username AS agent_username
              FROM orders
              INNER JOIN products ON orders.product_id = products.id
              INNER JOIN users ON orders.agent_id = users.id
              WHERE products.supplier_id = $supplierId AND orders.approval_status = 'Pending'
              ORDER BY orders.order_date DESC";

    try {
        // Execute the query
        $result = mysqli_query($conn, $query);

        if (!$result) {
            throw new Exception(mysqli_error($conn));
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        $conn->close();
        exit();
    }
    $conn->close(); ?>
    <h3>Orders Pending Approval:</h3>
    <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Order Date</th>
                    <th>Agent</th>
                    <th>Product</th>
                    <th>Address</th>
                    <th>Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['order_date']; ?></td>
                        <td><?php echo $row['agent_username']; ?></td>
                        <td><?php echo $row['product_name']; ?></td>
                        <td><?php echo $row['customer_address']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td>
                            <form method="post" action="updateOrderStatus.php">
                                <a class="btn btn-s" href="xx.php?id=<?php echo $row['id']; ?>">Details</a>
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="status" value="Approved">
                                <button class="btn btn-s" type="submit">Approve</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No orders pending approval.</p>
    <?php endif;
}



?>

<section>
    <h2>Welcome, Supplier!</h2>

    <p>to do:</p>
    <p>-limited stock alert</p>
    <p>-stock history graph /analytic</p>
    <p>-sales report</p>
</section>

<?php
// Display low stock products
fetchLowStockProducts($conn, $_SESSION['user_id']);

// Display orders with 'Pending' approval status
fetchPendingOrders($conn, $_SESSION['user_id']);


if (isset($_SESSION['order_approved'])) {
    echo '<script>alert("Order has been updated!");</script>';
    // Unset the session variable to avoid displaying the alert multiple times
    unset($_SESSION['order_approved']);
}
?>

<?php
include 'footer.php';
?>