<?php
session_start();
require 'conn/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("
        SELECT u.*, r.role_name
        FROM users u
        JOIN roles r ON u.role_id = r.role_id
        WHERE u.username = ?
          AND u.status = 'active'
    ");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role']    = $user['role_name'];
        $_SESSION['name']    = $user['name'];

        /* ROLE-BASED REDIRECT */
        switch ($user['role_name']) {

            case 'Admin':
                header("Location: views/admin/dashboard.php");
                break;

            case 'Cashier':
                header("Location: views/cashier/dashboard.php");
                break;

            case 'Warehouse Manager':
                header("Location: views/warehouse/warehouse_dashboard.php");
                break;

            case 'Inventory Manager':
                header("Location: views/inventory/inventory_dashboard.php");
                break;

            default:
                $error = "Role not recognized.";
                session_destroy();
                break;
        }

        exit;

    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<div class="login-wrapper">

    <!-- LEFT IMAGE -->
    <div class="login-image"></div>

    <!-- RIGHT FORM -->
    <div class="login-container">
        <h2>Inventory & POS System</h2>

        <?php if ($error): ?>
            <p style="color:red; text-align:center; margin-bottom:15px;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit">Login</button>
        </form>

        <div class="footer-text">
            Made by 🔥😉 Mavuika 😉🔥
        </div>
    </div>

</div>

</body>

</html>
