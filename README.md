# Website Pencarian dan Manajemen Kos

Aplikasi ini dibangun dengan Laravel 10, PHP, MySQL, Blade, dan Bootstrap 5.
Fitur dikembangkan bertahap sesuai `prd.md` dan `task.md`.

Panduan langkah demi langkah owner dan penyewa tersedia di [PANDUAN.md](PANDUAN.md).
Dashboard memakai adaptasi visual shadcn/ui pada Blade + Bootstrap (tema zinc,
sidebar responsif, kartu, tabel, dan form); tidak memasang komponen React.
Untuk membuat akun uji lokal dengan password acak, jalankan
`php artisan kos:akun-lokal`. Password hanya ditampilkan ketika akun baru
dibuat dan akun yang sudah ada tidak diubah.

## Persyaratan lokal

- PHP 8.1 atau 8.2 sesuai standar proyek. PHP 8.3 juga dapat digunakan selama
  seluruh dependensi tetap kompatibel.
- Composer
- MySQL
- Node.js dan npm untuk proses asset Vite

## Setup pertama kali

Jalankan perintah berikut dari folder proyek pada macOS/Linux (hanya salin
`.env.example` jika `.env` belum ada):

```bash
composer install
test -f .env || cp .env.example .env
php artisan key:generate
```

Pada Windows Command Prompt, gunakan `if not exist .env copy .env.example .env`
sebagai pengganti baris penyalinan di atas. Perintah `copy` tidak tersedia di
terminal zsh macOS. Jalankan `key:generate` hanya saat setup pertama kali jika
`APP_KEY` masih kosong.

Pesan Composer tentang funding dan `No publishable resources for tag
[laravel-assets]` adalah informasi, bukan kegagalan instalasi. Jika
`key:generate` melaporkan `.env` tidak ditemukan, pastikan langkah penyalinan
di atas berhasil sebelum mengulang perintah tersebut.

Edit file `.env` dan isi koneksi MySQL lokal. Minimal bagian berikut perlu
disesuaikan:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_lokal
DB_USERNAME=root
DB_PASSWORD=
```

Jangan mengisi atau membagikan rahasia produksi di source code. File `.env`
tidak boleh di-commit.

Jika memakai ServBay di macOS, pastikan layanan MySQL sudah berjalan di
ServBay. Untuk instalasi MySQL 8.4, layanan juga dapat dinyalakan melalui:

```bash
/Applications/ServBay/script/servbayctl start mysql 8.4
```

Gunakan host, port, username, dan password MySQL yang ditampilkan pada
pengaturan ServBay untuk mengisi `.env`. Jangan menganggap password `root`
selalu kosong. Pastikan database sesuai `DB_DATABASE` sudah dibuat.

- `SQLSTATE[HY000] [2002] Connection refused`: periksa apakah layanan MySQL
  berjalan dan `DB_HOST`/`DB_PORT` sesuai.
- Error MySQL `1045` / `Access denied`: periksa `DB_USERNAME` dan `DB_PASSWORD`.
- Error MySQL `1049` / `Unknown database`: buat database sesuai `DB_DATABASE`.

Setelah mengubah `.env`, jalankan `php artisan config:clear`, lalu periksa
koneksi dengan `php artisan migrate:status`. Pada database baru, pesan bahwa
tabel migration belum ada berarti koneksi berhasil dan migration perlu
dijalankan.

Setelah Node.js dan npm terpasang, siapkan asset frontend:

```bash
npm install
npm run build
```

Untuk tahap berikutnya, migration dan seeder akan dijalankan dengan:

```bash
php artisan migrate
php artisan db:seed
```

Jangan menggunakan `php artisan migrate:fresh` pada database yang berisi data
penting karena perintah tersebut menghapus tabel dan data.

## Menjalankan aplikasi

```bash
php artisan serve
```

Buka `http://127.0.0.1:8000` di browser. Jika fitur upload foto sudah dibuat,
jalankan juga:

```bash
php artisan storage:link
```

## Pemeriksaan yang berguna

```bash
php artisan --version
php artisan route:list
php artisan migrate:status
php artisan test
```

## Status setup saat ini

- Laravel Framework 10.50.3 terpasang.
- PHP 8.3.30 dan Composer 2.10.2 tersedia pada environment saat ini.
- `APP_KEY` lokal sudah dibuat.
- Database lokal sudah dapat diakses dan seluruh migration aplikasi berstatus
  `Ran`.
- Node.js `v24.22.0` tersedia. Karena PowerShell memblokir `npm.ps1`, perintah
  npm dijalankan menggunakan `npm.cmd`.
