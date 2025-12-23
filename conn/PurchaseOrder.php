<?php
session_start();
require 'db.php';
require 'audit_helper.php';

if (isset($_POST['request_po'])) {

    $pdo->beginTransaction();

    try {
        // 1. Create PO header
        $stmt = $pdo->prepare("
            INSERT INTO purchase_orders (supplier_id, requested_by)
            VALUES (?, ?)
        ");
        $stmt->execute([
            $_POST['supplier_id'],
            $_SESSION['user_id']
        ]);

        $po_id = $pdo->lastInsertId();

        // 2. Insert PO items
        foreach ($_POST['items'] as $product_id => $qty) {
            if ($qty > 0) {
                $stmt = $pdo->prepare("
                    INSERT INTO purchase_order_items (po_id, product_id, quantity)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$po_id, $product_id, $qty]);
            }
        }

        // 3. Audit log
        logAction(
            $pdo,
            $_SESSION['user_id'],
            "Created Purchase Order #$po_id"
        );

        $pdo->commit();
        header("Location: ../views/admin/purchase_orders.php");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("PO Error: " . $e->getMessage());
    }
}
