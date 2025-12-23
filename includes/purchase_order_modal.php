<div id="poModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>

        <h2>Request Purchase Order</h2>

        <form method="POST" action="../../conn/PurchaseOrder.php">
            <select name="product_id" required>
                <option value="">Select Product</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?= $p['product_id'] ?>">
                        <?= htmlspecialchars($p['product_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="number" name="quantity" min="1" placeholder="Quantity" required>

            <button type="submit" name="request_po">Submit Request</button>
        </form>
    </div>
</div>
