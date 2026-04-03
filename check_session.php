<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Proteksi Session Timeout (Opsional: 30 menit)
if (time() - $_SESSION['last_activity'] > 1800) {
    session_destroy();
    header("Location: login.php?timeout=1");
}
$_SESSION['last_activity'] = time();