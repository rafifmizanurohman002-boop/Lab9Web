<?php
/**
 * index.php — Titik masuk aplikasi (router)
 * Membaca parameter URL "page", memvalidasi terhadap whitelist rute,
 * lalu memuat file konten yang sesuai. Tidak ada path yang disusun
 * langsung dari input URL (mencegah path traversal).
 */
require_once __DIR__ . '/helpers/functions.php';
require_once __DIR__ . '/config/database.php';

// Whitelist rute: hanya halaman ini yang boleh diakses
$routes = [
    'dashboard'     => __DIR__ . '/views/dashboard.php',
    'home'          => __DIR__ . '/views/home.php',
    'profil'        => __DIR__ . '/views/profil.php',
    'kontak'        => __DIR__ . '/views/kontak.php',
    'barang/list'   => __DIR__ . '/modules/barang/list.php',
    'barang/add'    => __DIR__ . '/modules/barang/add.php',
    'barang/edit'   => __DIR__ . '/modules/barang/edit.php',
    'barang/delete' => __DIR__ . '/modules/barang/delete.php',
];

// Ambil parameter page. Page kosong dianggap dashboard; page dengan
// tipe salah (misalnya array) tidak akan lolos whitelist -> 404.
$page = $_GET['page'] ?? 'dashboard';
if (is_string($page) && $page === '') {
    $page = 'dashboard';
}

if (!is_string($page) || !array_key_exists($page, $routes)) {
    // Rute tidak dikenal -> 404
    http_response_code(404);
    $page = '404';
    $contentFile = __DIR__ . '/views/404.php';
} else {
    $contentFile = $routes[$page];
}

// Buffer output: agar redirect() pada modul CRUD tetap bisa
// mengirim header Location sebelum HTML terkirim ke browser.
ob_start();
require_once __DIR__ . '/views/header.php';   // template bersama (bagian atas)
require $contentFile;                          // konten halaman sesuai rute
require_once __DIR__ . '/views/footer.php';   // template bersama (bagian bawah)
ob_end_flush();
