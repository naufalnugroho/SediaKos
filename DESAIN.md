Metoric Design System
Sistem desain admin dashboard SaaS terang: aksen ungu tunggal, kartu shell ganda 18/12, Plus Jakarta Sans, badge status teks+ikon. Halaman: Dashboard, Orders, Products.

Overview
Metoric adalah sistem admin dashboard SaaS bergaya terang (light theme) untuk manajemen toko online: Dashboard, Orders, Products, Customers, Analytics. Karakter visual: canvas abu mendingin #f6f6f8, aksen ungu tunggal #7a5af8, kartu berlapis dua (shell luar #fafafc radius 18 + kartu dalam putih radius 12), tipografi Plus Jakarta Sans, ikon garis Iconify lucide:*. Semua halaman memakai shell yang sama: sidebar 296px + area utama (topbar judul → toolbar tab+aksi → konten). Status tidak pernah disampaikan dengan warna saja — selalu badge berisi teks + ikon.

Colors
Satu hue aksen (ungu) + satu ramp netral + empat ramp status. Pakai token, jangan hardcode.

Aksen: --color-primary #7a5af8 untuk satu aksi primer per view, item nav aktif, angka fokus; --color-primary-deep #6a48f0 untuk hover/border tombol primer.
Netral: teks --color-text #17171d; teks sekunder --color-text-muted #5b5c66; teks redup/label --color-text-subtle #9c9daa; garis --color-border #e9e9f0.
Permukaan: --color-canvas #f6f6f8 (latar halaman), --color-surface #ffffff (kartu dalam), --color-surface-subtle #fafafc (shell kartu/KPI luar), --color-surface-raised #fbfbfc (sidebar/topbar).
Status (selalu pasangan teks+bg untuk badge): success #189a4f / #e4f6ec (Paid, Active, delta naik); warning #f0a51f / #fdf3df (Pending, Unfulfilled, Low stock); danger #e5484d / #fdebeb (Refunded, Out of stock, delta turun buruk); info #2f6fed / #e9f0fd (In transit).
Arti arah delta mengikuti makna bisnis, bukan arah panah: Returns turun = hijau (baik). Jangan menambah warna status baru; jangan memakai warna status untuk dekorasi.
Typography
Keluarga font tunggal: --font-family-sans "Plus Jakarta Sans" (fallback "Segoe UI", system-ui). Dimuat via Google Fonts display=swap; offline jatuh mulus ke fallback, jangan tambah keluarga kedua.

Skala: --text-xs 12 (label uppercase letter-spacing:.09em), --text-sm 13.5 (meta, tanggal, info pager), --text-base 14.5 (tabel, tombol, nav), --text-body 15 (dasar halaman, line-height 1.45), --text-title 21 (logo), --text-display 26 (judul halaman & judul kartu, letter-spacing:-.02em).
Bobot: --weight-regular 400 teks isi; --weight-medium 500 label & tab; --weight-semibold 600 tombol, badge, nilai tabel, nav aktif; --weight-bold 700 angka KPI, harga, total, judul.
Label kartu/KPI selalu uppercase 12px semibold subtle. Angka penting (nilai KPI, total order, harga) tampil bold dengan peluru -0.02em.
Layout
Grid shell dua kolom: sidebar 296px + minmax(0,1fr); sidebar sticky full-height.

Konten: padding 22px 28px 30px, gap antar-section --space-5 (20px).
Baris KPI: grid 4 kolom; ≤1400px jatuh ke 2 kolom; sidebar menyusut ke 250px.
≤980px: sidebar disembunyikan, padding halaman 18px, tabel scroll horizontal (.m-table-wrap{overflow-x:auto}), toolbar & pager wrap.
Pola halaman konsisten: topbar (judul --text-display + aksi ikon) → toolbar (tab pill + aksi kanan) → konten (KPI → kartu utama).
KPI grid: 4 kartu minmax(0,1fr) gap 20. Jangan biarkan overflow: semua sel grid memakai minmax(0, …).
Elevation & Depth
Desain datar: kedalaman dari border 1px + bayangan sangat lembut, bukan offset besar.

