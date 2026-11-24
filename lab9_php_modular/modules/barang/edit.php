<?php
/**
 * modules/barang/edit.php — UPDATE: form ubah + proses perbarui data.
 * id diambil dari query string, divalidasi sebagai integer.
 */
$judul = 'Ubah Barang';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        set_flash('gagal', 'Token CSRF tidak valid. Silakan coba lagi.');
        redirect('index.php?page=barang/list');
    }

    $id         = (int) ($_POST['id'] ?? 0);
    $nama       = trim((string) ($_POST['nama'] ?? ''));
    $kategori   = (string) ($_POST['kategori'] ?? '');
    $harga_jual = (string) ($_POST['harga_jual'] ?? '');
    $harga_beli = (string) ($_POST['harga_beli'] ?? '');
    $stok       = (string) ($_POST['stok'] ?? '');

    if ($id <= 0 || $nama === '' || $harga_jual === '' || $harga_beli === '' || $stok === '') {
        set_flash('gagal', 'Data tidak lengkap atau id tidak valid.');
        redirect('index.php?page=barang/list');
    }

    // Unggah gambar baru (opsional): bila kosong, gambar lama dipakai)
    $gambar = null;
    $file = $_FILES['file_gambar'] ?? null;
    if ($file && $file['error'] === UPLOAD_ERR_OK) {
        $filename = str_replace(' ', '_', $file['name']);
        $destination = dirname(__DIR__, 2) . '/gambar/' . $filename;
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $gambar = $filename;
        }
    }

    if ($gambar !== null) {
        $stmt = mysqli_prepare(
            $conn,
            'UPDATE data_barang SET nama = ?, kategori = ?, harga_jual = ?, harga_beli = ?, stok = ?, gambar = ? WHERE id_barang = ?'
        );
        mysqli_stmt_bind_param($stmt, 'sssdisi', $nama, $kategori, $harga_jual, $harga_beli, $stok, $gambar, $id);
    } else {
        $stmt = mysqli_prepare(
            $conn,
            'UPDATE data_barang SET nama = ?, kategori = ?, harga_jual = ?, harga_beli = ?, stok = ? WHERE id_barang = ?'
        );
        mysqli_stmt_bind_param($stmt, 'sssdii', $nama, $kategori, $harga_jual, $harga_beli, $stok, $id);
    }
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    set_flash('sukses', 'Data barang "' . $nama . '" berhasil diubah.');
    redirect('index.php?page=barang/list');
}

// Tampilkan data lama pada form (id dari query string)
$id = (int) ($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, 'SELECT * FROM data_barang WHERE id_barang = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = $result ? mysqli_fetch_array($result) : null;
mysqli_stmt_close($stmt);

if (!$data) {
    set_flash('gagal', 'Data dengan id ' . $id . ' tidak ditemukan.');
    redirect('index.php?page=barang/list');
}

/** Membuat atribut selected pada <select> kategori */
function is_select($var, $val)
{
    return $var == $val ? 'selected="selected"' : '';
}
?>
<section class="content">
    <h2>Ubah Barang #<?= (int) $data['id_barang'] ?></h2>
    <form method="post" action="index.php?page=barang/edit" enctype="multipart/form-data" class="form-terbatas">
        <div class="input">
            <label>Nama Barang</label>
            <input type="text" name="nama" value="<?= e($data['nama']) ?>" required>
        </div>
        <div class="input">
            <label>Kategori</label>
            <select name="kategori">
                <option <?= is_select('Komputer', $data['kategori']) ?> value="Komputer">Komputer</option>
                <option <?= is_select('Elektronik', $data['kategori']) ?> value="Elektronik">Elektronik</option>
                <option <?= is_select('Hand Phone', $data['kategori']) ?> value="Hand Phone">Hand Phone</option>
            </select>
        </div>
        <div class="input">
            <label>Harga Jual (diskon 15% = <?= formatRupiah(hitungDiskon((float) $data['harga_jual'], 15)) ?>)</label>
            <input type="number" name="harga_jual" value="<?= e($data['harga_jual']) ?>" min="0" required>
        </div>
        <div class="input">
            <label>Harga Beli</label>
            <input type="number" name="harga_beli" value="<?= e($data['harga_beli']) ?>" min="0" required>
        </div>
        <div class="input">
            <label>Stok</label>
            <input type="number" name="stok" value="<?= e($data['stok']) ?>" min="0" required>
        </div>
        <div class="input">
            <label>File Gambar (kosongkan jika tidak diganti)</label>
            <input type="file" name="file_gambar" accept="image/*">
            <?php if (!empty($data['gambar'])): ?>
                <p class="catatan">Gambar saat ini: <img src="gambar/<?= e($data['gambar']) ?>" alt="gambar saat ini" class="thumb"></p>
            <?php endif; ?>
        </div>
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="id" value="<?= (int) $data['id_barang'] ?>">
        <div class="submit">
            <input type="submit" name="submit" value="Simpan">
            <a class="btn-batal" href="index.php?page=barang/list">Batal</a>
        </div>
    </form>
</section>
