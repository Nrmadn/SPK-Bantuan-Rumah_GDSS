<?php
$pageTitle = 'Hasil Final (Konsensus Borda)';
require_once '../includes/header.php';
requireDM();

require_once '../functions/borda.php';

// Cek apakah sudah ada hasil Borda
if (!cekHasilBordaAda()) {
    echo '<div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <strong>Belum Ada Hasil!</strong> Admin belum melakukan perhitungan konsensus Borda.
          </div>';
    require_once '../includes/footer.php';
    exit;
}

$hasilBorda = getHasilBorda();
$pemenang = getPemenang();

// Data untuk chart
$labels = [];
$poinBorda = [];
$colors = ['#FFD700', '#C0C0C0', '#CD7F32', '#4BC0C0'];

foreach ($hasilBorda as $h) {
    $labels[] = $h['nama'];
    $poinBorda[] = $h['total_poin'];
}
?>

<!-- Alert Pemenang -->
<div class="alert"
    style="background: linear-gradient(135deg, #0a1929 0%, #1a237e 100%); border: none; color: #fff; box-shadow: 0 8px 32px rgba(13, 71, 161, 0.4);">
    <div class="row align-items-center">
        <div class="col-md-2 text-center">
            <i class="bi bi-trophy-fill"
                style="font-size: 100px; color: #42a5f5; text-shadow: 0 0 20px rgba(66, 165, 245, 0.6);"></i>
        </div>
        <div class="col-md-10">
            <h2 class="mb-3" style="color: #64b5f6; text-shadow: 0 0 10px rgba(100, 181, 246, 0.5);">🎉 PENERIMA BANTUAN
                TERPILIH</h2>
            <h3 class="mb-2"><strong style="color: #90caf9;"><?= $pemenang['nama'] ?></strong></h3>
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
                <strong style="color: #64b5f6;">Total Poin Borda: <?= $pemenang['total_poin'] ?></strong> |
                <strong style="color: #64b5f6;">Bobot: <?= number_format($pemenang['bobot'], 3) ?></strong>
            </p>
        </div>
    </div>
</div>

<!-- Info Konsensus -->
<div class="card mb-4">
    <div class="card-body">
        <h5><i class="bi bi-info-circle"></i> Tentang Konsensus Borda</h5>
        <p>
            Hasil ini merupakan <strong>konsensus kelompok</strong> dari penilaian
            <strong>3 Decision Maker</strong> (Kepala Desa, Sekretaris Desa, dan Ketua RT)
            yang dihitung menggunakan <strong>Metode Borda</strong>.
        </p>
        <p class="mb-0">
            Metode Borda mengagregasi ranking dari setiap Decision Maker untuk menghasilkan
            ranking final yang lebih objektif dan representatif.
        </p>
    </div>
</div>

<!-- Tabel Hasil Final -->
<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-table"></i> Hasil Ranking Final (Konsensus Borda)
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr class="text-center">
                        <th width="15%">Ranking Final</th>
                        <th width="20%">Kode</th>
                        <th width="25%">Nama</th>
                        <th width="15%">Rank DM1</th>
                        <th width="15%">Rank DM2</th>
                        <th width="15%">Rank DM3</th>
                        <th width="15%">Total Poin</th>
                        <th width="15%">Bobot</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($hasilBorda as $h): ?>
                        <tr>
                            <td class="text-center">
                                <?php if ($h['ranking_final'] == 1): ?>
                                    <span class="badge badge-ranking-1 fs-4">
                                        <i class="bi bi-trophy-fill"></i> Juara 1
                                    </span>
                                <?php elseif ($h['ranking_final'] == 2): ?>
                                    <span class="badge badge-ranking-2 fs-5">
                                        <i class="bi bi-award-fill"></i> Juara 2
                                    </span>
                                <?php elseif ($h['ranking_final'] == 3): ?>
                                    <span class="badge badge-ranking-3 fs-5">
                                        <i class="bi bi-award-fill"></i> Juara 3
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary fs-5">
                                        Peringkat <?= $h['ranking_final'] ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= $h['kode'] ?></strong></td>
                            <td><?= $h['nama'] ?></td>
                            <td class="text-center">
                                <span class="badge bg-primary">#<?= $h['rank_dm1'] ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">#<?= $h['rank_dm2'] ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">#<?= $h['rank_dm3'] ?></span>
                            </td>
                            <td class="text-center">
                                <strong class="text-primary"><?= $h['total_poin'] ?></strong>
                            </td>
                            <td class="text-center">
                                <?= number_format($h['bobot'], 3) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="alert alert-info mt-3">
            <small>
                <strong>Keterangan:</strong><br>
                <span class="badge bg-primary">DM1</span> = Kepala Desa |
                <span class="badge bg-success">DM2</span> = Sekretaris Desa |
                <span class="badge bg-info">DM3</span> = Ketua RT/RW
            </small>
        </div>
    </div>
