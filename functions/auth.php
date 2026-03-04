<?php
/**
 * Fungsi Authentication & Session Management
 * GDSS Bantuan Rumah Keluarga Miskin
 */

// Mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

/**
 * Login user
 * @param string $username
 * @param string $password
 * @return bool
 */
function login($username, $password) {
    $username = clean($username);
    $password = md5($password); // MD5 untuk keamanan sederhana
    
    $user = fetch("SELECT * FROM users WHERE username = '$username' AND password = '$password'");
    
    if ($user) {
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['level'] = $user['level'];
        $_SESSION['login_time'] = time();
        
        // Log aktivitas
        query("INSERT INTO log_aktivitas (user_id, aktivitas) VALUES ({$user['id']}, 'Login')");
        
        return true;
    }
    
    return false;
}

/**
 * Logout user
 */
function logout() {
    if (isset($_SESSION['user_id'])) {
        // Log aktivitas
        query("INSERT INTO log_aktivitas (user_id, aktivitas) VALUES ({$_SESSION['user_id']}, 'Logout')");
    }
    
    // Hapus semua session
    session_unset();
    session_destroy();
    
    header("Location: ../login.php");
    exit;
}

/**
 * Cek apakah user sudah login
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Cek role user
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

function isDM() {
    return isset($_SESSION['role']) && $_SESSION['role'] != 'admin';
}

/**
 * Proteksi halaman - harus login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../login.php?error=not_logged_in");
        exit;
    }
}

/**
 * Proteksi halaman - hanya admin
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: ../index.php?error=access_denied");
        exit;
    }
}

/**
 * Proteksi halaman - hanya DM
 */
function requireDM() {
    requireLogin();
    if (!isDM()) {
        header("Location: ../index.php?error=access_denied");
        exit;
    }
}

/**
 * Get user info
 */
function getUserInfo() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'nama' => $_SESSION['nama'],
        'role' => $_SESSION['role'],
        'level' => $_SESSION['level']
    ];
}

/**
 * Get role display name
 */
function getRoleDisplay($role) {
    $roles = [
        'admin' => 'Administrator',
        'kepala_desa' => 'Kepala Desa',
        'sekretaris' => 'Sekretaris Desa',
        'ketua_rt' => 'Ketua RT/RW'
    ];
    
    return $roles[$role] ?? $role;
}

// Fungsi untuk memastikan hanya Kepala Desa yang bisa akses
function requireKepalaDesa() {
    requireLogin();
    
    // Level 1 = Kepala Desa
    if (!isset($_SESSION['level']) || $_SESSION['level'] != 1) {
        redirect('../dm/dashboard.php', 'Akses ditolak! Hanya Kepala Desa yang dapat mengakses halaman ini.', 'danger');
        exit;
    }
}

?>