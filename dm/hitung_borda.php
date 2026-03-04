<?php
$pageTitle = 'Konsensus Borda';
require_once '../includes/header.php';
requireKepalaDesa();

require_once '../functions/topsis.php';
require_once '../functions/borda.php';

$message = '';
$messageType = '';

// Proses hitung Borda
if (isset($_POST['hitung_borda'])) {
    $hasilBorda = hitungBorda();

    if (isset($hasilBorda['error'])) {
        $message = $hasilBorda['error'];
        $messageType = 'danger';
    } else {
        simpanHasilBorda($hasilBorda);
        $message = 'Perhitungan konsensus Weighted Borda berhasil dilakukan!';
        $messageType = 'success';
    }
}

// Reset hasil Borda
if (isset($_POST['reset_borda'])) {
    query("DELETE FROM hasil_borda");
    query("INSERT INTO log_aktivitas (user_id, aktivitas, keterangan) 
           VALUES (1, 'Reset Borda', 'Hasil konsensus Borda dihapus')");
    $message = 'Hasil konsensus Borda berhasil direset!';
    $messageType = 'warning';
}

// Cek status penilaian
$dmList = [
    2 => 'Kepala Desa',
    3 => 'Sekretaris Desa',
    4 => 'Ketua RT/RW'
];

$statusDM = [];
$semuaSelesai = true;

foreach ($dmList as $userId => $namaDM) {
    $penilaianLengkap = cekPenilaianLengkap($userId);
    $hasilTOPSIS = getHasilTOPSIS($userId);
    $statusDM[$userId] = [
        'nama' => $namaDM,
        'penilaian_lengkap' => $penilaianLengkap,
        'hasil_topsis' => !empty($hasilTOPSIS)
    ];

    if (!$penilaianLengkap || empty($hasilTOPSIS)) {
        $semuaSelesai = false;
    }
}

// Ambil hasil TOPSIS dari semua DM
$hasilTOPSISDM1 = getHasilTOPSIS(2);
$hasilTOPSISDM2 = getHasilTOPSIS(3);
$hasilTOPSISDM3 = getHasilTOPSIS(4);

// Cek hasil Borda
$hasilBordaAda = cekHasilBordaAda();
$hasilBordaData = $hasilBordaAda ? getHasilBorda() : [];

// Ambil detail lengkap untuk tabel Borda
$detailBorda = $semuaSelesai ? getDetailBordaLengkap() : [];
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
        <i class="bi bi-<?= $messageType == 'success' ? 'check-circle' : 'exclamation-triangle' ?>-fill"></i>
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Status Checklist -->
<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-list-check"></i> Status Kelengkapan Data
    </div>
    <div class="card-body">
        <div class="row">
            <?php foreach ($statusDM as $userId => $status): ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="text-primary">
                                <i class="bi bi-person-badge"></i> <?= $status['nama'] ?>
                            </h6>
                            <ul class="list-unstyled mb-0 mt-3">
                                <li class="mb-2">
                                    <?php if ($status['penilaian_lengkap']): ?>
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        <span class="text-success">Penilaian Lengkap</span>
                                    <?php else: ?>
                                        <i class="bi bi-x-circle-fill text-danger"></i>
                                        <span class="text-danger">Penilaian Belum Lengkap</span>
                                    <?php endif; ?>
                                </li>
                                <li>
                                    <?php if ($status['hasil_topsis']): ?>
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        <span class="text-success">Hasil TOPSIS Ada</span>
                                    <?php else: ?>
                                        <i class="bi bi-x-circle-fill text-danger"></i>
                                        <span class="text-danger">Hasil TOPSIS Belum Ada</span>
                                    <?php endif; ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <hr>

        <div class="text-center">
            <?php if ($semuaSelesai): ?>
                <div class="alert alert-success mb-3">
                    <i class="bi bi-check-circle-fill"></i>
                    <strong>Semua data lengkap!</strong> Sistem siap melakukan konsensus Weighted Borda.
                </div>

                <form method="POST" action="" style="display: inline;">
                    <button type="submit" name="hitung_borda" class="btn btn-success btn-lg"
                        onclick="return confirm('Yakin ingin melakukan perhitungan konsensus Borda?')">
                        <i class="bi bi-calculator"></i> Hitung Konsensus Borda
                    </button>
                </form>

                <?php if ($hasilBordaAda): ?>
                    <form method="POST" action="" style="display: inline;">
                        <button type="submit" name="reset_borda" class="btn btn-warning btn-lg"
                            onclick="return confirm('Yakin ingin mereset hasil Borda? Data akan dihapus!')">
                            <i class="bi bi-arrow-clockwise"></i> Reset Hasil
                        </button>
                    </form>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <strong>Belum bisa melakukan konsensus!</strong>
                    Pastikan semua Decision Maker sudah menyelesaikan penilaian.
                </div>
                <button class="btn btn-secondary btn-lg" disabled>
                    <i class="bi bi-lock"></i> Hitung Konsensus Borda
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- TABEL 1: BOBOT BORDA (Ranking dari 3 DM) -->
<?php if ($semuaSelesai): ?>
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-table"></i> TABEL 1: Bobot Borda (Ranking dari Decision Maker)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th rowspan="2">Alternatif</th>
                            <th colspan="3">Ranking dari Decision Maker</th>
                        </tr>
                        <tr class="text-center">
                            <th>DM1<br><small>(Kepala Desa)</small></th>
                            <th>DM2<br><small>(Sekretaris)</small></th>
                            <th>DM3<br><small>(RT/RW)</small></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $alternatif = fetchAll("SELECT * FROM alternatif ORDER BY kode");
                        foreach ($alternatif as $alt):
                            $dm1 = array_values(array_filter($hasilTOPSISDM1, fn($h) => $h['alternatif_id'] == $alt['id']))[0] ?? null;
                            $dm2 = array_values(array_filter($hasilTOPSISDM2, fn($h) => $h['alternatif_id'] == $alt['id']))[0] ?? null;
                            $dm3 = array_values(array_filter($hasilTOPSISDM3, fn($h) => $h['alternatif_id'] == $alt['id']))[0] ?? null;
                            ?>
                            <tr>
                                <td><strong><?= $alt['kode'] ?> - <?= $alt['nama'] ?></strong></td>
                                <td class="text-center">
                                    <span class="badge bg-info">#<?= $dm1 ? $dm1['ranking'] : '-' ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">#<?= $dm2 ? $dm2['ranking'] : '-' ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">#<?= $dm3 ? $dm3['ranking'] : '-' ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TABEL 2: PERHITUNGAN AKHIR GDSS (WEIGHTED BORDA) -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="bi bi-calculator-fill"></i>
                TABEL 2: Perhitungan Akhir Group Decision Support System (GDSS)
            </h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <h6><strong>📊 Rumus Weighted Borda Count:</strong></h6>
                <p class="mb-2">
                    <strong>Total Poin Borda</strong> = Σ (Skor TOPSIS × Bobot Ranking)
                </p>
                <p class="mb-0">
                    <strong>Bobot Ranking:</strong> Rank #1 = 4 poin | Rank #2 = 3 poin | Rank #3 = 2 poin | Rank #4 = 1
                    poin
                </p>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr class="text-center align-middle">
                            <th rowspan="2">Alternatif</th>
                            <th colspan="4">Akumulasi Skor TOPSIS per Ranking</th>
                            <th rowspan="2">Total Poin<br>Borda</th>
                            <th rowspan="2">Nilai Borda<br>(Normalisasi)</th>
                            <th rowspan="2">Ranking<br>Final</th>
                        </tr>
                        <tr class="text-center">
                            <th>Rank #1<br><small>(Bobot 4)</small></th>
                            <th>Rank #2<br><small>(Bobot 3)</small></th>
                            <th>Rank #3<br><small>(Bobot 2)</small></th>
                            <th>Rank #4<br><small>(Bobot 1)</small></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $totalSemuaPoin = 0;
                        foreach ($detailBorda as $d):
                            $totalSemuaPoin += $d['total_poin'];
                            ?>
                            <tr>
                                <td><strong><?= $d['kode'] ?> - <?= $d['nama'] ?></strong></td>
                                <td class="text-center">
                                    <?php if ($d['skor_rank_1'] > 0): ?>
                                        <span class="badge bg-warning text-dark">
                                            <?= number_format($d['skor_rank_1'], 4) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($d['skor_rank_2'] > 0): ?>
                                        <span class="badge bg-info">
                                            <?= number_format($d['skor_rank_2'], 4) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($d['skor_rank_3'] > 0): ?>
                                        <span class="badge bg-primary">
                                            <?= number_format($d['skor_rank_3'], 4) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($d['skor_rank_4'] > 0): ?>
                                        <span class="badge bg-secondary">
                                            <?= number_format($d['skor_rank_4'], 4) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center bg-light">
                                    <strong class="text-primary">
                                        <?= number_format($d['total_poin'], 4) ?>
                                    </strong>
                                </td>
                                <td class="text-center">
                                    <?= number_format($d['bobot'], 4) ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($d['ranking_final'] == 1): ?>
                                        <span class="badge badge-ranking-1 fs-6">
                                            <i class="bi bi-trophy-fill"></i> #1
                                        </span>
                                    <?php elseif ($d['ranking_final'] == 2): ?>
                                        <span class="badge badge-ranking-2 fs-6">
                                            <i class="bi bi-award-fill"></i> #2
                                        </span>
                                    <?php elseif ($d['ranking_final'] == 3): ?>
                                        <span class="badge badge-ranking-3 fs-6">
                                            <i class="bi bi-award-fill"></i> #3
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary fs-6">#<?= $d['ranking_final'] ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="table-warning fw-bold">
                            <td colspan="5" class="text-end">TOTAL:</td>
                            <td class="text-center">
                                <strong class="text-danger fs-5">
                                    <?= number_format($totalSemuaPoin, 4) ?>
                                </strong>
                            </td>
                            <td class="text-center">1.0000</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="alert alert-warning mt-3">
                <h6><strong>📌 Keterangan Perhitungan:</strong></h6>
                <ol class="mb-0">
                    <li><strong>Kolom Rank #1, #2, #3, #4:</strong> Jumlah akumulasi skor TOPSIS dari DM yang memberikan
                        ranking tersebut pada alternatif</li>
                    <li><strong>Total Poin Borda:</strong> Dihitung dengan rumus:<br>
                        <code>(Skor Rank #1 × 4) + (Skor Rank #2 × 3) + (Skor Rank #3 × 2) + (Skor Rank #4 × 1)</code>
                    </li>
                    <li><strong>Nilai Borda (Normalisasi):</strong> Total Poin dibagi dengan Total Semua Poin (untuk
                        mendapat nilai 0-1)</li>
                    <li><strong>Ranking Final:</strong> Urutan berdasarkan Total Poin Borda (tertinggi = #1)</li>
                </ol>
            </div>

            <div class="alert alert-success mt-3">
                <h6><strong>✅ Contoh Perhitungan:</strong></h6>
                <?php if (!empty($detailBorda)): ?>
                    <?php $contoh = $detailBorda[0]; ?>
                    <p class="mb-1">
                        <strong><?= $contoh['nama'] ?>:</strong>
                    </p>
                    <p class="mb-0">
                        Total Poin = (<?= number_format($contoh['skor_rank_1'], 4) ?> × 4) +
                        (<?= number_format($contoh['skor_rank_2'], 4) ?> × 3) +
                        (<?= number_format($contoh['skor_rank_3'], 4) ?> × 2) +
                        (<?= number_format($contoh['skor_rank_4'], 4) ?> × 1)<br>
                        = <?= number_format($contoh['skor_rank_1'] * 4, 4) ?> +
                        <?= number_format($contoh['skor_rank_2'] * 3, 4) ?> +
                        <?= number_format($contoh['skor_rank_3'] * 2, 4) ?> +
                        <?= number_format($contoh['skor_rank_4'] * 1, 4) ?><br>
                        = <strong><?= number_format($contoh['total_poin'], 4) ?></strong>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Hasil Konsensus Borda (Final) -->
<?php if ($hasilBordaAda): ?>
    <!-- TABEL: DATA ASLI DARI TABEL hasil_borda (TANPA KOLOM TANGGAL) -->
    <div class="card mt-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
                <i class="bi bi-database-fill"></i>
                DATA RAW – Tabel hasil_borda
            </h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-secondary">
                        <tr class="text-center">
                            <th>ID</th>
                            <th>Alternatif</th>
                            <th>Rank DM1</th>
                            <th>Rank DM2</th>
                            <th>Rank DM3</th>
                            <th>Skor DM1</th>
                            <th>Skor DM2</th>
                            <th>Skor DM3</th>
                            <th>Total Poin</th>
                            <th>Bobot</th>
                            <th>Ranking Final</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $rawBorda = fetchAll("
                        SELECT hb.*, a.kode, a.nama 
                        FROM hasil_borda hb
                        JOIN alternatif a ON a.id = hb.alternatif_id
                        ORDER BY hb.ranking_final ASC
                    ");

                        foreach ($rawBorda as $row):
                            ?>
                            <tr>
                                <td class="text-center"><?= $row['id'] ?></td>
                                <td><strong><?= $row['kode'] ?> - <?= $row['nama'] ?></strong></td>
                                <td class="text-center">#<?= $row['rank_dm1'] ?></td>
                                <td class="text-center">#<?= $row['rank_dm2'] ?></td>
                                <td class="text-center">#<?= $row['rank_dm3'] ?></td>
                                <td class="text-center"><?= number_format($row['skor_dm1'], 8) ?></td>
                                <td class="text-center"><?= number_format($row['skor_dm2'], 8) ?></td>
                                <td class="text-center"><?= number_format($row['skor_dm3'], 8) ?></td>
                                <td class="text-center"><strong><?= number_format($row['total_poin'], 8) ?></strong></td>
                                <td class="text-center"><?= number_format($row['bobot'], 8) ?></td>
                                <td class="text-center"><strong>#<?= $row['ranking_final'] ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);">
            <h5 class="mb-0 text-dark">
                <i class="bi bi-trophy-fill"></i>
                HASIL KONSENSUS BORDA (FINAL RANKING)
            </h5>
        </div>
        <div class="card-body">
            <!-- Pemenang -->
            <?php $pemenang = $hasilBordaData[0]; ?>
            <div class="alert"
                style="background: linear-gradient(135deg, #0a1929 0%, #1a237e 100%); border: none; color: #fff; box-shadow: 0 8px 32px rgba(13, 71, 161, 0.4);">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center">
                        <i class="bi bi-trophy-fill"
                            style="font-size: 80px; color: #42a5f5; text-shadow: 0 0 20px rgba(66, 165, 245, 0.6);"></i>
                    </div>
                    <div class="col-md-10">
                        <h3 class="mb-2" style="color: #64b5f6; text-shadow: 0 0 10px rgba(100, 181, 246, 0.5);">🎉
                            PEMENANG: <?= $pemenang['nama'] ?></h3>
                        <p class="mb-1" style="color: #e3f2fd;">
                            <i class="bi bi-geo-alt-fill" style="color: #42a5f5;"></i> <strong>Alamat:</strong>
                            <?= $pemenang['alamat'] ?>
                        </p>
                        <p class="mb-1" style="color: #e3f2fd;">
                            <i class="bi bi-card-text" style="color: #42a5f5;"></i> <strong>No. KK:</strong>
                            <?= $pemenang['no_kk'] ?>
                        </p>
                        <p class="mb-0" style="color: #e3f2fd;">
                            <i class="bi bi-star-fill" style="color: #42a5f5;"></i>
                            <strong style="color: #64b5f6;">Total Poin Borda:
                                <?= number_format($pemenang['total_poin'], 4) ?></strong> |
                            <strong style="color: #64b5f6;">Bobot: <?= number_format($pemenang['bobot'], 4) ?></strong>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tabel Ranking Lengkap -->
            <div class="table-responsive mt-4">
                <table class="table table-bordered table-hover">
                    <thead class="table-success">
                        <tr class="text-center">
                            <th>Ranking Final</th>
                            <th>Kode</th>
                            <th>Nama Alternatif</th>
                            <th>Rank DM1</th>
                            <th>Rank DM2</th>
                            <th>Rank DM3</th>
                            <th>Total Poin</th>
                            <th>Bobot</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hasilBordaData as $h): ?>
                            <tr>
                                <td class="text-center">
                                    <?php if ($h['ranking_final'] == 1): ?>
                                        <span class="badge badge-ranking-1 fs-5">
                                            <i class="bi bi-trophy-fill"></i> #1
                                        </span>
                                    <?php elseif ($h['ranking_final'] == 2): ?>
                                        <span class="badge badge-ranking-2 fs-5">
                                            <i class="bi bi-award-fill"></i> #2
                                        </span>
                                    <?php elseif ($h['ranking_final'] == 3): ?>
                                        <span class="badge badge-ranking-3 fs-5">
                                            <i class="bi bi-award-fill"></i> #3
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary fs-5">#<?= $h['ranking_final'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= $h['kode'] ?></strong></td>
                                <td><?= $h['nama'] ?></td>
                                <td class="text-center">#<?= $h['rank_dm1'] ?></td>
                                <td class="text-center">#<?= $h['rank_dm2'] ?></td>
                                <td class="text-center">#<?= $h['rank_dm3'] ?></td>
                                <td class="text-center"><strong><?= number_format($h['total_poin'], 4) ?></strong></td>
                                <td class="text-center"><?= number_format($h['bobot'], 4) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="text-center mt-4">
                <a href="laporan.php" class="btn btn-success btn-lg">
                    <i class="bi bi-file-earmark-text"></i> Lihat Laporan Lengkap
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>