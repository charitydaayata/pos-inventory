<?php
session_start();

require '../../includes/auth.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../index.php");
    exit;
}

require '../../conn/db.php';

/* =============================
   SEARCH / FILTER / PAGINATION
============================= */
$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? 'all'; // all | low | out
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$limit  = 5;
$offset = ($page - 1) * $limit;

/* =============================
   BASE WHERE CONDITION
============================= */
$where = "
    p.status = 'active'
    AND (p.quantity = 0 OR p.quantity <= p.stock_threshold)
    AND p.product_name LIKE :search
";

if ($filter === 'low') {
    $where .= " AND p.quantity > 0 AND p.quantity <= p.stock_threshold";
} elseif ($filter === 'out') {
    $where .= " AND p.quantity = 0";
}

/* =============================
   COUNT PRODUCTS
============================= */
$countStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM products p
    WHERE $where
");
$countStmt->execute([
    ':search' => "%$search%"
]);
$totalProducts = $countStmt->fetchColumn();
$totalPages = ceil($totalProducts / $limit);

/* =============================
   FETCH PRODUCTS
============================= */
$stmt = $pdo->prepare("
    SELECT p.product_name, p.quantity, p.stock_threshold
    FROM products p
    WHERE $where
    ORDER BY p.quantity ASC
    LIMIT :limit OFFSET :offset
");

$stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin | Low Stock Items</title>

    <link rel="stylesheet" href="../../assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="../../assets/css/stock.css">
</head>
<body>

<div class="dashboard">

<aside class="sidebar">
    <h2>Admin Panel</h2>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
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
    <h1>Low / Out of Stock Items</h1>

    <!-- BACK BUTTON -->
    <div class="stock-actions">
        <a href="stock_management.php" class="low-stock-btn">
            Back to Stock Management
        </a>
    </div>
    <br>
    <!-- SEARCH & FILTER -->
    <form method="GET" class="filter-bar">
        <input type="text"
               name="search"
               placeholder="Search product"
               value="<?= htmlspecialchars($search) ?>">

        <select name="filter">
            <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All</option>
            <option value="low" <?= $filter === 'low' ? 'selected' : '' ?>>Low Stock</option>
            <option value="out" <?= $filter === 'out' ? 'selected' : '' ?>>Out of Stock</option>
        </select>

        <button type="submit">Apply</button>
    </form>

    <!-- TABLE -->
    <table>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Status</th>
        </tr>

        <?php if (count($products) === 0): ?>
            <tr>
                <td colspan="3" style="text-align:center;">No items found.</td>
            </tr>
        <?php endif; ?>

        <?php foreach ($products as $p): ?>
        <?php
            $isOut = ($p['quantity'] == 0);
            $rowClass = $isOut ? 'out' : 'low';
            $status = $isOut ? 'Out of Stock' : 'Low Stock';
        ?>
        <tr class="<?= $rowClass ?>">
            <td><?= htmlspecialchars($p['product_name']) ?></td>
            <td><?= $p['quantity'] ?></td>
            <td><?= $status ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- PAGINATION -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&filter=<?= $filter ?>"
               class="<?= $i == $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

</main>
</div>

</body>
</html>
