@extends('layouts.app')
@section('content')
@php($sewa = $pembayaran->tagihan->penyewaan)
<div class="m-shell"><aside class="m-sidebar">@include('admin._sidebar')</aside><main class="m-main">
<a class="ui-back" href="{{ route('admin.verifikasi.index') }}"><iconify-icon icon="lucide:arrow-left"></iconify-icon>Semua bukti pembayaran</a>
<div class="m-topbar"><div><h1 class="m-page-title">Detail pembayaran #{{ $pembayaran->id }}</h1><p class="m-page-subtitle">{{ $sewa->penghuni->nama }} · {{ $sewa->kamar->kos->nama }} / {{ $sewa->kamar->kode }}</p></div><x-status :value="$pembayaran->status" /></div>
<div class="workspace-columns"><section class="m-card m-card-body">
<div class="ui-section-heading"><h2>Bukti transfer</h2>@if($fileAda)<a class="m-btn m-btn-secondary" href="{{ route('pembayaran.bukti', $pembayaran) }}"><iconify-icon icon="lucide:download"></iconify-icon>Unduh</a>@endif</div>
@if($fileAda)
    @if(str_starts_with($mime ?? '', 'image/'))<a href="{{ route('pembayaran.preview', $pembayaran) }}" target="_blank" rel="noopener"><img class="proof-image" src="{{ route('pembayaran.preview', $pembayaran) }}" alt="Bukti transfer pembayaran {{ $pembayaran->id }}"></a><p class="ui-help mt-3">Klik gambar untuk melihat ukuran penuh.</p>
    @elseif($mime === 'application/pdf')<iframe class="proof-pdf" src="{{ route('pembayaran.preview', $pembayaran) }}" title="Bukti transfer PDF"></iframe><a class="ui-link" href="{{ route('pembayaran.preview', $pembayaran) }}" target="_blank" rel="noopener">Buka PDF di tab baru</a>
    @else<p>Format ini tidak dapat dipreview. Gunakan tombol unduh untuk memeriksanya.</p>@endif
@else<div class="ui-empty"><iconify-icon icon="lucide:file-warning"></iconify-icon><h3>File bukti tidak tersedia</h3><p>Minta penyewa mengunggah ulang. Pembayaran tidak dapat diterima sebelum bukti tersedia.</p></div>@endif
</section><aside><section class="m-card m-card-body mb-4"><h2 class="m-card-title mb-4">Rincian transfer</h2><dl class="ui-description"><div><dt>Nominal</dt><dd class="ui-money">Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</dd></div><div><dt>Tanggal transfer</dt><dd>{{ $pembayaran->tanggal_bayar->format('d/m/Y') }}</dd></div><div><dt>Kontak penyewa</dt><dd>{{ $sewa->penghuni->telepon }}</dd></div><div><dt>Tagihan</dt><dd><a class="ui-link ui-reference" href="{{ route('admin.tagihan.show', $pembayaran->tagihan) }}">{{ $pembayaran->tagihan->nomor_referensi }}</a></dd></div><div><dt>Status sewa</dt><dd><x-status :value="$sewa->status" /></dd></div></dl></section>
<section class="m-card m-card-body"><h2 class="m-card-title mb-3">Hasil pemeriksaan</h2>
@if($pembayaran->status === 'menunggu_verifikasi')
<p class="ui-help mb-3">Pastikan uang sudah masuk ke rekening pengelola dan nominalnya sesuai.</p>
<form method="POST" action="{{ route('admin.verifikasi.update', $pembayaran) }}" onsubmit="return confirm('Simpan hasil verifikasi pembayaran?')">@csrf @method('PATCH')<label class="ui-field">Alasan penolakan<textarea class="m-form-control" name="alasan" rows="3" maxlength="1000" placeholder="Wajib diisi jika bukti ditolak">{{ old('alasan') }}</textarea></label><div class="d-flex flex-wrap gap-2"><button class="m-btn m-btn-primary" name="keputusan" value="terima" @disabled(!$fileAda)>Pembayaran sesuai</button><button class="m-btn m-btn-danger" name="keputusan" value="tolak">Tolak bukti</button></div></form>
@else
<x-status :value="$pembayaran->status" /><p class="ui-help mt-3 mb-0">{{ $pembayaran->alasan_penolakan ?: 'Hasil pemeriksaan sudah disimpan.' }}</p>
@if($pembayaran->diverifikasi_pada)
<p class="ui-help mt-2 mb-0">{{ $pembayaran->adminPencatat?->name }} · {{ $pembayaran->diverifikasi_pada->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</p>
@endif
@endif
</section></aside></div>
</main></div>
@endsection