- Dependency frontend sudah terpasang dan asset Vite sudah berhasil dibuat.
- Admin pertama dibuat secara eksplisit agar password tidak masuk source code.
  Isi `ADMIN_NAME`, `ADMIN_EMAIL`, dan `ADMIN_PASSWORD` di `.env` lokal, lalu
  jalankan `php artisan db:seed --class=AdminUserSeeder`. Jangan menaruh nilai
  tersebut di `README.md` atau file yang di-commit.

Lihat `claude.md` dan `agents.md` untuk aturan implementasi, keamanan, dan
prosedur verifikasi setiap tahap.

## Modul admin yang tersedia

Setelah login admin, menu dashboard menyediakan modul wilayah, kos, fasilitas,
kamar, penghuni, penyewaan, tagihan, pembayaran, dan laporan. Semua route admin
dilindungi middleware autentikasi dan role admin.

Untuk mencatat pembayaran manual, buat tagihan terlebih dahulu, buka aksi
`Catat bayar`, lalu masukkan nominal yang tidak melebihi sisa tagihan. Status
tagihan dihitung dari pembayaran berstatus `valid`; pembayaran yang dibatalkan
tidak dihitung sebagai penerimaan.

Halaman publik tersedia pada `/kos` dengan pencarian nama, filter wilayah,
fasilitas, rentang harga, filter kamar tersedia, urutan harga termurah,
pagination, detail kos aktif, dan galeri foto. Halaman wilayah tersedia pada
`/wilayah/{wilayah}`.

## Batasan yang masih disengaja

- Pembayaran masih dicatat manual oleh admin; belum ada gateway pembayaran
  online atau notifikasi otomatis.
- Laporan tersedia dalam tampilan web dan CSV; belum ada PDF.
- Refund dan perpanjangan sewa mandiri belum tersedia.

## Alur sewa online untuk tiga cabang

Owner mengelola Jatiwaringin, Kalibata, dan Margonda. Pemesanan langsung
diterima tanpa persetujuan owner; yang diverifikasi owner adalah bukti
pembayaran. Penyewa membayar penuh harga bulanan × durasi (1–12 bulan).

Untuk memperbarui database yang sudah ada:

```bash
php artisan migrate
php artisan db:seed --class=CabangKosSeeder
npm run build
```

Seeder tersebut tidak mengubah data cabang yang sudah dibuat dan tidak
mengarang harga atau kamar. Masuk sebagai admin, lengkapi alamat, kontak,
foto, fasilitas, dan kamar beserta harga tiap cabang. Untuk menyembunyikan
sepuluh kos demo Jogja tanpa menghapus histori, jalankan secara eksplisit:

```bash
php artisan kos:nonaktifkan-demo
```

Atur rekening penerima di `.env` lokal, lalu jalankan `php artisan config:clear`:

```dotenv
PAYMENT_BANK=
PAYMENT_ACCOUNT_NUMBER=
PAYMENT_ACCOUNT_NAME=
```

Isi dengan rekening owner sebenarnya. Jika belum diatur, halaman pembayaran
meminta penyewa menghubungi pengelola untuk memastikan rekening tujuan.

Penyewa mendaftar melalui `/daftar`, memilih kamar, dan melanjutkan ke
`/sewa-saya`. Kamar ditahan 24 jam untuk upload bukti JPG/PNG/PDF maksimal 5 MB.
Setelah upload, status menjadi menunggu verifikasi; bukti belum dihitung
sebagai pemasukan. Owner membuka `/admin/verifikasi-pembayaran` untuk menerima
atau menolak bukti. Penolakan wajib beralasan dan memberi 24 jam untuk upload
ulang. Pembayaran yang diterima mengaktifkan sewa dan mengisi kamar.

Bukti disimpan privat di `storage/app/bukti-pembayaran`, tidak melalui tautan
`public/storage`. `storage:link` tetap diperlukan untuk foto kos publik.

Jalankan penjadwal Laravel agar pesanan tanpa bukti dilepas secara berkala:

```bash
php artisan schedule:work
```

Di server gunakan cron `* * * * * cd /path/proyek && php artisan schedule:run`.
Saat halaman katalog/sewa diakses, aplikasi juga memeriksa pesanan kedaluwarsa.
Kamar tetap ditahan selama bukti menunggu verifikasi. Pemesanan berbasis slot
tanggal dan refund online belum tersedia.

Test otomatis menggunakan SQLite dalam memori agar tidak mengubah database
MySQL lokal:

```bash
php artisan test
```
