<?php
$pageTitle = 'Input Penilaian';
require_once '../includes/header.php';
requireDM();
require_once '../functions/topsis.php';

$user_id = $_SESSION['user_id'];
$message = '';
$messageType = '';

// Proses submit form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $alternatif_id = clean($_POST['alternatif_id']);
    $c1 = clean($_POST['c1_pekerjaan']);
    $c2 = clean($_POST['c2_tanggungan']);
    $c3 = clean($_POST['c3_penghasilan']);
    $c4 = clean($_POST['c4_kondisi_rumah']);
    $c5 = clean($_POST['c5_status_rumah']);
    $c6 = clean($_POST['c6_kepemilikan']);
    
    // Validasi nilai 1-6
    if ($c1 < 1 || $c1 > 6 || $c2 < 1 || $c2 > 6 || $c3 < 1 || $c3 > 6 || 
        $c4 < 1 || $c4 > 6 || $c5 < 1 || $c5 > 6 || $c6 < 1 || $c6 > 6) {
        $message = 'Nilai harus antara 1 sampai 6!';
        $messageType = 'danger';
    } else {
        // Cek apakah sudah ada penilaian sebelumnya
        $cek = fetch("SELECT * FROM penilaian WHERE user_id = $user_id AND alternatif_id = $alternatif_id");
        
        if ($cek) {
            // Update
            query("UPDATE penilaian SET 
                   c1_pekerjaan = $c1,
                   c2_tanggungan = $c2,
                   c3_penghasilan = $c3,
                   c4_kondisi_rumah = $c4,
                   c5_status_rumah = $c5,
                   c6_kepemilikan = $c6
                   WHERE user_id = $user_id AND alternatif_id = $alternatif_id");
            $message = 'Penilaian berhasil diupdate!';
        } else {
            // Insert
            query("INSERT INTO penilaian 
                   (user_id, alternatif_id, c1_pekerjaan, c2_tanggungan, c3_penghasilan, 
                    c4_kondisi_rumah, c5_status_rumah, c6_kepemilikan)
                   VALUES ($user_id, $alternatif_id, $c1, $c2, $c3, $c4, $c5, $c6)");
            $message = 'Penilaian berhasil disimpan!';
        }
        
        $messageType = 'success';
        
        // Log aktivitas
        $namaAlternatif = fetch("SELECT nama FROM alternatif WHERE id = $alternatif_id")['nama'];
        query("INSERT INTO log_aktivitas (user_id, aktivitas, keterangan) 
               VALUES ($user_id, 'Input Penilaian', 'Penilaian untuk $namaAlternatif')");
        
        // Cek jika semua penilaian sudah lengkap, otomatis hitung TOPSIS
        if (cekPenilaianLengkap($user_id)) {
            $hasilTOPSIS = hitungTOPSIS($user_id);
            if (!isset($hasilTOPSIS['error'])) {
                simpanHasilTOPSIS($user_id, $hasilTOPSIS);
                $message .= ' Perhitungan TOPSIS otomatis telah selesai!';
            }
        }
    }
}

// Ambil semua alternatif
$alternatif = fetchAll("SELECT * FROM alternatif ORDER BY kode");

// Ambil penilaian yang sudah ada
$penilaianAda = fetchAll("SELECT * FROM penilaian WHERE user_id = $user_id");
$penilaianMap = [];
foreach ($penilaianAda as $p) {
    $penilaianMap[$p['alternatif_id']] = $p;
}
?>

<?php if ($message): ?>
<div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
    <i class="bi bi-<?= $messageType == 'success' ? 'check-circle' : 'exclamation-triangle' ?>-fill"></i>
    <?= $message ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Info -->
<div class="alert alert-info">
    <i class="bi bi-info-circle-fill"></i>
    <strong>Petunjuk:</strong>
    <ul class="mb-0 mt-2">
        <li>Berikan penilaian untuk setiap alternatif berdasarkan kondisi yang Anda ketahui</li>
        <li>Nilai berkisar antara <strong>1 sampai 6</strong> untuk setiap kriteria</li>
        <li>Anda dapat mengubah penilaian kapan saja sebelum Kepala Desa melakukan konsensus</li>
        <li>Sistem akan otomatis menghitung TOPSIS setelah semua penilaian selesai</li>
    </ul>
</div>

<!-- Form Penilaian -->
<?php foreach ($alternatif as $alt): ?>
<?php
$sudahDinilai = isset($penilaianMap[$alt['id']]);
$nilai = $sudahDinilai ? $penilaianMap[$alt['id']] : null;
?>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-person-badge"></i>
            <strong><?= $alt['kode'] ?> - <?= $alt['nama'] ?></strong>
        </span>
        <?php if ($sudahDinilai): ?>
            <span class="badge bg-success">
                <i class="bi bi-check-circle"></i> Sudah Dinilai
            </span>
        <?php else: ?>
            <span class="badge bg-warning">
                <i class="bi bi-hourglass-split"></i> Belum Dinilai
            </span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <!-- Info Alternatif -->
        <div class="alert alert-light">
            <div class="row">
                <div class="col-md-6">
                    <small><strong>Alamat:</strong> <?= $alt['alamat'] ?></small>
                </div>
                <div class="col-md-6">
                    <small><strong>Keterangan:</strong> <?= $alt['keterangan'] ?></small>
                </div>
            </div>
        </div>
        
        <!-- Form -->
        <form method="POST" action="">
            <input type="hidden" name="alternatif_id" value="<?= $alt['id'] ?>">
            
            <div class="row">
                <!-- C1 -->
                <div class="col-md-4 mb-3">
                    <label class="form-label"><strong>C1 - Pekerjaan Orang Tua</strong></label>
                    <select name="c1_pekerjaan" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <?php for($i=1; $i<=6; $i++): ?>
                        <option value="<?= $i ?>" <?= $nilai && $nilai['c1_pekerjaan'] == $i ? 'selected' : '' ?>>
                            <?= $i ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                    <small class="text-muted">1=Pengangguran, 6=PNS</small>
                </div>
                
                <!-- C2 -->
                <div class="col-md-4 mb-3">
                    <label class="form-label"><strong>C2 - Jumlah Tanggungan</strong></label>
                    <select name="c2_tanggungan" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <?php for($i=1; $i<=6; $i++): ?>
                        <option value="<?= $i ?>" <?= $nilai && $nilai['c2_tanggungan'] == $i ? 'selected' : '' ?>>
                            <?= $i ?> orang
                        </option>
                        <?php endfor; ?>
                    </select>
                    <small class="text-muted">1=1 orang, 6=6+ orang</small>
                </div>
                
                <!-- C3 -->
                <div class="col-md-4 mb-3">
                    <label class="form-label"><strong>C3 - Sumber Penghasilan</strong></label>
                    <select name="c3_penghasilan" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <?php for($i=1; $i<=6; $i++): ?>
                        <option value="<?= $i ?>" <?= $nilai && $nilai['c3_penghasilan'] == $i ? 'selected' : '' ?>>
                            <?= $i ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                    <small class="text-muted">1=<500rb, 6=>3jt</small>
                </div>
                
                <!-- C4 -->
                <div class="col-md-4 mb-3">
                    <label class="form-label"><strong>C4 - Kondisi Rumah</strong></label>
                    <select name="c4_kondisi_rumah" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <?php for($i=1; $i<=6; $i++): ?>
                        <option value="<?= $i ?>" <?= $nilai && $nilai['c4_kondisi_rumah'] == $i ? 'selected' : '' ?>>
                            <?= $i ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                    <small class="text-muted">1=Sangat rusak, 6=Sangat bagus</small>
                </div>
                
                <!-- C5 -->
                <div class="col-md-4 mb-3">
                    <label class="form-label"><strong>C5 - Status Rumah</strong></label>
                    <select name="c5_status_rumah" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <?php for($i=1; $i<=6; $i++): ?>
                        <option value="<?= $i ?>" <?= $nilai && $nilai['c5_status_rumah'] == $i ? 'selected' : '' ?>>
                            <?= $i ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                    <small class="text-muted">1=Menumpang, 6=Milik sendiri</small>
                </div>
                
                <!-- C6 -->
                <div class="col-md-4 mb-3">
                    <label class="form-label"><strong>C6 - Kepemilikan Rumah Lain</strong></label>
                    <select name="c6_kepemilikan" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <?php for($i=1; $i<=6; $i++): ?>
                        <option value="<?= $i ?>" <?= $nilai && $nilai['c6_kepemilikan'] == $i ? 'selected' : '' ?>>
                            <?= $i ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                    <small class="text-muted">1=Tidak punya, 6=Punya 3+</small>
                </div>
            </div>
            
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> 
                    <?= $sudahDinilai ? 'Update Penilaian' : 'Simpan Penilaian' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?php endforeach; ?>

<!-- Tombol Hitung Manual TOPSIS (jika belum otomatis) -->
<?php if (cekPenilaianLengkap($user_id)): ?>
<div class="card">
    <div class="card-body text-center">
        <h5>Semua Penilaian Sudah Lengkap!</h5>
        <p class="text-muted">Klik tombol di bawah untuk menghitung atau melihat hasil TOPSIS Anda</p>
        <a href="hasil_topsis.php" class="btn btn-success btn-lg">
            <i class="bi bi-bar-chart"></i> Lihat Hasil TOPSIS
        </a>
    </div>
</div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>