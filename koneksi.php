<?php
// Konfigurasi database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_mahasiswa');

// Koneksi menggunakan mysqli procedural
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if (!$conn) {
    die('<div class="error-box">❌ Koneksi database gagal: ' . mysqli_connect_error() . '</div>');
}

// Set charset
mysqli_set_charset($conn, 'utf8');
?>
