<?php
/**
 * Logout Page
 * GDSS Bantuan Rumah Keluarga Miskin
 */

session_start();
require_once 'config/database.php';

// Log aktivitas logout
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    query("INSERT INTO log_aktivitas (user_id, aktivitas, keterangan) 
           VALUES ($user_id, 'Logout', 'User keluar dari sistem')");
}

// Hapus semua session
session_unset();
session_destroy();

// Redirect ke login dengan pesan
header("Location: login.php?logout=success");
exit;
?>