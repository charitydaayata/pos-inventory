<?php
session_start();
require '../../includes/auth.php';


/* ROLE RESTRICTION */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Inventory Manager') {
    header("Location: ../../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory Manager Dashboard</title>

    <!-- SAME ADMIN UI -->
    <link rel="stylesheet" href="../../assets/css/admin_dashboard.css">
</head>
<body>

<div class="dashboard">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <h2>Inventory Panel</h2>
        <ul>
            <li class="active"><a href="inventory_dashboard.php">Dashboard</a></li>
            <li><a href="inventory_products.php">Products</a></li>
            <li><a href="inventory_categories.php">Categories</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </aside>

    <!-- CONTENT -->
    <main class="content">
        <h1>Welcome, <?= htmlspecialchars($_SESSION['name']) ?></h1>

        <div class="cards">
            <div class="card">
                View Products
            </div>

            <div class="card">
                Manage Categories
            </div>
        </div>
    </main>

</div>

</body>
</html>
