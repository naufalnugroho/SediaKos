# TASK.md --- Daftar Pekerjaan Website Kos Laravel 10

> Status awal semua task: `TODO`. Ubah menjadi `IN PROGRESS` saat
> dikerjakan dan `DONE` hanya setelah implementasi diverifikasi.

## Tahap 0 --- Pemeriksaan dan Setup

> Revisi 26 Oktober 2026: produk untuk satu owner dan tiga cabang, dengan
> pemesanan online dan upload bukti transfer. PRD revisi menggantikan cakupan
> historis 15 wilayah. Status checklist lama bukan verifikasi alur baru.

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

### Revisi alur satu owner, tiga cabang — 26 Oktober 2026

- [x] Registrasi penyewa dan login berdasarkan role; registrasi tidak dapat
  membuat akun owner.
- [x] Pemesanan kamar otomatis tanpa persetujuan owner; tagihan dihitung dari
  harga database dan durasi 1–12 bulan.
- [x] Kamar ditahan 24 jam untuk upload bukti, dapat dibatalkan sebelum
  verifikasi, dan dilepas setelah kedaluwarsa; scheduler disiapkan.
- [x] Upload bukti JPG/PNG/PDF maksimal 5 MB disimpan privat; akses dibatasi
  pada penyewa terkait dan admin.
- [x] Owner menerima/menolak pembayaran dengan alasan penolakan; hanya
  pembayaran valid dihitung sebagai pemasukan. Pembayaran penuh mengaktifkan
  sewa; tidak ada tahap persetujuan pengajuan.
- [x] Halaman sewa dan riwayat bukti untuk penyewa; halaman verifikasi owner.
- [x] Penguncian kamar dalam transaksi digunakan oleh alur online dan admin;
  perubahan kamar yang sedang dipesan/disewa ditolak.
- [x] Beranda mengikuti referensi `desain.html` dengan Blade/Bootstrap/CSS,
  ilustrasi bangunan, pencarian, dan kartu tiga lokasi dari database.
- [x] Seeder default tiga cabang tanpa harga/kamar/rekening fiktif; migration
  dan `CabangKosSeeder` berhasil dijalankan pada MySQL lokal. Data lama tidak
  dihapus; katalog publik dibatasi ke tiga lokasi bisnis.
- [x] Build Vite, kompilasi Blade, pemeriksaan format/diff, HTTP 200 beranda,
  dan 15 test (96 assertions) berhasil. Test berjalan pada SQLite dalam
  memori, bukan MySQL lokal. Persaingan dua koneksi MySQL secara bersamaan
  belum diuji otomatis.
- [ ] Owner mengisi kamar, harga, foto, alamat lengkap, kontak, dan rekening
  sebenarnya. Seeder hanya membuat informasi dasar cabang.
- [ ] Verifikasi visual desktop/mobile dan uji transfer manual bersama owner.
- [ ] Jalankan `schedule:work` lokal atau cron scheduler pada server.

Aturan: total harga dibayar penuh untuk durasi pilihan; belum ada DP, refund,
perpanjangan mandiri, atau pemesanan slot tanggal. Pembayaran valid dari alur
online tidak dapat dibatalkan melalui menu pencatatan pembayaran manual.

### Riwayat setup

### Perapian UI penyewa dan perbaikan admin — 26 Oktober 2026

- [x] UI katalog/detail/pemesanan/Sewa saya diselaraskan: kartu lokasi/foto,
  filter berlabel, ringkasan biaya dinamis, tahap sewa, tab berjalan/riwayat,
  batas bayar WIB, upload dengan nama file, dan riwayat bukti yang jelas.
- [x] Preview bukti privat gambar/PDF dan download ditambahkan; halaman detail
  admin memuat bukti, rincian transfer, hasil verifikasi, dan alasan penolakan.
  File lokal yang sudah ada berhasil dilihat di browser admin.
- [x] Tambah pengaturan rekening dari dashboard dengan validasi dan otorisasi;
  migration diterapkan pada MySQL lokal. Tidak mengisi rekening fiktif.
- [x] Tambah detail tagihan, riwayat pembayaran dan akses pembatalan manual;
  filter daftar kamar/penyewaan/tagihan/verifikasi, pagination Bootstrap 5.
- [x] Validasi pembayaran manual memeriksa sisa di dalam lock transaksi;
  file bukti hilang tidak bisa diterima; penghuni yang selesai diperbarui
  jika tidak memiliki sewa aktif lain; cegah nonaktif penghuni aktif.
