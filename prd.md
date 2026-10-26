# PRD --- Website Pencarian dan Manajemen Kos

## Revisi kebutuhan — 26 Oktober 2026 (menggantikan cakupan lama yang bertentangan)

Produk melayani satu owner dengan tiga cabang: Jatiwaringin, Kalibata,
dan Margonda. Website mendukung penyewaan online, bukan hanya katalog.

- Pengunjung melihat lokasi, fasilitas, harga bulanan, dan kamar tersedia.
  Beranda mengikuti referensi `desain.html`, diimplementasikan dengan Blade,
  Bootstrap 5, dan CSS proyek.
- Calon penyewa membuat akun, memilih kamar, tanggal masuk, dan durasi 1–12
  bulan. Pesanan langsung diterima tanpa persetujuan owner.
- Server menghitung total harga kamar × durasi dan membuat tagihan. Pembayaran
  awal melunasi seluruh durasi yang dipilih; tidak ada DP pada alur ini.
- Kamar ditahan 24 jam dengan status `dipesan`. Pesanan yang belum mengunggah
  bukti setelah batas waktu menjadi `kedaluwarsa`, lalu kamar dilepas.
- Penyewa transfer manual dan upload JPG/PNG/PDF maksimal 5 MB. Bukti privat
  hanya dapat diunduh penyewa terkait dan admin/owner.
- Owner hanya memverifikasi pembayaran. Bukti menunggu verifikasi tidak
  dihitung sebagai pemasukan. Selama verifikasi kamar tetap ditahan.
- Pembayaran sesuai mengaktifkan sewa dan mengubah kamar menjadi `terisi`.
  Penolakan wajib disertai alasan, memberi 24 jam baru untuk upload ulang.
- Penyewa melihat sewa, tagihan, dan riwayat bukti miliknya. Pembatalan mandiri
  hanya sebelum bukti sedang diverifikasi. Riwayat transaksi tetap disimpan.
- Penguncian baris kamar dalam transaksi dipakai bersama untuk pemesanan
  online dan pembuatan sewa admin. Kamar dianggap terpakai untuk seluruh
  pesanan; pemesanan berdasarkan slot tanggal yang tidak tumpang tindih belum
  disediakan.
- Owner tetap memakai role backend `admin`; registrasi publik selalu
  menghasilkan role `penyewa`. Multi-owner dan payment gateway tidak termasuk.
- Seeder default hanya membuat tiga cabang tanpa mengarang kamar, harga,
  alamat rinci, foto, atau rekening. Data contoh lama tidak dihapus otomatis.
- Refund, pembatalan pembayaran online valid, dan perpanjangan mandiri belum
  disediakan. Pengelola menangani koreksi tersebut di luar alur saat ini.

Bagian berikut adalah baseline historis. Ketentuan 15 wilayah, demo Jogja,
serta pengecualian akun publik/pemesanan mandiri di bawah tidak lagi berlaku.

## 1. Ringkasan Produk

Website pencarian dan manajemen kos berbasis **Laravel 10**, PHP, MySQL,
Blade, dan Bootstrap 5. Aplikasi memiliki dua area: - **Pengguna
publik:** mencari kos berdasarkan wilayah dan harga, melihat foto,
fasilitas, harga, serta ketersediaan kamar. - **Admin:** mengelola
wilayah, kos, foto, fasilitas, kamar, penghuni, tagihan, pembayaran, dan
laporan.

Proyek ditujukan untuk pengembang pemula. Implementasi harus sederhana,
bertahap, mudah dipahami, dan mengikuti pola standar Laravel 10.

## 2. Tujuan

1.  Memudahkan pengguna menemukan kos yang sesuai lokasi dan anggaran.
2.  Memusatkan data kos yang tersebar di 15 wilayah.
3.  Membantu admin memelihara data kos dan kamar.
4.  Mencatat tagihan serta pembayaran penghuni secara konsisten.
5.  Menyediakan ringkasan operasional dan laporan pembayaran.

## 3. Di Luar Cakupan MVP

-   Pembayaran online otomatis/payment gateway.
-   Akun pengguna publik dan pemesanan mandiri.
-   Aplikasi mobile native.
-   Peta interaktif dan geocoding.
-   Notifikasi SMS/WhatsApp otomatis.
-   Multi-tenant atau banyak organisasi pemilik kos.

Fitur di luar cakupan dapat dipertimbangkan setelah MVP berjalan stabil.

## 4. Pengguna dan Hak Akses

### Pengunjung

-   Dapat membuka halaman beranda, daftar kos, halaman wilayah, dan
    detail kos.
-   Dapat mencari dan memfilter kos.
-   Tidak dapat mengakses dashboard atau mengubah data.

### Admin

-   Login dan logout.
-   Mengelola wilayah, kos, foto, fasilitas, kamar, penghuni, penyewaan,
    tagihan, dan pembayaran.
