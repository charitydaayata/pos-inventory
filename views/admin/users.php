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
   INPUT VALIDATION
========================= */
$search = trim($_GET['search'] ?? '');
$search = substr($search, 0, 50);

$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 10;
$offset = ($page - 1) * $limit;

/* =========================
   COUNT USERS
========================= */
$countStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM users u
    JOIN roles r ON u.role_id = r.role_id
    WHERE u.status = 'active'
      AND (u.name LIKE :search OR u.username LIKE :search)
");
$countStmt->execute([':search' => "%$search%"]);
$totalUsers = (int)$countStmt->fetchColumn();
$totalPages = max(1, ceil($totalUsers / $limit));

/* =========================
   FETCH USERS
========================= */
$stmt = $pdo->prepare("
    SELECT u.*, r.role_name
    FROM users u
    JOIN roles r ON u.role_id = r.role_id
    WHERE u.status = 'active'
      AND (u.name LIKE :search OR u.username LIKE :search)
    ORDER BY u.name ASC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll();

/* =========================
   FETCH ROLES
========================= */
$roles = $pdo->query("SELECT * FROM roles")->fetchAll();

/* =========================
   FETCH USER FOR EDIT (VALIDATED)
========================= */
$editUser = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id=?");
    $stmt->execute([$id]);
    $editUser = $stmt->fetch();

    if (!$editUser) {
        $_SESSION['error'] = "User not found.";
        header("Location: users.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin | Users</title>
    <link rel="stylesheet" href="../../assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="../../assets/css/users.css">
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
            <li><a href="archives.php">Archives</a></li>
            <li class="active"><a href="users.php">Manage Users</a></li>
            <li><a href="audit_logs.php">Audit Logs</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </aside>

    <main class="content">

        <!-- HEADER -->
        <div class="page-header">
            <h1>Manage Users</h1>
            <button class="add-btn" id="openUserModal">+ Add User</button>
        </div>

        <!-- FLASH MESSAGES -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?= htmlspecialchars($_SESSION['success']) ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- SEARCH -->
        <form method="GET" class="search-form">
            <input type="text"
                   name="search"
                   placeholder="Search name or username"
                   value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>

        <br>

        <!-- TABLE -->
        <table>
            <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Role</th>
                <th>Action</th>
            </tr>

            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="4" class="empty">No users found.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td><?= htmlspecialchars($u['role_name']) ?></td>
                <td>
                    <a href="users.php?edit=<?= $u['user_id'] ?>">Edit</a> |
                    <a href="../../conn/Users.php?archive=<?= $u['user_id'] ?>"
                       onclick="return confirm('Archive this user?')">Archive</a> |
                    <a href="../../conn/Users.php?delete=<?= $u['user_id'] ?>"
                       class="danger"
                       onclick="return confirm('Delete user permanently?')">Delete</a>
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

        <?php include '../../includes/users_modal.php'; ?>

    </main>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("userModal");
    const btn   = document.getElementById("openUserModal");
    const close = modal.querySelector(".close");

    btn.onclick = () => modal.style.display = "block";
    close.onclick = () => modal.style.display = "none";

    <?php if ($editUser): ?>
        modal.style.display = "block";
    <?php endif; ?>

    window.onclick = e => {
        if (e.target === modal) modal.style.display = "none";
    };
});
</script>

</body>
</html>
