# TASK.md --- Daftar Pekerjaan Website Kos Laravel 10

> Status awal semua task: `TODO`. Ubah menjadi `IN PROGRESS` saat
> dikerjakan dan `DONE` hanya setelah implementasi diverifikasi.

## Tahap 0 --- Pemeriksaan dan Setup

-   [x] T0.1 Periksa versi PHP, Composer, Node.js, dan lingkungan
    lokal.
-   [x] T0.2 Buat/periksa proyek Laravel 10 dan pastikan `composer.json`
    sesuai.
-   [x] T0.3 Siapkan database MySQL dan konfigurasi `.env` lokal.
-   [x] T0.4 Pastikan halaman awal Laravel berjalan.
-   [x] T0.5 Buat `.env.example` tanpa rahasia.
-   [x] T0.6 Dokumentasikan perintah setup untuk pemula.

**Kriteria selesai:** aplikasi berjalan lokal dan dapat terhubung ke
MySQL.

## Tahap 1 --- Skema Database dan Data Awal

-   [x] T1.1 Rancang migration untuk `wilayah`.
-   [x] T1.2 Rancang migration untuk `kos`.
-   [x] T1.3 Rancang migration untuk `foto_kos`.
-   [x] T1.4 Rancang migration untuk `fasilitas` dan `kos_fasilitas`.
-   [x] T1.5 Rancang migration untuk `kamar`.
-   [x] T1.6 Rancang migration untuk `penghuni` dan `penyewaan`.
-   [x] T1.7 Rancang migration untuk `tagihan` dan `pembayaran`.
-   [x] T1.8 Tambahkan foreign key, indeks, timestamps, serta aturan
    penghapusan.
-   [x] T1.9 Buat model dan relasi Eloquent.
-   [x] T1.10 Buat seeder 15 wilayah contoh.
-   [ ] T1.11 Buat factory/seeder untuk data kos dan data uji bila
    diperlukan.
-   [x] T1.12 Jalankan migration dan seeder dari database kosong.

**Kriteria selesai:** migration berhasil, relasi sesuai desain, 15
wilayah masuk ke database.

## Tahap 2 --- Autentikasi dan Layout

-   [x] T2.1 Pilih dan pasang autentikasi yang kompatibel dengan Laravel
    10 (misalnya Breeze Blade).
-   [x] T2.2 Tambahkan role admin dengan default aman.
-   [x] T2.3 Buat middleware/otorisasi admin sesuai struktur Laravel 10.
-   [x] T2.4 Buat cara aman membuat akun admin pertama.
-   [x] T2.5 Buat layout publik Blade + Bootstrap 5.
-   [x] T2.6 Buat layout dashboard admin dengan sidebar.
-   [x] T2.7 Tambahkan flash message dan tampilan error validasi.
-   [x] T2.8 Uji login, logout, dan penolakan akses non-admin.

**Kriteria selesai:** hanya admin terotorisasi dapat membuka route
admin.

## Tahap 3 --- Manajemen Wilayah

-   [x] T3.1 Buat route bernama untuk wilayah admin.
-   [x] T3.2 Buat controller wilayah.
-   [x] T3.3 Buat halaman daftar wilayah dengan pagination bila
    diperlukan.
-   [x] T3.4 Buat form tambah dan edit.
-   [x] T3.5 Buat validasi nama wilayah unik.
-   [x] T3.6 Buat aksi hapus yang aman.
-   [x] T3.7 Uji CRUD dan relasi ke kos.

**Kriteria selesai:** admin dapat mengelola wilayah tanpa merusak data
kos terkait.

## Tahap 4 --- Manajemen Kos, Foto, dan Fasilitas

-   [x] T4.1 Buat CRUD kos.
-   [x] T4.2 Buat validasi data kos dan relasi wilayah.
-   [x] T4.3 Buat upload multi-foto melalui Laravel Storage.
-   [x] T4.4 Buat foto utama dan pengurutan galeri.
-   [x] T4.5 Buat penghapusan/penggantian foto yang aman.
-   [x] T4.6 Buat CRUD fasilitas.
-   [x] T4.7 Buat relasi kos-fasilitas.
-   [x] T4.8 Buat halaman detail kos admin.
-   [x] T4.9 Uji CRUD, upload, validasi file, dan tampilan foto.

**Kriteria selesai:** admin bisa mengelola data kos, foto, dan
fasilitas.

## Tahap 5 --- Kamar dan Penyewaan

-   [x] T5.1 Buat CRUD kamar.
-   [x] T5.2 Implementasikan status tersedia/terisi/pemeliharaan.
-   [x] T5.3 Buat data penghuni.
-   [x] T5.4 Buat riwayat penyewaan dengan tanggal mulai/akhir.
-   [x] T5.5 Cegah penyewaan aktif ganda untuk kamar yang sama.
-   [x] T5.6 Sinkronkan status kamar berdasarkan penyewaan sesuai aturan
    bisnis.
