<!-- uji-include.php — Praktik bagian C: menguji include dan require -->
<?php
echo "Sebelum pemanggilan<br>";
include __DIR__ . '/pesan.php';
echo "Sesudah pemanggilan<br>";

// Percobaan (dilakukan saat praktikum, hasil dicatat pada README):
// 1. Jika pesan.php di-rename -> include hanya menghasilkan WARNING,
//    baris "Sesudah pemanggilan" TETAP dijalankan.
// 2. Jika memakai require -> error FATAL, program BERHENTI,
//    baris "Sesudah pemanggilan" TIDAK dijalankan.
echo "<br>Uji berikutnya: buka <a href=\"uji-require.php\">uji-require.php</a>";
?>
