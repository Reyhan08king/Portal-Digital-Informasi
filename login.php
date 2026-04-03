<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - RW13 Digital</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: var(--bg-light);
        }
        .login-box {
            background: var(--white);
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-box h2 { color: var(--primary-color); margin-bottom: 1.5rem; }
        .error-msg { color: #ef4444; margin-bottom: 1rem; font-size: 0.9rem; }
        .success-msg { 
            background: #ecfdf5; 
            color: #065f46; 
            padding: 0.85rem; 
            border-radius: 12px; 
            margin-bottom: 1.5rem; 
            font-size: 0.85rem; 
            display: flex; 
            align-items: center; 
            gap: 10px;
            border: 1px solid #a7f3d0;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="logo" style="justify-content: center; margin-bottom: 1rem;">
                <i class="fas fa-house-user"></i> RW13 Digital
            </div>
            <h2>Login Admin</h2>

            <?php if(isset($_GET['reset']) && $_GET['reset'] == 'success'): ?>
                <div class="success-msg">
                    <i class="fas fa-check-circle"></i> Password berhasil diperbarui! Silakan login kembali.
                </div>
            <?php endif; ?>
            
            <?php if(isset($_GET['error'])): ?>
                <div class="error-msg">
                    <?php 
                        if($_GET['error'] == 'csrf') echo "Sesi login kedaluwarsa, silakan coba lagi.";
                        elseif($_GET['error'] == 'empty') echo "Username dan Password harus diisi.";
                        elseif($_GET['error'] == 'system') echo "Terjadi masalah pada koneksi database.";
                        else echo "Username atau password salah!";
                    ?>
                </div>
            <?php endif; ?>

            <form action="auth.php" method="POST" class="contact-form" style="background: none; padding: 0; border: none;">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required style="width: 100%; padding: 1rem; border: 1px solid #cbd5e1; border-radius: 12px; margin-bottom: 1rem;">
                
                <div style="text-align: right; margin-top: -0.5rem; margin-bottom: 1.5rem;">
                    <a href="forgot_password.php" style="font-size: 0.8rem; color: var(--primary-color); text-decoration: none;">Lupa Password?</a>
                </div>

                <button type="submit" class="submit-btn" style="width: 100%;">Masuk</button>
            </form>
            <br>
            <a href="index.php" style="text-decoration: none; color: var(--text-muted); font-size: 0.8rem;">&larr; Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>