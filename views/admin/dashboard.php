<?php
session_start();

require '../../includes/auth.php';

/* SESSION CHECK MUST HAPPEN BEFORE DB */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../index.php");
    exit;
}

/*  ONLY REQUIRE DB AFTER SESSION IS VALID */
require '../../conn/db.php';

/* DASHBOARD COUNTS */

/*  TOTAL PRODUCTS = ACTIVE ONLY */
$totalProducts = $pdo->query("
    SELECT COUNT(*)
    FROM products
    WHERE status = 'active'
")->fetchColumn();

/* ARCHIVED PRODUCTS = INACTIVE ONLY */
$archivedProducts = $pdo->query("
    SELECT COUNT(*)
    FROM products
    WHERE status = 'inactive'
")->fetchColumn();

/*  TOTAL CATEGORIES = ACTIVE ONLY */
$totalCategories = $pdo->query("
    SELECT COUNT(*)
    FROM categories
    WHERE status = 'active'
")->fetchColumn();

/*  ARCHIVED CATEGORIES = INACTIVE ONLY */
$archivedCategories = $pdo->query("
    SELECT COUNT(*)
    FROM categories
    WHERE status = 'inactive'
")->fetchColumn();

/* LOW STOCK (OLD LOGIC — KEPT) */
$lowStock = $pdo->query("
    SELECT COUNT(*) 
    FROM products 
    WHERE quantity <= 5 
      AND status = 'active'
")->fetchColumn();

/* LOW / OUT OF STOCK (THRESHOLD-BASED) */
$lowStockCount = $pdo->query("
    SELECT COUNT(*)
    FROM products
    WHERE status = 'active'
      AND (quantity = 0 OR quantity <= stock_threshold)
")->fetchColumn();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/admin_dashboard.css">
</head>
<body>

<div class="dashboard">
    <aside class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li class="active"><a href="dashboard.php">Dashboard</a></li>
            <li><a href="products.php">Products</a></li>
            <li><a href="categories.php">Categories</a></li>
            <li><a href="stock_management.php">Stock Management</a></li>
            <!-- <li><a href="purchase_orders.php">Purchase Orders</a></li> -->
            <li><a href="archives.php">Archives</a></li>
            <li><a href="users.php">Manage Users</a></li>
            <li><a href="audit_logs.php">Audit Logs</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </aside>

    <main class="content">
        <h1>Welcome, <?= htmlspecialchars($_SESSION['name']) ?></h1>

        <div class="cards">

            <div class="card">
                Total Products
                <h2><?= $totalProducts ?></h2>
            </div>

            <div class="card">
                Archived Products
                <h2><?= $archivedProducts ?></h2>
            </div>

            <div class="card">
                Total Categories
                <h2><?= $totalCategories ?></h2>
            </div>

            <div class="card">
                Archived Categories
                <h2><?= $archivedCategories ?></h2>
            </div>

            <a href="low_stock.php"
               class="card clickable alert-card <?= $lowStockCount == 0 ? 'disabled' : '' ?>">
                Monitor Low / Out of Stock
                <?php if ($lowStockCount > 0): ?>
                    <span class="badge big"><?= $lowStockCount ?></span>
                <?php endif; ?>
            </a>

        </div>
    </main>
</div>

</body>
</html>
