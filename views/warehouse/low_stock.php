<?php
session_start();
require '../../includes/auth.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Warehouse Manager') {
    header("Location: ../../index.php");
    exit;
}

require '../../conn/db.php';

/* FETCH LOW / OUT OF STOCK ONLY */
$products = $pdo->query("
    SELECT product_id, product_name, quantity, stock_threshold
    FROM products
    WHERE status='active'
      AND (quantity = 0 OR quantity <= stock_threshold)
    ORDER BY quantity ASC
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Warehouse | Low Stock Items</title>

    <link rel="stylesheet" href="../../assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="../../assets/css/stock.css">
</head>
<body>

<div class="dashboard">

    <aside class="sidebar">
        <h2>Warehouse Panel</h2>
        <ul>
            <li><a href="warehouse_dashboard.php">Dashboard</a></li>
            <li><a href="stock_management.php">Stock Management</a></li>
            <li style="opacity:0.5; cursor:not-allowed;">Request Purchase Order (Soon)</li>
            <!-- <li class="active"><a href="low_stock.php">Low Stock Items</a></li> --> <!-- Disabled for now -->
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </aside>

    <main class="content">
        <h1>Low / Out of Stock Items</h1>

        <table>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Threshold</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php if (count($products) === 0): ?>
                <tr>
                    <td colspan="5" style="text-align:center;">
                        🎉 All products have sufficient stock.
                    </td>
                </tr>
            <?php endif; ?>

            <?php foreach ($products as $p): ?>
            <?php
                $isOut = $p['quantity'] == 0;
                $status = $isOut ? 'Out of Stock' : 'Low Stock';
                $rowClass = $isOut ? 'out' : 'low';
            ?>
            <tr class="<?= $rowClass ?>">
                <td><?= htmlspecialchars($p['product_name']) ?></td>
                <td><?= $p['quantity'] ?></td>
                <td><?= $p['stock_threshold'] ?></td>
                <td><?= $status ?></td>
                <td>
                    <form method="POST" action="../../conn/Stock.php" class="inline-form">
                        <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">

                        <input type="number" name="quantity" min="0" placeholder="New Qty" required>
                        <input type="number" name="threshold" min="1" placeholder="Threshold" required>

                        <button type="submit" name="update_stock">Update</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

    </main>
</div>

</body>
</html>
