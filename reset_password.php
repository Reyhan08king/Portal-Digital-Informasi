<?php
session_start();
require 'db.php'; 

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Ambil data dari form (Pastikan NAME di <input> sama dengan ini)
    $username_admin = trim($_POST['username_admin'] ?? '');
    $passwordbaru_plain = $_POST['passwordbaru'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    if (empty($username_admin) || empty($passwordbaru_plain) || empty($confirm_pass)) {
        $error = "Semua field harus diisi.";
    } elseif ($passwordbaru_plain !== $confirm_pass) {
        $error = "Konfirmasi password tidak cocok.";
    } elseif (strlen($passwordbaru_plain) < 8) {
        $error = "Password minimal harus 8 karakter.";
    } else {
        try {
            /** * CATATAN PENTING:
             * Jika error "Table doesn't exist" muncul lagi, berarti nama tabel di phpMyAdmin 
             * BUKAN 'admin'. Silakan cek phpMyAdmin Anda sekarang.
             */
            
            // 2. Cek apakah user ada
            $stmt = $pdo->prepare("SELECT id FROM tb_user WHERE username_admin = ?");
            $stmt->execute([$username_admin]);
            $user = $stmt->fetch();

            if ($user) {
                // 3. Hash password
                $passwordbaru_hashed = password_hash($passwordbaru_plain, PASSWORD_BCRYPT);

                // 4. Update Database
                // Pastikan nama kolom di database Anda adalah 'passwordbaru' dan 'username_admin'
                $update = $pdo->prepare("UPDATE tb_user SET passwordbaru = ? WHERE username_admin = ?");
                $result = $update->execute([$passwordbaru_hashed, $username_admin]);

                if ($result) {
                    $success = "Password berhasil diperbarui! Silakan login kembali.";
                } else {
                    $error = "Gagal memperbarui database.";
                }
            } else {
                $error = "Username '$username_admin' tidak ditemukan di tabel admin.";
            }
        } catch (PDOException $e) {
            // Ini akan menangkap jika tabel 'admin' benar-benar tidak ada
            $error = "Kesalahan Database: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - RW13 Digital</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary-color: #4f46e5; --bg-light: #f3f4f6; --white: #ffffff; --text-muted: #6b7280; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg-light); display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .login-box { background: var(--white); padding: 2.5rem; border-radius: 20px; box-shadow: 0 10px 15px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        .error-msg { background: #fee2e2; color: #ef4444; padding: 0.8rem; border-radius: 8px; font-size: 0.85rem; margin-bottom: 1rem; text-align: left; border: 1px solid #fecaca; }
        .success-msg { background: #dcfce7; color: #16a34a; padding: 0.8rem; border-radius: 8px; font-size: 0.85rem; margin-bottom: 1rem; }
        input { width: 100%; padding: 0.8rem; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 10px; box-sizing: border-box; }
        .submit-btn { width: 100%; padding: 0.8rem; background: var(--primary-color); color: white; border: none; border-radius: 10px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="login-box">
        <div style="font-size: 1.5rem; font-weight: bold; color: var(--primary-color); margin-bottom: 1rem;">
            <i class="fas fa-lock-open"></i> RW13 Digital
        </div>
        <h2>Perbarui Password</h2>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">Silakan isi data di bawah ini.</p>

        <?php if($error): ?> <div class="error-msg"><?= $error ?></div> <?php endif; ?>
        <?php if($success): ?> <div class="success-msg"><?= $success ?></div> <?php endif; ?>

        <form method="POST">
            <input type="text" name="username_admin" placeholder="Username Anda" required>
            <input type="password" name="passwordbaru" placeholder="Password Baru" required>
            <input type="password" name="confirm_password" placeholder="Konfirmasi Password Baru" required>
            <button type="submit" class="submit-btn">Simpan Password Baru</button>
        </form>
        
        <br>
        <a href="login.php" style="text-decoration: none; color: var(--text-muted); font-size: 0.8rem;">&larr; Kembali ke Login</a>
    </div>
</body>
</html>