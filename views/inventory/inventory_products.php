<?php
session_start();
require '../../includes/auth.php';

/* =========================
   ROLE RESTRICTION
========================= */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Inventory Manager') {
    header("Location: ../../index.php");
    exit;
}

require '../../conn/db.php';

/* =========================
   INPUT VALIDATION
========================= */
$search = trim($_GET['search'] ?? '');
$search = substr($search, 0, 50);

$category_id = $_GET['category_id'] ?? '';
$category_id = $category_id !== '' ? (int)$category_id : '';

$page  = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

/* =========================
   COUNT PRODUCTS
========================= */
$countSql = "
    SELECT COUNT(*)
    FROM products p
    WHERE p.status = 'active'
      AND p.product_name LIKE :search
";
$params = [':search' => "%$search%"];

if ($category_id !== '') {
    $countSql .= " AND p.category_id = :category_id";
    $params[':category_id'] = $category_id;
}

$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalProducts = (int)$countStmt->fetchColumn();
$totalPages = max(1, ceil($totalProducts / $limit));

/* =========================
   FETCH PRODUCTS
========================= */
$sql = "
    SELECT p.*, c.category_name
    FROM products p
    JOIN categories c ON p.category_id = c.category_id
    WHERE p.status = 'active'
      AND p.product_name LIKE :search
";

if ($category_id !== '') {
    $sql .= " AND p.category_id = :category_id";
}

$sql .= "
    ORDER BY p.product_name ASC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

if ($category_id !== '') {
    $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
}

$stmt->execute();
$products = $stmt->fetchAll();

/* =========================
   FETCH CATEGORIES
========================= */
$categories = $pdo->query("
    SELECT category_id, category_name
    FROM categories
    WHERE description NOT LIKE '[ARCHIVED]%' OR description IS NULL
")->fetchAll();

/* =========================
   EDIT PRODUCT VALIDATION
========================= */
$editProduct = null;

if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];

    if ($id <= 0) {
        $_SESSION['error'] = "Invalid product ID.";
        header("Location: inventory_products.php");
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT *
        FROM products
        WHERE product_id = ?
        LIMIT 1
    ");
    $stmt->execute([$id]);
    $editProduct = $stmt->fetch();

    if (!$editProduct) {
        $_SESSION['error'] = "Product not found.";
        header("Location: inventory_products.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory | Products</title>
    <link rel="stylesheet" href="../../assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="../../assets/css/products.css">
</head>
<body>

<div class="dashboard">
    <aside class="sidebar">
        <h2>Inventory Panel</h2>
        <ul>
            <li><a href="inventory_dashboard.php">Dashboard</a></li>
            <li class="active"><a href="inventory_products.php">Products</a></li>
            <li><a href="inventory_categories.php">Categories</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </aside>

    <main class="content">

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

        <!-- HEADER -->
        <div class="page-header">
            <h1>Product List</h1>
            <button class="add-btn" id="openModalBtn">+ Add Product</button>
        </div>

        <!-- SEARCH & FILTER -->
        <form method="GET" class="filter-bar">
            <input type="text"
                   name="search"
                   placeholder="Search product"
                   value="<?= htmlspecialchars($search) ?>">

            <select name="category_id">
                <option value="">All Categories</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['category_id'] ?>"
                        <?= $category_id === (int)$c['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['category_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Filter</button>
        </form>

        <!-- TABLE -->
        <table>
            <tr>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Action</th>
            </tr>

            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="5" class="empty">No products found.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($products as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['product_name']) ?></td>
                <td><?= htmlspecialchars($p['category_name']) ?></td>
                <td><?= number_format($p['price'], 2) ?></td>
                <td><?= (int)$p['quantity'] ?></td>
                <td>
                    <a href="inventory_products.php?edit=<?= $p['product_id'] ?>">Edit</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <!-- PAGINATION -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category_id=<?= $category_id ?>"
                   class="<?= $i === $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

        <!-- MODAL -->
        <?php include '../../includes/product_modal.php'; ?>

    </main>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("productModal");
    const openBtn = document.getElementById("openModalBtn");

    if (openBtn) {
        openBtn.onclick = () => modal.style.display = "block";
    }

    document.querySelectorAll(".modal .close").forEach(btn => {
        btn.onclick = () => btn.closest(".modal").style.display = "none";
    });

    window.onclick = e => {
        if (e.target.classList.contains("modal")) {
            e.target.style.display = "none";
        }
    };

    <?php if ($editProduct): ?>
        modal.style.display = "block";
    <?php endif; ?>
});
</script>

</body>
</html>
