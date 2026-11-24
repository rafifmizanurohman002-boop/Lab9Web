<?php
/**
 * views/404.php — Halaman untuk rute yang tidak ada pada whitelist.
 */
$judul = '404';
?>
<section class="content">
    <h2>404 - Halaman tidak ditemukan</h2>
    <p>Halaman yang diminta tidak tersedia atau tidak terdaftar pada whitelist rute.</p>
    <p><strong>Parameter diterima:</strong> <code>page=<?= e(isset($_GET['page']) && is_string($_GET['page']) ? $_GET['page'] : '-') ?></code></p>
    <a class="btn" href="index.php">Kembali ke dashboard</a>
</section>
