<div id="userModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>

        <h2><?= $editUser ? 'Edit User' : 'Add User' ?></h2>

        <form method="POST" action="../../conn/Users.php">

            <?php if ($editUser): ?>
                <input type="hidden" name="user_id" value="<?= $editUser['user_id'] ?>">
            <?php endif; ?>

            <input type="text"
                   name="name"
                   placeholder="Full Name"
                   value="<?= $editUser['name'] ?? '' ?>"
                   required>

            <input type="text"
                   name="username"
                   placeholder="Username"
                   value="<?= $editUser['username'] ?? '' ?>"
                   required>

            <?php if (!$editUser): ?>
                <input type="password"
                       name="password"
                       placeholder="Password"
                       required>
            <?php endif; ?>

            <select name="role_id" required>
                <option value="">Select Role</option>
                <?php foreach ($roles as $r): ?>
                    <option value="<?= $r['role_id'] ?>"
                        <?= ($editUser && $editUser['role_id'] == $r['role_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($r['role_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if ($editUser): ?>
                <button type="submit" name="update_user">Update User</button>
            <?php else: ?>
                <button type="submit" name="add_user">Add User</button>
            <?php endif; ?>

        </form>
    </div>
</div>
