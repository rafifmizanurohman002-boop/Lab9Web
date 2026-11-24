// app.js — perilaku kecil sisi klien: pesan flash otomatis menghilang
document.addEventListener('DOMContentLoaded', function () {
    var flash = document.querySelector('.flash');
    if (flash) {
        setTimeout(function () {
            flash.style.transition = 'opacity .5s';
            flash.style.opacity = '0';
            setTimeout(function () { flash.remove(); }, 500);
        }, 4000);
    }
});
