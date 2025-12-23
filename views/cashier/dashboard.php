<?php
session_start();
require '../../includes/auth.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Cashier') {
    header("Location: ../../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cashier Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/cashier_dashboard.css">
</head>
<body>

<div class="dashboard">
    <header class="topbar">
        <h2>Cashier Panel</h2>
        <a href="../../logout.php">Logout</a>
    </header>

    <main class="content">
        <h1>Welcome, <?php echo $_SESSION['name']; ?></h1>

        <div class="actions">
            <a href="pos.php" class="action-card">Start POS</a>
            <a href="product_cashier.php" class="action-card">View Products</a>
            <a href="todays_sales.php" class="action-card">Today’s Sales</a>
        </div>
    </main>
</div>

</body>
</html>
