<?php
$pageTitle = 'Hasil TOPSIS Individual';
require_once '../includes/header.php';
requireDM();

require_once '../functions/topsis.php';

$user_id = $_SESSION['user_id'];
$user_info = fetch("SELECT nama, role FROM users WHERE id = $user_id");

// PAKSA HITUNG ULANG SELALU
query("DELETE FROM hasil_topsis WHERE user_id = $user_id");

// Cek apakah sudah ada penilaian lengkap
if (!cekPenilaianLengkap($user_id)) {
    echo '<div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <strong>Perhatian!</strong> Anda belum menyelesaikan semua penilaian.
            <a href="input_penilaian.php" class="alert-link">Lengkapi penilaian →</a>
          </div>';
    require_once '../includes/footer.php';
    exit;
}

// Hitung TOPSIS dengan detail lengkap
$hasilLengkap = hitungTOPSIS($user_id);

if (isset($hasilLengkap['error'])) {
    echo '<div class="alert alert-danger">' . $hasilLengkap['error'] . '</div>';
    require_once '../includes/footer.php';
    exit;
}

// Simpan hasil ke database
simpanHasilTOPSIS($user_id, $hasilLengkap);

// Ekstrak data
$matriks = $hasilLengkap['matriks_keputusan'];
$normalized = $hasilLengkap['matriks_ternormalisasi'];
$weighted = $hasilLengkap['matriks_terbobot'];
$idealPositif = $hasilLengkap['ideal_positif'];
$idealNegatif = $hasilLengkap['ideal_negatif'];
$jarakPositif = $hasilLengkap['jarak_positif'];
$jarakNegatif = $hasilLengkap['jarak_negatif'];
$preferensi = $hasilLengkap['preferensi'];
$alternatif = $hasilLengkap['alternatif'];
$kriteria = $hasilLengkap['kriteria'];
$ranking = $hasilLengkap['ranking'];

// Data untuk chart
$labels = [];
$scores = [];
$colors = ['#FFD700', '#C0C0C0', '#CD7F32', '#4BC0C0'];

foreach ($ranking as $h) {
    $labels[] = $h['nama'];
    $scores[] = round($h['skor'], 8);
}
?>

<style>
@media print {
    .no-print { display: none !important; }
    .card { page-break-inside: avoid; }
}
</style>

<!-- Header Info -->
<div class="alert alert-info mb-4 no-print">
    <div class="row">
        <div class="col-md-6">
            <h5><i class="bi bi-person-badge"></i> Decision Maker: <?= $user_info['nama'] ?></h5>
            <p class="mb-0">Role: <?= getRoleDisplay($user_info['role']) ?></p>
        </div>
        <div class="col-md-6 text-end">
            <h5><i class="bi bi-calendar"></i> Tanggal: <?= date('d F Y') ?></h5>
            <p class="mb-0">Waktu: <?= date('H:i:s') ?> WIB</p>
        </div>
    </div>
</div>

<!-- KESIMPULAN -->
<div class="card mb-4" style="background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); border: 3px solid #FF8C00;">
    <div class="card-body">
        <div class="text-center" style="color: #000;">
            <h3 class="mb-3">
                <i class="bi bi-check-circle-fill"></i> KESIMPULAN
            </h3>
            <hr style="border-color: #000; opacity: 0.3;">
            <?php $pemenang = $ranking[0]; ?>
            <h4>
                Alternatif terbaik adalah <strong style="font-size: 28px;"><?= $pemenang['nama'] ?></strong>
            </h4>
            <h5>
                dengan skor V<sub>i</sub> = <code style="font-size: 24px; background: #000; color: #FFD700; padding: 10px 20px; border-radius: 8px;"><strong><?= number_format($pemenang['skor'], 8) ?></strong></code>
            </h5>
        </div>
    </div>
</div>

