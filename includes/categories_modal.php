<div id="categoryModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>

        <h2><?= isset($editCategory) ? 'Edit Category' : 'Add Category' ?></h2>

        <form method="POST" action="../../conn/Categories.php">
            <input type="hidden" name="category_id"
                   value="<?= $editCategory['category_id'] ?? '' ?>">

            <input type="text"
                   name="category_name"
                   placeholder="Category Name"
                   value="<?= $editCategory['category_name'] ?? '' ?>"
                   required>

            <textarea name="description"
                      placeholder="Description"><?= $editCategory['description'] ?? '' ?></textarea>

            <?php if ($editCategory): ?>
                <button type="submit" name="update_category">Update Category</button>
            <?php else: ?>
                <button type="submit" name="add_category">Add Category</button>
            <?php endif; ?>
        </form>
    </div>
</div>
