<?php
/**
 * modules/barang/list.php — READ: menampilkan seluruh data barang.
 * Function formatRupiah() dan e() dipanggil dari helpers/functions.php.
 */
$judul = 'Data Barang';

$sql = 'SELECT * FROM data_barang ORDER BY id_barang';
$result = mysqli_query($conn, $sql);
?>
<section class="content">
    <div class="toolbar">
        <h2>Data Barang</h2>
        <a class="btn" href="index.php?page=barang/add">+ Tambah Barang</a>
    </div>

    <div class="main">
        <table>
            <tr>
                <th>Gambar</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Harga Jual</th>
                <th>Harga Beli</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_array($result)): ?>
                <tr>
                    <td><img src="gambar/<?= e($row['gambar']) ?>" alt="<?= e($row['nama']) ?>"></td>
                    <td><?= e($row['nama']) ?></td>
                    <td><?= e($row['kategori']) ?></td>
                    <td><?= formatRupiah((float) $row['harga_jual']) ?></td>
                    <td><?= formatRupiah((float) $row['harga_beli']) ?></td>
                    <td><?= (int) $row['stok'] ?></td>
                    <td class="aksi">
                        <a class="btn-kecil" href="index.php?page=barang/edit&id=<?= (int) $row['id_barang'] ?>">Ubah</a>
                        <form method="post" action="index.php?page=barang/delete" class="inline"
                              onsubmit="return confirm('Hapus data <?= e($row['nama']) ?>?');">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <input type="hidden" name="id" value="<?= (int) $row['id_barang'] ?>">
                            <button class="btn-kecil btn-hapus" type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">Belum ada data</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</section>
