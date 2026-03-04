<?php
$pageTitle = 'Kelola Alternatif';
require_once '../includes/header.php';
requireAdmin();

$message = '';
$messageType = '';

// TAMBAH ALTERNATIF
if (isset($_POST['tambah'])) {
    $kode = clean($_POST['kode']);
    $nama = clean($_POST['nama']);
    $alamat = clean($_POST['alamat']);
    $no_kk = clean($_POST['no_kk']);
    $no_ktp = clean($_POST['no_ktp']);
    $keterangan = clean($_POST['keterangan']);
    
    // Cek kode sudah ada?
    $cek = fetch("SELECT * FROM alternatif WHERE kode = '$kode'");
    if ($cek) {
        $message = 'Kode alternatif sudah digunakan!';
        $messageType = 'danger';
    } else {
        query("INSERT INTO alternatif (kode, nama, alamat, no_kk, no_ktp, keterangan) 
               VALUES ('$kode', '$nama', '$alamat', '$no_kk', '$no_ktp', '$keterangan')");
        query("INSERT INTO log_aktivitas (user_id, aktivitas, keterangan) 
               VALUES (1, 'Tambah Alternatif', 'Alternatif $nama ditambahkan')");
        $message = 'Alternatif berhasil ditambahkan!';
        $messageType = 'success';
    }
}

// EDIT ALTERNATIF
if (isset($_POST['edit'])) {
    $id = clean($_POST['id']);
    $kode = clean($_POST['kode']);
    $nama = clean($_POST['nama']);
    $alamat = clean($_POST['alamat']);
    $no_kk = clean($_POST['no_kk']);
    $no_ktp = clean($_POST['no_ktp']);
    $keterangan = clean($_POST['keterangan']);
    
    query("UPDATE alternatif SET 
           kode = '$kode',
           nama = '$nama',
           alamat = '$alamat',
           no_kk = '$no_kk',
           no_ktp = '$no_ktp',
           keterangan = '$keterangan'
           WHERE id = $id");
    query("INSERT INTO log_aktivitas (user_id, aktivitas, keterangan) 
           VALUES (1, 'Edit Alternatif', 'Alternatif $nama diupdate')");
    $message = 'Alternatif berhasil diupdate!';
    $messageType = 'success';
}

// HAPUS ALTERNATIF
if (isset($_GET['hapus'])) {
    $id = clean($_GET['hapus']);
    $nama = fetch("SELECT nama FROM alternatif WHERE id = $id")['nama'];
    query("DELETE FROM alternatif WHERE id = $id");
    query("INSERT INTO log_aktivitas (user_id, aktivitas, keterangan) 
           VALUES (1, 'Hapus Alternatif', 'Alternatif $nama dihapus')");
    $message = 'Alternatif berhasil dihapus!';
    $messageType = 'success';
}

// AMBIL DATA
$alternatif = fetchAll("SELECT * FROM alternatif ORDER BY kode");
$totalAlternatif = count($alternatif);
?>

<?php if ($message): ?>
<div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
    <i class="bi bi-<?= $messageType == 'success' ? 'check-circle' : 'exclamation-triangle' ?>-fill"></i>
    <?= $message ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Statistik -->
<div class="row mb-4">
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
</div>

<!-- Tombol Tambah -->
<div class="mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-circle"></i> Tambah Alternatif Baru
    </button>
</div>

<!-- Tabel Data -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-table"></i> Daftar Calon Penerima Bantuan
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="10%">Kode</th>
                        <th width="20%">Nama</th>
                        <th width="25%">Alamat</th>
                        <th width="12%">No. KK</th>
                        <th width="15%">Keterangan</th>
                        <th width="13%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($alternatif)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            <i class="bi bi-inbox"></i> Belum ada data alternatif
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($alternatif as $alt): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= $alt['kode'] ?></strong></td>
                            <td><?= $alt['nama'] ?></td>
                            <td><small><?= $alt['alamat'] ?></small></td>
                            <td><small><?= $alt['no_kk'] ?></small></td>
                            <td><small><?= $alt['keterangan'] ?></small></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEdit<?= $alt['id'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="?hapus=<?= $alt['id'] ?>" 
                                   class="btn btn-sm btn-danger btn-delete">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle"></i> Tambah Alternatif Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kode <span class="text-danger">*</span></label>
                            <input type="text" name="kode" class="form-control" 
                                   placeholder="Contoh: A1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" 
                                   placeholder="Nama lengkap" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea name="alamat" class="form-control" rows="2" 
                                  placeholder="Alamat lengkap dengan RT/RW" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. KK</label>
                            <input type="text" name="no_kk" class="form-control" 
                                   placeholder="Nomor Kartu Keluarga">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. KTP</label>
                            <input type="text" name="no_ktp" class="form-control" 
                                   placeholder="Nomor KTP">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2" 
                                  placeholder="Keterangan tambahan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit untuk setiap alternatif -->
<?php foreach ($alternatif as $alt): ?>
<div class="modal fade" id="modalEdit<?= $alt['id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?= $alt['id'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil"></i> Edit Alternatif
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kode <span class="text-danger">*</span></label>
                            <input type="text" name="kode" class="form-control" 
                                   value="<?= $alt['kode'] ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" 
                                   value="<?= $alt['nama'] ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea name="alamat" class="form-control" rows="2" required><?= $alt['alamat'] ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. KK</label>
                            <input type="text" name="no_kk" class="form-control" 
                                   value="<?= $alt['no_kk'] ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. KTP</label>
                            <input type="text" name="no_ktp" class="form-control" 
                                   value="<?= $alt['no_ktp'] ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"><?= $alt['keterangan'] ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="edit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script>
// Confirm delete
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function(e) {
        if (!confirm('Yakin ingin menghapus data ini? Semua penilaian terkait juga akan dihapus!')) {
            e.preventDefault();
        }
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>