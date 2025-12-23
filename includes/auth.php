<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* PREVENT BACK BUTTON CACHE */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

/* LOGIN CHECK */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}
