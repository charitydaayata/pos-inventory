<?php
// session_start();

/* Unset all session variables */
// $_SESSION = [];

/* Destroy the session */
// session_destroy();

/* Redirect to login */
// header("Location: index.php");
// exit;
session_start();

/* Unset all session variables */
$_SESSION = [];

/* Destroy the session */
session_destroy();

/* PREVENT BACK BUTTON CACHE */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

/* Redirect to login */
header("Location: index.php");
exit;