<!-- TABEL 1: KRITERIA DAN BOBOT -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-list-check"></i> TABEL 1: Kriteria dan Bobot Kriteria</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr class="text-center">
                        <th width="50%">Kriteria</th>
                        <th width="25%">Bobot</th>
                        <th width="25%">Tipe</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kriteria as $k): ?>
                    <tr>
                        <td><?= $k['nama_kriteria'] ?> (<?= $k['kode'] ?>)</td>
                        <td class="text-center"><strong><?= $k['bobot'] ?></strong></td>
                        <td class="text-center">
                            <span class="badge bg-<?= $k['jenis'] == 'benefit' ? 'success' : 'danger' ?>">
                                <?= strtoupper($k['jenis']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TABEL 2: MATRIKS KEPUTUSAN (X) -->
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="bi bi-table"></i> TABEL 2: Matriks Keputusan dengan Nilai Numerik</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr class="text-center">
                        <th>Alternatif</th>
                        <?php foreach ($kriteria as $k): ?>
                        <th><?= $k['kode'] ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < count($alternatif); $i++): ?>
                    <tr>
                        <td><strong><?= $alternatif[$i]['nama'] ?></strong></td>
                        <?php for ($j = 0; $j < count($matriks[$i]); $j++): ?>
                        <td class="text-center"><?= $matriks[$i][$j] ?></td>
                        <?php endfor; ?>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TABEL 3: MATRIKS TERNORMALISASI (R) -->
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="bi bi-calculator"></i> TABEL 3: Matriks Keputusan Ternormalisasi</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-light">
            <strong>Rumus:</strong> r<sub>ij</sub> = x<sub>ij</sub> / √(Σx<sub>ij</sub>²)
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr class="text-center">
                        <th>Alternatif</th>
                        <?php foreach ($kriteria as $k): ?>
                        <th><?= $k['kode'] ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < count($alternatif); $i++): ?>
                    <tr>
                        <td><strong><?= $alternatif[$i]['nama'] ?></strong></td>
                        <?php for ($j = 0; $j < count($normalized[$i]); $j++): ?>
                        <td class="text-center"><code><?= number_format($normalized[$i][$j], 8) ?></code></td>
                        <?php endfor; ?>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TABEL 4: MATRIKS TERBOBOT (Y) -->
<div class="card mb-4">
    <div class="card-header bg-warning">
        <h5 class="mb-0"><i class="bi bi-percent"></i> TABEL 4: Matriks Keputusan Ternormalisasi Terbobot</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-light">
            <strong>Rumus:</strong> y<sub>ij</sub> = r<sub>ij</sub> × w<sub>j</sub>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr class="text-center">
                        <th>Alternatif</th>
                        <?php foreach ($kriteria as $k): ?>
                        <th><?= $k['kode'] ?><br><small>(w=<?= $k['bobot'] ?>)</small></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < count($alternatif); $i++): ?>
                    <tr>
                        <td><strong><?= $alternatif[$i]['nama'] ?></strong></td>
                        <?php for ($j = 0; $j < count($weighted[$i]); $j++): ?>
                        <td class="text-center"><code><?= number_format($weighted[$i][$j], 8) ?></code></td>
                        <?php endfor; ?>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TABEL 5: SOLUSI IDEAL -->
<div class="card mb-4">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0"><i class="bi bi-star"></i> TABEL 5: Solusi Ideal Positif dan Negatif</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-light">
            <strong>Aturan:</strong><br>
            • <strong>Benefit:</strong> A<sup>+</sup> = max(y<sub>ij</sub>), A<sup>-</sup> = min(y<sub>ij</sub>)<br>
            • <strong>Cost:</strong> A<sup>+</sup> = min(y<sub>ij</sub>), A<sup>-</sup> = max(y<sub>ij</sub>)
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr class="text-center">
                        <th>Solusi Ideal</th>
                        <?php foreach ($kriteria as $k): ?>
                        <th><?= $k['kode'] ?><br><small>(<?= strtoupper($k['jenis']) ?>)</small></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr class="table-success">
                        <td><strong>A<sup>+</sup> (Positif)</strong></td>
                        <?php foreach ($idealPositif as $val): ?>
                        <td class="text-center"><code><?= number_format($val, 8) ?></code></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr class="table-danger">
                        <td><strong>A<sup>-</sup> (Negatif)</strong></td>
                        <?php foreach ($idealNegatif as $val): ?>
                        <td class="text-center"><code><?= number_format($val, 8) ?></code></td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TABEL 6: JARAK -->