Kartu & KPI: 0 1px 2px rgba(23,23,43,.02–.05) + --color-border.
Search field: 0 1px 2px rgba(23,23,43,.04); saat fokus ganti ke ring 0 0 0 3px rgba(122,90,248,.14) + border #c8b6fb.
Tombol primer: bayangan tint aksen 0 1px 2px rgba(122,90,248,.35).
Elemen mengambang (tooltip chart): 0 12px 28px rgba(23,23,43,.12) — satu-satunya bayangan besar, hanya untuk overlay.
Hover interaksi memakai perubahan latar #f5f5f8/#f3f3f6, bukan elevasi baru. Focus ring global: outline 2px var(--color-accent) offset 2.
Shapes
Radius berlapis untuk kartu: shell luar --radius-lg (18) + kartu dalam --radius-md (12) dengan padding shell 8px — ini ciri khas Metoric, pertahankan pada kartu besar (KPI, chart, tabel).

--radius-md 12: tombol, input/search, select group, kartu dalam, thumbnail 36–46px.
--radius-sm 8: kbd, tab aktif di dalam group 13px, aksi baris.
--radius-full 999: badge status, delta pill, count tab, toggle switch, avatar.
Group select/tabs memakai pembatas 1px --color-border antar item, bukan gap.
Components
Semua komponen page-owned (class prefix m-), tidak ada komponen runtime terdaftar. Resep inti:

Sidebar: logo lucide:loader-circle ungu 30px + nama 21px bold; search ⌘K; label menu --text-xs uppercase; nav item 40px radius 11 dengan ikon 20px; item aktif #f2f2f5 + teks/ikon aksen.
Topbar: judul halaman 26px bold; aksi ikon 40px radius 11; avatar gradient #9d85fb→#6a48f0 36px.
Tabs: group pill putih radius 13 border; tab aktif putih + inner ring; count pill 12px bold (aktif: aksen di #f1ecfe).
Tombol: primer aksen solid + ikon; sekunder putih border; icon-btn 40px; semua radius 12, teks 14.5 semibold.
KPI card: shell ganda; label uppercase; nilai 26px bold + delta pill; kaki kartu strong + span + panah.
Badge status: pill 12.5 semibold = ikon 14px + teks, pasangan warna status; teks wajib ada.
Data table: header 13.5 subtle; sel 13px 16px; baris hover #fafafd, terpilih #f7f4ff; checkbox aksen 16px.
Toggle: 46x27, on = #c9b7fa; knob 21px; role="switch".
Chart: area gradient aksen/amber opacity ≤.16, garis 2.5, dot putih 2.5, gridline dashed 3 6.
Pager: info kiri --text-sm subtle + tombol 36px; halaman aktif solid aksen.
Search: ikon kiri 18px, input 14.5, kbd kanan; fokus ring aksen.
Do's and Don'ts
Do

Pakai token --color-*, --text-*, --radius-* yang sudah terdaftar; ikon dari set lucide:* via <iconify-icon>.
Beri aria-label pada tombol ikon-only; ikon dekoratif aria-hidden="true".
Status = warna + ikon + teks sekaligus; delta memakai arah panah sesuai makna bisnis.
Jaga pola halaman: topbar → toolbar → konten; kartu besar selalu shell ganda 18/12.
Sertakan state: hover, fokus ring, baris terpilih, disabled pager, prefers-reduced-motion menonaktifkan animasi.
Don't

Jangan menambah hue baru, gradien dekoratif, glassmorphism, atau bayangan besar di luar tooltip.
Jangan hardcode nilai yang sudah ada tokennya (warna, radius, spacing, ukuran font).
Jangan memakai warna status untuk aksi/dekorasi biasa; satu tombol primer solid per view.
Jangan menyembunyikan status hanya di balik warna (mis. titik tanpa teks).
Jangan menambah keluarga font kedua atau bobot di luar 400–700.