<?php
/**
 * modules/barang/delete.php — DELETE: menghapus data berdasarkan id.
 * Hanya memproses metode POST dengan token CSRF valid;
 * penghapusan TIDAK dilakukan hanya karena pengguna membuka URL GET
 * (sesuai catatan keamanan modul).
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    set_flash('gagal', 'Penghapusan hanya dapat dilakukan lewat tombol Hapus (POST).');
    redirect('index.php?page=barang/list');
}

if (!csrf_valid()) {
    set_flash('gagal', 'Token CSRF tidak valid. Penghapusan dibatalkan.');
    redirect('index.php?page=barang/list');
}

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    set_flash('gagal', 'Id barang tidak valid.');
    redirect('index.php?page=barang/list');
}

// Ambil nama barang dulu untuk pesan konfirmasi
$nama = '';
$stmt = mysqli_prepare($conn, 'SELECT nama FROM data_barang WHERE id_barang = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_array(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);
if ($row) {
    $nama = $row['nama'];
}

$stmt = mysqli_prepare($conn, 'DELETE FROM data_barang WHERE id_barang = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($ok && $nama !== '') {
    set_flash('sukses', 'Data barang "' . $nama . '" berhasil dihapus.');
} else {
    set_flash('gagal', 'Data tidak ditemukan atau gagal dihapus.');
}
redirect('index.php?page=barang/list');