-   Melihat dashboard serta laporan.
-   Akses admin wajib diperiksa di backend melalui middleware/otorisasi,
    bukan sekadar menyembunyikan menu.

## 5. Ruang Lingkup Fitur

### 5.1 Halaman Publik

-   Beranda dengan pencarian, daftar wilayah, kos terbaru, dan
    rekomendasi.
-   Daftar kos berbentuk kartu.
-   Pencarian nama kos.
-   Filter wilayah, harga minimum/maksimum, fasilitas, dan ketersediaan
    kamar.
-   Urutan harga termurah/termahal.
-   Pagination.
-   Detail kos dengan galeri foto, alamat, deskripsi, harga, fasilitas,
    aturan, kontak, dan kamar tersedia.
-   Halaman/filter untuk masing-masing wilayah.
-   Kos tidak aktif tidak ditampilkan ke publik.
-   Empty state dan halaman 404 yang jelas.

### 5.2 Admin --- Dashboard

Menampilkan total wilayah, kos aktif, kamar, kamar tersedia/terisi,
penghuni aktif, tagihan belum lunas/terlambat, serta pembayaran yang
diterima pada periode terpilih. Angka harus berasal dari database.

### 5.3 Admin --- Wilayah

-   CRUD wilayah.
-   Nama wilayah unik.
-   Seeder awal berisi 15 wilayah contoh.
-   Tidak boleh menghapus wilayah yang masih memiliki kos tanpa proses
    yang aman.

### 5.4 Admin --- Kos

-   CRUD kos.
-   Data minimum: nama, slug opsional, wilayah, alamat, deskripsi,
    aturan, kontak, status.
-   Validasi server-side.
-   Penghapusan harus mempertimbangkan relasi foto, kamar, penyewaan,
    tagihan, dan pembayaran.

### 5.5 Admin --- Foto

-   Upload beberapa foto untuk satu kos.
-   Preview, hapus, urutkan, dan tandai foto utama.
-   Validasi MIME/type dan ukuran.
-   File disimpan menggunakan Laravel Storage; database menyimpan
    path/metadata.
-   Jangan menghapus file sebelum memastikan operasi database terkait
    berhasil.

### 5.6 Admin --- Fasilitas

-   CRUD fasilitas.
-   Relasi many-to-many antara kos dan fasilitas.
-   Contoh: Wi-Fi, AC, kasur, lemari, kamar mandi dalam, dapur bersama,
    parkir.

### 5.7 Admin --- Kamar dan Penyewaan

-   Kos memiliki banyak kamar.
-   Data kamar: kos, nomor/kode kamar, harga, periode harga, status.
-   Status kamar: tersedia, terisi, pemeliharaan.
-   Riwayat penyewaan menghubungkan penghuni dengan kamar dan tanggal
    mulai/akhir.
-   Cegah penyewaan aktif ganda pada kamar yang sama sesuai aturan
    bisnis.

### 5.8 Admin --- Penghuni

-   Data minimum: nama, telepon, catatan seperlunya, status.
-   Informasi pribadi hanya tersedia untuk admin berwenang.
-   Riwayat penyewaan dipertahankan untuk kebutuhan audit.

### 5.9 Admin --- Tagihan dan Pembayaran

-   Tagihan terkait penghuni dan penyewaan/periode.
-   Data tagihan: nomor referensi, periode, nominal, tanggal terbit,
    tanggal jatuh tempo, status.
-   Status tampilan: belum dibayar, dibayar sebagian, lunas, terlambat.
-   Satu tagihan dapat memiliki beberapa pembayaran.
-   Pembayaran mencatat nominal, tanggal, metode, referensi/catatan,
    status verifikasi, dan admin pencatat bila sesuai.
-   Pembayaran yang dibatalkan tidak dihitung sebagai penerimaan.
-   Sisa tagihan = nominal tagihan dikurangi total pembayaran valid.
-   Status lunas jika total pembayaran valid memenuhi nominal tagihan.
-   Status terlambat jika belum lunas dan tanggal jatuh tempo telah
    lewat.
-   Gunakan transaksi database saat mencatat pembayaran.
-   Riwayat transaksi tidak dihapus sembarangan; gunakan
    pembatalan/koreksi yang tercatat.
-   MVP memakai pencatatan manual, bukan pembayaran otomatis.

### 5.10 Laporan

-   Filter rentang tanggal, bulan/tahun, wilayah, kos, dan status.
-   Rekap tagihan, pembayaran diterima, sisa piutang, pembayaran
    terlambat.
-   Ekspor CSV jika waktu memungkinkan.
-   Bedakan nilai tagihan dari uang yang benar-benar diterima.

## 6. Model Data yang Disarankan

Tabel: 1. `users` --- akun admin dan role. 2. `wilayah` 3. `kos` 4.
`foto_kos` 5. `fasilitas` 6. `kos_fasilitas` --- pivot. 7. `kamar` 8.
`penghuni` 9. `penyewaan` 10. `tagihan` 11. `pembayaran`