</div>

<!-- Grafik Visualisasi -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-bar-chart-fill"></i> Grafik Perbandingan Total Poin
            </div>
            <div class="card-body">
                <canvas id="bardaChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pie-chart-fill"></i> Grafik Distribusi Bobot
            </div>
            <div class="card-body">
                <canvas id="pieBordaChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Penjelasan Metode -->
<div class="card mt-4">
    <div class="card-body">
        <h5 class="card-title">
            <i class="bi bi-book"></i> Cara Perhitungan Metode Borda
        </h5>

        <ol>
            <li class="mb-2">
                <strong>Setiap Decision Maker</strong> memberikan ranking untuk semua alternatif
                berdasarkan perhitungan TOPSIS masing-masing.
            </li>
            <li class="mb-2">
                <strong>Konversi Ranking ke Poin:</strong><br>
                Poin = (Jumlah Alternatif - Ranking + 1)<br>
                <small class="text-muted">
                    Contoh: Jika ada 4 alternatif, maka Ranking #1 = 4 poin, #2 = 3 poin, #3 = 2 poin, #4 = 1 poin
                </small>
            </li>
            <li class="mb-2">
                <strong>Agregasi Poin:</strong><br>
                Total Poin = Poin DM1 + Poin DM2 + Poin DM3
            </li>
            <li class="mb-2">
                <strong>Normalisasi Bobot:</strong><br>
                Bobot = Total Poin Alternatif / Total Semua Poin
            </li>
            <li class="mb-0">
                <strong>Ranking Final:</strong><br>
                Alternatif dengan Total Poin terbesar mendapat Ranking #1
            </li>
        </ol>

        <div class="alert alert-success mt-3 mb-0">
            <i class="bi bi-check-circle-fill"></i>
            <strong>Keunggulan Metode Borda:</strong>
            Menggabungkan perspektif dari berbagai Decision Maker untuk menghasilkan
            keputusan yang lebih objektif dan demokratis.
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="text-center mt-4 mb-4">
    <a href="hasil_topsis.php" class="btn btn-lg"
        style="background: linear-gradient(135deg, #1976d2 0%, #0d47a1 100%); color: white; border: none; box-shadow: 0 4px 15px rgba(13, 71, 161, 0.4);">
        <i class="bi bi-bar-chart"></i> Lihat Hasil TOPSIS Saya
    </a>
    <button onclick="window.print()" class="btn btn-lg"
        style="background: linear-gradient(135deg, #37474f 0%, #263238 100%); color: white; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
        <i class="bi bi-printer"></i> Cetak Hasil
    </button>
    <a href="dashboard.php" class="btn btn-lg"
        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
        <i class="bi bi-house"></i> Kembali ke Dashboard
    </a>
</div>

<script>
    // Data untuk Chart
    const labels = <?= json_encode($labels) ?>;
    const poinBorda = <?= json_encode($poinBorda) ?>;
    const colors = <?= json_encode($colors) ?>;

    // Bar Chart
    const barCtx = document.getElementById('bardaChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Poin Borda',
                data: poinBorda,
                backgroundColor: [
                    'rgba(248, 240, 11, 1)',
                    'rgba(143, 139, 139, 0.63)',
                    'rgba(215, 121, 27, 0.74)',
                    'rgba(31, 220, 220, 0.91)'
                ],
                borderColor: [
                    'rgba(255, 215, 0, 1)',
                    'rgba(101, 98, 98, 1)',
                    'rgba(214, 117, 20, 0.83)',
                    'rgba(47, 209, 209, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Perbandingan Total Poin Borda',
                    color: '#ffffff' // ⭐ DITAMBAHKAN
                }
            },
            scales: {
                x: { // ⭐ DITAMBAHKAN
                    ticks: {
                        color: '#ffffff'
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: { // ⭐ DITAMBAHKAN
                        color: '#ffffff'
                    },
                    grid: { // ⭐ DITAMBAHKAN
                        color: 'rgba(255, 255, 255, 0.1)'
                    }
                }
            }
        }
    });

    // Pie Chart
    const pieCtx = document.getElementById('pieBordaChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Poin',
                data: poinBorda,
                backgroundColor: colors,
                borderWidth: 3,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'right',
                    labels: {
                        color: '#ffffff' // ⭐ DITAMBAHKAN: Legend jadi putih
                    }
                },
                title: {
                    display: true,
                    text: 'Distribusi Total Poin Borda',
                    color: '#ffffff' // ⭐ DITAMBAHKAN: Judul jadi putih
                },
                tooltip: {
                    callbacks: {
                        labelTextColor: function (context) {
                            return '#ffffff'; // ⭐ DITAMBAHKAN: Text tooltip jadi putih
                        }
                    }
                }
            }
        }
    });
</script>

<?php require_once '../includes/footer.php'; ?>