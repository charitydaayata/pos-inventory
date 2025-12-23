<?php
session_start();
// wa;la niy labot na file
require '../../includes/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../index.php");
    exit;
}

require '../../conn/db.php';

/* FETCH PURCHASE ORDERS */
$pos = $pdo->query("
    SELECT 
        po.po_id,
        po.status,
        po.created_at,
        u.name AS requested_by_name,
        GROUP_CONCAT(
            CONCAT(p.product_name, ' (x', poi.quantity, ')')
            SEPARATOR ', '
        ) AS items
    FROM purchase_orders po
    JOIN users u ON po.requested_by = u.user_id
    JOIN purchase_order_items poi ON po.po_id = poi.po_id
    JOIN products p ON poi.product_id = p.product_id
    GROUP BY po.po_id
    ORDER BY po.created_at DESC
")->fetchAll();

/* FETCH PRODUCTS FOR MODAL */
$products = $pdo->query("
    SELECT product_id, product_name
    FROM products
    WHERE status='active'
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin | Purchase Orders</title>
    <link rel="stylesheet" href="../../assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="../../assets/css/purchase_orders.css">
    <!-- <link rel="stylesheet" href="../../assets/css/purchase_order.css"> -->
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
            <!-- <li class="active"><a href="purchase_orders.php">Purchase Orders</a></li> -->
            <li><a href="archives.php">Archives</a></li>
            <li><a href="users.php">Manage Users</a></li>
            <li><a href="audit_logs.php">Audit Logs</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </aside>

  <main class="content">

    <div class="page-header po-header">
        <h1>Purchase Orders</h1>
        <button class="add-btn po-btn" onclick="openModal()">+ Request PO</button>
    </div>

    <table class="po-table">
        <thead>
            <tr>
                <th>Items</th>
                <th>Status</th>
                <th>Requested By</th>
                <th>Date</th>
            </tr>
        </thead>

        <tbody>
        <?php if (count($pos) === 0): ?>
            <tr>
                <td colspan="4" class="empty-row">
                    No purchase orders found.
                </td>
            </tr>
        <?php endif; ?>

        <?php foreach ($pos as $po): ?>
            <tr>
                <td><?= htmlspecialchars($po['items']) ?></td>
                <td class="status <?= $po['status'] ?>">
                    <?= ucfirst($po['status']) ?>
                </td>
                <td><?= htmlspecialchars($po['requested_by_name']) ?></td>
                <td><?= date('M d, Y h:i A', strtotime($po['created_at'])) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php include '../../includes/purchase_order_modal.php'; ?>

</main>

</div>

<script>
function openModal() {
    document.getElementById('poModal').style.display = 'block';
}
function closeModal() {
    document.getElementById('poModal').style.display = 'none';
}
</script>

</body>
</html>
