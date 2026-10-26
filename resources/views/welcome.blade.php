@extends('layouts.app')
@section('content')
<main class="landing" id="top">
    <header class="landing-hero">
        <div class="landing-container landing-hero-content">
            <div class="landing-intro">
                <h1>Temukan kos.<br>Mulai cerita baru.</h1>
                <p>Pilih tempat tinggal Anda di Jatiwaringin, Kalibata, atau Margonda. Pesan kamar dan selesaikan pembayaran langsung dari sini.</p>
                <a class="landing-button" href="#lokasi">Pilih lokasi kos</a>
                <div class="landing-stats">
                    <div><strong>{{ $cabang->count() }}</strong><span>Lokasi kos</span></div>
                    <div><strong>{{ $cabang->sum('tersedia') }}</strong><span>Kamar tersedia</span></div>
                    <div><strong>Online</strong><span>Pesan & upload bukti</span></div>
                </div>
            </div>
            <div class="landing-building" aria-hidden="true">@include('partials.landing-building')</div>
        </div>
    </header>
    <div class="landing-container landing-search-wrap">
        <form class="landing-search" method="GET" action="{{ route('public.kos.index') }}">
            <h2>Di mana Anda ingin tinggal?</h2>
            <div class="row g-3 align-items-end">
                <div class="col-md-5"><label for="lokasi-kos">Lokasi kos</label><select id="lokasi-kos" class="form-select" name="wilayah_id"><option value="">Semua lokasi</option>@foreach($cabang->pluck('wilayah')->unique('id') as $wilayah)<option value="{{ $wilayah->id }}">{{ $wilayah->nama }}</option>@endforeach</select></div>
                <div class="col-md-4"><label for="harga-kos">Anggaran per bulan</label><input id="harga-kos" class="form-control" type="number" name="max_harga" min="0" placeholder="Harga maksimum (Rp)"></div>
                <div class="col-md-3"><button class="landing-button w-100" type="submit">Cari kamar</button></div>
            </div>
        </form>
    </div>
    <section class="landing-container landing-locations" id="lokasi">
        <div class="landing-section-heading"><div><h2>Tiga lokasi, satu pengelola.</h2><p>Temukan kamar yang sesuai dengan keseharian Anda.</p></div><a href="{{ route('public.kos.index') }}">Lihat semua kamar</a></div>
        <div class="row g-4">
            @forelse($cabang as $kos)
            <div class="col-md-4"><article class="landing-property h-100">
                @if($kos->fotoKos->isNotEmpty())<img class="landing-property-image" src="{{ Storage::disk('public')->url($kos->fotoKos->first()->path) }}" alt="{{ $kos->nama }}" loading="lazy">
                @else<div class="landing-property-placeholder"><iconify-icon icon="lucide:building-2" aria-hidden="true"></iconify-icon><span>{{ $kos->wilayah->nama }}</span><small>Foto akan ditambahkan pengelola</small></div>@endif
                <div class="landing-property-body"><p class="landing-location"><iconify-icon icon="lucide:map-pin" aria-hidden="true"></iconify-icon> {{ $kos->wilayah->nama }}</p><h3>{{ $kos->nama }}</h3><p>{{ $kos->alamat }}</p>
                    <div class="landing-price">@if($kos->kamar_min_harga !== null)<strong>Rp {{ number_format($kos->kamar_min_harga, 0, ',', '.') }}</strong><span> / bulan</span>@else<span>Harga kamar belum tersedia</span>@endif</div>
                    <p class="mt-3">{{ $kos->tersedia }} kamar tersedia</p><a class="landing-button landing-button-outline w-100" href="{{ route('public.kos.show', $kos) }}">Lihat kamar</a>
                </div>
            </article></div>
            @empty<div class="col-12"><p>Informasi cabang sedang disiapkan oleh pengelola.</p></div>@endforelse
        </div>
    </section>
    <section class="landing-how" id="cara-sewa"><div class="landing-container"><h2>Sewa kos dari mana saja.</h2><div class="row g-4 mt-3">
        <div class="col-md-4"><span class="landing-step">1</span><h3>Pilih kamar</h3><p>Lihat lokasi, fasilitas, dan harga. Buat akun untuk melanjutkan pemesanan.</p></div>
        <div class="col-md-4"><span class="landing-step">2</span><h3>Pesan & bayar</h3><p>Tentukan tanggal masuk dan durasi. Pesanan langsung diterima; upload bukti transfer dalam 24 jam.</p></div>
        <div class="col-md-4"><span class="landing-step">3</span><h3>Siap masuk kos</h3><p>Owner memverifikasi pembayaran. Pantau status sewa dan tagihan melalui akun Anda.</p></div>
    </div></div></section>
    <section class="landing-container landing-about" id="tentang">
        <div class="landing-about-heading"><h2>Dari mencari kamar<br>sampai siap tinggal.</h2><p>SediaKos menghubungkan Anda dengan tiga cabang kos dalam satu pengelolaan. Pilih kamar dan pantau proses sewa tanpa harus berpindah-pindah tempat.</p></div>
        <div class="row g-4 mt-3">
            <div class="col-md-4"><article class="landing-benefit"><iconify-icon icon="lucide:map-pin" aria-hidden="true"></iconify-icon><h3>Pilih lokasi Anda</h3><p>Bandingkan kamar di Jatiwaringin, Kalibata, dan Margonda. Lihat alamat, foto, dan fasilitas sebelum memesan.</p></article></div>
            <div class="col-md-4"><article class="landing-benefit"><iconify-icon icon="lucide:receipt-text" aria-hidden="true"></iconify-icon><h3>Biaya terlihat di awal</h3><p>Harga bulanan dan total durasi tampil sebelum pesanan dibuat. Tagihan bisa dilihat kembali di akun Anda.</p></article></div>
            <div class="col-md-4"><article class="landing-benefit"><iconify-icon icon="lucide:folder-check" aria-hidden="true"></iconify-icon><h3>Pantau dalam satu akun</h3><p>Upload bukti transfer, lihat hasil verifikasi, dan simpan riwayat pembayaran melalui halaman Sewa saya.</p></article></div>
        </div>
    </section>
    <section class="landing-faq-section" id="pertanyaan"><div class="landing-container landing-faq-grid">
        <div><h2>Sebelum Anda<br>memesan kamar.</h2><p>Beberapa hal yang perlu diketahui tentang proses sewa di SediaKos.</p><a href="#lokasi">Lihat pilihan lokasi</a></div>
        <div class="landing-faq">
            <details><summary>Apakah perlu menunggu persetujuan owner?</summary><p>Tidak. Setelah Anda memilih kamar dan mengisi data, pesanan langsung diterima. Owner memeriksa bukti pembayaran sebelum sewa diaktifkan.</p></details>
            <details><summary>Bagaimana cara membayar sewa?</summary><p>Transfer sesuai total tagihan ke rekening yang ditampilkan pada Sewa saya, lalu upload bukti JPG, PNG, atau PDF maksimal 5 MB. Pastikan rekening tujuan sudah tersedia atau hubungi pengelola melalui kontak di detail cabang.</p></details>
            <details><summary>Berapa lama batas upload bukti pembayaran?</summary><p>Anda memiliki 24 jam sejak pemesanan. Jika tidak mengunggah bukti sampai batas waktu, pesanan kedaluwarsa dan kamar kembali tersedia. Kamar tetap ditahan ketika bukti sedang diverifikasi.</p></details>
            <details><summary>Apakah bisa membayar uang muka?</summary><p>Saat ini pembayaran dilakukan penuh untuk durasi pilihan, mulai 1 sampai 12 bulan. Total dihitung dari harga bulanan kamar dikalikan jumlah bulan.</p></details>
            <details><summary>Bagaimana kalau bukti pembayaran ditolak?</summary><p>Alasan penolakan tampil di Sewa saya. Periksa alasannya dan upload bukti baru dalam 24 jam setelah penolakan. Hubungi pengelola jika memerlukan bantuan pengecekan transfer.</p></details>
            <details><summary>Apakah pesanan bisa dibatalkan?</summary><p>Pesanan dapat dibatalkan melalui Sewa saya sebelum bukti masuk proses verifikasi. Untuk pembayaran yang sudah dilakukan atau kebutuhan pengembalian dana, hubungi pengelola; refund otomatis belum tersedia.</p></details>
        </div>
    </div></section>
    <section class="landing-cta-section"><div class="landing-container landing-cta"><div><h2>Kamar berikutnya,<br>mulai dari sini.</h2><p>Pilih lokasi, lihat ketersediaan, dan temukan kamar untuk Anda.</p></div><a class="landing-button" href="#lokasi">Temukan kamar</a></div></section>
    <footer class="landing-footer-full"><div class="landing-container">
        <div class="landing-footer-grid">
            <div class="landing-footer-brand"><a href="{{ route('home') }}">SediaKos</a><p>Tempat baru untuk keseharian Anda.<br>Tiga lokasi, satu pengelola.</p></div>
            <nav aria-label="Pilihan lokasi di footer"><h3>Lokasi kos</h3>@foreach($cabang as $kos)<a href="{{ route('public.kos.show', $kos) }}">{{ $kos->wilayah->nama }}</a>@endforeach</nav>
            <nav aria-label="Informasi di footer"><h3>Jelajahi</h3><a href="{{ route('public.kos.index') }}">Cari kamar</a><a href="#cara-sewa">Cara sewa</a><a href="#tentang">Tentang SediaKos</a><a href="#pertanyaan">Pertanyaan umum</a></nav>
            <nav aria-label="Akun di footer"><h3>Akun Anda</h3>@auth<a href="{{ route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'penyewa.index') }}">{{ auth()->user()->isAdmin() ? 'Dashboard owner' : 'Sewa saya' }}</a>@else<a href="{{ route('login') }}">Masuk akun</a><a href="{{ route('register') }}">Daftar penyewa</a>@endauth<p>Kontak pengelola tersedia pada halaman detail masing-masing cabang.</p></nav>
        </div>
        <div class="landing-footer-bottom"><span>© {{ date('Y') }} SediaKos. Seluruh hak dilindungi.</span><a href="#top">Kembali ke atas ↑</a></div>
    </div></footer>
</main>
@endsection
