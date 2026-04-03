<?php
session_start();
require 'db.php';

$username = $_GET['user'] ?? '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = $_POST['otp'] ?? '';
    $username = $_POST['username'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE username = ? AND otp_code = ? AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$username, $otp]);
    
    if ($stmt->fetch()) {
        $_SESSION['reset_authorized_user'] = $username;
        header("Location: reset_password.php");
        exit;
    } else {
        $error = "Kode OTP salah atau sudah kedaluwarsa.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi OTP - RW13 Digital</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .login-container { display: flex; justify-content: center; align-items: center; min-height: 100vh; background: var(--bg-light); }
        .login-box { background: var(--white); padding: 2.5rem; border-radius: 20px; box-shadow: var(--shadow-lg); width: 100%; max-width: 400px; text-align: center; }
        .otp-input { letter-spacing: 0.5rem; font-size: 1.5rem; text-align: center; font-weight: bold; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Verifikasi Kode</h2>
            <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 1.5rem;">Masukkan 6 digit kode yang Anda dapatkan dari WhatsApp.</p>
            
            <?php if($error): ?><div style="color: #ef4444; font-size: 0.8rem; margin-bottom: 1rem;"><?php echo $error; ?></div><?php endif; ?>

            <form method="POST" class="contact-form" style="background: none; padding: 0; border: none;">
                <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
                <input type="text" name="otp" class="otp-input" maxlength="6" placeholder="000000" required>
                <button type="submit" class="submit-btn" style="width: 100%; margin-top: 1rem;">Verifikasi</button>
            </form>
        </div>
    </div>
</body>
</html>