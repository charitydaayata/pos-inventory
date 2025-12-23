<?php
session_start();

require 'db.php';
require 'audit_helper.php';

/* =========================
   ADD USER (WITH VALIDATION)
========================= */
if (isset($_POST['add_user'])) {

    $name     = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role_id  = (int)($_POST['role_id'] ?? 0);

    /* BASIC VALIDATION */
    if ($name === '' || $username === '' || $password === '' || $role_id <= 0) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../views/admin/users.php");
        exit;
    }

    /* USERNAME LENGTH */
    if (strlen($username) < 4) {
        $_SESSION['error'] = "Username must be at least 4 characters.";
        header("Location: ../views/admin/users.php");
        exit;
    }

    /* PASSWORD LENGTH */
    if (strlen($password) < 6) {
        $_SESSION['error'] = "Password must be at least 6 characters.";
        header("Location: ../views/admin/users.php");
        exit;
    }

    /* UNIQUE USERNAME */
    $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username=?");
    $check->execute([$username]);
    if ($check->fetchColumn() > 0) {
        $_SESSION['error'] = "Username already exists.";
        header("Location: ../views/admin/users.php");
        exit;
    }

    /* INSERT */
    $stmt = $pdo->prepare("
        INSERT INTO users (name, username, password, role_id, status)
        VALUES (?, ?, ?, ?, 'active')
    ");
    $stmt->execute([
        $name,
        $username,
        password_hash($password, PASSWORD_DEFAULT),
        $role_id
    ]);

    logAction(
        $pdo,
        $_SESSION['user_id'],
        'Added user: ' . $username
    );

    $_SESSION['success'] = "User added successfully.";
    header("Location: ../views/admin/users.php");
    exit;
}

/* =========================
   UPDATE USER (WITH VALIDATION)
========================= */
if (isset($_POST['update_user'])) {

    $user_id  = (int)($_POST['user_id'] ?? 0);
    $name     = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $role_id  = (int)($_POST['role_id'] ?? 0);

    if ($user_id <= 0 || $name === '' || $username === '' || $role_id <= 0) {
        $_SESSION['error'] = "Invalid user data.";
        header("Location: ../views/admin/users.php");
        exit;
    }

    /* USER EXISTS */
    $stmt = $pdo->prepare("SELECT username FROM users WHERE user_id=?");
    $stmt->execute([$user_id]);
    $old = $stmt->fetch();

    if (!$old) {
        $_SESSION['error'] = "User not found.";
        header("Location: ../views/admin/users.php");
        exit;
    }

    /* UNIQUE USERNAME (EXCEPT SELF) */
    $check = $pdo->prepare("
        SELECT COUNT(*) FROM users
        WHERE username=? AND user_id != ?
    ");
    $check->execute([$username, $user_id]);
    if ($check->fetchColumn() > 0) {
        $_SESSION['error'] = "Username already exists.";
        header("Location: ../views/admin/users.php");
        exit;
    }

    /* UPDATE */
    $stmt = $pdo->prepare("
        UPDATE users
        SET name=?, username=?, role_id=?
        WHERE user_id=?
    ");
    $stmt->execute([$name, $username, $role_id, $user_id]);

    logAction(
        $pdo,
        $_SESSION['user_id'],
        'Updated user: ' . ($old['username'] ?? 'ID '.$user_id)
    );

    $_SESSION['success'] = "User updated successfully.";
    header("Location: ../views/admin/users.php");
    exit;
}

/* =========================
   ARCHIVE USER (VALIDATED)
========================= */
if (isset($_GET['archive'])) {

    $id = (int)$_GET['archive'];

    $stmt = $pdo->prepare("SELECT username FROM users WHERE user_id=?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if (!$user) {
        $_SESSION['error'] = "User not found.";
        header("Location: ../views/admin/users.php");
        exit;
    }

    $pdo->prepare("UPDATE users SET status='inactive' WHERE user_id=?")
        ->execute([$id]);

    logAction(
        $pdo,
        $_SESSION['user_id'],
        'Archived user: ' . $user['username']
    );

    $_SESSION['success'] = "User archived successfully.";
    header("Location: ../views/admin/users.php");
    exit;
}

/* =========================
   DELETE USER (VALIDATED)
========================= */
if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    /* PREVENT SELF DELETE */
    if ($id === (int)$_SESSION['user_id']) {
        $_SESSION['error'] = "You cannot delete your own account.";
        header("Location: ../views/admin/users.php");
        exit;
    }

    $stmt = $pdo->prepare("SELECT username FROM users WHERE user_id=?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if (!$user) {
        $_SESSION['error'] = "User not found.";
        header("Location: ../views/admin/users.php");
        exit;
    }

    $pdo->prepare("DELETE FROM users WHERE user_id=?")
        ->execute([$id]);

    logAction(
        $pdo,
        $_SESSION['user_id'],
        'Deleted user: ' . $user['username']
    );

    $_SESSION['success'] = "User deleted successfully.";
    header("Location: ../views/admin/users.php");
    exit;
}

/* =========================
   FALLBACK
========================= */
$_SESSION['error'] = "Invalid user request.";
header("Location: ../views/admin/users.php");
exit;
