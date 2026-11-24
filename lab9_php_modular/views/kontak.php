<?php
/**
 * views/kontak.php — Latihan 1: halaman Kontak dengan template bersama.
 * Mendemonstrasikan function validasiEmail() dari helpers/functions.php.
 */
$judul = 'Kontak';

$hasilEmail = null;
$emailInput = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        set_flash('gagal', 'Token CSRF tidak valid. Silakan coba lagi.');
        redirect('index.php?page=kontak');
    }
    $emailInput = trim((string) ($_POST['email'] ?? ''));
    $hasilEmail = validasiEmail($emailInput);
}
?>
<section class="content">
    <h2>Kontak</h2>
    <p>Gunakan form berikut untuk menguji function <code>validasiEmail()</code>:</p>

    <form method="post" action="index.php?page=kontak" class="form-terbatas">
        <div class="input">
            <label for="email">Alamat Email</label>
            <input type="text" id="email" name="email" placeholder="nama@contoh.com"
                   value="<?= e($emailInput) ?>" required>
        </div>
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <div class="submit">
            <input type="submit" value="Validasi Email">
        </div>
    </form>

    <?php if ($hasilEmail !== null): ?>
        <?php if ($hasilEmail): ?>
            <p class="sukses">✔ "<?= e($emailInput) ?>" adalah email yang <strong>valid</strong>.</p>
        <?php else: ?>
            <p class="gagal">✘ "<?= e($emailInput) ?>" bukan email yang valid. Periksa formatnya.</p>
        <?php endif; ?>
    <?php endif; ?>

    <div class="card">
        <h3>Hubungi</h3>
        <p>Email: rafif.mizanurohman@mahasiswa.ac.id</p>
        <p>Kelas: I252A — Semester 3</p>
    </div>
</section>
