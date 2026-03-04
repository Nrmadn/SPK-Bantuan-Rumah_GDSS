<?php
$pageTitle = 'Dashboard Admin';
require_once '../includes/header.php';
requireAdmin();

require_once '../functions/topsis.php';
require_once '../functions/borda.php';

// Statistik
$totalAlternatif = fetch("SELECT COUNT(*) as total FROM alternatif")['total'];
$totalDM = fetch("SELECT COUNT(*) as total FROM users WHERE role != 'admin'")['total'];
$totalPenilaian = fetch("SELECT COUNT(*) as total FROM penilaian")['total'];
$totalPenilaianLengkap = $totalAlternatif * $totalDM;

// TAMBAHAN: Statistik Kriteria
$totalKriteria = fetch("SELECT COUNT(*) as total FROM kriteria")['total'];
$totalBobot = fetch("SELECT SUM(bobot) as total FROM kriteria")['total'];
$bobotValid = abs($totalBobot - 1) < 0.01; // Toleransi 0.01

// Status penilaian per DM
$statusPenilaian = fetchAll("SELECT u.nama, u.role,
                             COUNT(p.id) as jumlah_penilaian,
                             $totalAlternatif as total_alternatif,
                             CASE 
                                WHEN COUNT(p.id) = $totalAlternatif THEN 'Selesai'
                                ELSE 'Belum Selesai'
                             END as status
                             FROM users u
                             LEFT JOIN penilaian p ON u.id = p.user_id
                             WHERE u.role != 'admin'
                             GROUP BY u.id");

// Cek hasil Borda
$hasilBordaAda = cekHasilBordaAda();
$pemenang = $hasilBordaAda ? getPemenang() : null;

// Cek semua DM sudah penilaian
$semuaDMSelesai = $totalPenilaian >= $totalPenilaianLengkap;
?>

<!-- Statistik Cards Baris 1 -->
<div class="row">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Alternatif</p>
                        <h3 class="mb-0"><?= $totalAlternatif ?></h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                        <i class="bi bi-people-fill fs-2 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Decision Maker</p>
                        <h3 class="mb-0"><?= $totalDM ?></h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded">
                        <i class="bi bi-person-check-fill fs-2 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stat-card warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Penilaian</p>
                        <h3 class="mb-0"><?= $totalPenilaian ?> / <?= $totalPenilaianLengkap ?></h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded">
                        <i class="bi bi-clipboard-check-fill fs-2 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stat-card <?= $hasilBordaAda ? 'success' : 'danger' ?>">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Status Borda</p>
                        <h6 class="mb-0">
                            <?= $hasilBordaAda ? 'Sudah Dihitung' : 'Belum Dihitung' ?>
                        </h6>
                    </div>
                    <div class="bg-<?= $hasilBordaAda ? 'success' : 'danger' ?> bg-opacity-10 p-3 rounded">
                        <i class="bi bi-<?= $hasilBordaAda ? 'check-circle' : 'x-circle' ?>-fill fs-2 text-<?= $hasilBordaAda ? 'success' : 'danger' ?>"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistik Cards Baris 2: Kriteria -->
<div class="row mt-3">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Kriteria</p>
                        <h3 class="mb-0"><?= $totalKriteria ?></h3>
                    </div>
                    <div class="bg-info bg-opacity-10 p-3 rounded">
                        <i class="bi bi-list-check fs-2 text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Bobot</p>
                        <h3 class="mb-0 <?= $bobotValid ? 'text-success' : 'text-danger' ?>">
                            <?= number_format($totalBobot, 3) ?>
                        </h3>
                    </div>
                    <div class="bg-<?= $bobotValid ? 'success' : 'warning' ?> bg-opacity-10 p-3 rounded">
                        <i class="bi bi-calculator fs-2 text-<?= $bobotValid ? 'success' : 'warning' ?>"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Status Bobot</p>
                        <h3 class="mb-0">
                            <?php if ($bobotValid): ?>
                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> Valid</span>
                            <?php else: ?>
                                <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Invalid</span>
                            <?php endif; ?>
                        </h3>
                    </div>
                    <div class="bg-<?= $bobotValid ? 'success' : 'danger' ?> bg-opacity-10 p-3 rounded">
                        <i class="bi bi-<?= $bobotValid ? 'check' : 'exclamation' ?>-circle-fill fs-2 text-<?= $bobotValid ? 'success' : 'danger' ?>"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alert Bobot Tidak Valid -->
<?php if (!$bobotValid): ?>
<div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
    <h6 class="alert-heading">
        <i class="bi bi-exclamation-triangle-fill"></i> Perhatian: Total Bobot Kriteria Tidak Valid!
    </h6>
    <p class="mb-1">
        Total bobot kriteria saat ini adalah <strong><?= number_format($totalBobot, 3) ?></strong>. 
        Total bobot harus <strong>= 1.000</strong> agar perhitungan TOPSIS dapat berjalan dengan benar.
    </p>
    <hr>
    <a href="kelola_kriteria.php" class="btn btn-warning btn-sm">
        <i class="bi bi-pencil"></i> Sesuaikan Bobot Sekarang
    </a>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Alert Pemenang -->
<?php if ($pemenang): ?>
<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
    <h5 class="alert-heading">
        <i class="bi bi-trophy-fill"></i> Penerima Bantuan Terpilih!
    </h5>
    <hr>
    <div class="row align-items-center">
        <div class="col-md-8">
            <p class="mb-1"><strong>Nama:</strong> <?= $pemenang['nama'] ?></p>
            <p class="mb-1"><strong>Alamat:</strong> <?= $pemenang['alamat'] ?></p>
            <p class="mb-1"><strong>Bobot:</strong> <?= number_format($pemenang['bobot'], 3) ?></p>
        </div>
        <div class="col-md-4 text-end">
            <a href="laporan.php" class="btn btn-success">
                <i class="bi bi-file-earmark-text"></i> Lihat Laporan
            </a>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Status Penilaian Decision Maker -->
<div class="card mt-3">
    <div class="card-header">
        <i class="bi bi-clipboard-data"></i> Status Penilaian Decision Maker
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Decision Maker</th>
                        <th>Role</th>
                        <th>Progress</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($statusPenilaian as $dm): ?>
                    <tr>
                        <td>
                            <i class="bi bi-person-fill"></i> <?= $dm['nama'] ?>
                        </td>
                        <td><?= getRoleDisplay($dm['role']) ?></td>
                        <td>
                            <div class="progress" style="height: 25px;">
                                <?php 
                                $persentase = ($dm['jumlah_penilaian'] / $dm['total_alternatif']) * 100;
                                ?>
                                <div class="progress-bar" role="progressbar" 
                                     style="width: <?= $persentase ?>%"
                                     aria-valuenow="<?= $persentase ?>" 
                                     aria-valuemin="0" aria-valuemax="100">
                                    <?= $dm['jumlah_penilaian'] ?> / <?= $dm['total_alternatif'] ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if ($dm['status'] == 'Selesai'): ?>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i> Selesai
                                </span>
                            <?php else: ?>
                                <span class="badge bg-warning">
                                    <i class="bi bi-hourglass-split"></i> Belum Selesai
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Quick Actions - 4 CARD SAMA TINGGI -->
<div class="row mt-4 g-3">
    <div class="col-md-3">
        <div class="card text-center h-100 shadow-sm border-0">
            <div class="card-body d-flex flex-column p-4">
                <div class="icon-wrapper mb-3">
                    <i class="bi bi-people fs-1 text-primary"></i>
                </div>
                <h5 class="card-title">Kelola Alternatif</h5>
                <p class="text-muted small flex-grow-1">Tambah, edit, atau hapus data calon penerima</p>
                <a href="kelola_alternatif.php" class="btn btn-primary mt-auto">
                    <i class="bi bi-arrow-right-circle"></i> Kelola
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-center h-100 shadow-sm border-0">
            <div class="card-body d-flex flex-column p-4">
                <div class="icon-wrapper mb-3">
                    <i class="bi bi-list-check fs-1" style="color: #17a2b8;"></i>
                </div>
                <h5 class="card-title">Kelola Kriteria</h5>
                <p class="text-muted small flex-grow-1">Edit kriteria dan sesuaikan bobot penilaian</p>
                <a href="kelola_kriteria.php" class="btn mt-auto" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: none;">
                    <i class="bi bi-pencil-square"></i> Kelola
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-center h-100 shadow-sm border-0">
            <div class="card-body d-flex flex-column p-4">
                <div class="icon-wrapper mb-3">
                    <i class="bi bi-person-badge fs-1 text-warning"></i>
                </div>
                <h5 class="card-title">Kelola Decision Maker</h5>
                <p class="text-muted small flex-grow-1">Lihat dan update data Decision Maker</p>
                <a href="kelola_dm.php" class="btn btn-warning mt-auto">
                    <i class="bi bi-eye"></i> Lihat DM
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-center h-100 shadow-sm border-0">
            <div class="card-body d-flex flex-column p-4">
                <div class="icon-wrapper mb-3">
                    <i class="bi bi-clock-history fs-1 text-success"></i>
                </div>
                <h5 class="card-title">History Aktivitas</h5>
                <p class="text-muted small flex-grow-1">Lihat log aktivitas sistem</p>
                <a href="history.php" class="btn btn-success mt-auto">
                    <i class="bi bi-eye"></i> Lihat History
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>