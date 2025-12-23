<?php
session_start();
require 'db.php';

/* =========================
   REQUEST + SESSION VALIDATION
========================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/cashier/pos.php");
    exit;
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Cashier') {
    header("Location: ../index.php");
    exit;
}

/* =========================
   INPUT VALIDATION
========================= */
if (!isset($_POST['cart_data']) || trim($_POST['cart_data']) === '') {
    $_SESSION['error'] = "Cart is empty.";
    header("Location: ../views/cashier/pos.php");
    exit;
}

$payment_method = $_POST['payment_method'] ?? 'cash';
$amount_paid    = isset($_POST['amount_paid']) ? (float)$_POST['amount_paid'] : 0;
$user_id        = (int)$_SESSION['user_id'];

$allowedPayments = ['cash', 'gcash', 'card'];
if (!in_array($payment_method, $allowedPayments)) {
    $_SESSION['error'] = "Invalid payment method.";
    header("Location: ../views/cashier/pos.php");
    exit;
}

/* =========================
   DECODE CART
========================= */
$cart = json_decode($_POST['cart_data'], true);

if (!is_array($cart) || empty($cart)) {
    $_SESSION['error'] = "Invalid cart data.";
    header("Location: ../views/cashier/pos.php");
    exit;
}

try {
    $pdo->beginTransaction();

    $total = 0;
    $items = [];

    /* =========================
       VALIDATE PRODUCTS & STOCK
    ========================= */
    foreach ($cart as $product_id => $item) {

        $product_id = (int)$product_id;
        $qty = (int)($item['qty'] ?? 0);

        if ($product_id <= 0 || $qty <= 0) {
            throw new Exception("Invalid cart item.");
        }

        /* LOCK PRODUCT ROW */
        $stmt = $pdo->prepare("
            SELECT product_name, price, quantity
            FROM products
            WHERE product_id = ?
              AND status = 'active'
            FOR UPDATE
        ");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            throw new Exception("Product not found.");
        }

        if ($qty > $product['quantity']) {
            throw new Exception(
                "Insufficient stock for {$product['product_name']}."
            );
        }

        $subtotal = $qty * (float)$product['price'];
        $total += $subtotal;

        $items[] = [
            'product_id' => $product_id,
            'qty'        => $qty,
            'price'      => (float)$product['price']
        ];
    }

    if ($total <= 0) {
        throw new Exception("Invalid total amount.");
    }

    /* =========================
       PAYMENT VALIDATION
    ========================= */
    if ($payment_method === 'cash') {
        if ($amount_paid < $total) {
            throw new Exception("Insufficient cash payment.");
        }
    }

    /* =========================
       INSERT SALE
    ========================= */
    $stmt = $pdo->prepare("
        INSERT INTO sales (user_id, total_amount, payment_method)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([
        $user_id,
        $total,
        $payment_method
    ]);

    $sale_id = $pdo->lastInsertId();

    /* =========================
       INSERT ITEMS + DEDUCT STOCK
    ========================= */
    foreach ($items as $item) {

        $pdo->prepare("
            INSERT INTO sales_items (sale_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ")->execute([
            $sale_id,
            $item['product_id'],
            $item['qty'],
            $item['price']
        ]);

        $pdo->prepare("
            UPDATE products
            SET quantity = quantity - ?
            WHERE product_id = ?
        ")->execute([
            $item['qty'],
            $item['product_id']
        ]);
    }

    $pdo->commit();

    $_SESSION['success'] = "Sale completed successfully.";
    header("Location: ../views/cashier/pos.php?success=1");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();

    $_SESSION['error'] = $e->getMessage();
    header("Location: ../views/cashier/pos.php");
    exit;
}
