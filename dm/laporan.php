<?php
require_once '../functions/borda.php';
require_once '../functions/export.php';

// Export Excel HARUS DI ATAS sebelum header.php
if (isset($_GET['export']) && $_GET['export'] == 'borda') {
    exportHasilBordaExcel();
    exit; // WAJIB!
}

$pageTitle = 'Laporan';
require_once '../includes/header.php';
requireKepalaDesa();


// Cek hasil Borda
if (!cekHasilBordaAda()) {
    echo '<div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <strong>Belum Ada Data!</strong> Lakukan perhitungan konsensus Borda terlebih dahulu.
            <a href="hitung_borda.php" class="alert-link">Hitung Borda →</a>
          </div>';
    require_once '../includes/footer.php';
    exit;
}

$hasilBorda = getHasilBorda();
$pemenang = getPemenang();
$kriteria = fetchAll("SELECT * FROM kriteria ORDER BY kode");

// Data DM
$dmList = [
    2 => 'Kepala Desa',
    3 => 'Sekretaris Desa',
    4 => 'Ketua RT/RW'
];
?>

<!-- Action Buttons -->
<div class="mb-4 no-print">
    <button onclick="window.print()" class="btn btn-primary btn-lg">
        <i class="bi bi-printer"></i> Cetak Laporan
    </button>
    <a href="?export=borda" class="btn btn-success btn-lg">
        <i class="bi bi-file-earmark-excel"></i> Export ke Excel
    </a>
    <a href="dashboard.php" class="btn btn-secondary btn-lg">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<!-- Laporan (untuk print) -->
