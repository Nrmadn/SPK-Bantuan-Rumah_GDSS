<?php
$pageTitle = 'Dashboard Decision Maker';
require_once '../includes/header.php';
requireDM();

require_once '../functions/topsis.php';
require_once '../functions/borda.php';

$user_id = $_SESSION['user_id'];

// Statistik
$totalAlternatif = fetch("SELECT COUNT(*) as total FROM alternatif")['total'];
$jumlahPenilaian = fetch("SELECT COUNT(*) as total FROM penilaian WHERE user_id = $user_id")['total'];
$persentaseSelesai = ($jumlahPenilaian / $totalAlternatif) * 100;

// Cek apakah sudah ada hasil TOPSIS
$hasilTOPSIS = fetchAll("SELECT * FROM hasil_topsis WHERE user_id = $user_id");
$sudahHitungTOPSIS = !empty($hasilTOPSIS);

// Cek hasil Borda
$hasilBordaAda = cekHasilBordaAda();

// Cek apakah semua DM sudah selesai
$totalDM = 3; // DM1, DM2, DM3
$dmSelesai = fetch("
    SELECT COUNT(DISTINCT user_id) as total 
    FROM hasil_topsis
")['total'];
$semuaDMSelesai = ($dmSelesai >= $totalDM);

// Status penilaian
$penilaianLengkap = cekPenilaianLengkap($user_id);
?>

<!-- Welcome Card -->
<div class="card mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3>Selamat Datang, <?= $_SESSION['nama'] ?>!</h3>
                <p class="mb-0">
                    <i class="bi bi-briefcase"></i> <?= getRoleDisplay($_SESSION['role']) ?>
                </p>
                <hr class="bg-white">
                <p class="mb-0">
                    <i class="bi bi-info-circle"></i> 
                    Anda dapat melakukan penilaian terhadap calon penerima bantuan 
                    dan melihat hasil perhitungan TOPSIS individual.
                </p>
            </div>
            <div class="col-md-4 text-center">
                <i class="bi bi-person-badge" style="font-size: 100px; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Statistik Cards -->
<div class="row">
    <div class="col-md-4">
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
    
    <div class="col-md-4">
        <div class="card stat-card warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Penilaian Selesai</p>
                        <h3 class="mb-0"><?= $jumlahPenilaian ?> / <?= $totalAlternatif ?></h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded">
                        <i class="bi bi-clipboard-check-fill fs-2 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card stat-card <?= $sudahHitungTOPSIS ? 'success' : 'danger' ?>">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Status TOPSIS</p>
                        <h6 class="mb-0">
                            <?= $sudahHitungTOPSIS ? 'Sudah Dihitung' : 'Belum Dihitung' ?>
                        </h6>
                    </div>
                    <div class="bg-<?= $sudahHitungTOPSIS ? 'success' : 'danger' ?> bg-opacity-10 p-3 rounded">
                        <i class="bi bi-<?= $sudahHitungTOPSIS ? 'check-circle' : 'x-circle' ?>-fill fs-2 text-<?= $sudahHitungTOPSIS ? 'success' : 'danger' ?>"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Progress Penilaian -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-graph-up"></i> Progress Penilaian Anda
    </div>
    <div class="card-body">
        <div class="mb-3">
            <div class="d-flex justify-content-between mb-2">
                <span><strong>Progress:</strong></span>
                <span><strong><?= number_format($persentaseSelesai, 1) ?>%</strong></span>
            </div>
            <div class="progress" style="height: 30px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                     role="progressbar" 
                     style="width: <?= $persentaseSelesai ?>%"
                     aria-valuenow="<?= $persentaseSelesai ?>" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                    <?= $jumlahPenilaian ?> dari <?= $totalAlternatif ?> alternatif
                </div>
            </div>
        </div>
        
        <?php if ($penilaianLengkap): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill"></i> 
                <strong>Selamat!</strong> Anda telah menyelesaikan semua penilaian.
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle-fill"></i> 
                <strong>Perhatian!</strong> Anda masih memiliki <?= $totalAlternatif - $jumlahPenilaian ?> penilaian yang belum diselesaikan.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-md-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-pencil-square fs-1 text-primary"></i>
                <h5 class="mt-3">Input Penilaian</h5>
                <p class="text-muted">Berikan penilaian untuk setiap alternatif</p>
                <a href="input_penilaian.php" class="btn btn-primary">
                    <i class="bi bi-arrow-right-circle"></i> Mulai Menilai
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-bar-chart fs-1 text-success"></i>
                <h5 class="mt-3">Hasil TOPSIS</h5>
                <p class="text-muted">Lihat hasil perhitungan TOPSIS Anda</p>
                <?php if ($sudahHitungTOPSIS): ?>
                    <a href="hasil_topsis.php" class="btn btn-success">
                        <i class="bi bi-eye"></i> Lihat Hasil
                    </a>
                <?php else: ?>
                    <button class="btn btn-secondary" disabled>
                        <i class="bi bi-lock"></i> Belum Ada Data
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-trophy fs-1 text-info"></i>
                <h5 class="mt-3">Hasil Final</h5>
                <p class="text-muted">Lihat hasil konsensus Borda</p>
                <?php if ($hasilBordaAda): ?>
                    <a href="hasil_final.php" class="btn btn-info text-white">
                        <i class="bi bi-award"></i> Lihat Pemenang
                    </a>
                <?php else: ?>
                    <button class="btn btn-secondary" disabled>
                        <i class="bi bi-lock"></i> Belum Dihitung
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Khusus Kepala Desa -->
<?php if (isset($_SESSION['level']) && $_SESSION['level'] == 1): ?>
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="bi bi-star-fill"></i> Menu Khusus Kepala Desa
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card text-center h-100 border-warning">
                            <div class="card-body">
                                <i class="bi bi-clipboard-data fs-1 text-primary"></i>
                                <h5 class="mt-3">Lihat Penilaian</h5>
                                <p class="text-muted">Lihat penilaian dari semua Decision Maker</p>
                                <a href="lihat_penilaian.php" class="btn btn-primary">
                                    <i class="bi bi-eye"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card text-center h-100 border-warning">
                            <div class="card-body">
                                <i class="bi bi-calculator fs-1 text-success"></i>
                                <h5 class="mt-3">Konsensus Borda</h5>
                                <p class="text-muted">Hitung hasil konsensus kelompok</p>
                                <?php if ($semuaDMSelesai): ?>
                                    <a href="hitung_borda.php" class="btn btn-success">
                                        <i class="bi bi-play-circle"></i> Hitung Borda
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-secondary" disabled>
                                        <i class="bi bi-lock"></i> Belum Lengkap
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card text-center h-100 border-warning">
                            <div class="card-body">
                                <i class="bi bi-file-earmark-text fs-1 text-danger"></i>
                                <h5 class="mt-3">Laporan</h5>
                                <p class="text-muted">Cetak laporan hasil keputusan</p>
                                <a href="laporan.php" class="btn btn-danger">
                                    <i class="bi bi-printer"></i> Lihat Laporan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Informasi Kriteria -->
<div class="card mt-4">
    <div class="card-header">
        <i class="bi bi-info-circle"></i> Informasi Kriteria Penilaian
    </div>
    <div class="card-body">
        <div class="row">
            <?php
            $kriteria = fetchAll("SELECT * FROM kriteria ORDER BY kode");
            foreach ($kriteria as $k):
            ?>
            <div class="col-md-4 mb-3">
                <div class="border rounded p-3 h-100">
                    <h6 class="text-primary"><?= $k['kode'] ?> - <?= $k['nama_kriteria'] ?></h6>
                    <small class="text-muted">
                        <span class="badge bg-<?= $k['jenis'] == 'cost' ? 'danger' : 'success' ?>">
                            <?= strtoupper($k['jenis']) ?>
                        </span>
                        Bobot: <?= $k['bobot'] ?>
                    </small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="alert alert-info mt-3">
            <i class="bi bi-lightbulb"></i> 
            <strong>Tips:</strong> 
            <ul class="mb-0 mt-2">
                <li><strong>Cost:</strong> Semakin rendah nilai, semakin baik (contoh: Penghasilan)</li>
                <li><strong>Benefit:</strong> Semakin tinggi nilai, semakin baik (contoh: Jumlah Tanggungan)</li>
                <li>Skala penilaian: 1 - 6 untuk setiap kriteria</li>
            </ul>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>