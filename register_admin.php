<?php
require 'db.php';

// Ganti sesuai keinginan Anda
$username_admin = 'admin_rw13';
$password_asli = 'sekretariatan13'; 

// Proses Hashing
$passwordbaru = password_hash($password_asli, PASSWORD_BCRYPT);

try {
    // Menggunakan ON DUPLICATE KEY UPDATE agar password diperbarui jika username sudah ada
    $stmt = $pdo->prepare("INSERT INTO tb_user (username_admin, passwordbaru) 
                           VALUES (?, ?) 
                           ON DUPLICATE KEY UPDATE passwordbaru = VALUES(passwordbaru)");
    $stmt->execute([$username_admin, $passwordbaru]);
    echo "Berhasil! Data admin telah diperbarui.<br>Username: $username_admin<br>Password Baru: $password_asli";
    echo "<br><br><a href='login.php'>Ke Halaman Login</a>";
} catch (PDOException $e) {
    echo "Gagal membuat admin: " . $e->getMessage();
}