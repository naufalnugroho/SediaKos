# Hasil verifikasi fitur — 26 Oktober 2026

## Pembaruan UI transaksi dan pemeriksaan admin

Hasil terbaru: **27 test / 303 assertions lulus**. Build Vite, kompilasi
Blade, format PHP, dan diff check berhasil. Migration rekening pembayaran
sudah diterapkan pada MySQL lokal tanpa mengganti transaksi pengguna.

- UI katalog, detail cabang, pemesanan, Sewa saya, verifikasi admin, detail
  pembayaran, detail tagihan, dan pengaturan rekening diperbarui.
- Test tambahan memeriksa preview gambar/PDF, unduh dengan nama file jelas,
  akses pemilik/admin, larangan akses akun lain, dan file bukti hilang.
- Penerimaan pembayaran dengan file bukti hilang ditolak; penolakan beralasan
  tetap membuka kesempatan upload ulang. Rekening hanya dapat diubah admin.
- Test memeriksa filter kamar, penyewaan, tagihan, dan rekening tampil ke
  penyewa. Pembayaran manual memeriksa sisa setelah mengunci tagihan.
- Chromium pada MySQL lokal berhasil melihat gambar bukti yang benar-benar
  tersimpan (naturalWidth > 0) di halaman detail admin. Endpoint preview
  mengembalikan HTTP 200 dengan MIME gambar.
- Katalog, detail cabang, Sewa saya, daftar verifikasi, detail bukti, dan
  rekening diperiksa pada 1440/390 px tanpa overflow horizontal. Semua halaman
  indeks admin merespons HTTP 200, tanpa error JavaScript yang tertangkap.
- Screenshot Sewa saya desktop dan detail bukti admin diperiksa visual.

Pemesanan baru tidak dibuat lewat browser pada database pengguna karena
kamar yang tersedia sedang dipesan. Alur pesan/upload/terima/tolak diuji
otomatis pada database terpisah. Belum menguji transfer bank nyata,
persaingan dua koneksi MySQL, atau perilaku PDF di semua browser. Rekening
asli wajib diisi owner; refund otomatis dan perpanjangan tetap belum tersedia.

Catatan berikut menyimpan hasil pemeriksaan tahap sebelumnya.

Pemeriksaan otomatis: `php artisan test`, 23 test / 248 assertions lulus.
Database test memakai SQLite dalam memori, tidak mengubah MySQL lokal.
Pemeriksaan render mencakup 23 halaman admin beserta penolakan akses penyewa.

| Modul | Yang diperiksa | Hasil otomatis |
| --- | --- | --- |
| Login owner | Login berhasil, password salah ditolak, akses dashboard | Lulus |
| Login penyewa | Masuk ke sewa sendiri, logout, tidak masuk area admin | Lulus |
| Registrasi | Role dari input tidak dapat membuat admin | Lulus |
| Akun lokal | Hanya lokal, tidak menimpa password saat dijalankan ulang | Lulus |
| Lokasi | Tambah, ubah, hapus, cegah hapus lokasi berisi kos | Lulus |
| Fasilitas | CRUD dan validasi nama unik | Lulus |
| Cabang kos | Tambah, ubah, detail, relasi fasilitas, hapus | Lulus |
| Foto | Upload gambar, tolak file lain, pilih utama, hapus dan pilih pengganti | Lulus |
| Kamar | CRUD, kode unik per cabang, proteksi riwayat dan kamar dipesan | Lulus |
| Penghuni | Tambah, ubah, cegah hapus riwayat sewa | Lulus |
| Penyewaan manual | Kamar terisi, cegah sewa ganda, selesai melepaskan kamar | Lulus |
| Katalog | Pencarian nama, lokasi, fasilitas, rentang harga pada kamar sama | Lulus |
| Detail publik | Cabang aktif dapat dibuka, nonaktif 404, tombol sewa | Lulus |
| Booking online | Otomatis, harga dari database, kamar ditahan, cegah duplikat | Lulus |
| Upload bukti | Validasi tipe, file privat, tolak upload ganda/akses akun lain | Lulus |
| Verifikasi | Hanya admin, terima mengaktifkan sewa, tolak membuka upload ulang | Lulus |
| Pembayaran | Pending bukan pemasukan, verifikasi dua kali ditolak | Lulus |
| Pembayaran manual | Sebagian, penuh, pembatalan, tolak nominal berlebih | Lulus |
| Kedaluwarsa | Melepas kamar, membatalkan tagihan, tidak melepas saat review | Lulus |
| Pembatalan pesanan | Hanya pemilik pesanan, kamar dilepas | Lulus |
| Laporan | Penerimaan sesuai cabang, sisa tagihan, filter tanggal, CSV | Lulus |

Build `npm run build` dan kompilasi `php artisan view:cache` berhasil.
Beranda lokal merespons HTTP 200. Jadwal kedaluwarsa terdaftar setiap menit.
`storage:link` berhasil dibuat. Akun owner dan penyewa lokal berhasil dibuat
di MySQL; tidak ada password dalam source code.

Perbaikan dari pengujian: parameter route kos/fasilitas, pilihan foto utama,
filter laporan penerimaan per cabang, piutang pesanan batal/kedaluwarsa,
validasi kode kamar, filter rentang harga, dan pengalihan login penyewa.

Belum tercakup: transfer uang nyata, dua transaksi MySQL bersamaan, pengujian
email keluar (fitur notifikasi belum tersedia), audit keamanan menyeluruh,
dan pengukuran performa untuk data besar. Pengujian otomatis render tidak
menjamin kesesuaian visual atau setiap kombinasi input. Detail pemeriksaan
browser dicatat di bawah.

## Pemeriksaan browser lokal

Chromium Playwright berhasil menjalankan login owner memakai akun MySQL
lokal, logout, dan login penyewa. Halaman owner beralih ke `/admin`, penyewa
ke `/sewa-saya`. Menu sidebar pada viewport 390 × 844 berhasil dibuka dan
tautan verifikasi dapat diklik. Tidak ada error JavaScript yang tertangkap.
Beranda dan halaman verifikasi tidak memiliki overflow horizontal pada
viewport tersebut. Screenshot beranda dan admin diambil pada desktop
1440 × 1000 dan mobile 390 × 844; dashboard desktop diperiksa visual.
Ini bukan pengujian visual setiap halaman atau setiap ukuran layar.

Database lokal mempunyai 0 kamar pada tiga cabang asli ketika diperiksa.
Owner perlu menambah kamar/harga sebelum mencoba pemesanan dari katalog.
Data demo historis tetap ada dan ikut ringkasan keseluruhan dashboard;
hal ini dijelaskan di halaman dashboard. Password kedua akun lokal berhasil
dipakai untuk login browser.
