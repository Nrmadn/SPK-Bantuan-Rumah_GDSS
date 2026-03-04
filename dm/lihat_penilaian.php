<?php
$pageTitle = 'Lihat Penilaian';
require_once '../includes/header.php';
requireKepalaDesa();

// Filter DM
$filterDM = isset($_GET['dm']) ? clean($_GET['dm']) : '';

// Ambil data DM
$dmList = fetchAll("SELECT * FROM users WHERE role != 'admin' ORDER BY level");

// Ambil alternatif
$alternatif = fetchAll("SELECT * FROM alternatif ORDER BY kode");

// Ambil kriteria
$kriteria = fetchAll("SELECT * FROM kriteria ORDER BY kode");
?>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Pilih Decision Maker</label>
                    <select name="dm" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Pilih Decision Maker --</option>
                        <?php foreach ($dmList as $dm): ?>
                        <option value="<?= $dm['id'] ?>" <?= $filterDM == $dm['id'] ? 'selected' : '' ?>>
                            <?= $dm['nama'] ?> (<?= getRoleDisplay($dm['role']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <a href="lihat_penilaian.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if ($filterDM): ?>
    <?php
    $dmInfo = fetch("SELECT * FROM users WHERE id = $filterDM");
    $penilaian = fetchAll("SELECT p.*, a.kode, a.nama, a.alamat
                           FROM penilaian p
                           JOIN alternatif a ON p.alternatif_id = a.id
                           WHERE p.user_id = $filterDM
                           ORDER BY a.kode");
    
    $penilaianLengkap = count($penilaian) == count($alternatif);
    ?>
    
    <!-- Info DM -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">
                        <i class="bi bi-person-badge-fill"></i> 
                        <?= $dmInfo['nama'] ?>
                    </h5>
                    <p class="text-muted mb-0">
                        <?= getRoleDisplay($dmInfo['role']) ?>
                    </p>
                </div>
                <div class="col-md-6 text-end">
                    <?php if ($penilaianLengkap): ?>
                        <span class="badge bg-success fs-5">
                            <i class="bi bi-check-circle-fill"></i> Penilaian Lengkap
                        </span>
                    <?php else: ?>
                        <span class="badge bg-warning fs-5">
                            <i class="bi bi-hourglass-split"></i> 
                            <?= count($penilaian) ?> / <?= count($alternatif) ?> Selesai
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php if (empty($penilaian)): ?>
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <strong>Belum Ada Penilaian</strong> - Decision Maker ini belum melakukan penilaian apapun.
        </div>
    <?php else: ?>
        <!-- Tabel Penilaian -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-table"></i> Data Penilaian Detail
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr class="text-center">
                                <th rowspan="2">Kode</th>
                                <th rowspan="2">Nama Alternatif</th>
                                <th colspan="6">Nilai Kriteria</th>
                                <th rowspan="2">Waktu Input</th>
                            </tr>
                            <tr class="text-center">
                                <th>C1</th>
                                <th>C2</th>
                                <th>C3</th>
                                <th>C4</th>
                                <th>C5</th>
                                <th>C6</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($penilaian as $p): ?>
                            <tr>
                                <td><strong><?= $p['kode'] ?></strong></td>
                                <td><?= $p['nama'] ?></td>
                                <td class="text-center">
                                    <span class="badge bg-primary"><?= $p['c1_pekerjaan'] ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success"><?= $p['c2_tanggungan'] ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning"><?= $p['c3_penghasilan'] ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info"><?= $p['c4_kondisi_rumah'] ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger"><?= $p['c5_status_rumah'] ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?= $p['c6_kepemilikan'] ?></span>
                                </td>
                                <td class="text-center">
                                    <small><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></small>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Keterangan -->
                <div class="alert alert-info mt-3">
                    <h6><i class="bi bi-info-circle"></i> Keterangan Kriteria:</h6>
                    <div class="row">
                        <?php foreach ($kriteria as $k): ?>
                        <div class="col-md-4">
                            <strong><?= $k['kode'] ?>:</strong> <?= $k['nama_kriteria'] ?>
                            <span class="badge bg-<?= $k['jenis'] == 'cost' ? 'danger' : 'success' ?>">
                                <?= strtoupper($k['jenis']) ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Visualisasi Penilaian per Alternatif -->
        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-bar-chart-fill"></i> Visualisasi Penilaian per Alternatif
            </div>
            <div class="card-body">
                <canvas id="radarChart"></canvas>
            </div>
        </div>
    <?php endif; ?>
    
<?php else: ?>
    <!-- Ringkasan Semua DM -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-people-fill"></i> Ringkasan Penilaian Semua Decision Maker
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Decision Maker</th>
                            <th>Role</th>
                            <th class="text-center">Jumlah Penilaian</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Terakhir Update</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        foreach ($dmList as $dm): 
                            $jumlah = fetch("SELECT COUNT(*) as total FROM penilaian WHERE user_id = {$dm['id']}")['total'];
                            $lastUpdate = fetch("SELECT MAX(updated_at) as last FROM penilaian WHERE user_id = {$dm['id']}")['last'];
                            $lengkap = $jumlah == count($alternatif);
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <i class="bi bi-person-circle"></i> 
                                <strong><?= $dm['nama'] ?></strong>
                            </td>
                            <td><?= getRoleDisplay($dm['role']) ?></td>
                            <td class="text-center">
                                <?= $jumlah ?> / <?= count($alternatif) ?>
                            </td>
                            <td class="text-center">
                                <?php if ($lengkap): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i> Lengkap
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning">
                                        <i class="bi bi-hourglass-split"></i> Belum Lengkap
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($lastUpdate): ?>
                                    <small><?= date('d/m/Y H:i', strtotime($lastUpdate)) ?></small>
                                <?php else: ?>
                                    <small class="text-muted">-</small>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="?dm=<?= $dm['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> Lihat Detail
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Matrix Perbandingan -->
    <div class="card mt-4">
        <div class="card-header">
            <i class="bi bi-grid-3x3-gap-fill"></i> Matrix Perbandingan Penilaian (Semua DM)
        </div>
        <div class="card-body">
            <p class="text-muted">
                Tabel ini menampilkan perbandingan penilaian dari semua Decision Maker untuk setiap alternatif.
            </p>
            
            <?php foreach ($alternatif as $alt): ?>
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <strong><?= $alt['kode'] ?> - <?= $alt['nama'] ?></strong>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th>Decision Maker</th>
                                    <th>C1</th>
                                    <th>C2</th>
                                    <th>C3</th>
                                    <th>C4</th>
                                    <th>C5</th>
                                    <th>C6</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dmList as $dm): ?>
                                <?php 
                                $nilai = fetch("SELECT * FROM penilaian 
                                               WHERE user_id = {$dm['id']} AND alternatif_id = {$alt['id']}");
                                ?>
                                <tr>
                                    <td><?= $dm['nama'] ?></td>
                                    <td class="text-center">
                                        <?= $nilai ? $nilai['c1_pekerjaan'] : '<span class="text-muted">-</span>' ?>
                                    </td>
                                    <td class="text-center">
                                        <?= $nilai ? $nilai['c2_tanggungan'] : '<span class="text-muted">-</span>' ?>
                                    </td>
                                    <td class="text-center">
                                        <?= $nilai ? $nilai['c3_penghasilan'] : '<span class="text-muted">-</span>' ?>
                                    </td>
                                    <td class="text-center">
                                        <?= $nilai ? $nilai['c4_kondisi_rumah'] : '<span class="text-muted">-</span>' ?>
                                    </td>
                                    <td class="text-center">
                                        <?= $nilai ? $nilai['c5_status_rumah'] : '<span class="text-muted">-</span>' ?>
                                    </td>
                                    <td class="text-center">
                                        <?= $nilai ? $nilai['c6_kepemilikan'] : '<span class="text-muted">-</span>' ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php if ($filterDM && !empty($penilaian)): ?>
<script>
// Radar Chart untuk visualisasi
const ctx = document.getElementById('radarChart').getContext('2d');

const data = {
    labels: ['C1: Pekerjaan', 'C2: Tanggungan', 'C3: Penghasilan', 'C4: Kondisi Rumah', 'C5: Status', 'C6: Kepemilikan'],
    datasets: [
        <?php foreach ($penilaian as $p): ?>
        {
            label: '<?= $p['nama'] ?>',
            data: [<?= $p['c1_pekerjaan'] ?>, <?= $p['c2_tanggungan'] ?>, <?= $p['c3_penghasilan'] ?>, 
                   <?= $p['c4_kondisi_rumah'] ?>, <?= $p['c5_status_rumah'] ?>, <?= $p['c6_kepemilikan'] ?>],
            fill: true,
            backgroundColor: 'rgba(<?= rand(0,255) ?>, <?= rand(0,255) ?>, <?= rand(0,255) ?>, 0.2)',
            borderColor: 'rgb(<?= rand(0,255) ?>, <?= rand(0,255) ?>, <?= rand(0,255) ?>)',
            pointBackgroundColor: 'rgb(<?= rand(0,255) ?>, <?= rand(0,255) ?>, <?= rand(0,255) ?>)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgb(<?= rand(0,255) ?>, <?= rand(0,255) ?>, <?= rand(0,255) ?>)'
        },
        <?php endforeach; ?>
    ]
};

new Chart(ctx, {
    type: 'radar',
    data: data,
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Pola Penilaian per Alternatif'
            }
        },
        scales: {
            r: {
                beginAtZero: true,
                max: 6,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>