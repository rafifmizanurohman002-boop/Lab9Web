<?php
/**
 * config/database.php — SATU-SATUNYA tempat konfigurasi koneksi database.
 * Dimuat dengan require_once dari index.php, sehingga tidak pernah
 * dideklarasikan berulang pada halaman lain.
 */
$host = "localhost";
$user = "root";
$pass = "";
$db   = "latihan1";

$conn = @mysqli_connect($host, $user, $pass, $db);
if ($conn === false) {
    // Detail error hanya dicatat ke log, tidak ditampilkan ke pengguna
    error_log('Koneksi database gagal: ' . mysqli_connect_error());
    http_response_code(500);
    exit('Terjadi kesalahan pada koneksi database.');
}
