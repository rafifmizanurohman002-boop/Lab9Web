#!/usr/bin/env python3
"""
uji_otomatis.py — Pengujian otomatis aplikasi lab9_php_modular
Menggunakan Playwright/Chromium headless: membuka halaman, mengisi form,
mengunggah file, menekan tombol — lalu merekam screenshot + hasil check.

Cara pakai (dengan Apache + MySQL aktif dan proyek ter-deploy di htdocs):
    python uji_otomatis.py
"""
import os
import datetime
from playwright.sync_api import sync_playwright

BASE = "http://localhost/lab9_php_modular"
HERE = os.path.dirname(os.path.abspath(__file__))
SHOTS = os.path.join(HERE, "screenshots")
GAMBAR_UJI = r"C:\apip\PERKULIAHAN DUNIAWI\TUGAS KULIAH\semester 3\lab8web\lab8_php_database\gambar\hp_apple.jpg"

os.makedirs(SHOTS, exist_ok=True)

RESULTS = []
ERRORS = []  # error console / JS / pesan PHP
EXPECTED_404 = []  # URL yang sengaja diuji berstatus 404


def check(nama, kondisi, detail=""):
    ok = bool(kondisi)
    RESULTS.append((nama, ok, detail))
    print(("PASS  " if ok else "FAIL  ") + nama + (("  | " + detail) if detail else ""))


def ada_error_php(html):
    for tanda in ["Fatal error", "Parse error", "Warning:", "Notice:", "Deprecated:"]:
        if tanda in html:
            return tanda
    return None


def shot(page, nama):
    page.screenshot(path=os.path.join(SHOTS, nama), full_page=True)
    print("      screenshot ->", nama)


def buat_struktur_direktori_html():
    """Membuat tampilan teks struktur direktori proyek (tanpa .git)."""
    baris = ["lab9_php_modular/"]

    def langkah(path, prefix):
        entri = sorted(
            [e for e in os.listdir(path) if e != ".git"],
            key=lambda s: (os.path.isfile(os.path.join(path, s)), s.lower()),
        )
        for i, nama in enumerate(entri):
            akhir = i == len(entri) - 1
            cabang = "`-- " if akhir else "|-- "
            full = os.path.join(path, nama)
            baris.append(prefix + cabang + nama + ("/" if os.path.isdir(full) else ""))
            if os.path.isdir(full):
                langkah(full, prefix + ("    " if akhir else "|   "))

    langkah(HERE, "")
    html = """<!DOCTYPE html><html><head><meta charset="utf-8">
<style>body{font-family:Consolas,monospace;font-size:13px;background:#fff;color:#222;padding:16px}
h2{font-family:Segoe UI,Arial;font-size:16px;color:#1a3c6e}</style></head><body>
<h2>Struktur Direktori proyek (output tree)</h2><pre>""" + "\n".join(baris) + "</pre></body></html>"
    path = os.path.join(HERE, "_struktur.html")
    with open(path, "w", encoding="utf-8") as f:
        f.write(html)
    return path


