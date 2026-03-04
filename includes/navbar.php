<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="nav flex-column" style="padding: 10px 0;">
    <?php if (isAdmin()): ?>
        <!-- Menu Admin -->
        <a class="nav-link <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>" 
           href="dashboard.php">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        
        <a class="nav-link <?= $currentPage == 'kelola_alternatif.php' ? 'active' : '' ?>" 
           href="kelola_alternatif.php">
            <i class="bi bi-people"></i> Kelola Alternatif
        </a>
        
        <!-- MENU BARU -->
        <a class="nav-link <?= $currentPage == 'kelola_dm.php' ? 'active' : '' ?>" 
           href="kelola_dm.php">
            <i class="bi bi-person-badge"></i> Kelola DM
        </a>

                <!-- MENU BARU -->
        <a class="nav-link <?= $currentPage == 'kelola_kriteria.php' ? 'active' : '' ?>" 
           href="kelola_kriteria.php">
            <i class="bi bi-pencil-square"></i> Kelola Kriteria
        </a>
        
        <a class="nav-link <?= $currentPage == 'history.php' ? 'active' : '' ?>" 
           href="history.php">
            <i class="bi bi-clock-history"></i> History
        </a>
        
    <?php else: ?>
        <!-- Menu Decision Maker -->
        <a class="nav-link <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>" 
           href="dashboard.php">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        
        <a class="nav-link <?= $currentPage == 'input_penilaian.php' ? 'active' : '' ?>" 
           href="input_penilaian.php">
            <i class="bi bi-pencil-square"></i> Input Penilaian
        </a>
        
        <a class="nav-link <?= $currentPage == 'hasil_topsis.php' ? 'active' : '' ?>" 
           href="hasil_topsis.php">
            <i class="bi bi-bar-chart"></i> Hasil TOPSIS
        </a>
        
        <!-- Menu Khusus Kepala Desa (Level 1) -->
        <?php if (isset($_SESSION['level']) && $_SESSION['level'] == 1): ?>
        <a class="nav-link <?= $currentPage == 'lihat_penilaian.php' ? 'active' : '' ?>" 
           href="lihat_penilaian.php">
            <i class="bi bi-clipboard-data"></i> Lihat Penilaian
        </a>
        
        <a class="nav-link <?= $currentPage == 'hitung_borda.php' ? 'active' : '' ?>" 
           href="hitung_borda.php">
            <i class="bi bi-calculator"></i> Konsensus Borda
        </a>
        
        <a class="nav-link <?= $currentPage == 'laporan.php' ? 'active' : '' ?>" 
           href="laporan.php">
            <i class="bi bi-file-earmark-text"></i> Laporan
        </a>
        <?php endif; ?>
        
        <a class="nav-link <?= $currentPage == 'hasil_final.php' ? 'active' : '' ?>" 
           href="hasil_final.php">
            <i class="bi bi-trophy"></i> Hasil Final
        </a>
        
        <a class="nav-link <?= $currentPage == 'panduan.php' ? 'active' : '' ?>" 
           href="panduan.php">
            <i class="bi bi-book"></i> Panduan
        </a>
    <?php endif; ?>
</nav>