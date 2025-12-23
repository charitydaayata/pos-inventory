<?php
session_start();
require '../../includes/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Cashier') {
    header("Location: ../../index.php");
    exit;
}

require '../../conn/db.php';

/* SEARCH */
$search = $_GET['search'] ?? '';

$stmt = $pdo->prepare("
    SELECT product_id, product_name, price, quantity
    FROM products
    WHERE status='active'
      AND quantity > 0
      AND product_name LIKE :search
    ORDER BY product_name ASC
");
$stmt->execute([':search' => "%$search%"]);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cashier | POS</title>
    <link rel="stylesheet" href="../../assets/css/cashier_dashboard.css">
    <link rel="stylesheet" href="../../assets/css/pos.css">
</head>
<body>

<div class="dashboard">

<header class="topbar">
    <h2>Cashier Panel</h2>
    <div class="top-actions">
        <a href="dashboard.php" class="back-btn">← Back</a>
        <a href="../../logout.php" class="logout-btn">Logout</a>
    </div>
</header>

<main class="content">

<!-- =========================
     FLASH MESSAGES (FIX)
========================= -->
<?php if (isset($_SESSION['error'])): ?>
    <div class="pos-error">
        <?= htmlspecialchars($_SESSION['error']) ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="pos-success">
        <?= htmlspecialchars($_SESSION['success']) ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<!-- SEARCH -->
<form method="GET" class="pos-search">
    <input type="text" name="search" placeholder="Search product..."
           value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>
</form>

<h3>Products</h3>

<table class="pos-table">
<tr>
    <th>Product</th>
    <th>Price</th>
    <th>Stock</th>
    <th>Add</th>
</tr>

<?php foreach ($products as $p): ?>
<tr>
    <td><?= htmlspecialchars($p['product_name']) ?></td>
    <td><?= number_format($p['price'], 2) ?></td>
    <td><?= (int)$p['quantity'] ?></td>
    <td>
        <button type="button"
            onclick="addToCart(
                <?= $p['product_id'] ?>,
                '<?= htmlspecialchars($p['product_name'], ENT_QUOTES) ?>',
                <?= $p['price'] ?>,
                <?= $p['quantity'] ?>
            )">
            Add
        </button>
    </td>
</tr>
<?php endforeach; ?>
</table>

<hr>

<h3>Cart</h3>

<form method="POST" action="../../conn/POS.php" id="posForm">

<table class="pos-table" id="cartTable">
<tr>
    <th>Product</th>
    <th>Price</th>
    <th>Qty</th>
    <th>Total</th>
    <th>Action</th>
</tr>
</table>

<input type="hidden" name="cart_data" id="cartData">

<div class="checkout-bar">

<div class="checkout-group">
    <label>Payment Method</label>
    <select name="payment_method" required>
        <option value="cash">Cash</option>
        <option value="gcash">GCash</option>
    </select>
</div>

<div class="checkout-group">
    <label>Total</label>
    <input type="text" id="totalAmount" readonly value="0.00">
</div>

<div class="checkout-group">
    <label>Amount Paid</label>
    <input type="number" name="amount_paid" id="amountPaid" step="0.01" required>
</div>

<div class="checkout-group">
    <label>Change</label>
    <input type="text" id="changeAmount" readonly>
</div>

<button type="button" class="checkout-btn" onclick="checkout()">
    Checkout
</button>

</div>

<!-- =========================
     RECEIPT MODAL
========================= -->
<div id="receiptModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeReceiptModal()">&times;</span>

        <h2>Transaction Summary</h2>

        <p><strong>Total:</strong> ₱<span id="summaryTotal">0.00</span></p>
        <p><strong>Paid:</strong> ₱<span id="summaryPaid">0.00</span></p>
        <p><strong>Change:</strong> ₱<span id="summaryChange">0.00</span></p>

        <div class="receipt-actions">
            <button type="submit" class="confirm-btn">
                Confirm & Save
            </button>

            <button type="button" onclick="window.print()" class="print-btn">
                Print
            </button>

            <button type="button" onclick="closeReceiptModal()" class="cancel-btn">
                Close
            </button>
        </div>
    </div>
</div>

</form>

</main>
</div>

<script>
let cart = {};

function addToCart(id, name, price, maxQty) {
    if (!cart[id]) {
        cart[id] = { name, price, qty: 1, maxQty };
    } else if (cart[id].qty < maxQty) {
        cart[id].qty++;
    }
    renderCart();
}

function removeItem(id) {
    delete cart[id];
    renderCart();
}

function renderCart() {
    const table = document.getElementById("cartTable");
    table.innerHTML = `
        <tr>
            <th>Product</th><th>Price</th><th>Qty</th><th>Total</th><th>Action</th>
        </tr>
    `;

    let total = 0;

    for (let id in cart) {
        let item = cart[id];
        item.qty = Math.min(item.qty, item.maxQty);

        let rowTotal = item.qty * item.price;
        total += rowTotal;

        table.innerHTML += `
        <tr>
            <td>${item.name}</td>
            <td>${item.price.toFixed(2)}</td>
            <td>
                <input type="number" min="1" max="${item.maxQty}"
                       value="${item.qty}"
                       onchange="cart[${id}].qty=parseInt(this.value)||1; renderCart();">
            </td>
            <td>${rowTotal.toFixed(2)}</td>
            <td>
                <button type="button" onclick="removeItem(${id})">Remove</button>
            </td>
        </tr>`;
    }

    totalAmount.value = total.toFixed(2);
    cartData.value = JSON.stringify(cart);
    calculateChange();
}

function calculateChange() {
    const total = parseFloat(totalAmount.value) || 0;
    const paid  = parseFloat(amountPaid.value) || 0;
    changeAmount.value = paid >= total ? (paid - total).toFixed(2) : "0.00";
}

amountPaid.addEventListener("input", calculateChange);

function checkout() {
    if (!Object.keys(cart).length) {
        alert("Cart is empty.");
        return;
    }

    const total = parseFloat(totalAmount.value) || 0;
    const paid  = parseFloat(amountPaid.value) || 0;

    if (paid < total) {
        alert("Insufficient payment.");
        return;
    }

    summaryTotal.textContent  = total.toFixed(2);
    summaryPaid.textContent   = paid.toFixed(2);
    summaryChange.textContent = (paid - total).toFixed(2);

    receiptModal.classList.add("show");
}

function closeReceiptModal() {
    receiptModal.classList.remove("show");
}
</script>

</body>
</html>
