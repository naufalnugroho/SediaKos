@extends('layouts.app')
@section('content')
<main class="guest-workspace">
    <div class="guest-heading"><div><p class="ui-eyebrow">Akun penyewa</p><h1>Sewa saya</h1><p>Semua urusan kamar dan pembayaran, dalam satu tempat.</p></div><a class="m-btn m-btn-secondary" href="{{ route('public.kos.index') }}"><iconify-icon icon="lucide:search"></iconify-icon>Cari kamar lain</a></div>
    <div class="rental-stats"><div><iconify-icon icon="lucide:key-round"></iconify-icon><span>Sewa aktif<strong>{{ $jumlahAktif }}</strong></span></div><div><iconify-icon icon="lucide:clock-3"></iconify-icon><span>Dalam proses<strong>{{ $jumlahMenunggu }}</strong></span></div><div><iconify-icon icon="lucide:history"></iconify-icon><span>Riwayat sewa<strong>{{ $jumlahSelesai }}</strong></span></div></div>
    <nav class="ui-tabs" aria-label="Filter sewa">@foreach(['' => 'Semua sewa', 'berjalan' => 'Sedang berjalan', 'riwayat' => 'Riwayat'] as $value => $label)<a class="{{ request('status', '') === $value ? 'active' : '' }}" href="{{ route('penyewa.index', $value ? ['status' => $value] : []) }}">{{ $label }}</a>@endforeach</nav>
    @forelse($penyewaan as $sewa)
    @php($berakhir = in_array($sewa->status, ['batal', 'kedaluwarsa', 'selesai']))
    <article class="rental-card">
        <header class="rental-card-header"><div class="rental-property"><span class="rental-property-icon"><iconify-icon icon="lucide:building-2"></iconify-icon></span><div><p>{{ $sewa->kamar->kos->wilayah->nama }}</p><h2>{{ $sewa->kamar->kos->nama }}</h2><span>Kamar {{ $sewa->kamar->kode }}</span></div></div><x-status :value="$sewa->status" /></header>
        <div class="rental-dates"><div><span>Tanggal masuk</span><strong>{{ $sewa->tanggal_mulai->translatedFormat('d M Y') }}</strong></div><iconify-icon icon="lucide:arrow-right" aria-hidden="true"></iconify-icon><div><span>Sampai dengan</span><strong>{{ $sewa->tanggal_akhir?->translatedFormat('d M Y') ?: 'Belum ditentukan' }}</strong></div><a class="ui-link" href="{{ route('public.kos.show', $sewa->kamar->kos) }}">Detail kos</a></div>
        @if(!$berakhir)<ol class="rental-progress" aria-label="Tahapan sewa"><li class="done"><span>1</span>Pesanan dibuat</li><li class="{{ $sewa->status !== 'menunggu_pembayaran' ? 'done' : 'current' }}"><span>2</span>Pembayaran</li><li class="{{ $sewa->status === 'aktif' ? 'done' : ($sewa->status === 'menunggu_verifikasi' ? 'current' : '') }}"><span>3</span>Sewa aktif</li></ol>@endif
        @foreach($sewa->tagihan as $tagihan)
        <div class="rental-content {{ $sewa->status === 'menunggu_pembayaran' ? 'rental-content-split' : '' }}">
            <section class="rental-invoice"><div class="ui-section-heading"><h3>Ringkasan tagihan</h3><x-status :value="$tagihan->status" /></div>
                <dl class="ui-description"><div><dt>Total sewa</dt><dd>Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</dd></div><div><dt>Pembayaran diterima</dt><dd>Rp {{ number_format($tagihan->totalPembayaranValid(), 0, ',', '.') }}</dd></div><div class="invoice-total"><dt>{{ in_array($sewa->status, ['batal', 'kedaluwarsa']) ? 'Tagihan dibatalkan' : 'Sisa pembayaran' }}</dt><dd>Rp {{ number_format(in_array($sewa->status, ['batal', 'kedaluwarsa']) ? 0 : $tagihan->sisaNominal(), 0, ',', '.') }}</dd></div></dl>
                <details class="invoice-reference"><summary>Nomor tagihan</summary><p class="ui-reference">{{ $tagihan->nomor_referensi }}</p></details>
                @if($sewa->status === 'menunggu_pembayaran')
                    <div class="payment-deadline"><iconify-icon icon="lucide:clock-3"></iconify-icon><div>Upload bukti sebelum<strong>{{ $sewa->batas_bayar->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</strong></div></div>
                    @if(!empty($rekening['rekening']) && !empty($rekening['bank']) && !empty($rekening['pemilik']))
                        <div class="bank-card"><span>Rekening tujuan transfer</span><strong>{{ $rekening['bank'] }}</strong><div class="bank-number">{{ $rekening['rekening'] }}<button type="button" class="m-icon-btn" data-copy-text="{{ $rekening['rekening'] }}" aria-label="Salin nomor rekening"><iconify-icon icon="lucide:copy"></iconify-icon></button></div><p>a.n. {{ $rekening['pemilik'] }}</p></div>
                    @else<div class="ui-note ui-note-warning"><iconify-icon icon="lucide:info"></iconify-icon><p><strong>Konfirmasi rekening terlebih dahulu.</strong>Pengelola belum mengisi rekening pembayaran. Hubungi pengelola{{ $sewa->kamar->kos->kontak ? ' di '.$sewa->kamar->kos->kontak : '' }} sebelum transfer.</p></div>@endif
                @endif
            </section>
            @if($sewa->status === 'menunggu_pembayaran' && $tagihan->sisaNominal() > 0)
            <section class="rental-upload"><h3>Konfirmasi pembayaran</h3><p class="ui-help">Sudah transfer? Unggah bukti agar pengelola dapat memeriksa pembayaran Anda.</p>
                <form method="POST" action="{{ route('penyewa.upload', $tagihan) }}" enctype="multipart/form-data">@csrf
                    <label class="ui-field">Tanggal transfer<input type="date" class="m-form-control" name="tanggal_bayar" min="{{ $tagihan->tanggal_terbit->toDateString() }}" max="{{ today()->toDateString() }}" value="{{ old('tanggal_bayar', today()->toDateString()) }}" required></label>
                    <label class="upload-zone"><iconify-icon icon="lucide:cloud-upload" aria-hidden="true"></iconify-icon><strong>Pilih bukti transfer</strong><span>JPG, PNG, atau PDF · maksimal 5 MB</span><input type="file" name="bukti" accept=".jpg,.jpeg,.png,.pdf" required data-file-input><span data-file-name aria-live="polite">Belum ada file dipilih</span></label>
                    <button class="m-btn m-btn-primary w-100 mt-3">Upload bukti pembayaran<iconify-icon icon="lucide:arrow-up-right"></iconify-icon></button>
                </form>
            </section>@endif
        </div>
        @if($tagihan->pembayaran->isNotEmpty())<section class="rental-history"><h3>Riwayat pembayaran</h3>@foreach($tagihan->pembayaran as $bayar)<div class="payment-history-row"><span class="payment-file-icon"><iconify-icon icon="lucide:file-check-2"></iconify-icon></span><div><strong>Rp {{ number_format($bayar->nominal, 0, ',', '.') }}</strong><p>{{ $bayar->tanggal_bayar->format('d/m/Y') }} · Transfer</p>@if($bayar->alasan_penolakan)<p class="text-danger">{{ $bayar->alasan_penolakan }}</p>@endif</div><x-status :value="$bayar->status" />@if($bayar->bukti_path)<a class="m-btn m-btn-secondary" href="{{ route('pembayaran.preview', $bayar) }}" target="_blank" rel="noopener">Lihat bukti</a><a class="ui-link" href="{{ route('pembayaran.bukti', $bayar) }}">Unduh</a>@endif</div>@endforeach</section>@endif
        @endforeach
        <footer class="rental-card-footer">
            @if($sewa->status === 'menunggu_pembayaran')<p>Pesanan belum dibayar? Anda dapat membatalkannya sebelum verifikasi.</p><form method="POST" action="{{ route('penyewa.cancel', $sewa) }}" onsubmit="return confirm('Batalkan pemesanan kamar ini?')">@csrf @method('PATCH')<button class="ui-text-button">Batalkan pemesanan</button></form>
            @elseif($sewa->status === 'menunggu_verifikasi')<p><iconify-icon icon="lucide:shield-check"></iconify-icon>Bukti diterima. Pengelola sedang memeriksa pembayaran; kamar tetap ditahan untuk Anda.</p>
            @elseif($sewa->status === 'aktif')<p><iconify-icon icon="lucide:circle-check"></iconify-icon>Pembayaran sudah diverifikasi. Hubungi pengelola untuk persiapan masuk kos.</p>
            @else<p>{{ $sewa->status === 'selesai' ? 'Masa sewa ini sudah selesai. Riwayat pembayaran tetap tersimpan.' : 'Pesanan ini sudah berakhir. Anda dapat memilih kamar yang tersedia kembali.' }}</p>@endif
        </footer>
    </article>
    @empty<section class="ui-empty ui-panel"><iconify-icon icon="lucide:house-plus"></iconify-icon><h2>Tempat baru Anda dimulai di sini.</h2><p>Belum ada sewa pada daftar ini. Pilih lokasi dan kamar yang sesuai untuk mulai memesan.</p><a class="m-btn m-btn-primary" href="{{ route('public.kos.index') }}">Jelajahi kamar</a></section>@endforelse
    {{ $penyewaan->links() }}
</main>
@endsection
