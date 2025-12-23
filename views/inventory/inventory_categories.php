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

$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 10;
$offset = ($page - 1) * $limit;

/* =========================
   COUNT CATEGORIES
========================= */
$countStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM categories
    WHERE (description NOT LIKE '[ARCHIVED]%' OR description IS NULL)
      AND category_name LIKE :search
");
$countStmt->execute([':search' => "%$search%"]);
$totalCategories = (int)$countStmt->fetchColumn();
$totalPages      = max(1, ceil($totalCategories / $limit));

/* =========================
   FETCH CATEGORIES
========================= */
$stmt = $pdo->prepare("
    SELECT *
    FROM categories
    WHERE (description NOT LIKE '[ARCHIVED]%' OR description IS NULL)
      AND category_name LIKE :search
    ORDER BY category_name ASC
    LIMIT :limit OFFSET :offset
");

$stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$categories = $stmt->fetchAll();

/* =========================
   FETCH CATEGORY FOR EDIT (VALIDATED)
========================= */
$editCategory = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];

    $stmt = $pdo->prepare("SELECT * FROM categories WHERE category_id=?");
    $stmt->execute([$id]);
    $editCategory = $stmt->fetch();

    if (!$editCategory) {
        $_SESSION['error'] = "Category not found.";
        header("Location: inventory_categories.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory | Categories</title>

    <link rel="stylesheet" href="../../assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="../../assets/css/categories.css">
    <link rel="stylesheet" href="../../assets/css/inventory_category_search.css">
</head>
<body>

<div class="dashboard">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <h2>Inventory Panel</h2>
        <ul>
            <li><a href="inventory_dashboard.php">Dashboard</a></li>
            <li><a href="inventory_products.php">Products</a></li>
            <li class="active"><a href="inventory_categories.php">Categories</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </aside>

    <!-- CONTENT -->
    <main class="content">

        <!-- HEADER -->
        <div class="page-header">
            <h1>Manage Categories</h1>
            <button class="add-btn" id="openCategoryModal">+ Add Category</button>
        </div>

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

        <!-- SEARCH -->
        <form method="GET" class="filter-bar">
            <input type="text"
                   name="search"
                   placeholder="Search category"
                   value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>

        <!-- TABLE -->
        <table>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Action</th>
            </tr>

            <?php if (empty($categories)): ?>
                <tr>
                    <td colspan="3" class="empty">No categories found.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($categories as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['category_name']) ?></td>
                <td><?= htmlspecialchars($c['description']) ?></td>
                <td>
                    <a href="inventory_categories.php?edit=<?= $c['category_id'] ?>">
                        Edit
                    </a>
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

        <!-- MODAL -->
        <?php include '../../includes/categories_modal.php'; ?>

    </main>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("categoryModal");
    const btn   = document.getElementById("openCategoryModal");
    const close = modal.querySelector(".close");

    btn.onclick = () => modal.style.display = "block";
    close.onclick = () => modal.style.display = "none";

    window.onclick = e => {
        if (e.target === modal) modal.style.display = "none";
    };

    <?php if ($editCategory): ?>
        modal.style.display = "block";
    <?php endif; ?>
});
</script>

</body>
</html>
