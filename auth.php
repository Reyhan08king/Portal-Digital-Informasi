<?php
ob_start(); 
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

// 1. Validasi CSRF Token
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    header("Location: login.php?error=csrf");
    exit;
}

/**
 * PENTING: Pastikan di file login.php, 
 * input username memiliki name="username" 
 * dan input password memiliki name="password"
 */
$username_input = trim($_POST['username'] ?? '');
$password_input = $_POST['password'] ?? '';

// 2. Cek apakah input kosong
if (empty($username_input) || empty($password_input)) {
    header("Location: login.php?error=empty");
    exit;
}

try {
    if (!isset($pdo)) {
        throw new Exception("Koneksi database tidak ditemukan di file db.php.");
    }

    /**
     * PERHATIAN: 
     * Saya menggunakan tabel 'admin' dan kolom 'username_admin' sesuai error kamu sebelumnya.
     * Jika nama tabel di phpMyAdmin adalah 'tb_user', ganti 'admin' menjadi 'tb_user'.
     */
    $query = "SELECT * FROM admin WHERE username_admin = ? LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$username_input]);
    $admin = $stmt->fetch();

    // 3. Verifikasi Password
    // Pastikan kolom di database namanya 'passwordbaru'
    if ($admin && password_verify($password_input, $admin['passwordbaru'])) {
        
        // Login berhasil, buat session baru untuk keamanan
        session_regenerate_id(true);
        
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['username'] = $admin['username_admin'];
        $_SESSION['is_logged_in'] = true;
        $_SESSION['last_activity'] = time();

        // Arahkan ke dashboard
        header("Location: admin_dashboard.php");
        exit;
    } else {
        // Jika salah password atau username tidak ada
        header("Location: login.php?error=1");
        exit;
    }

} catch (Exception $e) {
    // Catat error ke log server, jangan tampilkan detail ke user demi keamanan
    error_log("Auth Error: " . $e->getMessage());
    header("Location: login.php?error=system");
    exit;
}

ob_end_flush();