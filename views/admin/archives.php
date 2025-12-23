<?php
session_start();

require '../../includes/auth.php';

/* ROLE CHECK */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../index.php");
    exit;
}

require '../../conn/db.php';

/* =======================
   DATA VALIDATION
======================= */

/* VALID TAB WHITELIST */
$allowedTabs = ['products', 'categories', 'users'];
$tab = $_GET['tab'] ?? 'products';

if (!in_array($tab, $allowedTabs)) {
    $tab = 'products';
}

/* SEARCH VALIDATION */
$search = trim($_GET['search'] ?? '');
$search = substr($search, 0, 50); // limit length

/* PAGINATION VALIDATION */
$page   = max(1, intval($_GET['page'] ?? 1));
$limit  = 10;
$offset = ($page - 1) * $limit;

/* =======================
   PRODUCTS ARCHIVE
======================= */
if ($tab === 'products') {

    $stmt = $pdo->prepare("
        SELECT p.*, c.category_name
        FROM products p
        JOIN categories c ON p.category_id = c.category_id
        WHERE p.status = 'inactive'
          AND p.product_name LIKE ?
        LIMIT $limit OFFSET $offset
    ");
    $stmt->execute(["%$search%"]);
    $data = $stmt->fetchAll();

    $count = $pdo->prepare("
        SELECT COUNT(*)
        FROM products
        WHERE status='inactive'
          AND product_name LIKE ?
    ");
    $count->execute(["%$search%"]);
}

/* =======================
   CATEGORIES ARCHIVE
======================= */
if ($tab === 'categories') {

    $stmt = $pdo->prepare("
        SELECT *
        FROM categories
        WHERE description LIKE '[ARCHIVED]%'
          AND category_name LIKE ?
        LIMIT $limit OFFSET $offset
    ");
    $stmt->execute(["%$search%"]);
    $data = $stmt->fetchAll();

    $count = $pdo->prepare("
        SELECT COUNT(*)
        FROM categories
        WHERE description LIKE '[ARCHIVED]%'
          AND category_name LIKE ?
    ");
    $count->execute(["%$search%"]);
}

/* =======================
   USERS ARCHIVE
======================= */
if ($tab === 'users') {

    $stmt = $pdo->prepare("
        SELECT *
        FROM users
        WHERE status='inactive'
          AND (name LIKE ? OR username LIKE ?)
        LIMIT $limit OFFSET $offset
    ");
    $stmt->execute(["%$search%", "%$search%"]);
    $data = $stmt->fetchAll();

    $count = $pdo->prepare("
        SELECT COUNT(*)
        FROM users
        WHERE status='inactive'
          AND (name LIKE ? OR username LIKE ?)
    ");
    $count->execute(["%$search%", "%$search%"]);
}

$totalRows  = $count->fetchColumn();
$totalPages = max(1, ceil($totalRows / $limit));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin | Archives</title>

    <link rel="stylesheet" href="../../assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="../../assets/css/archives.css">
</head>
<body>

<div class="dashboard">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="products.php">Products</a></li>
            <li><a href="categories.php">Categories</a></li>
            <li><a href="stock_management.php">Stock Management</a></li>
            <li class="active"><a href="archives.php">Archives</a></li>
            <li><a href="users.php">Manage Users</a></li>
            <li><a href="audit_logs.php">Audit Logs</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </aside>

    <main class="content">

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

        <!-- TABS -->
        <div class="archive-tabs">
            <a href="?tab=products"   class="<?= $tab==='products'?'active':'' ?>">Products</a>
            <a href="?tab=categories" class="<?= $tab==='categories'?'active':'' ?>">Categories</a>
            <a href="?tab=users"      class="<?= $tab==='users'?'active':'' ?>">Users</a>
        </div>

        <!-- SEARCH -->
        <form method="GET" class="search-bar">
            <input type="hidden" name="tab" value="<?= $tab ?>">
            <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>

        <!-- TABLE -->
        <table>
            <tr>
                <?php if ($tab === 'products'): ?>
                    <th>Name</th><th>Category</th><th>Action</th>
                <?php elseif ($tab === 'categories'): ?>
                    <th>Name</th><th>Action</th>
                <?php else: ?>
                    <th>Name</th><th>Username</th><th>Action</th>
                <?php endif; ?>
            </tr>

            <?php if (empty($data)): ?>
                <tr>
                    <td colspan="3" style="text-align:center; padding:20px;">
                        No archived records found.
                    </td>
                </tr>
            <?php endif; ?>

            <?php foreach ($data as $row): ?>
            <tr>
                <?php if ($tab === 'products'): ?>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td><?= htmlspecialchars($row['category_name']) ?></td>
                    <td>
                        <a href="/pos-inventory/conn/Archives.php?restore_product=<?= $row['product_id'] ?>">
                            Restore
                        </a>
                    </td>

                <?php elseif ($tab === 'categories'): ?>
                    <td><?= htmlspecialchars($row['category_name']) ?></td>
                    <td>
                        <a href="/pos-inventory/conn/Archives.php?restore_category=<?= $row['category_id'] ?>">
                            Restore
                        </a>
                    </td>

                <?php else: ?>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td>
                        <a href="/pos-inventory/conn/Archives.php?restore_user=<?= $row['user_id'] ?>">
                            Restore
                        </a>
                    </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </table>

        <!-- PAGINATION -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?tab=<?= $tab ?>&search=<?= urlencode($search) ?>&page=<?= $page-1 ?>">Prev</a>
            <?php endif; ?>

            <span>Page <?= $page ?> of <?= $totalPages ?></span>

            <?php if ($page < $totalPages): ?>
                <a href="?tab=<?= $tab ?>&search=<?= urlencode($search) ?>&page=<?= $page+1 ?>">Next</a>
            <?php endif; ?>
        </div>

    </main>
</div>

</body>
</html>
