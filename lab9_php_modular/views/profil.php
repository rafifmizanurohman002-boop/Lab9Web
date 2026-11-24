<?php
/**
 * views/profil.php — Latihan 1: halaman Profil dengan template bersama.
 * Mendemonstrasikan function ringkasTeks() dari helpers/functions.php.
 */
$judul = 'Profil';

$bio = 'Rafif Mizanurohman adalah mahasiswa Program Studi Teknik Informatika '
    . 'yang mengikuti mata kuliah Pemrograman Web (OBE) pada semester 3. '
    . 'Praktikum 9 ini mempelajari modularisasi program PHP: memecah aplikasi '
    . 'menjadi modul-modul bertanggung jawab jelas, membuat template halaman '
    . 'bersama, menyusun pustaka function, serta membangun routing sederhana '
    . 'berbasis parameter URL dengan whitelist rute dan halaman 404.';
?>
<section class="content">
    <h2>Profil</h2>
    <div class="grid">
        <div class="card">
            <h3>Data Diri</h3>
            <table class="tabel-profil">
                <tr><td>Nama</td><td>Rafif Mizanurohman</td></tr>
                <tr><td>NIM</td><td>312510377</td></tr>
                <tr><td>Kelas</td><td>I252A</td></tr>
                <tr><td>Mata Kuliah</td><td>Pemrograman Web (OBE)</td></tr>
                <tr><td>Praktikum</td><td>9 — PHP Modular</td></tr>
            </table>
        </div>
        <div class="card">
            <h3>Demo Function: ringkasTeks()</h3>
            <p><strong>Teks lengkap (<?= mb_strlen($bio) ?> karakter):</strong></p>
            <p><?= e($bio) ?></p>
            <p><strong>Diringkas menjadi 120 karakter:</strong></p>
            <p><?= e(ringkasTeks($bio, 120)) ?></p>
        </div>
    </div>
</section>
