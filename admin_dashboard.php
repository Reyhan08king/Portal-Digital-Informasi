<?php 
require 'check_session.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - RW13 Digital</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="admin-active">
    <header>
        <nav>
            <div class="logo">Panel Admin RW13</div>
            <ul class="nav-links">
                <li><a href="index.php">Lihat Situs</a></li>
                <li><a href="logout.php" style="color: #ef4444;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container" style="padding-top: 50px;">
        <div class="section-header-admin">
            <h2>Manajemen Keluhan Warga</h2>
            <div class="admin-actions" style="display: flex; gap: 10px; margin-top: 15px; flex-wrap: wrap;">
                <button onclick="exportToCSV()" class="admin-btn-primary" style="background: #16a34a;">
                    <i class="fas fa-file-excel"></i> Ekspor Excel (CSV)
                </button>
                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <div style="display: flex; gap: 5px; align-items: center;">
                        <label for="startDateAdmin" style="font-size: 0.8rem;">Dari:</label>
                        <input type="date" id="startDateAdmin" class="admin-status-select" style="display: inline-block; padding: 0.4rem;">
                        <label for="endDateAdmin" style="font-size: 0.8rem;">Sampai:</label>
                        <input type="date" id="endDateAdmin" class="admin-status-select" style="display: inline-block; padding: 0.4rem;">
                    </div>
                    <select id="filterCategoryAdmin" class="admin-status-select" style="display: inline-block; padding: 0.5rem;">
                        <option value="">Semua Kategori (Print Semua)</option>
                        <option value="Keamanan">Keamanan</option>
                        <option value="Kebersihan">Kebersihan</option>
                        <option value="Infrastruktur">Infrastruktur</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                    <button onclick="window.print()" class="admin-btn-secondary">
                        <i class="fas fa-print"></i> Cetak Laporan
                    </button>
                </div>
            </div>
        </div>
        
        <p>Halo, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>! Anda memiliki akses penuh untuk mengelola data.</p>
        
        <div id="complaints-container" class="complaints-list" style="margin-top: 30px;">
            <!-- Data bisa diambil via script.js atau query SQL langsung -->
            <p style="text-align: center; color: var(--text-muted);">Memuat data keluhan...</p>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>