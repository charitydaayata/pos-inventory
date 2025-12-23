<?php
session_start();
require '../../includes/auth.php';

/* =========================
   ROLE CHECK
========================= */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../index.php");
    exit;
}

require '../../conn/db.php';

/* =========================
   DATA VALIDATION (INPUT)
========================= */
$search     = trim($_GET['search'] ?? '');
$search     = substr($search, 0, 50);

$categoryId = $_GET['category'] ?? '';
$categoryId = $categoryId !== '' ? (int)$categoryId : '';

$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 10;
$offset = ($page - 1) * $limit;

/* =========================
   FETCH CATEGORIES
========================= */
$categories = $pdo->query("
    SELECT category_id, category_name
    FROM categories
    WHERE status='active'
")->fetchAll();

/* =========================
   COUNT PRODUCTS
========================= */
$countSql = "
    SELECT COUNT(*)
    FROM products
    WHERE status='active'
      AND product_name LIKE :search
";
$params = [':search' => "%$search%"];

if ($categoryId !== '') {
    $countSql .= " AND category_id = :category";
    $params[':category'] = $categoryId;
}

$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalProducts = (int)$countStmt->fetchColumn();
$totalPages    = max(1, ceil($totalProducts / $limit));

/* =========================
   FETCH PRODUCTS
========================= */
$sql = "
    SELECT product_id, product_name, quantity, stock_threshold
    FROM products
    WHERE status='active'
      AND product_name LIKE :search
";

if ($categoryId !== '') {
    $sql .= " AND category_id = :category";
}

$sql .= " ORDER BY product_name ASC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);

if ($categoryId !== '') {
    $stmt->bindValue(':category', $categoryId, PDO::PARAM_INT);
}

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin | Stock Management</title>
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
            <li class="active"><a href="stock_management.php">Stock Management</a></li>
            <li><a href="archives.php">Archives</a></li>
            <li><a href="users.php">Manage Users</a></li>
            <li><a href="audit_logs.php">Audit Logs</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </aside>

    <main class="content">

        <h1>Stock Management</h1>

        <!-- FLASH MESSAGES -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="stock-actions">
            <a href="low_stock.php" class="low-stock-btn">
                View All Low & No Stock
            </a>
        </div>

        <!-- SEARCH & FILTER -->
        <form method="GET" class="filter-bar">
            <input type="text"
                   name="search"
                   placeholder="Search product"
                   value="<?= htmlspecialchars($search) ?>">

            <select name="category">
                <option value="">All Categories</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['category_id'] ?>"
                        <?= $categoryId === (int)$c['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['category_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Filter</button>
        </form>

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
                    <td colspan="5" class="empty">No products found.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($products as $p): ?>
            <?php
                $isOut = ($p['quantity'] == 0);
                $isLow = ($p['quantity'] > 0 && $p['quantity'] <= $p['stock_threshold']);

                if ($isOut) {
                    $status = 'Out of Stock';
                    $rowClass = 'out';
                } elseif ($isLow) {
                    $status = 'Low Stock';
                    $rowClass = 'low';
                } else {
                    $status = 'Normal';
                    $rowClass = '';
                }
            ?>
            <tr class="<?= $rowClass ?>">
                <td><?= htmlspecialchars($p['product_name']) ?></td>
                <td><?= (int)$p['quantity'] ?></td>
                <td><?= (int)$p['stock_threshold'] ?></td>
                <td><?= $status ?></td>
                <td>
                    <?php if ($isLow || $isOut): ?>
                       <form method="POST" action="../../conn/Stock.php" class="inline-form">
                            <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">

                            <input type="number"
                             name="quantity"
                                min="0"
                                placeholder="Qty"
                                required>

                            <input type="number"
                                 name="threshold"
                                min="0"
                                placeholder="Threshold"
                                required>
                                <button type="submit" name="update_stock">Update</button>
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
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category=<?= $categoryId ?>"
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
