<?php
/**
 * Index Page - Redirect ke Dashboard
 * GDSS Bantuan Rumah Keluarga Miskin
 */

session_start();
require_once 'functions/auth.php';

// Jika belum login, redirect ke login
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Redirect berdasarkan role
if (isAdmin()) {
    header("Location: admin/dashboard.php");
} else {
    header("Location: dm/dashboard.php");
}
exit;
?>