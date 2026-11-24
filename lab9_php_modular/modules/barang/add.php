<?php
/**
 * modules/barang/add.php — CREATE: form tambah + proses simpan data.
 * Diproses lewat POST dengan validasi CSRF dan prepared statement.
 */
$judul = 'Tambah Barang';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Validasi token CSRF (perlindungan tambahan operasi ubah data)
    if (!csrf_valid()) {
        set_flash('gagal', 'Token CSRF tidak valid. Silakan coba lagi.');
        redirect('index.php?page=barang/add');
    }

    // 2. Ambil dan rapikan input dari form
    $nama       = trim((string) ($_POST['nama'] ?? ''));
    $kategori   = (string) ($_POST['kategori'] ?? '');
    $harga_jual = (string) ($_POST['harga_jual'] ?? '');
    $harga_beli = (string) ($_POST['harga_beli'] ?? '');
    $stok       = (string) ($_POST['stok'] ?? '');

    if ($nama === '' || $harga_jual === '' || $harga_beli === '' || $stok === '') {
        set_flash('gagal', 'Semua kolom wajib diisi.');
        redirect('index.php?page=barang/add');
    }

    // 3. Proses unggah gambar (dipindahkan ke folder gambar/ di root proyek)
    $gambar = null;
    $file = $_FILES['file_gambar'] ?? null;
    if ($file && $file['error'] === UPLOAD_ERR_OK) {
        $filename = str_replace(' ', '_', $file['name']);
        $destination = dirname(__DIR__, 2) . '/gambar/' . $filename;
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $gambar = $filename;
        }
    }

    // 4. Simpan data (prepared statement — aman dari SQL injection)
    $stmt = mysqli_prepare(
        $conn,
        'INSERT INTO data_barang (nama, kategori, harga_jual, harga_beli, stok, gambar) VALUES (?, ?, ?, ?, ?, ?)'
    );
    mysqli_stmt_bind_param($stmt, 'sssdis', $nama, $kategori, $harga_jual, $harga_beli, $stok, $gambar);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    set_flash('sukses', 'Data barang "' . $nama . '" berhasil ditambahkan.');
    redirect('index.php?page=barang/list');
}
?>
<section class="content">
    <h2>Tambah Barang</h2>
    <form method="post" action="index.php?page=barang/add" enctype="multipart/form-data" class="form-terbatas">
        <div class="input">
            <label>Nama Barang</label>
            <input type="text" name="nama" required>
        </div>
        <div class="input">
            <label>Kategori</label>
            <select name="kategori">
                <option value="Komputer">Komputer</option>
                <option value="Elektronik" selected>Elektronik</option>
                <option value="Hand Phone">Hand Phone</option>
            </select>
        </div>
        <div class="input">
            <label>Harga Jual</label>
            <input type="number" name="harga_jual" min="0" required>
        </div>
        <div class="input">
            <label>Harga Beli</label>
            <input type="number" name="harga_beli" min="0" required>
        </div>
        <div class="input">
            <label>Stok</label>
            <input type="number" name="stok" min="0" required>
        </div>
        <div class="input">
            <label>File Gambar</label>
            <input type="file" name="file_gambar" accept="image/*">
        </div>
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <div class="submit">
            <input type="submit" name="submit" value="Simpan">
            <a class="btn-batal" href="index.php?page=barang/list">Batal</a>
        </div>
    </form>
</section>
