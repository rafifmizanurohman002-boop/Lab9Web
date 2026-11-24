<?php
/**
 * views/home.php — Latihan 1: halaman Home dengan template bersama.
 */
$judul = 'Home';
?>
<section class="content">
    <h2>Halaman Home</h2>
    <p>Ini adalah konten khusus halaman beranda.</p>
    <p>
        Header, navigasi, dan footer pada halaman ini <strong>bukan</strong> disalin dari
        file lain — melainkan dipanggil dari <code>views/header.php</code> dan
        <code>views/footer.php</code> oleh router <code>index.php</code>.
        Ubah teks navigasi pada <code>header.php</code> saja, maka seluruh halaman
        ikut berubah.
    </p>
    <p>
        Selamat datang di aplikasi hasil refactor praktikum 8 (CRUD data barang)
        ke dalam struktur modular: <code>config/</code>, <code>views/</code>,
        <code>helpers/</code>, <code>modules/</code>, dan <code>assets/</code>.
    </p>
</section>
