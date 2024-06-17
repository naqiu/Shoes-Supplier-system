<?php
// Include database connection
include 'header.php';

// Query to retrieve inventory levels (all products) and total sold
$inventory_query = "SELECT p.product_name, p.stock, COALESCE(SUM(o.quantity), 0) AS total_sold
                    FROM products p
                    LEFT JOIN orders o ON p.id = o.product_id
                    GROUP BY p.product_name, p.stock
                    ORDER BY p.product_name ASC";
$inventory_result = mysqli_query($conn, $inventory_query);

// Query to retrieve stock movements (last 30 days)
$stock_movements_query = "SELECT p.product_name, o.quantity, o.order_date
                         FROM orders o
                         JOIN products p ON o.product_id = p.id
                         WHERE o.order_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                         ORDER BY o.order_date DESC";
$stock_movements_result = mysqli_query($conn, $stock_movements_query);
?>

<title>Inventory Reports</title>

<style>
    .container {
        max-width: 900px;
        margin-left: 0;
        padding: 20px;
    }

    td {
        padding: 5px;
    }

    .btn {
        text-decoration: none;
        color: #000;
        margin-top: 15px;
    }
</style>

<div id="content" class="container">
    <h2>Inventory Reports</h2>
    <h3>Inventory Levels</h3>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Stock</th>
                <th>Total Sold</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($inventory_result)): ?>
                <tr>
                    <td><?php echo $row['product_name']; ?></td>
                    <td><?php echo $row['stock']; ?></td>
                    <td><?php echo $row['total_sold']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h3>Stock Movements (Last 30 Days)</h3>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Order Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($stock_movements_result)): ?>
                <tr>
                    <td><?php echo $row['product_name']; ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td><?php echo $row['order_date']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div>
    <button class="btn btn-s" onclick="printContent()">Print</button>
    <a class="btn btn-s" href="viewProduct.php">Back to Products</a>
</div>


<script>
    function printContent() {
        const content = document.getElementById("content").innerHTML;
        const styles = `
    <style>
      table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
      }
      th, td {
        border: 1px solid #000;
        padding: 8px;
        text-align: left;
      }
      th {
        background-color: #f2f2f2;
      }
      tr:nth-child(even) {
        background-color: #f9f9f9;
      }
      caption {
        font-weight: bold;
        margin-bottom: 10px;
      }
    </style>
  `;

        const printFrame = document.createElement('iframe');
        printFrame.style.display = 'none';
        document.body.appendChild(printFrame);

        printFrame.contentDocument.write('<html><head><title>Print</title>');
        printFrame.contentDocument.write(styles); // Include the CSS styles inline
        printFrame.contentDocument.write('</head><body>');
        printFrame.contentDocument.write(content);
        printFrame.contentDocument.write('</body></html>');

        printFrame.contentWindow.print();

        setTimeout(() => {
            document.body.removeChild(printFrame);
        }, 1000); // Remove the iframe after printing
    }
</script>

<?php include 'footer.php'; ?>