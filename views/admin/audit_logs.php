<?php
session_start();
require '../../includes/auth.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../index.php");
    exit;
}

require '../../conn/db.php';

/* ===============================
   PAGINATION
================================ */
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

/* ===============================
   SEARCH & FILTER
================================ */
$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? '';

$where = [];
$params = [];

/* SEARCH */
if (!empty($search)) {
    $where[] = "(a.action LIKE ? OR u.name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

/* FILTER */
if (!empty($filter)) {
    $where[] = "a.action LIKE ?";
    $params[] = "%$filter%";
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

/* ===============================
   COUNT TOTAL ROWS
================================ */
$countStmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM audit_logs a
    JOIN users u ON a.user_id = u.user_id
    $whereSQL
");
$countStmt->execute($params);
$totalRows = $countStmt->fetchColumn();
$totalPages = ceil($totalRows / $limit);

/* ===============================
   FETCH LOGS
================================ */
$stmt = $pdo->prepare("
    SELECT a.*, u.name
    FROM audit_logs a
    JOIN users u ON a.user_id = u.user_id
    $whereSQL
    ORDER BY a.log_date DESC
    LIMIT $limit OFFSET $offset
");
$stmt->execute($params);
$logs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin | Audit Logs</title>

    <link rel="stylesheet" href="../../assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="../../assets/css/audit_logs.css">
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
            <li class="active"><a href="audit_logs.php">Audit Logs</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </aside>

    <main class="content">
        <h1>Audit Logs</h1>

        <!-- SEARCH & FILTER -->
        <form method="GET" class="log-filters">
            <input
                type="text"
                name="search"
                placeholder="Search user or action..."
                value="<?= htmlspecialchars($search) ?>"
            >

            <select name="filter">
                <option value="">All Actions</option>
                <option value="Added" <?= $filter === 'Added' ? 'selected' : '' ?>>Added</option>
                <option value="Updated" <?= $filter === 'Updated' ? 'selected' : '' ?>>Updated</option>
                <option value="Archived" <?= $filter === 'Archived' ? 'selected' : '' ?>>Archived</option>
                <option value="Restored" <?= $filter === 'Restored' ? 'selected' : '' ?>>Restored</option>
                <option value="Deleted" <?= $filter === 'Deleted' ? 'selected' : '' ?>>Deleted</option>
            </select>

            <button type="submit">Apply</button>
        </form>

        <!-- TABLE -->
        <table>
            <tr>
                <th>User</th>
                <th>Action</th>
                <th>Date</th>
            </tr>

            <?php if (count($logs) === 0): ?>
                <tr>
                    <td colspan="3" style="text-align:center;">No audit logs found.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($logs as $log): ?>
            <tr>
                <td><?= htmlspecialchars($log['name']) ?></td>
                <td><?= htmlspecialchars($log['action']) ?></td>
                <td><?= $log['log_date'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <!-- PAGINATION -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&filter=<?= urlencode($filter) ?>">« Prev</a>
            <?php endif; ?>

            <span>Page <?= $page ?> of <?= $totalPages ?></span>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&filter=<?= urlencode($filter) ?>">Next »</a>
            <?php endif; ?>
        </div>

    </main>
</div>

</body>
</html>
