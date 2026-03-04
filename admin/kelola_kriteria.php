<?php
$pageTitle = 'Kelola Kriteria';
require_once '../includes/header.php';
requireAdmin();

$message = '';
$messageType = '';

// UPDATE KRITERIA
if (isset($_POST['update_kriteria'])) {
    $id = clean($_POST['id']);
    $nama_kriteria = clean($_POST['nama_kriteria']);
    $bobot = clean($_POST['bobot']);
    $jenis = clean($_POST['jenis']);
    
    // Validasi bobot (harus antara 0-1)
    if ($bobot < 0 || $bobot > 1) {
        $message = 'Bobot harus antara 0 dan 1!';
        $messageType = 'danger';
    } else {
        query("UPDATE kriteria SET 
               nama_kriteria = '$nama_kriteria',
               bobot = '$bobot',
               jenis = '$jenis'
               WHERE id = $id");
        
        $kode = fetch("SELECT kode FROM kriteria WHERE id = $id")['kode'];
        query("INSERT INTO log_aktivitas (user_id, aktivitas, keterangan) 
               VALUES (1, 'Update Kriteria', 'Kriteria $kode diupdate: $nama_kriteria (Bobot: $bobot)')");
        
        $message = 'Kriteria berhasil diupdate!';
        $messageType = 'success';
    }
}

// Ambil semua kriteria
$kriteriaList = fetchAll("SELECT * FROM kriteria ORDER BY kode");

// Hitung total bobot
$totalBobot = 0;
foreach ($kriteriaList as $k) {
    $totalBobot += $k['bobot'];
}
?>

<?php if ($message): ?>
<div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
    <i class="bi bi-<?= $messageType == 'success' ? 'check-circle' : 'exclamation-triangle' ?>-fill"></i>
    <?= $message ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Info -->
<div class="alert alert-info">
    <i class="bi bi-info-circle-fill"></i>
    <strong>Informasi:</strong>
    <ul class="mb-0 mt-2">
        <li>Anda dapat mengupdate nama kriteria, bobot, dan jenis (Cost/Benefit)</li>
        <li>Total bobot harus <strong>= 1.000</strong> (atau mendekati 1)</li>
        <li>Kode kriteria tidak dapat diubah untuk menjaga konsistensi sistem</li>
        <li><strong>Cost:</strong> Semakin kecil nilainya, semakin baik</li>
        <li><strong>Benefit:</strong> Semakin besar nilainya, semakin baik</li>
    </ul>
</div>

<!-- Statistik -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Kriteria</p>
                        <h3 class="mb-0"><?= count($kriteriaList) ?></h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                        <i class="bi bi-list-check fs-2 text-primary"></i>
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
                        <h3 class="mb-0 <?= abs($totalBobot - 1) < 0.01 ? 'text-success' : 'text-danger' ?>">
                            <?= number_format($totalBobot, 3) ?>
                        </h3>
                    </div>
                    <div class="bg-<?= abs($totalBobot - 1) < 0.01 ? 'success' : 'warning' ?> bg-opacity-10 p-3 rounded">
                        <i class="bi bi-calculator fs-2 text-<?= abs($totalBobot - 1) < 0.01 ? 'success' : 'warning' ?>"></i>
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
                            <?php if (abs($totalBobot - 1) < 0.01): ?>
                                <span class="badge bg-success">Valid</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Invalid</span>
                            <?php endif; ?>
                        </h3>
                    </div>
                    <div class="bg-info bg-opacity-10 p-3 rounded">
                        <i class="bi bi-check-circle fs-2 text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Warning jika total bobot tidak valid -->
<?php if (abs($totalBobot - 1) >= 0.01): ?>
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <strong>Perhatian!</strong> Total bobot saat ini <strong><?= number_format($totalBobot, 3) ?></strong>. 
    Total bobot harus <strong>= 1.000</strong> agar perhitungan TOPSIS valid. 
    Silakan sesuaikan bobot kriteria.
</div>
<?php endif; ?>

<!-- Tabel Data Kriteria -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-list-check"></i> Daftar Kriteria Penilaian
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="10%">Kode</th>
                        <th width="30%">Nama Kriteria</th>
                        <th width="15%">Jenis</th>
                        <th width="20%">Bobot</th>
                        <th width="20%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($kriteriaList)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            <i class="bi bi-inbox"></i> Tidak ada data kriteria
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($kriteriaList as $kriteria): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="text-center">
                                <span class="badge bg-primary"><?= $kriteria['kode'] ?></span>
                            </td>
                            <td><strong><?= $kriteria['nama_kriteria'] ?></strong></td>
                            <td class="text-center">
                                <span class="badge bg-<?= $kriteria['jenis'] == 'cost' ? 'danger' : 'success' ?>">
                                    <?= strtoupper($kriteria['jenis']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <code class="fs-6"><?= number_format($kriteria['bobot'], 3) ?></code>
                            </td>
                            <td class="text-center">
                                <!-- Tombol Edit -->
                                <button class="btn btn-sm btn-warning" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEdit<?= $kriteria['id'] ?>"
                                        title="Edit Kriteria">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot class="table-dark">
                    <tr>
                        <td colspan="4" class="text-end"><strong>TOTAL BOBOT:</strong></td>
                        <td class="text-center">
                            <code class="fs-5 fw-bold text-<?= abs($totalBobot - 1) < 0.01 ? 'success' : 'danger' ?>">
                                <?= number_format($totalBobot, 3) ?>
                            </code>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Penjelasan Kriteria -->
<div class="card mt-4">
    <div class="card-header bg-info text-white">
        <i class="bi bi-info-circle"></i> Penjelasan Kriteria
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-danger"><i class="bi bi-arrow-down-circle"></i> Kriteria COST</h6>
                <p class="small">Kriteria yang nilainya lebih kecil lebih diinginkan (semakin kecil semakin baik).</p>
                <ul class="small">
                    <li><strong>C1 - Pekerjaan Orang Tua:</strong> Semakin rendah tingkat pekerjaan, semakin butuh bantuan</li>
                    <li><strong>C3 - Sumber Penghasilan:</strong> Semakin kecil penghasilan, semakin layak</li>
                    <li><strong>C4 - Kondisi Rumah:</strong> Semakin rusak, semakin butuh bantuan</li>
                    <li><strong>C5 - Status Rumah:</strong> Mengontrak lebih butuh daripada milik sendiri</li>
                    <li><strong>C6 - Kepemilikan Rumah Lain:</strong> Tidak punya rumah lain lebih layak</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6 class="text-success"><i class="bi bi-arrow-up-circle"></i> Kriteria BENEFIT</h6>
                <p class="small">Kriteria yang nilainya lebih besar lebih diinginkan (semakin besar semakin baik).</p>
                <ul class="small">
                    <li><strong>C2 - Jumlah Tanggungan:</strong> Semakin banyak tanggungan, semakin butuh bantuan</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Kriteria untuk setiap kriteria -->
<?php foreach ($kriteriaList as $kriteria): ?>
<div class="modal fade" id="modalEdit<?= $kriteria['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?= $kriteria['id'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil"></i> Edit Kriteria <?= $kriteria['kode'] ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <small><strong>Perhatian:</strong> Kode kriteria tidak dapat diubah untuk menjaga konsistensi sistem.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Kode Kriteria</label>
                        <input type="text" class="form-control" 
                               value="<?= $kriteria['kode'] ?>" disabled>
                        <small class="text-muted">Kode tidak dapat diubah</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Kriteria <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kriteria" class="form-control" 
                               value="<?= $kriteria['nama_kriteria'] ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Jenis Kriteria <span class="text-danger">*</span></label>
                        <select name="jenis" class="form-select" required>
                            <option value="cost" <?= $kriteria['jenis'] == 'cost' ? 'selected' : '' ?>>
                                COST (Semakin kecil semakin baik)
                            </option>
                            <option value="benefit" <?= $kriteria['jenis'] == 'benefit' ? 'selected' : '' ?>>
                                BENEFIT (Semakin besar semakin baik)
                            </option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Bobot Kriteria <span class="text-danger">*</span></label>
                        <input type="number" name="bobot" class="form-control" 
                               value="<?= $kriteria['bobot'] ?>" 
                               step="0.001" min="0" max="1" required>
                        <small class="text-muted">
                            Nilai antara 0 dan 1. Total semua bobot harus = 1.000<br>
                            Saat ini: <strong><?= number_format($kriteria['bobot'], 3) ?></strong> 
                            (<?= number_format($kriteria['bobot'] * 100, 1) ?>%)
                        </small>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-lightbulb"></i>
                        <small><strong>Tips:</strong> Total bobot seluruh kriteria saat ini adalah 
                        <strong><?= number_format($totalBobot, 3) ?></strong>. 
                        Pastikan setelah perubahan, total bobot tetap = 1.000</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="update_kriteria" class="btn btn-warning">
                        <i class="bi bi-save"></i> Update Kriteria
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- CSS Tambahan -->
<style>
.stat-card {
    transition: transform 0.2s;
}
.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
</style>

<?php require_once '../includes/footer.php'; ?>