-   [x] T5.7 Uji relasi kamar, penghuni, dan penyewaan.

**Kriteria selesai:** admin dapat mengelola kamar dan penghuni dengan
riwayat yang konsisten.

## Tahap 6 --- Halaman Publik

-   [x] T6.1 Buat beranda.
-   [x] T6.2 Buat daftar kos aktif.
-   [x] T6.3 Buat detail kos dan galeri foto.
-   [x] T6.4 Buat halaman/daftar wilayah.
-   [x] T6.5 Implementasikan pencarian nama kos.
-   [x] T6.6 Implementasikan filter wilayah dan rentang harga.
-   [x] T6.7 Implementasikan filter fasilitas dan ketersediaan bila
    sudah siap.
-   [x] T6.8 Implementasikan urutan harga.
-   [x] T6.9 Tambahkan pagination yang mempertahankan query filter.
-   [x] T6.10 Buat empty state dan halaman 404.
-   [x] T6.11 Uji halaman publik pada layar mobile dan desktop.

**Kriteria selesai:** pengunjung dapat mencari dan melihat kos aktif
tanpa login.

## Tahap 7 --- Tagihan dan Pembayaran

-   [x] T7.1 Buat CRUD/daftar tagihan.
-   [x] T7.2 Validasi nominal, periode, dan tanggal jatuh tempo.
-   [x] T7.3 Buat pencatatan pembayaran manual.
-   [x] T7.4 Gunakan DB transaction untuk menyimpan pembayaran.
-   [x] T7.5 Buat status pembayaran valid/dibatalkan atau status
    verifikasi yang jelas.
-   [x] T7.6 Hitung total pembayaran valid dan sisa tagihan.
-   [x] T7.7 Implementasikan status belum dibayar/dibayar
    sebagian/lunas/terlambat.
-   [x] T7.8 Buat riwayat pembayaran per tagihan dan penghuni.
-   [x] T7.9 Buat daftar/filter pembayaran berdasarkan status, tanggal,
    wilayah, dan kos.
-   [x] T7.10 Uji pembayaran penuh, sebagian, pembatalan, dan input
    berulang.

**Kriteria selesai:** perhitungan tagihan dan pembayaran konsisten dan
dapat diuji.

## Tahap 8 --- Dashboard dan Laporan

-   [x] T8.1 Buat kartu statistik berbasis query database.
-   [x] T8.2 Buat ringkasan penghuni dan kamar.
-   [x] T8.3 Buat rekap tagihan dan pembayaran diterima.
-   [x] T8.4 Buat laporan sisa tagihan dan tunggakan.
-   [x] T8.5 Buat filter periode, wilayah, dan kos.
-   [x] T8.6 Buat ekspor CSV jika memungkinkan.
-   [x] T8.7 Uji angka dashboard terhadap data uji yang diketahui.

**Kriteria selesai:** statistik dan laporan sesuai dengan data
transaksi.

## Tahap 9 --- Keamanan, QA, dan Dokumentasi

-   [x] T9.1 Periksa seluruh route admin dilindungi.
-   [x] T9.2 Periksa validasi server-side untuk semua form.
-   [x] T9.3 Periksa CSRF, escaping Blade, dan upload file.
-   [x] T9.4 Periksa foreign key dan aturan penghapusan.
-   [x] T9.5 Tambahkan test untuk alur utama.
-   [x] T9.6 Jalankan test suite dan perbaiki kegagalan.
-   [x] T9.7 Periksa query N+1 dan pagination.
-   [x] T9.8 Buat README instalasi lokal.
-   [x] T9.9 Pastikan `.env` dan rahasia tidak masuk version control.
-   [x] T9.10 Dokumentasikan fitur yang belum dibuat.

**Kriteria selesai:** setup dapat diikuti dari awal, fitur inti teruji,
dan keterbatasan didokumentasikan.

## Urutan Prioritas MVP

1.  Setup Laravel 10 dan MySQL.
2.  Migration, model, dan 15 wilayah.
3.  Login admin dan middleware.
4.  CRUD wilayah dan kos.
5.  Halaman publik serta pencarian.
6.  Foto, fasilitas, dan kamar.
7.  Penghuni, tagihan, pembayaran.
8.  Dashboard, laporan, test, dokumentasi.

## Catatan

-   Jangan menandai task `DONE` hanya karena file sudah dibuat; uji
    perilakunya.
-   Jika task terlalu besar, pecah menjadi subtask.
-   Catat blocker dan keputusan teknis di bagian bawah file ini.
-   Jangan menambahkan payment gateway pada MVP kecuali diminta secara
    eksplisit.

### Blocker / Keputusan

-   Tidak ada blocker Tahap 0. PowerShell memblokir `npm.ps1`, sehingga
    perintah frontend dijalankan menggunakan `npm.cmd`.