def main():
    with sync_playwright() as p:
        browser = p.chromium.launch()
        page = browser.new_page(viewport={"width": 1280, "height": 900})

        def on_console(m):
            if m.type != "error":
                return
            loc = (m.location or {}).get("url", "")
            if "favicon" in m.text or "favicon" in loc:
                return
            # Navigasi yang sengaja diuji berstatus 404 bukanlah error
            if any(loc.startswith(u) for u in EXPECTED_404):
                return
            ERRORS.append(m.text + (" @ " + loc if loc else ""))

        page.on("console", on_console)
        page.on("pageerror", lambda e: ERRORS.append(str(e)))

        def buka(url):
            resp = page.goto(url, wait_until="networkidle")
            html = page.content()
            err = ada_error_php(html)
            if err:
                ERRORS.append(f"{url} -> {err}")
            return resp, html

        print("=== 1. Router & Dashboard ===")
        resp, html = buka(f"{BASE}/index.php")
        check("Dashboard HTTP 200", resp.status == 200, str(resp.status))
        check("Dashboard memakai template bersama (header+nav+footer)",
              "Aplikasi PHP Modular" in html and "<nav>" in html and "<footer>" in html)
        check("Koneksi database tampil di dashboard", "berhasil" in html and "latihan1" in html)
        check("Demo function hitungDiskon+formatRupiah (15% = Rp 30.000)", "Rp 30.000" in html)
        check("Demo function hitungDiskon+formatRupiah (10% = Rp 20.000)", "Rp 20.000" in html)
        shot(page, "routing-dashboard.png")

        print("=== 2. Praktik include/require ===")
        resp, html = buka(f"{BASE}/uji-include.php")
        check("uji-include.php menampilkan Sebelum & Sesudah pemanggilan",
              "Sebelum pemanggilan" in html and "Sesudah pemanggilan" in html)
        shot(page, "praktik-include.png")
        resp, html = buka(f"{BASE}/uji-require.php")
        check("uji-require.php menampilkan keluaran sama", "Sesudah pemanggilan (require)" in html)

        print("=== 3. Template multi-halaman (Latihan 1) ===")
        for page_name, judul, menu in [
            ("home", "Halaman Home", "home"),
            ("profil", "Profil", "profil"),
            ("kontak", "Kontak", "kontak"),
        ]:
            resp, html = buka(f"{BASE}/index.php?page={page_name}")
            check(f"Halaman {page_name} HTTP 200", resp.status == 200, str(resp.status))
            check(f"Halaman {page_name} menampilkan judul konten", judul in html)
            check(f"Menu '{menu}' aktif (class=aktif)",
                  f'page={menu}" class="aktif"' in html)
            check(f"Halaman {page_name} tanpa salinan header/footer (hanya 1 <footer>)",
                  html.count("<footer>") == 1 and html.count("<h1>") == 1)
            shot(page, f"template-{page_name}.png")

        print("=== 4. Function helpers (Latihan 2) ===")
        resp, html = buka(f"{BASE}/index.php?page=profil")
        check("ringkasTeks() memotong bio + elipsis", "Diringkas menjadi 120 karakter" in html and "…" in html)
        buka(f"{BASE}/index.php?page=kontak")
        page.fill("#email", "bukan-email-salah")
        page.click(".submit input[type=submit]")
        page.wait_for_load_state("networkidle")
        check("validasiEmail() menolak email salah", "bukan email yang valid" in page.content())
        shot(page, "function-email-invalid.png")
        page.fill("#email", "rafif@contoh.com")
        page.click(".submit input[type=submit]")
        page.wait_for_load_state("networkidle")
        check("validasiEmail() menerima email benar", "adalah email yang" in page.content() and "valid" in page.content())
        shot(page, "function-email-valid.png")

        print("=== 5. Routing aman (Latihan 3) ===")
        tujuan_404 = [
            f"{BASE}/index.php?page=admin/rahasia",
            f"{BASE}/index.php?page[]=x",
            f"{BASE}/index.php?page=../../etc/passwd",
        ]
        EXPECTED_404.extend(tujuan_404)
        resp, html = buka(tujuan_404[0])
        check("Rute tak dikenal -> status 404", resp.status == 404, str(resp.status))
        check("Halaman 404 tampil", "404 - Halaman tidak ditemukan" in html)
        shot(page, "routing-404.png")
        resp, html = buka(f"{BASE}/index.php?page=")
        check("Parameter page kosong -> dashboard (200, aman)", resp.status == 200 and "Dashboard" in html)
        resp, html = buka(tujuan_404[1])
        check("Parameter page bertipe array -> 404 (aman)", resp.status == 404, str(resp.status))
        resp, html = buka(tujuan_404[2])
        check("Path traversal ditolak whitelist -> 404", resp.status == 404, str(resp.status))
        resp, html = buka(f"{BASE}/index.php?page=barang/list")
        check("Menu 'Data Barang' aktif di halaman list", 'page=barang/list" class="aktif"' in html)

        print("=== 6. CRUD lewat router (Latihan 4) ===")
        resp, html = buka(f"{BASE}/index.php?page=barang/list")
        jumlah = page.locator("table tr").count() - 1
        check("Daftar barang menampilkan 3 data awal", jumlah == 3, f"baris={jumlah}")
        check("Harga tampil dalam format Rupiah (formatRupiah)", "Rp 2.400.000" in html and "Rp 2.000.000" in html)
        check("Tautan aksi memakai pola index.php?page=...",
              "index.php?page=barang/edit" in html and "index.php?page=barang/delete" in html)
        shot(page, "crud-list-awal.png")

        # CREATE
        resp, html = buka(f"{BASE}/index.php?page=barang/add")
        check("Form tambah tampil", "Tambah Barang" in html and 'name="csrf_token"' in html)
        shot(page, "crud-tambah-form.png")
        page.fill('input[name="nama"]', "HP Apple iPhone")
        page.select_option('select[name="kategori"]', "Elektronik")
        page.fill('input[name="harga_jual"]', "5000000")
        page.fill('input[name="harga_beli"]', "4500000")
        page.fill('input[name="stok"]', "3")
        page.set_input_files('input[name="file_gambar"]', GAMBAR_UJI)
        page.click(".submit input[type=submit]")
        page.wait_for_load_state("networkidle")
        html = page.content()
        jumlah = page.locator("table tr").count() - 1
        check("Setelah Simpan: redirect ke daftar barang", "barang/list" in page.url)
        check("Setelah Simpan: pesan sukses tampil (flash)", "berhasil ditambahkan" in html)
        check("Setelah Simpan: data ke-4 muncul", jumlah == 4 and "HP Apple iPhone" in html, f"baris={jumlah}")
        check("Gambar unggahan tampil", "hp_apple.jpg" in html)
        shot(page, "crud-setelah-tambah.png")

        # UPDATE
        page.click('tr:has-text("HP Apple iPhone") a.btn-kecil')
        page.wait_for_load_state("networkidle")
        html = page.content()
        check("Form ubah terisi data lama", "Ubah Barang" in html and 'value="HP Apple iPhone"' in html)
        check("Form ubah memakai helper hitungDiskon untuk pratinjau", "Rp 750.000" in html)
        shot(page, "crud-ubah-form.png")
        page.fill('input[name="nama"]', "HP Apple iPhone 128GB")
        page.fill('input[name="stok"]', "7")
        page.click(".submit input[type=submit]")
        page.wait_for_load_state("networkidle")
        html = page.content()
        check("Setelah Simpan: nama baru tampil", "HP Apple iPhone 128GB" in html)
        check("Setelah Simpan: stok baru = 7", ">7</td>" in html)
        check("Setelah Simpan: pesan sukses tampil", "berhasil diubah" in html)
        shot(page, "crud-setelah-ubah.png")

        # DELETE (lewat tombol POST + dialog konfirmasi)
        page.on("dialog", lambda d: d.accept())
        page.click('tr:has-text("HP Apple iPhone 128GB") button.btn-hapus')
        page.wait_for_load_state("networkidle")
        html = page.content()
        jumlah = page.locator("table tr").count() - 1
        check("Setelah Hapus: kembali 3 data", jumlah == 3, f"baris={jumlah}")
        check("Setelah Hapus: pesan sukses tampil", "berhasil dihapus" in html)
        check("Setelah Hapus: data terhapus tidak ada lagi",
              page.locator('table tr:has-text("HP Apple iPhone")').count() == 0)
        shot(page, "crud-setelah-hapus.png")

        # KEAMANAN: GET delete tidak menghapus data
        buka(f"{BASE}/index.php?page=barang/delete")
        html = page.content()
        jumlah = page.locator("table tr").count() - 1
        check("Akses GET page=barang/delete DIBLOKIR (tidak menghapus)", jumlah == 3 and "hanya dapat dilakukan" in html, f"baris={jumlah}")

        # KEAMANAN: POST tanpa token CSRF ditolak
        ctx = page.context
        resp = ctx.request.post(f"{BASE}/index.php?page=barang/delete", data={"id": "1"})
        check("POST tanpa token CSRF ditolak (data id=1 utuh)", resp.status in (200, 302))
        resp, html = buka(f"{BASE}/index.php?page=barang/list")
        check("Data id=1 masih ada setelah POST tanpa CSRF", "HP Samsung Android" in html)

        print("=== 7. Struktur direktori ===")
        struktur_html = buat_struktur_direktori_html()
        page.goto("file:///" + struktur_html.replace("\\", "/"))
        shot(page, "struktur-direktori.png")
        os.remove(struktur_html)

        print("=== 8. Pemeriksaan global ===")
        check("Tidak ada pesan error PHP/JS selama pengujian", len(ERRORS) == 0,
              "; ".join(ERRORS[:5]) if ERRORS else "")

        browser.close()

    # ---- Rekapan hasil ----
    lulus = sum(1 for _, ok, _ in RESULTS if ok)
    gagal = len(RESULTS) - lulus
    waktu = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    baris = [
        "HASIL PENGUJIAN OTOMATIS - lab9_php_modular (Praktikum 9: PHP Modular)",
        "Waktu        : " + waktu,
        "Browser      : Playwright/Chromium headless (interaksi form, unggah, klik tombol)",
        "Base URL     : " + BASE,
        "Total        : %d pemeriksaan | %d PASS | %d FAIL" % (len(RESULTS), lulus, gagal),
        "=" * 88,
    ]
    for i, (nama, ok, detail) in enumerate(RESULTS, 1):
        baris.append("%2d. [%s] %s%s" % (i, "PASS" if ok else "FAIL", nama, ("  | " + detail) if detail else ""))
    if ERRORS:
        baris.append("")
        baris.append("Error console/PHP terkumpul:")
        baris.extend("  - " + e for e in ERRORS)
    baris.append("=" * 88)
    baris.append("Kesimpulan: %d/%d pemeriksaan lulus." % (lulus, len(RESULTS)))

    out = os.path.join(HERE, "hasil_pengujian.txt")
    with open(out, "w", encoding="utf-8") as f:
        f.write("\n".join(baris) + "\n")
    print("\n".join(baris))
    return 0 if gagal == 0 else 1


if __name__ == "__main__":
    raise SystemExit(main())
