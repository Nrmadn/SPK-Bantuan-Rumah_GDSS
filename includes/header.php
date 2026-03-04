<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../functions/auth.php';
requireLogin();

$userInfo = getUserInfo();
$pageTitle = $pageTitle ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - GDSS Bantuan Rumah</title>

    <!-- Bootstrap CSS lokal -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar .logo {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            border-radius: 0;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .top-navbar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            border-radius: 10px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            font-weight: 600;
        }

        .stat-card {
            border-left: 4px solid var(--primary-color);
        }

        .stat-card.success {
            border-left-color: #28a745;
        }

        .stat-card.warning {
            border-left-color: #ffc107;
        }

        .stat-card.danger {
            border-left-color: #dc3545;
        }

        .stat-card.info {
            border-left-color: #17a2b8;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .table thead {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
        }

        .badge-ranking-1 {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: #000;
        }

        .badge-ranking-2 {
            background: linear-gradient(135deg, #C0C0C0 0%, #808080 100%);
            color: #fff;
        }

        .badge-ranking-3 {
            background: linear-gradient(135deg, #CD7F32 0%, #8B4513 100%);
            color: #fff;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <div class="text-center">
                <i class="bi bi-house-heart-fill" style="font-size: 50px;"></i>
                <h5 class="mt-3 mb-0">GDSS</h5>
                <small>Bantuan Rumah</small>
            </div>
        </div>

        <div class="user-info p-3 border-bottom border-white border-opacity-25">
            <div class="d-flex align-items-center">
                <div class="bg-white bg-opacity-25 rounded-circle p-2 me-2">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div>
                    <small class="d-block opacity-75"><?= getRoleDisplay($userInfo['role']) ?></small>
                    <strong><?= $userInfo['nama'] ?></strong>
                </div>
            </div>
        </div>

        <!-- Menu akan diload dari navbar.php -->
        <?php include __DIR__ . '/navbar.php'; ?>

        <div class="p-3 mt-auto">
            <a href="../logout.php" class="btn btn-light w-100" onclick="return confirm('Yakin ingin logout?')">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0"><?= $pageTitle ?></h4>
                <small class="text-muted">
                    <i class="bi bi-calendar"></i> <?= date('l, d F Y') ?>
                </small>
            </div>
            <div>
                <span class="badge bg-primary">
                    <i class="bi bi-person-circle"></i> <?= $userInfo['nama'] ?>
                </span>
            </div>
        </div>