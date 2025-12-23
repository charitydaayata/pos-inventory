<?php
session_start();

require 'db.php';
require 'audit_helper.php';

/* ROLE CHECK */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit;
}

/* =======================
   RESTORE PRODUCT
======================= */
if (isset($_GET['restore_product'])) {

    $stmt = $pdo->prepare("SELECT product_name FROM products WHERE product_id=?");
    $stmt->execute([$_GET['restore_product']]);
    $product = $stmt->fetch();

    $pdo->prepare("UPDATE products SET status='active' WHERE product_id=?")
        ->execute([$_GET['restore_product']]);

    logAction(
        $pdo,
        $_SESSION['user_id'],
        'Restored product: ' . $product['product_name']
    );

    /* FLASH MESSAGE */
    $_SESSION['success'] = "Product restored successfully.";

    header("Location: ../views/admin/archives.php?tab=products");
    exit;
}

/* =======================
   RESTORE CATEGORY
======================= */
if (isset($_GET['restore_category'])) {

    $stmt = $pdo->prepare("SELECT category_name FROM categories WHERE category_id=?");
    $stmt->execute([$_GET['restore_category']]);
    $category = $stmt->fetch();

    $pdo->prepare("
        UPDATE categories
        SET description = REPLACE(description, '[ARCHIVED] ', '')
        WHERE category_id=?
    ")->execute([$_GET['restore_category']]);

    logAction(
        $pdo,
        $_SESSION['user_id'],
        'Restored category: ' . $category['category_name']
    );

    /* FLASH MESSAGE */
    $_SESSION['success'] = "Category restored successfully.";

    header("Location: ../views/admin/archives.php?tab=categories");
    exit;
}

/* =======================
   RESTORE USER
======================= */
if (isset($_GET['restore_user'])) {

    $stmt = $pdo->prepare("SELECT username FROM users WHERE user_id=?");
    $stmt->execute([$_GET['restore_user']]);
    $user = $stmt->fetch();

    $pdo->prepare("UPDATE users SET status='active' WHERE user_id=?")
        ->execute([$_GET['restore_user']]);

    logAction(
        $pdo,
        $_SESSION['user_id'],
        'Restored user: ' . $user['username']
    );

    /* FLASH MESSAGE */
    $_SESSION['success'] = "User restored successfully.";

    header("Location: ../views/admin/archives.php?tab=users");
    exit;
}

/* =======================
   FALLBACK (INVALID REQUEST)
======================= */
$_SESSION['error'] = "Invalid restore request.";
header("Location: ../views/admin/archives.php");
exit;
