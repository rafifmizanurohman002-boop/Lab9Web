<?php
/**
 * views/header.php — Template bagian atas (dipakai bersama semua halaman).
 * Berisi awal dokumen HTML, judul, navigasi, dan pesan flash.
 */
if (!isset($page)) {
    $page = 'dashboard';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($judul ?? 'Aplikasi PHP Modular') ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Aplikasi PHP Modular</h1>
        <p>Praktikum 9 — Modularisasi, Function, Template &amp; Routing</p>
    </header>
    <nav>
        <a href="index.php?page=dashboard"<?= nav_aktif('dashboard', $page) ?>>Dashboard</a>
        <a href="index.php?page=home"<?= nav_aktif('home', $page) ?>>Home</a>
        <a href="index.php?page=profil"<?= nav_aktif('profil', $page) ?>>Profil</a>
        <a href="index.php?page=kontak"<?= nav_aktif('kontak', $page) ?>>Kontak</a>
        <a href="index.php?page=barang/list"<?= nav_aktif('barang/list', $page) ?>>Data Barang</a>
    </nav>
    <?php $flash = get_flash(); ?>
    <?php if ($flash): ?>
    <div class="flash flash-<?= e($flash['tipe']) ?>"><?= e($flash['pesan']) ?></div>
    <?php endif; ?>
    <main>
