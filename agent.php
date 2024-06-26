<style>
    label {
        min-width: 160px;
        display: inline-block;
        vertical-align: top;
        padding-top: 9px;
    }
    td {
        padding: 5px;
    }
</style>
<?php
include 'header.php';

// Check if the user is logged in as an agent
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'agent') {
    header('Location: login.php');
    exit();
}
?>

<h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

<?php
$query = "SELECT orders.*, products.product_name
          FROM orders
          INNER JOIN products ON orders.product_id = products.id
          WHERE orders.agent_id = {$_SESSION['user_id']} AND (orders.approval_status = 'Approved' OR orders.approval_status = 'Rejected')
          ORDER BY orders.order_date DESC";

$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0): ?>
    <h3>Approved Orders:</h3>
    <table>
        <thead>
            <tr>
                <th>Order Date</th>
                <th>Product</th>
                <th>Customer Name</th>
                <th>Address</th>
                <th>Contact Number</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['customer_address']); ?></td>
                    <td><?php echo htmlspecialchars($row['customer_contact']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No approved orders for you at the moment.</p>
<?php endif;

if (isset($_SESSION['order_approved'])) {
    echo '<script>alert("Your order has been approved!");</script>';
    unset($_SESSION['order_approved']);
}
?>
<?php include 'footer.php'; ?>
