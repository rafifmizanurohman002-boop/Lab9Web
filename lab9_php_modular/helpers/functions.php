<?php
/**
 * helpers/functions.php — Pustaka function umum seluruh aplikasi.
 * Dimuat satu kali dengan require_once oleh index.php, sehingga
 * tidak pernah terjadi "Function already declared".
 */

/** Escape teks agar aman ditampilkan ke HTML (mencegah XSS). */
function e(?string $nilai): string
{
    return htmlspecialchars((string) $nilai, ENT_QUOTES, 'UTF-8');
}

/** Format angka menjadi Rupiah: 150000 -> "Rp 150.000". */
function formatRupiah(float $angka): string
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

/** Hitung nilai diskon (persen dari harga). Default 10%. */
function hitungDiskon(float $harga, float $persen = 10): float
{
    return $harga * ($persen / 100);
}

/** Validasi format email, mengembalikan true/false. */
function validasiEmail(string $email): bool
{
    return filter_var(trim($email), FILTER_VALIDATE_EMAIL) !== false;
}

/** Potong teks panjang maksimal $panjang karakter lalu tambahkan elipsis. */
function ringkasTeks(string $teks, int $panjang = 80): string
{
    $teks = trim($teks);
    if (mb_strlen($teks, 'UTF-8') <= $panjang) {
        return $teks;
    }
    return mb_substr($teks, 0, $panjang, 'UTF-8') . '…';
}

/** Tandai menu navigasi yang sedang aktif berdasarkan rute berjalan. */
function nav_aktif(string $rute, ?string $page): string
{
    return $page === $rute ? ' class="aktif"' : '';
}

/* ---------- Flash message (pesan sukses/gagal antarhalaman) ---------- */

function set_flash(string $tipe, string $pesan): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash'] = ['tipe' => $tipe, 'pesan' => $pesan];
}

function get_flash(): ?array
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

/* ---------------------- Perlindungan CSRF ---------------------- */

function csrf_token(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_valid(): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], (string) $_POST['csrf_token']);
}

/** Redirect setelah membuang buffer output (header Location tetap terkirim). */
function redirect(string $url): void
{
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Location: ' . $url);
    exit;
}
