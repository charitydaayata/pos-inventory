<?php
session_start();

require 'db.php';
require 'audit_helper.php';

/* =========================
   ROLE GUARD
========================= */
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['Admin', 'Inventory Manager'])) {
    header("Location: ../index.php");
    exit;
}

/* =========================
   ROLE-BASED REDIRECT
========================= */
function redirectAfterProductAction() {
    if ($_SESSION['role'] === 'Inventory Manager') {
        header("Location: ../views/inventory/inventory_products.php");
    } else {
        header("Location: ../views/admin/products.php");
    }
    exit;
}

/* =========================
   ADD PRODUCT
========================= */
if (isset($_POST['add_product'])) {

    $name     = trim($_POST['product_name'] ?? '');
    $category = intval($_POST['category_id'] ?? 0);
    $price    = floatval($_POST['price'] ?? -1);
    $qty      = intval($_POST['quantity'] ?? -1);
    $barcode  = trim($_POST['barcode'] ?? '');

    /* BASIC VALIDATION */
    if ($name === '' || $category <= 0 || $price < 0 || $qty < 0) {
        $_SESSION['error'] = "Invalid product data. Please check inputs.";
        redirectAfterProductAction();
    }

    /* CATEGORY EXISTS */
    $checkCat = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE category_id=?");
    $checkCat->execute([$category]);
    if ($checkCat->fetchColumn() == 0) {
        $_SESSION['error'] = "Selected category does not exist.";
        redirectAfterProductAction();
    }

    /* BARCODE UNIQUE */
    if ($barcode !== '') {
        $checkBarcode = $pdo->prepare("SELECT COUNT(*) FROM products WHERE barcode=?");
        $checkBarcode->execute([$barcode]);
        if ($checkBarcode->fetchColumn() > 0) {
            $_SESSION['error'] = "Barcode already exists.";
            redirectAfterProductAction();
        }
    }

    /* INSERT */
    $stmt = $pdo->prepare("
        INSERT INTO products (product_name, category_id, price, quantity, barcode, status)
        VALUES (?, ?, ?, ?, ?, 'active')
    ");
    $stmt->execute([$name, $category, $price, $qty, $barcode]);

    logAction(
        $pdo,
        $_SESSION['user_id'],
        'Added product: ' . $name
    );

    $_SESSION['success'] = "Product added successfully.";
    redirectAfterProductAction();
}

/* =========================
   UPDATE PRODUCT
========================= */
if (isset($_POST['update_product'])) {

    $id       = intval($_POST['product_id'] ?? 0);
    $name     = trim($_POST['product_name'] ?? '');
    $category = intval($_POST['category_id'] ?? 0);
    $price    = floatval($_POST['price'] ?? -1);
    $qty      = intval($_POST['quantity'] ?? -1);
    $barcode  = trim($_POST['barcode'] ?? '');

    if ($id <= 0 || $name === '' || $category <= 0 || $price < 0 || $qty < 0) {
        $_SESSION['error'] = "Invalid product update data.";
        redirectAfterProductAction();
    }

    /* PRODUCT EXISTS */
    $checkProd = $pdo->prepare("SELECT COUNT(*) FROM products WHERE product_id=?");
    $checkProd->execute([$id]);
    if ($checkProd->fetchColumn() == 0) {
        $_SESSION['error'] = "Product not found.";
        redirectAfterProductAction();
    }

    /* BARCODE UNIQUE (EXCEPT SELF) */
    if ($barcode !== '') {
        $checkBarcode = $pdo->prepare("
            SELECT COUNT(*) FROM products
            WHERE barcode=? AND product_id != ?
        ");
        $checkBarcode->execute([$barcode, $id]);
        if ($checkBarcode->fetchColumn() > 0) {
            $_SESSION['error'] = "Barcode already exists.";
            redirectAfterProductAction();
        }
    }

    /* UPDATE */
    $stmt = $pdo->prepare("
        UPDATE products
        SET product_name=?, category_id=?, price=?, quantity=?, barcode=?
        WHERE product_id=?
    ");
    $stmt->execute([$name, $category, $price, $qty, $barcode, $id]);

    logAction(
        $pdo,
        $_SESSION['user_id'],
        'Updated product: ' . $name
    );

    $_SESSION['success'] = "Product updated successfully.";
    redirectAfterProductAction();
}

/* =========================
   ARCHIVE PRODUCT
========================= */
if (isset($_GET['archive'])) {

    $id = intval($_GET['archive']);

    $stmt = $pdo->prepare("SELECT product_name FROM products WHERE product_id=?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        $_SESSION['error'] = "Product not found.";
        redirectAfterProductAction();
    }

    $pdo->prepare("UPDATE products SET status='inactive' WHERE product_id=?")
        ->execute([$id]);

    logAction(
        $pdo,
        $_SESSION['user_id'],
        'Archived product: ' . $product['product_name']
    );

    $_SESSION['success'] = "Product archived successfully.";
    redirectAfterProductAction();
}

/* =========================
   DELETE PRODUCT
========================= */
if (isset($_GET['delete'])) {

    $id = intval($_GET['delete']);

    $stmt = $pdo->prepare("SELECT product_name FROM products WHERE product_id=?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        $_SESSION['error'] = "Product not found.";
        redirectAfterProductAction();
    }

    $pdo->prepare("DELETE FROM products WHERE product_id=?")
        ->execute([$id]);

    logAction(
        $pdo,
        $_SESSION['user_id'],
        'Deleted product: ' . $product['product_name']
    );

    $_SESSION['success'] = "Product deleted successfully.";
    redirectAfterProductAction();
}

/* =========================
   FALLBACK
========================= */
$_SESSION['error'] = "Invalid product request.";
redirectAfterProductAction();
