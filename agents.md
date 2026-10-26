# AGENTS.md --- Panduan Agen Pengembangan

## Misi

Bangun aplikasi pencarian dan manajemen kos dengan Laravel 10, PHP,
MySQL, Blade, dan Bootstrap 5. Prioritaskan kode yang benar, mudah
dipahami pemula, aman, dan bisa dijalankan secara lokal.

## Aturan Wajib

1.  **Versi framework harus Laravel 10.** Jangan migrasi ke Laravel
    11/12 dan jangan memakai pola bootstrap/middleware yang hanya
    berlaku pada versi tersebut.
2.  Gunakan PHP 8.1/8.2 sesuai kompatibilitas dependensi.
3.  Gunakan Blade + Bootstrap 5. Jangan menambahkan React, Vue, Inertia,
    atau SPA tanpa permintaan eksplisit.
4.  Gunakan MySQL dan Eloquent ORM.
5.  Ikuti pola MVC Laravel dan konvensi penamaan.
6.  Jangan mengarang hasil test, status implementasi, data produksi,
    atau kredensial.
7.  Jangan mengklaim fitur selesai sebelum memeriksa kode dan
    menjalankan test yang relevan.
8.  Jangan menaruh rahasia di source code. `.env` tidak boleh di-commit.
9.  Jangan menghapus atau menimpa file yang sudah ada tanpa memeriksa
    isinya.
10. Jika repo sudah memiliki konvensi, ikuti konvensi yang ada selama
    tidak bertentangan dengan kebutuhan Laravel 10.

## Cara Bekerja

-   Baca `prd.md` dan `task.md` sebelum mulai.
-   Periksa struktur repo, `composer.json`, versi PHP, route, migration,
    dan dependensi yang sudah ada.
-   Kerjakan satu task atau satu tahap pada satu waktu.
-   Sebelum mengedit, sebutkan file yang akan dibuat/diubah dan
    alasannya.
-   Buat perubahan sekecil mungkin yang menyelesaikan task.
-   Setelah perubahan, jalankan test/lint/perintah relevan bila
    lingkungan mendukung.
-   Jika tidak dapat menjalankan test, jelaskan dengan jujur apa yang
    belum diverifikasi.
-   Perbarui `task.md` setelah pekerjaan benar-benar selesai.
-   Jangan membuat file besar yang mencampur banyak domain tanpa alasan.

## Standar Laravel 10

-   Routes web didefinisikan di `routes/web.php`.
-   Gunakan controller untuk request dan response; hindari logika bisnis
    kompleks di Blade.
-   Gunakan Eloquent relationship untuk relasi data.
-   Gunakan Form Request atau `$request->validate()` untuk validasi
    server-side.
-   Gunakan route name yang konsisten.
-   Gunakan middleware `auth` dan middleware/authorization untuk role
    admin.
-   Pastikan konfigurasi middleware mengikuti struktur Laravel 10
    (`app/Http/Kernel.php` jika perlu).
-   Gunakan Blade escaping `{{ }}` untuk konten pengguna; hindari
    `{!! !!}` kecuali konten tepercaya dan memang diperlukan.
-   Gunakan CSRF token pada form yang mengubah data.
-   Gunakan `@csrf` dan `@method('PUT')`/`@method('DELETE')` sesuai
    kebutuhan.
-   Gunakan transaksi database (`DB::transaction`) untuk proses yang
    harus atomik, terutama pembayaran.
-   Gunakan Laravel Storage untuk file dan dokumentasikan
    `storage:link`.
-   Jangan mengirim file `.env` atau kredensial nyata ke output.
-   Pastikan query list menggunakan pagination dan hindari N+1 dengan
    eager loading saat sesuai.

## Keamanan dan Integritas Data

-   Setiap operasi admin harus diperiksa di backend.
-   Validasi ID dan pastikan record yang dirujuk memang ada.
-   Validasi upload berdasarkan tipe dan ukuran.
-   Jangan percaya nominal pembayaran dari input tanpa validasi.
-   Jangan menghitung pembayaran yang dibatalkan sebagai pemasukan.
-   Jangan menghapus histori keuangan secara permanen sebagai tindakan
    rutin.
-   Gunakan foreign key dan aturan penghapusan yang eksplisit.
-   Cegah kamar memiliki penyewaan aktif ganda sesuai aturan bisnis.
-   Lindungi data pribadi penghuni dari halaman publik.
-   Jangan menampilkan stack trace atau rahasia kepada pengguna akhir.

## Gaya Kode

-   Nama class mengikuti PascalCase; method/variabel mengikuti
    camelCase.
-   Nama tabel/kolom konsisten dengan konvensi Laravel.
-   Gunakan nama yang deskriptif dan mudah dipahami.
-   Hindari abstraksi berlebihan untuk proyek pemula.
-   Jangan duplikasi layout; gunakan Blade layouts/partials.
-   Komentar menjelaskan alasan yang tidak jelas, bukan mengulang kode.
-   Format harga dan tanggal secara konsisten di UI.

## Database

-   Semua perubahan skema dibuat melalui migration.
-   Data awal dibuat melalui seeder/factory.
-   Seeder harus dapat dijalankan ulang dengan aman bila memungkinkan
    (`updateOrCreate` atau pendekatan idempotent).
-   Gunakan `DECIMAL` untuk nominal uang.
-   Definisikan foreign key dan indeks yang diperlukan.
-   Jangan menyimpan daftar wilayah atau fasilitas hanya sebagai array
    statis di frontend.

## Testing

Minimal uji: - autentikasi dan otorisasi admin; - CRUD utama; - filter
wilayah/harga; - upload foto; - pembuatan tagihan; - pembayaran penuh
dan sebagian; - pembatalan pembayaran; - perhitungan sisa dan status
tagihan.

Gunakan PHPUnit atau Pest yang sudah dipasang pada proyek. Jangan
menambahkan framework test baru tanpa kebutuhan.

## Format Jawaban Agen

Untuk setiap tahap, berikan: 1. Tujuan tahap. 2. File yang
dibuat/diubah. 3. Kode lengkap atau ringkasan perubahan yang dapat
diverifikasi. 4. Perintah terminal yang perlu dijalankan. 5. Cara
menguji secara manual. 6. Hasil test yang benar-benar dijalankan. 7.
Risiko atau pekerjaan yang masih tersisa.

Jelaskan istilah teknis dengan bahasa Indonesia sederhana karena pemilik
proyek masih pemula.