- [x] 27 test / 303 assertions lulus, build Vite/cache Blade/Pint/diff check
  berhasil. Chromium: enam halaman publik/admin di 1440/390px tidak overflow,
  seluruh indeks admin HTTP 200, preview gambar asli termuat, tanpa error JS.
- [x] PANDUAN.md dan VERIFIKASI.md diperbarui dengan hasil dan batasnya.

Belum diuji: transfer bank nyata, persaingan dua koneksi MySQL, PDF pada semua
browser. Pemesanan baru di database pengguna tidak dibuat untuk menghindari
mengubah kamar yang sedang dipesan; alur transaksi diuji di database terpisah.

- Register diselaraskan dengan login: judul di atas kartu dan tautan masuk
  di bawah kartu; padding desktop khusus register 170px, mobile 36px.
  Build Vite, kompilasi Blade, dan diff check berhasil.

- Posisi autentikasi desktop diturunkan dengan padding atas header 200px;
  padding mobile tetap 36px. Perubahan CSS dibuild ulang melalui Vite.

- Revisi login mengikuti referensi terbaru: background abu muda, logo/judul
  terpusat, kartu putih dan tombol masuk gelap, tautan daftar di bawah kartu.
  Tambahkan toggle password yang dapat diakses. Build/cache Blade/diff check
  berhasil; 5 test autentikasi (20 assertions) lulus. Chromium pada 1440/390 px
  tidak overflow dan tombol tampilkan password berfungsi.

- Penyesuaian visual terbaru: ikon login/daftar disamakan dengan ikon atap
  biru pada home, form auth dipusatkan horizontal, dan section CTA sebelum
  footer memakai latar hitam selebar layar dengan teks terang/tombol putih.
  Build Vite, kompilasi Blade, dan diff check berhasil.