<div class="card mb-4">
    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <h5 class="mb-0"><i class="bi bi-rulers"></i> TABEL 6: Jarak Tiap Alternatif dari Solusi Ideal Positif dan Negatif</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-light">
            <strong>Rumus:</strong><br>
            • D<sup>+</sup> = √(Σ(y<sub>ij</sub> - A<sup>+</sup>)<sup>2</sup>)<br>
            • D<sup>-</sup> = √(Σ(y<sub>ij</sub> - A<sup>-</sup>)<sup>2</sup>)
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr class="text-center">
                        <th width="40%">Alternatif</th>
                        <th width="30%">D<sup>+</sup> (Jarak ke A+)</th>
                        <th width="30%">D<sup>-</sup> (Jarak ke A-)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < count($alternatif); $i++): ?>
                    <tr>
                        <td><strong><?= $alternatif[$i]['nama'] ?></strong></td>
                        <td class="text-center"><code><?= number_format($jarakPositif[$i], 8) ?></code></td>
                        <td class="text-center"><code><?= number_format($jarakNegatif[$i], 8) ?></code></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TABEL 7: SKOR PREFERENSI -->
<div class="card mb-4">
    <div class="card-header bg-secondary text-white">
        <h5 class="mb-0"><i class="bi bi-award"></i> TABEL 7: Skor Akhir dari Setiap Alternatif</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-light">
            <strong>Rumus:</strong> V<sub>i</sub> = D<sup>-</sup> / (D<sup>+</sup> + D<sup>-</sup>)
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr class="text-center">
                        <th width="60%">Alternatif</th>
                        <th width="40%">V<sub>i</sub> (Skor Preferensi)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < count($alternatif); $i++): ?>
                    <tr>
                        <td><strong><?= $alternatif[$i]['nama'] ?></strong></td>
                        <td class="text-center"><code style="font-size: 16px;"><strong><?= number_format($preferensi[$i], 8) ?></strong></code></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TABEL 8: RANKING FINAL -->
<div class="card mb-4" style="border: 3px solid #FFD700;">
    <div class="card-header" style="background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); color: #000;">
        <h5 class="mb-0"><i class="bi bi-trophy-fill"></i> TABEL 8: Hasil Akhir dan Ranking</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <tr class="text-center">
                        <th width="20%">Ranking</th>
                        <th width="15%">Kode</th>
                        <th width="35%">Alternatif</th>
                        <th width="30%">Skor (V<sub>i</sub>)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ranking as $h): ?>
                    <tr>
                        <td class="text-center">
                            <?php if ($h['ranking'] == 1): ?>
                                <span class="badge badge-ranking-1" style="font-size: 18px; padding: 10px 15px;">
                                    <i class="bi bi-trophy-fill"></i> PERINGKAT #<?= $h['ranking'] ?>
                                </span>
                            <?php elseif ($h['ranking'] == 2): ?>
                                <span class="badge badge-ranking-2" style="font-size: 16px; padding: 8px 12px;">
                                    <i class="bi bi-award-fill"></i> PERINGKAT #<?= $h['ranking'] ?>
                                </span>
                            <?php elseif ($h['ranking'] == 3): ?>
                                <span class="badge badge-ranking-3" style="font-size: 16px; padding: 8px 12px;">
                                    <i class="bi bi-award-fill"></i> PERINGKAT #<?= $h['ranking'] ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary" style="font-size: 14px; padding: 6px 10px;">
                                    PERINGKAT #<?= $h['ranking'] ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center"><strong><?= $h['kode'] ?></strong></td>
                        <td><strong><?= $h['nama'] ?></strong></td>
                        <td class="text-center">
                            <code style="font-size: 18px; font-weight: bold; color: #000;">
                                <?= number_format($h['skor'], 8) ?>
                            </code>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="text-center mt-4 mb-4 no-print">
    <a href="export.php" class="btn btn-success btn-lg">
        <i class="bi bi-file-earmark-excel"></i> Export ke Excel (Lengkap)
    </a>
    <button onclick="window.print()" class="btn btn-secondary btn-lg">
        <i class="bi bi-printer"></i> Cetak Hasil
    </button>
    <a href="dashboard.php" class="btn btn-primary btn-lg">
        <i class="bi bi-house"></i> Kembali ke Dashboard
    </a>
</div>

<?php require_once '../includes/footer.php'; ?>