# Panduan penggunaan SediaKos

## Membuka aplikasi

Jalankan `php artisan serve`, lalu buka http://127.0.0.1:8000.
Jalankan `php artisan schedule:work` pada terminal lain untuk melepaskan
pesanan yang melewati batas upload bukti. Hasil build frontend sudah tersedia;
setelah mengubah CSS/JavaScript jalankan `npm run build` kembali.

## Akun lokal

Akun owner: `owner.local@sediakos.test`. Akun penyewa:
`penyewa.local@sediakos.test`. Password acak diberikan pada saat pembuatan
akun, tidak ditulis dalam dokumentasi atau source code. Akun tersimpan di
database, jadi tidak perlu dibuat ulang setiap menyalakan aplikasi.

Perintah `php artisan kos:akun-lokal` hanya berjalan di `APP_ENV=local`.
Jika akun sudah ada, perintah tidak mengubah password. Akun ini untuk
pengujian lokal. Untuk owner sebenarnya gunakan konfigurasi `ADMIN_*` lokal
dan `php artisan db:seed --class=AdminUserSeeder`.

## Sebagai admin / owner

1. Buka `/login`, masukkan akun owner. Setelah login, buka workspace `/admin`.
2. Buka **Fasilitas**. Tambahkan fasilitas sebenarnya seperti WiFi atau AC.
3. Buka **Cabang kos**. Edit Jatiwaringin, Kalibata, dan Margonda: lengkapi
   alamat, kontak, deskripsi, aturan, fasilitas, serta foto. Aktifkan cabang
   agar ditampilkan. Buka detail cabang untuk memilih foto utama atau menghapus
   foto. Cabang di luar tiga lokasi bisnis tidak ditampilkan ke publik.
4. Buka **Kamar**, klik tambah. Pilih cabang, isi kode kamar unik per cabang,
   harga per bulan, dan status tersedia. Jangan memasukkan harga/data contoh
   sebagai informasi bisnis sebenarnya.
5. Buka **Rekening pembayaran**, isi nama bank, nomor rekening, dan nama
   pemilik sebenarnya, lalu simpan. Penyewa langsung melihat rekening ini
   pada **Sewa saya**. Nilai `.env` menjadi fallback jika pengaturan database
   belum diisi; setelah disimpan dari dashboard, pengaturan dashboard berlaku.
6. Ketika penyewa memesan, pesanan langsung diterima tanpa persetujuan owner.
   Periksa pesanan di **Penyewaan**, dan bukti di **Verifikasi pembayaran**.
7. Klik **Lihat bukti** untuk membuka halaman detail. Gambar dapat dilihat
   langsung dan diklik untuk ukuran penuh; PDF tampil pada pembaca browser,
   dengan pilihan buka tab baru atau unduh. Cocokkan dengan uang yang
   benar-benar masuk ke rekening.
   Pilih **Pembayaran sesuai** untuk mengaktifkan sewa. Jika tidak sesuai,
   isi alasan lalu pilih **Tolak bukti**; penyewa mendapat 24 jam untuk upload
   ulang. Bukti pending/ditolak tidak dihitung sebagai pemasukan.
8. Buka **Tagihan** dan pilih **Detail tagihan** untuk melihat rincian
   penyewaan, pembayaran valid, sisa, serta riwayat pembayaran dan bukti.
   Pembayaran manual valid dapat dibatalkan dari riwayat dengan konfirmasi.
   Pesanan online mempunyai
   tagihan otomatis. Menu buat tagihan/catat pembayaran manual digunakan
   untuk penyewaan manual, termasuk pembayaran sebagian.
9. Buka **Laporan**, pilih periode, cabang, dan/atau lokasi. Penerimaan memakai
   tanggal pembayaran valid; piutang berasal dari tagihan yang diterbitkan
   pada periode. Pesanan batal/kedaluwarsa tidak dimasukkan ke piutang.
   Klik **Ekspor CSV** untuk mengunduh data sesuai filter.
10. Saat penghuni benar-benar selesai tinggal, buka **Penyewaan** dan pilih
    **Selesaikan**. Kamar akan tersedia lagi. Riwayat tetap disimpan.

Menu Kamar, Penyewaan, Tagihan, dan Verifikasi menyediakan filter agar data
lebih mudah ditemukan. Kamar yang masih dipesan/disewa mengarah ke daftar
penyewaan; statusnya tidak bisa ditimpa lewat edit kamar. Jika file bukti
hilang, admin tidak dapat menerima pembayaran; tolak dengan alasan agar
penyewa dapat mengunggah ulang. Akun lain tidak dapat melihat bukti tersebut.

Menu **Penghuni** dan tambah penyewaan manual tersedia untuk penghuni yang
dicatat langsung oleh owner. Kamar yang sedang disewa/dipesan dilindungi dari
perubahan yang dapat merusak proses. Cabang/lokasi/kamar/penghuni yang memiliki
relasi tidak dapat dihapus sembarangan. Di HP buka tombol menu pada header.

## Sebagai pengunjung / penyewa

1. Buka beranda, pilih Jatiwaringin, Kalibata, atau Margonda. Katalog dapat
   difilter berdasarkan lokasi, fasilitas, harga, serta ketersediaan kamar.
2. Buka detail cabang untuk melihat foto, fasilitas, harga, dan kamar tersedia.
3. Klik **Sewa kamar**. Jika belum login, masuk atau daftar akun penyewa.
4. Isi nomor telepon, tanggal masuk mulai hari ini, dan durasi 1–12 bulan.
   Total pembayaran adalah harga bulanan × durasi pilihan, dibayar penuh.
5. Klik **Pesan dan lanjut pembayaran**. Pesanan langsung diterima dan kamar
   ditahan 24 jam. Tidak perlu menunggu owner menyetujui pengajuan.
6. Pada **Sewa saya**, periksa total, rekening penerima, dan batas upload.
   Jika rekening belum tersedia, hubungi pengelola sebelum transfer.
7. Transfer sesuai tagihan, pilih tanggal transfer, lalu upload bukti JPG,
   PNG, atau PDF maksimal 5 MB. Status menjadi menunggu verifikasi.
8. Pantau **Sewa saya**. Pembayaran diterima membuat sewa aktif. Jika ditolak,
   baca alasannya dan upload bukti baru sebelum batas waktu berikutnya.
9. Pesanan yang belum upload dapat dibatalkan dari **Sewa saya**. Jika batas
   upload lewat, pesanan kedaluwarsa dan kamar dilepas. Kamar tetap ditahan
   selama bukti menunggu verifikasi owner.
10. Klik **Keluar** setelah selesai, terutama pada perangkat bersama.

## Batasan

Belum tersedia DP, refund otomatis, perpanjangan mandiri, dan kalender
pemesanan dengan slot tanggal. Penyewaan aktif tidak dilepas otomatis pada
tanggal akhir; owner menyelesaikannya setelah penghuni keluar. Rekening,
harga, foto, kontak, dan alamat sebenarnya harus diisi owner. Bukti transfer
privat hanya dapat diunduh akun penyewa terkait dan admin.
