<div id="inventoryCategoryModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeInventoryCategoryModal()">&times;</span>

        <h2>Add Category</h2>

        <form method="POST" action="../../conn/Categories.php">
            <input type="text"
                   name="category_name"
                   placeholder="Category Name"
                   required>

            <textarea name="description"
                      placeholder="Description (optional)"></textarea>

            <button type="submit" name="add_category">
                Add Category
            </button>
        </form>
    </div>
</div>

<script>
function openInventoryCategoryModal() {
    document.getElementById('inventoryCategoryModal').style.display = 'block';
}

function closeInventoryCategoryModal() {
    document.getElementById('inventoryCategoryModal').style.display = 'none';
}
</script>