<div id="laporanContent">
    <!-- Header Laporan -->
    <div class="text-center mb-4">
        <h3><strong>LAPORAN HASIL KEPUTUSAN</strong></h3>
        <h4>Group Decision Support System</h4>
        <h5>Penentuan Kelayakan Penerima Bantuan Rumah Keluarga Miskin</h5>
        <hr>
        <p class="mb-0">
            <strong>Tanggal:</strong> <?= date('d F Y') ?><br>
            <strong>Waktu:</strong> <?= date('H:i:s') ?> WIB
        </p>
    </div>
    
    <!-- 1. Pemenang -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="bi bi-trophy-fill"></i> 
                I. HASIL KEPUTUSAN PENERIMA BANTUAN
            </h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th width="30%">Kode Alternatif</th>
                    <td><?= $pemenang['kode'] ?></td>
                </tr>
                <tr>
                    <th>Nama Lengkap</th>
                    <td><strong><?= $pemenang['nama'] ?></strong></td>
                </tr>
                <tr>
                    <th>Alamat</th>
                    <td><?= $pemenang['alamat'] ?></td>
                </tr>
                <tr>
                    <th>No. Kartu Keluarga</th>
                    <td><?= $pemenang['no_kk'] ?></td>
                </tr>
                <tr>
                    <th>Ranking Final</th>
                    <td><span class="badge bg-success fs-5">#1</span></td>
                </tr>
                <tr>
                    <th>Total Poin Borda</th>
                    <td><strong><?= $pemenang['total_poin'] ?></strong></td>
                </tr>
                <tr>
                    <th>Bobot</th>
                    <td><strong><?= number_format($pemenang['bobot'], 3) ?></strong></td>
                </tr>
            </table>
        </div>
    </div>
    
    <!-- 2. Metode yang Digunakan -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="bi bi-gear-fill"></i> 
                II. METODE YANG DIGUNAKAN
            </h5>
        </div>
        <div class="card-body">
            <h6><strong>A. Metode TOPSIS (Penilaian Individual)</strong></h6>
            <p>
                Setiap Decision Maker melakukan penilaian individual terhadap alternatif 
                menggunakan metode TOPSIS (Technique for Order of Preference by Similarity 
                to Ideal Solution) dengan 6 kriteria.
            </p>
            
            <h6 class="mt-3"><strong>B. Metode Borda (Konsensus Kelompok)</strong></h6>
            <p>
                Hasil ranking dari ketiga Decision Maker diagregasi menggunakan metode Borda 
                untuk mendapatkan konsensus kelompok yang objektif.
            </p>
            
            <h6 class="mt-3"><strong>C. Decision Maker yang Terlibat</strong></h6>
            <ul>
                <li>DM1: Kepala Desa</li>
                <li>DM2: Sekretaris Desa</li>
                <li>DM3: Ketua RT/RW</li>
            </ul>
        </div>
    </div>
    
    <!-- 3. Kriteria Penilaian -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="bi bi-list-check"></i> 
                III. KRITERIA PENILAIAN
            </h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Kriteria</th>
                        <th>Jenis</th>
                        <th>Bobot</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kriteria as $k): ?>
                    <tr>
                        <td><strong><?= $k['kode'] ?></strong></td>
                        <td><?= $k['nama_kriteria'] ?></td>
                        <td>
                            <span class="badge bg-<?= $k['jenis'] == 'cost' ? 'danger' : 'success' ?>">
                                <?= strtoupper($k['jenis']) ?>
                            </span>
                        </td>
                        <td><?= $k['bobot'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 5. TABEL RAW BORDA -->
<div class="card mb-4">
    <div class="card-header bg-secondary text-white">
        <h5 class="mb-0">
            <i class="bi bi-table"></i> 
            IV. TABEL PERHITUNGAN BORDA (RAW DATA)
        </h5>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
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

                    foreach ($rawBorda as $row): ?>
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
    
    <!-- 4. Ranking Lengkap -->
    <div class="card mb-4">
        <div class="card-header bg-warning">
            <h5 class="mb-0">
                <i class="bi bi-bar-chart-fill"></i> 
                V. HASIL RANKING LENGKAP
            </h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr class="text-center">
                        <th>Ranking Final</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Rank DM1</th>
                        <th>Rank DM2</th>
                        <th>Rank DM3</th>
                        <th>Total Poin</th>
                        <th>Bobot</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($hasilBorda as $h): ?>
                    <tr>
                        <td class="text-center">
                            <strong><?= $h['ranking_final'] ?></strong>
                        </td>
                        <td><?= $h['kode'] ?></td>
                        <td><?= $h['nama'] ?></td>
                        <td class="text-center"><?= $h['rank_dm1'] ?></td>
                        <td class="text-center"><?= $h['rank_dm2'] ?></td>
                        <td class="text-center"><?= $h['rank_dm3'] ?></td>
                        <td class="text-center"><strong><?= $h['total_poin'] ?></strong></td>
                        <td class="text-center"><?= number_format($h['bobot'], 3) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- 5. Kesimpulan -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="bi bi-check-circle-fill"></i> 
                VI. KESIMPULAN
            </h5>
        </div>
        <div class="card-body">
            <p>
                Berdasarkan hasil perhitungan menggunakan Group Decision Support System 
                dengan metode TOPSIS dan Borda, maka:
            </p>
            <ol>
                <li>
                    <strong>Penerima bantuan rumah yang terpilih adalah:</strong><br>
                    <strong class="text-primary fs-5"><?= $pemenang['nama'] ?></strong> 
                    (<?= $pemenang['kode'] ?>)
                </li>
                <li>
                    Alternatif tersebut mendapat <strong>Ranking #1</strong> dengan 
                    <strong>Total Poin Borda: <?= $pemenang['total_poin'] ?></strong>
                </li>
                <li>
                    Keputusan ini merupakan hasil konsensus dari 3 Decision Maker yang 
                    telah melakukan penilaian secara objektif berdasarkan kriteria yang telah ditetapkan.
                </li>
            </ol>
        </div>
    </div>
    
    <!-- Tanda Tangan -->
    <div class="mt-5">
        <div class="row">
            <div class="col-md-6 text-center">
                <p>Mengetahui,</p>
                <p><strong>Kepala Desa</strong></p>
                <br><br><br>
                <p>_______________________</p>
            </div>
            <div class="col-md-6 text-center">
                <p><?= date('d F Y') ?></p>
                <p><strong>Administrator</strong></p>
                <br><br><br>
                <p>_______________________</p>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    .sidebar, .top-navbar, footer {
        display: none !important;
    }
    .main-content {
        margin-left: 0 !important;
    }
    .card {
        page-break-inside: avoid;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?>