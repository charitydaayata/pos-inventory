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
   FETCH ACTIVE CATEGORIES
========================= */
$categories = $pdo->query("
    SELECT *
    FROM categories
    WHERE description NOT LIKE '[ARCHIVED]%'
       OR description IS NULL
")->fetchAll();

/* =========================
   EDIT CATEGORY (VALIDATED)
========================= */
$editCategory = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];

    $stmt = $pdo->prepare("SELECT * FROM categories WHERE category_id = ?");
    $stmt->execute([$id]);
    $editCategory = $stmt->fetch();

    if (!$editCategory) {
        $_SESSION['error'] = "Category not found.";
        header("Location: categories.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin | Categories</title>

    <link rel="stylesheet" href="../../assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="../../assets/css/categories.css">
</head>
<body>

<div class="dashboard">

    <!-- =========================
         SIDEBAR
    ========================= -->
    <aside class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="products.php">Products</a></li>
            <li class="active"><a href="categories.php">Categories</a></li>
            <li><a href="stock_management.php">Stock Management</a></li>
            <!-- <li><a href="purchase_orders.php">Purchase Orders</a></li> -->
            <li><a href="archives.php">Archives</a></li>
            <li><a href="users.php">Manage Users</a></li>
            <li><a href="audit_logs.php">Audit Logs</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </aside>

    <!-- =========================
         MAIN CONTENT
    ========================= -->
    <main class="content">

        <div class="page-header">
            <h1>Manage Categories</h1>
            <button class="add-btn" id="openCategoryModal">+ Add Category</button>
        </div>

        <!-- =========================
             FLASH MESSAGES (DATA VALIDATION)
        ========================= -->
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

        <!-- =========================
             CATEGORIES TABLE
        ========================= -->
        <table>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Action</th>
            </tr>

            <?php if (empty($categories)): ?>
                <tr>
                    <td colspan="3" class="empty">
                        No categories found.
                    </td>
                </tr>
            <?php endif; ?>

            <?php foreach ($categories as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['category_name']) ?></td>
                <td><?= htmlspecialchars($c['description']) ?></td>
                <td>
                    <a href="categories.php?edit=<?= $c['category_id'] ?>">Edit</a> |
                    <a href="../../conn/Categories.php?archive=<?= $c['category_id'] ?>"
                       onclick="return confirm('Archive category?')">
                        Archive
                    </a> |
                    <a href="../../conn/Categories.php?delete=<?= $c['category_id'] ?>"
                       class="danger"
                       onclick="return confirm('⚠️ Permanently delete this category?')">
                        Delete
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <!-- =========================
             CATEGORY MODAL
        ========================= -->
        <?php include '../../includes/categories_modal.php'; ?>

    </main>
</div>

<!-- =========================
     MODAL SCRIPT
========================= -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("categoryModal");
    const btn = document.getElementById("openCategoryModal");
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
