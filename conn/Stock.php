<?php
session_start();

require 'db.php';
require 'audit_helper.php';

/* =========================
   ROLE CHECK
========================= */
if (
    !isset($_SESSION['user_id']) ||
    !in_array($_SESSION['role'], ['Admin', 'Warehouse Manager'])
) {
    header("Location: ../index.php");
    exit;
}

/* =========================
   UPDATE STOCK (WITH DATA VALIDATION)
========================= */
if (isset($_POST['update_stock'])) {

    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity   = (int)($_POST['quantity'] ?? -1);
    $threshold  = (int)($_POST['threshold'] ?? -1);

    /* BASIC VALIDATION */
    if ($product_id <= 0 || $quantity < 0 || $threshold < 0) {
        $_SESSION['error'] = "Invalid stock values provided.";
        goto redirect;
    }

    /* PRODUCT EXISTS CHECK */
    $stmt = $pdo->prepare("
        SELECT product_name
        FROM products
        WHERE product_id = ?
    ");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if (!$product) {
        $_SESSION['error'] = "Product not found.";
        goto redirect;
    }

    /* BUSINESS RULE VALIDATION */
    if ($threshold > $quantity) {
        $_SESSION['error'] = "Stock threshold cannot be greater than quantity.";
        goto redirect;
    }

    /* UPDATE STOCK */
    $stmt = $pdo->prepare("
        UPDATE products
        SET quantity = ?, stock_threshold = ?
        WHERE product_id = ?
    ");
    $stmt->execute([$quantity, $threshold, $product_id]);

    /* AUDIT LOG */
    logAction(
        $pdo,
        $_SESSION['user_id'],
        'Updated stock: ' . $product['product_name'] .
        ' (Qty: ' . $quantity .
        ', Threshold: ' . $threshold . ')'
    );

    $_SESSION['success'] = "Stock updated successfully.";

redirect:
    /* ROLE-BASED REDIRECT */
    if ($_SESSION['role'] === 'Warehouse Manager') {
        header("Location: ../views/warehouse/stock_management.php");
    } else {
        header("Location: ../views/admin/stock_management.php");
    }
    exit;
}

/* =========================
   FALLBACK
========================= */
$_SESSION['error'] = "Invalid stock request.";

if ($_SESSION['role'] === 'Warehouse Manager') {
    header("Location: ../views/warehouse/stock_management.php");
} else {
    header("Location: ../views/admin/stock_management.php");
}
exit;
