<?php
$pageTitle = 'History Aktivitas';
require_once '../includes/header.php';
requireAdmin();

// Filter
$filterUser = isset($_GET['user']) ? clean($_GET['user']) : '';
$filterDate = isset($_GET['date']) ? clean($_GET['date']) : '';

// Query
$sql = "SELECT la.*, u.nama, u.role 
        FROM log_aktivitas la
        JOIN users u ON la.user_id = u.id
        WHERE 1=1";

if ($filterUser) {
    $sql .= " AND la.user_id = '$filterUser'";
}

if ($filterDate) {
    $sql .= " AND DATE(la.created_at) = '$filterDate'";
}

$sql .= " ORDER BY la.created_at DESC LIMIT 100";

$logs = fetchAll($sql);

// Users untuk filter
$users = fetchAll("SELECT id, nama, role FROM users ORDER BY nama");
?>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Filter User</label>
                    <select name="user" class="form-select">
                        <option value="">-- Semua User --</option>
                        <?php foreach ($users as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= $filterUser == $u['id'] ? 'selected' : '' ?>>
                            <?= $u['nama'] ?> (<?= getRoleDisplay($u['role']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Filter Tanggal</label>
                    <input type="date" name="date" class="form-control" value="<?= $filterDate ?>">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Filter
                        </button>
                        <a href="history.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Log -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-clock-history"></i> Log Aktivitas Sistem (100 Terbaru)
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Waktu</th>
                        <th width="20%">User</th>
                        <th width="15%">Role</th>
                        <th width="20%">Aktivitas</th>
                        <th width="25%">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            <i class="bi bi-inbox"></i> Tidak ada log aktivitas
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($logs as $log): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <small>
                                    <?= date('d/m/Y H:i', strtotime($log['created_at'])) ?>
                                </small>
                            </td>
                            <td>
                                <i class="bi bi-person-circle"></i> 
                                <?= $log['nama'] ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= $log['role'] == 'admin' ? 'danger' : 'primary' ?>">
                                    <?= getRoleDisplay($log['role']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (strpos($log['aktivitas'], 'Login') !== false): ?>
                                    <i class="bi bi-box-arrow-in-right text-success"></i>
                                <?php elseif (strpos($log['aktivitas'], 'Logout') !== false): ?>
                                    <i class="bi bi-box-arrow-right text-danger"></i>
                                <?php else: ?>
                                    <i class="bi bi-activity text-primary"></i>
                                <?php endif; ?>
                                <?= $log['aktivitas'] ?>
                            </td>
                            <td><small class="text-muted"><?= $log['keterangan'] ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>