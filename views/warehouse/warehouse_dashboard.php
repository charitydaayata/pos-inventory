<?php
session_start();

require '../../includes/auth.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Warehouse Manager') {
    header("Location: ../../index.php");
    exit;
}

require '../../conn/db.php';

/* COUNT LOW / OUT OF STOCK */
$countStmt = $pdo->query("
    SELECT COUNT(*)
    FROM products
    WHERE status='active'
      AND (quantity = 0 OR quantity <= stock_threshold)
");

$lowStockCount = $pdo->query("
    SELECT COUNT(*)
    FROM products
    WHERE status = 'active'
      AND (quantity = 0 OR quantity <= stock_threshold)
")->fetchColumn();

$lowStockCount = $countStmt->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Warehouse Manager Dashboard</title>

    <link rel="stylesheet" href="../../assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="../../assets/css/warehouse_dashboard.css">
</head>
<body>

<div class="dashboard">

    <aside class="sidebar">
        <h2>Warehouse Panel</h2>
        <ul>
            <li class="active">
                <a href="warehouse_dashboard.php">Dashboard</a>
            </li>
            <li>
                <a href="stock_management.php">Stock Management</a>
            </li>
            <li style="opacity:0.5; cursor:not-allowed;">Request Purchase Order (Soon)</li>
            <!-- <li>
                <a href="low_stock.php">
                    Low Stock Items
                    <?php if ($lowStockCount > 0): ?>
                        <span class="badge"><?= $lowStockCount ?></span>
                    <?php endif; ?>
                </a>
            </li> -->   <!-- Disabled for now -->
            <li>
                <a href="../../logout.php">Logout</a>
            </li>
        </ul>
    </aside>

    <main class="content">
        <h1>Welcome, <?= htmlspecialchars($_SESSION['name']) ?></h1>

        <div class="cards">

            <a href="stock_management.php" class="card clickable">
                Manage Inventory Stock
            </a>

            <a href="low_stock.php" class="card clickable alert-card">
                Monitor Low / Out of Stock
                <?php if ($lowStockCount > 0): ?>
                    <span class="badge big"><?= $lowStockCount ?></span>
                <?php endif; ?>
            </a>

            <div class="card disabled">
                Coordinate with PO Manager (Soon)
            </div>

        </div>
    </main>

</div>

</body>
</html>
