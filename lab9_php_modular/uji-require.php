<!-- uji-require.php — membandingkan require dengan include -->
<?php
echo "Sebelum pemanggilan (require)<br>";
require __DIR__ . '/pesan.php';
echo "Sesudah pemanggilan (require)<br>";
// Saat pesan.php tersedia: keluaran sama dengan uji-include.php.
// Jika pesan.php di-rename: PHP mengeluarkan error FATAL dan baris
// "Sesudah pemanggilan (require)" tidak dieksekusi sama sekali.
?>
