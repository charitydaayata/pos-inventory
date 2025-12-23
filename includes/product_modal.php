<div id="productModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>

        <h2><?= $editProduct ? 'Edit Product' : 'Add Product' ?></h2>

        <form method="POST" action="../../conn/Product.php">
            <?php if ($editProduct): ?>
                <input type="hidden" name="product_id" value="<?= $editProduct['product_id'] ?>">
            <?php endif; ?>

            <input type="text" name="product_name" placeholder="Product Name"
                   value="<?= $editProduct['product_name'] ?? '' ?>" required>

            <select name="category_id" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['category_id'] ?>"
                        <?= isset($editProduct) && $editProduct['category_id'] == $c['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['category_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="number" step="0.01" name="price" placeholder="Price"
                   value="<?= $editProduct['price'] ?? '' ?>" required>

            <input type="number" name="quantity" placeholder="Quantity"
                   value="<?= $editProduct['quantity'] ?? '' ?>" required>

            <input type="text" name="barcode" placeholder="Barcode"
                   value="<?= $editProduct['barcode'] ?? '' ?>">

            <?php if ($editProduct): ?>
                <button type="submit" name="update_product">Update Product</button>
            <?php else: ?>
                <button type="submit" name="add_product">Add Product</button>
            <?php endif; ?>
        </form>
    </div>
</div>