- Penyesuaian footer: latar biru muda diganti hitam (#000), teks/tautan
  menjadi terang dan garis pembatas abu gelap. Build Vite dan diff check
  berhasil; perubahan pada resources/css/landing.css.

### Landing lengkap dan desain autentikasi — 26 Oktober 2026

- [x] Login dan daftar memakai layout auth khusus sesuai referensi: logo
  kiri atas, latar putih, form 360 px, input 44 px, tombol biru, Inter dan
  Plus Jakarta Sans. CSRF, error validasi, old input, dan autentikasi tetap
  berfungsi. Google/Apple tidak ditampilkan karena belum terintegrasi.
- [x] Landing dilengkapi informasi layanan, FAQ interaktif, CTA, footer
  navigasi cabang/informasi/akun, copyright, dan kembali ke atas.
- [x] Build Vite dan cache Blade berhasil; 5 test autentikasi / 20 assertions
  lulus. Login owner/penyewa dan logout berhasil di Chromium.
- [x] Login, daftar, dan landing diperiksa pada viewport 1440 dan 390 px:
  tidak ada overflow horizontal. FAQ dapat dibuka dan target anchor footer
  valid. Screenshot login desktop diperiksa visual; tidak ada error JS pada
  alur browser login/dashboard yang diperiksa.

### Perbaikan 403 pada pemesanan — 26 Oktober 2026

- [x] Temukan ketidaksesuaian: GET form terbuka bagi owner tetapi POST
  hanya menerima role penyewa dan mengembalikan 403.
- [x] Owner kini melihat penjelasan akun dan tombol keluar/ganti akun;
  kiriman form lama dialihkan ke penjelasan tersebut tanpa membuat pesanan.
- [x] Test regresi memeriksa GET/POST owner, kamar tetap tersedia, dan
  penyewa bisa memesan sampai halaman upload pembayaran. OnlineRentalTest:
  9 test / 91 assertions lulus; kompilasi Blade dan diff check berhasil.

### Pembersihan data dummy atas permintaan pemilik — 26 Oktober 2026

- [x] Identifikasi data dari DemoDataSeeder/WilayahSeeder dan periksa relasi;
  tidak ditemukan transaksi non-demo pada kos contoh.
- [x] Cadangkan record target ke JSON privat di direktori temporer lokal,
  lalu hapus dalam transaksi: 10 kos, 30 kamar, 15 wilayah, 5 fasilitas contoh
  yang tidak digunakan, 1 penghuni, 1 penyewaan, 1 tagihan, 1 pembayaran demo,
  serta 32 relasi kos-fasilitas. Tidak ada foto pada kos demo.
- [x] Verifikasi tersisa hanya tiga cabang/lokasi bisnis dan dua akun login;
  kamar/penghuni/penyewaan/tagihan/pembayaran/fasilitas kosong. Beranda HTTP 200.

Seeder default tetap CabangKosSeeder, sehingga `db:seed` biasa tidak
mengembalikan data demo lama. Tidak menjalankan `migrate:fresh`.

### Penyelarasan UI dan QA modul — 26 Oktober 2026

- [x] Buat akun uji owner/penyewa melalui perintah khusus lingkungan lokal,
  password acak, tanpa menyimpan password ke source atau menimpa akun lama.
- [x] Adaptasi visual shadcn/ui dengan Blade/Bootstrap: tema zinc, sidebar
  responsif, header, tabel, form, kartu, dan menu verifikasi.
- [x] Samakan layout form/detail kos, fasilitas, dan lokasi. Warna publik
  menggunakan aksen biru sesuai landing; dashboard menggunakan tema netral.
- [x] Perbaiki binding route kos/fasilitas dan foto utama yang ditemukan
  ketika pengujian modul, termasuk pengganti foto utama setelah dihapus.
- [x] Perbaiki filter laporan pembayaran per cabang dan pengecualian piutang
  dari sewa batal/kedaluwarsa; validasi filter tanggal.
- [x] Perbaiki validasi kode kamar, filter harga pada kamar sama, dan redirect
  login penyewa. Menu tagihan online mengarah ke verifikasi bukti.
- [x] 23 test / 248 assertions lulus; 23 halaman admin dirender, akses penyewa
  ditolak; build Vite, kompilasi Blade, jadwal, dan HTTP beranda diperiksa.
- [x] Buat PANDUAN.md dan VERIFIKASI.md tanpa password dalam source.
- [x] Pemeriksaan browser Chromium: login owner/penyewa lokal, logout, sidebar
  mobile, dan navigasi verifikasi berhasil; tidak ada error JavaScript.
  Screenshot desktop/mobile tersedia; dashboard desktop diperiksa visual.
  Beranda dan verifikasi mobile tidak mempunyai overflow horizontal.

Test menggunakan SQLite dalam memori. Transfer uang nyata dan persaingan
dua transaksi MySQL belum diuji. Data kamar/harga/rekening asli tetap perlu
diisi owner.

-   Tidak ada blocker Tahap 0. PowerShell memblokir `npm.ps1`, sehingga
    perintah frontend dijalankan menggunakan `npm.cmd`.
-   Verifikasi setup macOS (26 Oktober 2026): perintah Windows `copy`
    gagal di zsh sehingga `.env` belum tersedia. `.env` lokal dibuat dari
    `.env.example`, lalu `php artisan key:generate` berhasil. Format kunci
    diverifikasi tanpa menampilkan nilainya; `.env` dipastikan diabaikan
    Git. `php artisan --version` menghasilkan Laravel Framework 10.50.3.
    README diperbarui dengan perintah macOS/Linux dan Windows. Koneksi
    MySQL dan halaman aplikasi pada mesin ini belum diverifikasi ulang.
-   Pemeriksaan lanjutan macOS (26 Oktober 2026): error koneksi `2002`
    disebabkan layanan MySQL ServBay belum berjalan. MySQL 8.4 berhasil
    dinyalakan melalui `servbayctl` dan port 3306 sudah terbuka. Pemeriksaan
    koneksi Laravel selanjutnya menghasilkan kode MySQL `1045` (akses
    ditolak). Blocker saat ini: username/password MySQL lokal perlu diisi
    oleh pemilik di `.env`; migration dan halaman beranda belum dapat
    diverifikasi. Panduan pemeriksaan koneksi ditambahkan ke README.
-   Verifikasi asset macOS (26 Oktober 2026): `vite: command not found`
    ditangani dengan `npm ci --include=dev --no-audit --no-fund` berdasarkan
    lockfile yang tersedia. `npm run build` berhasil dengan Vite 5.4.21;
    `public/build/manifest.json` dan kedua file CSS/JS tersedia. Pemanggilan
    Vite melalui Laravel berhasil menghasilkan tag asset dari manifest.
    Tidak ada perubahan dependensi atau template; tampilan di browser belum
    diperiksa ulang.
