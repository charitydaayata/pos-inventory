<?php
session_start();
require '../../includes/auth.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Cashier') {
    header("Location: ../../index.php");
    exit;
}

require '../../conn/db.php';

/* =============================
   SEARCH, FILTER, PAGINATION
============================= */
$search     = $_GET['search'] ?? '';
$categoryId = $_GET['category'] ?? '';
$page       = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit      = 10;
$offset     = ($page - 1) * $limit;

/* =============================
   FETCH CATEGORIES (FILTER)
============================= */
$categories = $pdo->query("
    SELECT category_id, category_name
    FROM categories
    WHERE description NOT LIKE '[ARCHIVED]%'
       OR description IS NULL
")->fetchAll();

/* =============================
   COUNT PRODUCTS
============================= */
$countSql = "
    SELECT COUNT(*)
    FROM products p
    WHERE p.status = 'active'
      AND p.product_name LIKE :search
";
$params = [':search' => "%$search%"];

if ($categoryId) {
    $countSql .= " AND p.category_id = :category";
    $params[':category'] = $categoryId;
}

$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalProducts = $countStmt->fetchColumn();
$totalPages = ceil($totalProducts / $limit);

/* =============================
   FETCH PRODUCTS
============================= */
$sql = "
    SELECT p.product_name, p.price, p.quantity, c.category_name
    FROM products p
    JOIN categories c ON p.category_id = c.category_id
    WHERE p.status = 'active'
      AND p.product_name LIKE :search
";

if ($categoryId) {
    $sql .= " AND p.category_id = :category";
}

$sql .= " ORDER BY p.product_name ASC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
if ($categoryId) {
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
    <title>Cashier | Products</title>

    <link rel="stylesheet" href="../../assets/css/cashier_dashboard.css">
    <link rel="stylesheet" href="../../assets/css/product_cashier.css">
</head>
<body>

<div class="dashboard">

    <!-- TOP BAR -->
<header class="topbar">
    <h2>Cashier Panel</h2>

    <div class="top-actions">
        <a href="dashboard.php" class="back-btn">← Back</a>
        <a href="../../logout.php" class="logout-btn">Logout</a>
    </div>
</header>

    <main class="content">

        <!-- SEARCH & FILTER -->
        <form method="GET" class="filter-bar">
            <input type="text" name="search"
                   placeholder="Search product..."
                   value="<?= htmlspecialchars($search) ?>">

            <select name="category">
                <option value="">All Categories</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['category_id'] ?>"
                        <?= $categoryId == $c['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['category_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Search</button>
        </form>

        <!-- PRODUCT TABLE -->
        <table>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Price</th>
                <th>Available</th>
            </tr>

            <?php if (count($products) === 0): ?>
                <tr>
                    <td colspan="4" style="text-align:center;">
                        No products found.
                    </td>
                </tr>
            <?php endif; ?>

            <?php foreach ($products as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['product_name']) ?></td>
                <td><?= htmlspecialchars($p['category_name']) ?></td>
                <td><?= number_format($p['price'], 2) ?></td>
                <td><?= $p['quantity'] ?></td>
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
