<?php
session_start();
require 'db.php';

// Generate CSRF token jika belum ada
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
$step = 1; // 1: Input Username, 2: Send WA & Verify
$waData = ['url' => '', 'user' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi CSRF Token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = "Sesi permintaan kedaluwarsa, silakan coba lagi.";
    } else {
        $username = trim($_POST['username_admin'] ?? '');

        try {
            // Cek apakah user ada
            $stmt = $pdo->prepare("SELECT * FROM tb_user WHERE username_admin = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user) {
                $otp = rand(100000, 999999); // Generate 6 digit OTP
                $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));

                // Simpan ke database
                $stmt = $pdo->prepare("INSERT INTO password_resets (username, otp_code, expires_at) VALUES (?, ?, ?)");
                $stmt->execute([$username, $otp, $expires]);

                // Siapkan link WA
                $phone = "6281234567890"; // Ganti nomor pengurus
                $msg = "Halo Pengurus RW13, kode OTP reset saya adalah: $otp (Username: $username)";
                
                $waData['url'] = "https://wa.me/$phone?text=" . urlencode($msg);
                $waData['user'] = $username;
                $step = 2;
                
            } else {
                $error = "Username tidak ditemukan.";
            }
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan sistem. Silakan coba lagi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password - RW13 Digital</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .login-container { display: flex; justify-content: center; align-items: center; min-height: 100vh; background: var(--bg-light); }
        .login-box { background: var(--white); padding: 2.5rem; border-radius: 20px; box-shadow: var(--shadow-lg); width: 100%; max-width: 400px; text-align: center; }
        .login-box h2 { color: var(--primary-color); margin-bottom: 1rem; }
        .info-text { color: var(--text-muted); margin-bottom: 1.5rem; font-size: 0.9rem; line-height: 1.4; }
        .error-msg { color: #ef4444; font-size: 0.8rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="logo" style="justify-content: center; margin-bottom: 1rem;">
                <i class="fas fa-unlock-alt"></i> RW13 Digital
            </div>
            <h2>Reset Password</h2>
            <p class="info-text">Masukkan username dan password baru Anda untuk memperbarui akses.</p>

            <?php if($error): ?><div class="error-msg"><?php echo $error; ?></div><?php endif; ?>

            <form method="POST" class="contact-form" style="background: none; padding: 0; border: none;">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="text" name="username_admin" placeholder="Username Anda" required autofocus style="width: 100%; padding: 1rem; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 10px;">
                <input type="password" name="passwordbaru" placeholder="Password Baru" required minlength="8" style="width: 100%; padding: 1rem; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 10px;">
                <input type="password" name="confirm_password" placeholder="Konfirmasi Password Baru" required style="width: 100%; padding: 1rem; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 10px;">
                <button type="submit" class="submit-btn" style="width: 100%;">Perbarui Password</button>
            </form>
            
            <br>
            <a href="login.php" style="text-decoration: none; color: var(--text-muted); font-size: 0.8rem;">&larr; Kembali ke Login</a>
        </div>
    </div>
</body>
</html>