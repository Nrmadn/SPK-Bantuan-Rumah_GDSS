<?php
/**
 * Database Configuration
 * GDSS Bantuan Rumah Keluarga Miskin
 */

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Kosongkan jika default XAMPP
define('DB_NAME', 'db_gdss_bantuan_rumah');

// Koneksi Database
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8");

// Fungsi untuk mencegah SQL Injection
function clean($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

// Fungsi query
function query($sql) {
    global $conn;
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Query Error: " . mysqli_error($conn));
    }
    return $result;
}

// Fungsi untuk mendapatkan satu baris
function fetch($sql) {
    $result = query($sql);
    return mysqli_fetch_assoc($result);
}

// Fungsi untuk mendapatkan semua baris
function fetchAll($sql) {
    $result = query($sql);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}
?>