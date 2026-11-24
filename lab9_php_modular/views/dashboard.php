<?php
/**
 * views/dashboard.php — Halaman utama setelah aplikasi dibuka.
 * Menampilkan status koneksi database dan demo pemakaian function
 * dari helpers/functions.php.
 */
$judul = 'Dashboard';
?>
<section class="content">
    <h2>Dashboard</h2>
    <p>
        Halaman ini dimuat oleh <strong>index.php</strong> (router) tanpa parameter
        <code>page</code>. Seluruh halaman menggunakan header, navigasi, dan footer
        yang sama dari folder <code>views/</code>.
    </p>

    <div class="grid">
        <div class="card">
            <h3>Status Koneksi Database</h3>
            <p class="sukses">
                Koneksi ke database <strong>latihan1</strong> berhasil
                (<code>mysqli</code> via <code>config/database.php</code>).
            </p>
        </div>
        <div class="card">
            <h3>Demo Function: hitungDiskon() + formatRupiah()</h3>
            <p>
                Harga Rp 200.000, diskon 15% =>
                <strong><?= formatRupiah(hitungDiskon(200000, 15)) ?></strong><br>
                Harga Rp 200.000, diskon default 10% =>
                <strong><?= formatRupiah(hitungDiskon(200000)) ?></strong>
            </p>
        </div>
    </div>

    <h3>Daftar Rute Aplikasi</h3>
    <ul class="daftar-rute">
        <li><a href="index.php?page=dashboard">index.php?page=dashboard</a> — Dashboard</li>
        <li><a href="index.php?page=home">index.php?page=home</a> — Halaman Home (template)</li>
        <li><a href="index.php?page=profil">index.php?page=profil</a> — Halaman Profil (template)</li>
        <li><a href="index.php?page=kontak">index.php?page=kontak</a> — Halaman Kontak (template + validasiEmail)</li>
        <li><a href="index.php?page=barang/list">index.php?page=barang/list</a> — Read: daftar barang</li>
        <li><a href="index.php?page=barang/add">index.php?page=barang/add</a> — Create: tambah barang</li>
        <li><a href="index.php?page=barang/edit&amp;id=1">index.php?page=barang/edit&amp;id=1</a> — Update: ubah barang</li>
        <li><a href="index.php?page=barang/delete">index.php?page=barang/delete</a> — Delete (hanya lewat POST + CSRF)</li>
    </ul>
    <p class="catatan">
        Rute yang tidak ada pada whitelist (misalnya
        <a href="index.php?page=admin/rahasia"><code>page=admin/rahasia</code></a>)
        akan menampilkan halaman 404.
    </p>
</section>
