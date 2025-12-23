<?php
session_start();
require '../../includes/auth.php';

/* ROLE RESTRICTION */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Warehouse Manager') {
    header("Location: ../../index.php");
    exit;
}

require '../../conn/db.php';

/* =============================
   DATA VALIDATION (INPUT)
============================= */
$search = trim($_GET['search'] ?? '');
$search = substr($search, 0, 50);

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

/* =============================
   COUNT PRODUCTS
============================= */
$countStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM products
    WHERE status = 'active'
      AND product_name LIKE :search
");
$countStmt->execute([
    ':search' => "%$search%"
]);
$totalRows  = (int)$countStmt->fetchColumn();
$totalPages = max(1, ceil($totalRows / $limit));

/* =============================
   FETCH PRODUCTS
============================= */
$stmt = $pdo->prepare("
    SELECT product_id, product_name, quantity, stock_threshold
    FROM products
    WHERE status = 'active'
      AND product_name LIKE :search
    ORDER BY product_name ASC
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
    <title>Warehouse | Stock Management</title>

    <link rel="stylesheet" href="../../assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="../../assets/css/stock.css">
    <link rel="stylesheet" href="../../assets/css/searchbar_warehouse.css">
</head>
<body>

<div class="dashboard">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <h2>Warehouse Panel</h2>
        <ul>
            <li><a href="warehouse_dashboard.php">Dashboard</a></li>
            <li class="active"><a href="stock_management.php">Stock Management</a></li>
            <li style="opacity:0.5; cursor:not-allowed;">Request Purchase Order (Soon)</li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </aside>

    <!-- CONTENT -->
    <main class="content">
        <h1>Stock Management</h1>

        <!-- FLASH MESSAGES -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <!-- SEARCH BAR -->
        <form method="GET" class="warehouse-search">
            <input type="text"
                   name="search"
                   placeholder="Search product..."
                   value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>

        <br>

        <!-- TABLE -->
        <table>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Threshold</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="5" style="text-align:center;">No products found.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($products as $p): ?>
            <?php
                $qty = (int)$p['quantity'];
                $threshold = (int)$p['stock_threshold'];

                if ($qty === 0) {
                    $status = 'Out of Stock';
                    $rowClass = 'out';
                } elseif ($qty <= $threshold) {
                    $status = 'Low Stock';
                    $rowClass = 'low';
                } else {
                    $status = 'Normal';
                    $rowClass = '';
                }
            ?>
            <tr class="<?= $rowClass ?>">
                <td><?= htmlspecialchars($p['product_name']) ?></td>
                <td><?= $qty ?></td>
                <td><?= $threshold ?></td>
                <td><?= $status ?></td>
                <td>
                    <?php if ($qty === 0 || $qty <= $threshold): ?>
                        <form method="POST"
                              action="../../conn/Stock.php"
                              class="inline-form">

                            <input type="hidden"
                                   name="product_id"
                                   value="<?= (int)$p['product_id'] ?>">

                            <input type="number"
                                   name="quantity"
                                   min="0"
                                   max="100000"
                                   placeholder="Qty"
                                   required>

                            <input type="number"
                                   name="threshold"
                                   min="1"
                                   max="100000"
                                   placeholder="Threshold"
                                   required>

                            <button type="submit" name="update_stock">
                                Update
                            </button>
                        </form>
                    <?php else: ?>
                        <button class="disabled-btn" disabled>OK</button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <!-- PAGINATION -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"
                   class="<?= $i === $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

    </main>
</div>

</body>
</html>
