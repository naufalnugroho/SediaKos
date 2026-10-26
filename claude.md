# CLAUDE.md --- Instruksi Claude untuk Proyek Kos Laravel 10

## Konteks Proyek

Proyek ini adalah website pencarian dan manajemen kos untuk 15 wilayah.
Stack yang ditetapkan: - Laravel 10 - PHP 8.1/8.2 yang kompatibel -
MySQL - Laravel Blade - Bootstrap 5 - Eloquent ORM - Laravel Storage -
Autentikasi Laravel yang kompatibel dengan Laravel 10

Pemilik proyek masih pemula. Gunakan Bahasa Indonesia yang jelas,
jelaskan langkah penting, dan jangan mengasumsikan pengetahuan Laravel
tingkat lanjut.

## Baca Dokumen Ini Terlebih Dahulu

Sebelum mengerjakan fitur: 1. Baca `prd.md` untuk kebutuhan produk dan
kriteria penerimaan. 2. Baca `agents.md` untuk aturan implementasi. 3.
Baca `task.md` untuk urutan pengerjaan dan status task. 4. Periksa kode
yang sudah ada sebelum melakukan perubahan.

## Batasan Teknologi

-   Wajib Laravel 10; jangan upgrade versi framework tanpa izin.
-   Jangan gunakan API atau pola konfigurasi Laravel 11/12.
-   Gunakan Blade dan Bootstrap 5; jangan menambahkan React/Vue/Inertia
    tanpa persetujuan.
-   Gunakan MySQL dan Eloquent.
-   Pilih dependensi yang mendukung Laravel 10 dan versi PHP proyek.
-   Jika package tidak kompatibel, jelaskan masalah dan pilih alternatif
    yang sesuai, bukan memaksakan instalasi.

## Cara Implementasi

-   Kerjakan satu tahap pada satu waktu mengikuti `task.md`.
-   Awali dengan rencana singkat dan daftar file yang akan diubah.
-   Buat implementasi sederhana, idiomatis, dan mudah dipelajari.
-   Pisahkan route, controller, model, validasi, dan Blade view dengan
    jelas.
-   Gunakan route name yang konsisten.
-   Gunakan relasi Eloquent dan eager loading saat diperlukan.
-   Gunakan Form Request atau validasi Laravel pada sisi server.
-   Gunakan Blade layout/partial untuk komponen berulang.
-   Jangan menaruh seluruh logika bisnis dalam view atau satu
    controller.
-   Jangan menampilkan pseudocode jika diminta implementasi nyata.
-   Jangan menghilangkan bagian penting kode dengan frasa seperti "dan
    seterusnya".
-   Jika perubahan besar, lakukan per tahap dan verifikasi sebelum
    melanjutkan.

## Aturan Keamanan

-   Semua route admin harus memakai autentikasi dan pemeriksaan
    role/otorisasi di backend.
-   Jangan mengandalkan penyembunyian tombol sebagai keamanan.
-   Gunakan CSRF protection, validasi input, Blade escaping, password
    hashing, dan validasi file.
-   Jangan pernah menampilkan atau menyimpan rahasia nyata dalam kode
    atau dokumentasi.
-   Jangan membaca atau mengeluarkan isi `.env` yang berisi kredensial
    ke jawaban.
-   Jangan menghapus data pembayaran sah sebagai operasi rutin.
-   Pembayaran yang dibatalkan tidak dihitung sebagai pemasukan.
-   Gunakan `DB::transaction()` untuk proses keuangan yang perlu atomik.
-   Lindungi data pribadi penghuni.

## Aturan Database dan Pembayaran

-   Buat perubahan skema melalui migration.
-   Buat data contoh melalui seeder/factory.
-   Seeder sebaiknya aman dijalankan berulang.
-   Gunakan `DECIMAL` untuk nominal uang.
-   Pastikan foreign key menjaga integritas data.
-   Tagihan dapat memiliki beberapa pembayaran.
-   Sisa tagihan dihitung dari tagihan dikurangi pembayaran valid.
-   Status lunas hanya jika seluruh nominal terbayar.
-   Status terlambat ditentukan dari jatuh tempo dan sisa tagihan.
-   Hindari duplikasi pembayaran akibat submit berulang.
-   Jangan membuat pembayaran online otomatis; MVP mencatat pembayaran
    manual.

## Alur Kerja yang Diharapkan

Untuk setiap task: 1. Baca requirement terkait. 2. Periksa kondisi
proyek. 3. Jelaskan rencana singkat. 4. Implementasikan hanya scope task
tersebut. 5. Jalankan test atau perintah verifikasi yang relevan. 6.
Laporkan file yang dibuat/diubah. 7. Berikan perintah terminal dan
langkah uji manual. 8. Catat task yang selesai di `task.md` hanya jika
benar-benar diverifikasi. 9. Sebutkan keterbatasan atau test yang tidak
bisa dijalankan.

## Perintah yang Mungkin Berguna

Sesuaikan dengan kondisi proyek dan jangan jalankan perintah destruktif
tanpa memastikan targetnya.

``` bash
php artisan --version
php -v
composer install
npm install
php artisan migrate
php artisan db:seed
php artisan route:list
php artisan test
npm run build
php artisan storage:link
php artisan serve
```

Jangan menjalankan `migrate:fresh` pada database yang berisi data
penting. Untuk pengembangan lokal baru, pastikan database yang
ditargetkan benar sebelum menjalankan perintah yang menghapus
tabel/data.

## Gaya Komunikasi

-   Jawab dalam Bahasa Indonesia.
-   Bersikap langsung, ramah, dan sabar.
-   Jelaskan istilah baru dengan kalimat singkat.
-   Berikan kode lengkap dan path file yang jelas.
-   Jangan mengklaim test berhasil jika belum dijalankan.
-   Akhiri tiap tahap dengan cara memeriksa hasilnya dan task
    berikutnya.
