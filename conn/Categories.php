<?php
session_start();

require 'db.php';
require 'audit_helper.php';

/* =========================
   ROLE CHECK
========================= */
if (
    !isset($_SESSION['user_id']) ||
    !in_array($_SESSION['role'], ['Admin', 'Inventory Manager'])
) {
    header("Location: ../index.php");
    exit;
}

/* =========================
   ROLE-BASED REDIRECT
========================= */
$redirect = ($_SESSION['role'] === 'Inventory Manager')
    ? '../views/inventory/inventory_categories.php'
    : '../views/admin/categories.php';

/* =========================
   ADD CATEGORY (VALIDATED)
========================= */
if (isset($_POST['add_category'])) {

    $name = trim($_POST['category_name'] ?? '');
    $desc = trim($_POST['description'] ?? '');

    /* BASIC VALIDATION */
    if ($name === '') {
        $_SESSION['error'] = "Category name is required.";
        header("Location: $redirect");
        exit;
    }

    /* LENGTH VALIDATION */
    if (strlen($name) > 100) {
        $_SESSION['error'] = "Category name is too long.";
        header("Location: $redirect");
        exit;
    }

    /* DUPLICATE CHECK */
    $check = $pdo->prepare("
        SELECT COUNT(*) FROM categories WHERE category_name = ?
    ");
    $check->execute([$name]);

    if ($check->fetchColumn() > 0) {
        $_SESSION['error'] = "Category already exists.";
        header("Location: $redirect");
        exit;
    }

    /* INSERT */
    $stmt = $pdo->prepare("
        INSERT INTO categories (category_name, description)
        VALUES (?, ?)
    ");
    $stmt->execute([$name, $desc]);

    logAction(
        $pdo,
        $_SESSION['user_id'],
        'Added category: ' . $name
    );

    $_SESSION['success'] = "Category added successfully.";
    header("Location: $redirect");
    exit;
}

/* =========================
   UPDATE CATEGORY (VALIDATED)
========================= */
if (isset($_POST['update_category'])) {

    $id   = (int)($_POST['category_id'] ?? 0);
    $name = trim($_POST['category_name'] ?? '');
    $desc = trim($_POST['description'] ?? '');

    if ($id <= 0 || $name === '') {
        $_SESSION['error'] = "Invalid category data.";
        header("Location: $redirect");
        exit;
    }

    /* CATEGORY EXISTS */
    $exists = $pdo->prepare("
        SELECT COUNT(*) FROM categories WHERE category_id = ?
    ");
    $exists->execute([$id]);

    if ($exists->fetchColumn() == 0) {
        $_SESSION['error'] = "Category not found.";
        header("Location: $redirect");
        exit;
    }

    /* DUPLICATE NAME (EXCEPT SELF) */
    $check = $pdo->prepare("
        SELECT COUNT(*) FROM categories
        WHERE category_name = ? AND category_id != ?
    ");
    $check->execute([$name, $id]);

    if ($check->fetchColumn() > 0) {
        $_SESSION['error'] = "Another category with this name already exists.";
        header("Location: $redirect");
        exit;
    }

    /* UPDATE */
    $stmt = $pdo->prepare("
        UPDATE categories
        SET category_name = ?, description = ?
        WHERE category_id = ?
    ");
    $stmt->execute([$name, $desc, $id]);

    logAction(
        $pdo,
        $_SESSION['user_id'],
        'Updated category: ' . $name
    );

    $_SESSION['success'] = "Category updated successfully.";
    header("Location: $redirect");
    exit;
}

/* =========================
   ARCHIVE CATEGORY (VALIDATED)
========================= */
if (isset($_GET['archive'])) {

    $id = (int)$_GET['archive'];

    $stmt = $pdo->prepare("
        SELECT category_name FROM categories WHERE category_id = ?
    ");
    $stmt->execute([$id]);
    $category = $stmt->fetch();

    if (!$category) {
        $_SESSION['error'] = "Category not found.";
        header("Location: $redirect");
        exit;
    }

    $pdo->prepare("
        UPDATE categories
        SET description = CONCAT('[ARCHIVED] ', COALESCE(description, ''))
        WHERE category_id = ?
    ")->execute([$id]);

    logAction(
        $pdo,
        $_SESSION['user_id'],
        'Archived category: ' . $category['category_name']
    );

    $_SESSION['success'] = "Category archived successfully.";
    header("Location: $redirect");
    exit;
}

/* =========================
   DELETE CATEGORY (SAFE + VALIDATED)
========================= */
if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    /* CATEGORY EXISTS */
    $stmt = $pdo->prepare("
        SELECT category_name FROM categories WHERE category_id = ?
    ");
    $stmt->execute([$id]);
    $category = $stmt->fetch();

    if (!$category) {
        $_SESSION['error'] = "Category not found.";
        header("Location: $redirect");
        exit;
    }

    /* CHECK IF PRODUCTS EXIST */
    $check = $pdo->prepare("
        SELECT COUNT(*) FROM products WHERE category_id = ?
    ");
    $check->execute([$id]);

    if ($check->fetchColumn() > 0) {
        $_SESSION['error'] = "Cannot delete category. Products are still assigned to it.";
        header("Location: $redirect");
        exit;
    }

    /* DELETE */
    $pdo->prepare("
        DELETE FROM categories WHERE category_id = ?
    ")->execute([$id]);

    logAction(
        $pdo,
        $_SESSION['user_id'],
        'Deleted category: ' . $category['category_name']
    );

    $_SESSION['success'] = "Category deleted successfully.";
    header("Location: $redirect");
    exit;
}

/* =========================
   FALLBACK
========================= */
$_SESSION['error'] = "Invalid category request.";
header("Location: $redirect");
exit;
