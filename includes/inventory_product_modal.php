<div id="productModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>

        <h2>Add Product</h2>

        <form method="POST" action="../../conn/Product.php">

            <input type="text"
                   name="product_name"
                   placeholder="Product Name"
                   required>

            <select name="category_id" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['category_id'] ?>">
                        <?= htmlspecialchars($c['category_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="number"
                   step="0.01"
                   name="price"
                   placeholder="Price"
                   required>

            <input type="number"
                   name="quantity"
                   placeholder="Quantity"
                   required>

            <input type="text"
                   name="barcode"
                   placeholder="Barcode">

            <button type="submit" name="add_product">
                Add Product
            </button>
        </form>
    </div>
</div>
