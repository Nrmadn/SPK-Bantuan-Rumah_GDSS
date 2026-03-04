<?php
$pageTitle = 'Kelola Decision Maker';
require_once '../includes/header.php';
requireAdmin();

$message = '';
$messageType = '';

// UPDATE PASSWORD DM (PERBAIKAN)
if (isset($_POST['update_password'])) {
    $id = clean($_POST['id']);
    $password_baru = clean($_POST['password_baru']);
    
    if (strlen($password_baru) < 6) {
        $message = 'Password minimal 6 karakter!';
        $messageType = 'danger';
    } else {
        // PERBAIKAN: Gunakan MD5 agar konsisten dengan fungsi login
        $password_hash = md5($password_baru);
        query("UPDATE users SET password = '$password_hash' WHERE id = $id");
        
        $nama = fetch("SELECT nama FROM users WHERE id = $id")['nama'];
        query("INSERT INTO log_aktivitas (user_id, aktivitas, keterangan) 
               VALUES (1, 'Update Password DM', 'Password $nama diubah oleh Admin')");
        
        $message = 'Password berhasil diupdate!';
        $messageType = 'success';
    }
}

// UPDATE USERNAME DM
if (isset($_POST['update_username'])) {
    $id = clean($_POST['id']);
    $username_baru = clean($_POST['username_baru']);
    
    // Cek apakah username sudah dipakai
    $cek = fetch("SELECT * FROM users WHERE username = '$username_baru' AND id != $id");
    if ($cek) {
        $message = 'Username sudah digunakan oleh user lain!';
        $messageType = 'danger';
    } else {
        query("UPDATE users SET username = '$username_baru' WHERE id = $id");
        
        $nama = fetch("SELECT nama FROM users WHERE id = $id")['nama'];
        query("INSERT INTO log_aktivitas (user_id, aktivitas, keterangan) 
               VALUES (1, 'Update Username DM', 'Username $nama diubah menjadi $username_baru')");
        
        $message = 'Username berhasil diupdate!';
        $messageType = 'success';
    }
}

// UPDATE DATA DM
if (isset($_POST['update_data'])) {
    $id = clean($_POST['id']);
    $nama = clean($_POST['nama']);
    $username = clean($_POST['username']);
    
    // Cek username
    $cek = fetch("SELECT * FROM users WHERE username = '$username' AND id != $id");
    if ($cek) {
        $message = 'Username sudah digunakan!';
        $messageType = 'danger';
    } else {
        query("UPDATE users SET 
               nama = '$nama',
               username = '$username'
               WHERE id = $id");
        
        query("INSERT INTO log_aktivitas (user_id, aktivitas, keterangan) 
               VALUES (1, 'Update Data DM', 'Data $nama diupdate')");
        
        $message = 'Data Decision Maker berhasil diupdate!';
        $messageType = 'success';
    }
}

// Ambil semua DM (bukan admin)
$dmList = fetchAll("SELECT * FROM users WHERE role != 'admin' ORDER BY level");
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
        <li>Anda dapat melihat dan mengupdate data Decision Maker</li>
        <li>Fitur <strong>Tambah</strong> dan <strong>Hapus</strong> tidak tersedia untuk menjaga integritas data</li>
        <li>Password akan di-hash secara otomatis untuk keamanan</li>
    </ul>
</div>

<!-- Statistik -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Decision Maker</p>
                        <h3 class="mb-0"><?= count($dmList) ?></h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                        <i class="bi bi-people-fill fs-2 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Data DM -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-people"></i> Daftar Decision Maker
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="10%">Level</th>
                        <th width="20%">Nama</th>
                        <th width="15%">Username</th>
                        <th width="15%">Role</th>
                        <th width="15%">Dibuat</th>
                        <th width="20%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($dmList)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            <i class="bi bi-inbox"></i> Tidak ada data Decision Maker
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($dmList as $dm): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="text-center">
                                <span class="badge bg-primary"><?= $dm['level'] ?></span>
                            </td>
                            <td><strong><?= $dm['nama'] ?></strong></td>
                            <td><code><?= $dm['username'] ?></code></td>
                            <td>
                                <span class="badge bg-<?= $dm['role'] == 'kepala_desa' ? 'danger' : 'info' ?>">
                                    <?= getRoleDisplay($dm['role']) ?>
                                </span>
                            </td>
                            <td><small><?= date('d/m/Y H:i', strtotime($dm['created_at'])) ?></small></td>
                            <td class="text-center">
                                <!-- Tombol Edit Data -->
                                <button class="btn btn-sm btn-warning" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEdit<?= $dm['id'] ?>"
                                        title="Edit Data">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                
                                <!-- Tombol Reset Password -->
                                <button class="btn btn-sm btn-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalPassword<?= $dm['id'] ?>"
                                        title="Reset Password">
                                    <i class="bi bi-key"></i> Password
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Edit Data untuk setiap DM -->
<?php foreach ($dmList as $dm): ?>
<div class="modal fade" id="modalEdit<?= $dm['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?= $dm['id'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil"></i> Edit Data Decision Maker
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <small><strong>Perhatian:</strong> Role dan Level tidak dapat diubah untuk menjaga konsistensi sistem.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" 
                               value="<?= $dm['nama'] ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" 
                               value="<?= $dm['username'] ?>" required>
                        <small class="text-muted">Username harus unik</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <input type="text" class="form-control" 
                               value="<?= getRoleDisplay($dm['role']) ?>" disabled>
                        <small class="text-muted">Role tidak dapat diubah</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Level</label>
                        <input type="text" class="form-control" 
                               value="<?= $dm['level'] ?>" disabled>
                        <small class="text-muted">Level tidak dapat diubah</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="update_data" class="btn btn-warning">
                        <i class="bi bi-save"></i> Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Reset Password -->
<div class="modal fade" id="modalPassword<?= $dm['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?= $dm['id'] ?>">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-key"></i> Reset Password
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <strong>Perhatian!</strong> Password akan direset untuk:
                        <br><strong><?= $dm['nama'] ?></strong> (<?= getRoleDisplay($dm['role']) ?>)
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                        <input type="text" name="password_baru" class="form-control" 
                               placeholder="Minimal 6 karakter" required minlength="6">
                        <small class="text-muted">Password akan di-hash secara otomatis</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="update_password" class="btn btn-danger">
                        <i class="bi bi-key"></i> Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php require_once '../includes/footer.php'; ?>