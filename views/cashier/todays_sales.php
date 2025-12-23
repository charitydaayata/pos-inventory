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
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

/* =============================
   COUNT TODAY SALES ITEMS
============================= */
$countSql = "
    SELECT COUNT(*)
    FROM sales_items si
    JOIN sales s ON si.sale_id = s.sale_id
    JOIN products p ON si.product_id = p.product_id
    WHERE DATE(s.sale_date) = CURDATE()
      AND p.product_name LIKE :search
";
$params = [':search' => "%$search%"];

if ($categoryId) {
    $countSql .= " AND p.category_id = :category";
    $params[':category'] = $categoryId;
}

$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalRows  = $countStmt->fetchColumn();
$totalPages = ceil($totalRows / $limit);

/* =============================
   FETCH TODAY SALES ITEMS
============================= */
$dataSql = "
    SELECT 
        p.product_name,
        c.category_name,
        si.quantity,
        si.price,
        (si.quantity * si.price) AS total,
        s.sale_date
    FROM sales_items si
    JOIN sales s ON si.sale_id = s.sale_id
    JOIN products p ON si.product_id = p.product_id
    JOIN categories c ON p.category_id = c.category_id
    WHERE DATE(s.sale_date) = CURDATE()
      AND p.product_name LIKE :search
";

if ($categoryId) {
    $dataSql .= " AND p.category_id = :category";
}

$dataSql .= "
    ORDER BY s.sale_date DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($dataSql);
$stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
if ($categoryId) {
    $stmt->bindValue(':category', $categoryId, PDO::PARAM_INT);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$sales = $stmt->fetchAll();

/* =============================
   ANALYTICS (3 REQUIRED 😄)
============================= */

/* TOTAL SALES AMOUNT TODAY */
$totalSales = $pdo->query("
    SELECT SUM(total_amount)
    FROM sales
    WHERE DATE(sale_date) = CURDATE()
")->fetchColumn() ?? 0;

/* TOTAL PRODUCTS SOLD TODAY */
$totalItems = $pdo->query("
    SELECT SUM(quantity)
    FROM sales_items si
    JOIN sales s ON si.sale_id = s.sale_id
    WHERE DATE(s.sale_date) = CURDATE()
")->fetchColumn() ?? 0;

/* TOTAL TRANSACTIONS TODAY */
$totalTransactions = $pdo->query("
    SELECT COUNT(*)
    FROM sales
    WHERE DATE(sale_date) = CURDATE()
")->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cashier | Today’s Sales</title>

    <link rel="stylesheet" href="../../assets/css/cashier_dashboard.css">
    <link rel="stylesheet" href="../../assets/css/todays_sales.css">
</head>
<body>

<div class="dashboard">

    <!-- TOP BAR -->
    <header class="topbar">
        <h2>Today’s Sales</h2>

        <div class="top-actions">
            <a href="dashboard.php" class="back-btn">← Back</a>
            <a href="../../logout.php" class="logout-btn">Logout</a>
        </div>
    </header>

    <main class="content">

        <!-- ANALYTICS -->
        <div class="analytics">
            <div class="card">
                <h3>Total Sales</h3>
                <p>₱<?= number_format($totalSales, 2) ?></p>
            </div>
            <div class="card">
                <h3>Items Sold</h3>
                <p><?= $totalItems ?></p>
            </div>
            <div class="card">
                <h3>Transactions</h3>
                <p><?= $totalTransactions ?></p>
            </div>
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
                        <?= $categoryId == $c['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['category_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Filter</button>
        </form>

        <!-- SALES TABLE -->
        <table>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
                <th>Date</th>
            </tr>

            <?php if (!$sales): ?>
                <tr>
                    <td colspan="6" style="text-align:center;">No sales today.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($sales as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['product_name']) ?></td>
                <td><?= htmlspecialchars($s['category_name']) ?></td>
                <td><?= $s['quantity'] ?></td>
                <td>₱<?= number_format($s['price'], 2) ?></td>
                <td>₱<?= number_format($s['total'], 2) ?></td>
                <td><?= $s['sale_date'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <!-- PAGINATION -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category=<?= $categoryId ?>"
                   class="<?= $page === $i ? 'active' : '' ?>">
                   <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

    </main>
</div>

</body>
</html>
