# Website Pencarian dan Manajemen Kos

Aplikasi ini dibangun dengan Laravel 10, PHP, MySQL, Blade, dan Bootstrap 5.
Fitur dikembangkan bertahap sesuai `prd.md` dan `task.md`.

## Persyaratan lokal

- PHP 8.1 atau 8.2 sesuai standar proyek. PHP 8.3 juga dapat digunakan selama
  seluruh dependensi tetap kompatibel.
- Composer
- MySQL
- Node.js dan npm untuk proses asset Vite

## Setup pertama kali

Jalankan perintah berikut dari folder proyek:

```bash
composer install
copy .env.example .env
php artisan key:generate
```

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
- Pencarian publik belum memiliki akun penghuni atau fitur pemesanan mandiri.