Gunakan foreign key, indeks, timestamps, tipe data yang sesuai, dan
aturan penghapusan yang menjaga integritas. Gunakan `DECIMAL` untuk
nilai uang. Pertimbangkan soft delete hanya bila dibutuhkan dan pastikan
konsisten.

Relasi utama: - Wilayah hasMany Kos. - Kos belongsTo Wilayah; hasMany
FotoKos dan Kamar; belongsToMany Fasilitas. - Kamar belongsTo Kos;
hasMany Penyewaan. - Penghuni hasMany Penyewaan. - Penyewaan belongsTo
Penghuni dan Kamar; hasMany Tagihan. - Tagihan belongsTo Penyewaan;
hasMany Pembayaran. - Pembayaran belongsTo Tagihan dan dapat mencatat
admin pencatat.

Nama tabel/kolom harus mengikuti konvensi Laravel dan konsisten di semua
migration/model/controller/view.

## 7. Persyaratan Teknis

-   Laravel 10.x; jangan menggunakan API atau struktur khusus Laravel
    11/12.
-   PHP 8.1 atau 8.2 sesuai dependensi Laravel 10.
-   MySQL.
-   Blade dan Bootstrap 5.
-   Eloquent ORM.
-   Laravel Breeze dengan Blade bila kompatibel dengan proyek Laravel
    10.
-   Laravel Storage untuk foto.
-   Form Request atau validasi Laravel untuk input.
-   Route web bernama dan controller terpisah menurut domain.
-   Middleware `auth` dan pemeriksaan role admin.
-   CSRF protection, escaping Blade, validasi upload, dan authorization.
-   Rahasia hanya dalam `.env`; jangan commit `.env`.
-   Seeder/factory untuk data dummy, bukan data nyata.
-   Jangan memasukkan kredensial admin produksi ke source code.

## 8. UX dan Tampilan

-   Desain bersih, minimalis, responsif, dan ramah pemula.
-   Bootstrap 5, tanpa framework frontend tambahan yang tidak perlu.
-   Komponen Blade reusable untuk navbar, footer, sidebar, alert, badge,
    dan form.
-   Semua form menampilkan error validasi.
-   Semua operasi berbahaya meminta konfirmasi.
-   Tampilkan status loading/empty/error jika relevan.
-   Format harga menggunakan format Rupiah yang konsisten.

## 9. Kriteria Penerimaan MVP

MVP dianggap layak ketika: 1. Proyek berjalan di lingkungan lokal dengan
Laravel 10 dan MySQL. 2. Migration dan seeder berhasil dijalankan. 3.
Seeder membuat 15 wilayah contoh. 4. Pengunjung dapat melihat kos aktif
dan detailnya. 5. Pencarian/filter wilayah dan harga berfungsi, termasuk
ketika menggunakan pagination. 6. Admin dapat login/logout dan pengguna
non-admin tidak dapat mengakses dashboard. 7. Admin dapat CRUD wilayah,
kos, fasilitas, dan kamar. 8. Admin dapat upload, menampilkan, dan
menghapus foto dengan aman. 9. Admin dapat mengelola penghuni dan
riwayat penyewaan. 10. Admin dapat membuat tagihan dan mencatat
pembayaran penuh/sebagian. 11. Sisa tagihan, status lunas, dan status
terlambat dihitung dengan benar. 12. Pembayaran yang dibatalkan tidak
dihitung sebagai penerimaan. 13. Statistik dashboard berasal dari
database. 14. Validasi server-side, CSRF, dan otorisasi berjalan. 15.
Dokumentasi setup menjelaskan semua perintah dan konfigurasi yang
diperlukan.

## 10. Asumsi dan Keputusan

-   Wilayah awal adalah data contoh dan dapat diubah admin.
-   Pengguna publik tidak perlu login untuk mencari kos.
-   Pembayaran dicatat manual pada MVP.
-   Satu kamar tidak boleh memiliki lebih dari satu penyewaan aktif jika
    aturan bisnis satu penghuni/kamar diterapkan.
-   Jika ada ambiguitas, pilih implementasi paling sederhana yang aman,
    dokumentasikan asumsi, dan jangan menambahkan fitur di luar cakupan
    tanpa persetujuan.

## 11. Risiko

-   Ketidakcocokan versi paket dengan Laravel 10: pin dependensi yang
    kompatibel dan uji instalasi.
-   Data keuangan tidak konsisten: gunakan transaksi database, validasi
    nominal, dan simpan riwayat.
-   Penghapusan data terkait: gunakan foreign key dan kebijakan
    penghapusan eksplisit.
-   Foto tidak tampil di browser: dokumentasikan disk storage dan
    `php artisan storage:link`.
-   Pemula kewalahan: implementasikan fitur secara bertahap dan uji
    setiap tahap.
