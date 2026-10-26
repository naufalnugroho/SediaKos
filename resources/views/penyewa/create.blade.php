@extends('layouts.app')
@section('content')
<main class="guest-workspace">
<a class="ui-back" href="{{ route('public.kos.show', $kamar->kos) }}"><iconify-icon icon="lucide:arrow-left"></iconify-icon>Kembali ke detail kos</a>
<div class="guest-heading"><div><p class="ui-eyebrow">Pemesanan kamar</p><h1>Selangkah lagi menuju kamar Anda.</h1><p>Lengkapi data sewa, lalu lanjutkan ke pembayaran.</p></div></div>
<div class="booking-layout"><section class="ui-panel"><div class="ui-section-heading"><h2>Data penyewa & durasi</h2><span class="ui-help">Langkah 1 dari 2</span></div>
<form method="POST" action="{{ route('penyewa.store', $kamar) }}" data-booking-form data-monthly-price="{{ $kamar->harga }}">@csrf
    <div class="booking-account"><span class="rental-property-icon"><iconify-icon icon="lucide:user-round"></iconify-icon></span><div><strong>{{ auth()->user()->name }}</strong><span>{{ auth()->user()->email }}</span></div></div>
    <label class="ui-field">Nomor telepon<input class="m-form-control" type="tel" name="telepon" value="{{ old('telepon') }}" placeholder="Contoh: 081234567890" required maxlength="30" autocomplete="tel"><small>Digunakan pengelola untuk menghubungi Anda.</small></label>
    <div class="row g-3"><div class="col-md-6"><label class="ui-field">Tanggal masuk<input class="m-form-control" type="date" name="tanggal_mulai" min="{{ today()->toDateString() }}" value="{{ old('tanggal_mulai', today()->toDateString()) }}" required></label></div><div class="col-md-6"><label class="ui-field">Durasi sewa<select class="m-form-control" name="durasi" data-booking-duration>@for($i=1;$i<=12;$i++)<option value="{{ $i }}" @selected(old('durasi', 1) == $i)>{{ $i }} bulan</option>@endfor</select></label></div></div>
    <div class="ui-note mb-4"><iconify-icon icon="lucide:info"></iconify-icon><p><strong>Pesanan langsung diterima.</strong>Kamar ditahan 24 jam untuk pembayaran dan upload bukti. Sewa aktif setelah pembayaran diverifikasi pengelola.</p></div>
    <button class="m-btn m-btn-primary w-100">Pesan dan lanjut pembayaran<iconify-icon icon="lucide:arrow-right"></iconify-icon></button>
</form></section>
<aside class="booking-summary ui-panel"><div class="booking-property-art"><iconify-icon icon="lucide:building-2"></iconify-icon><span>{{ $kamar->kos->wilayah->nama }}</span></div><h2>{{ $kamar->kos->nama }}</h2><p class="ui-help">Kamar {{ $kamar->kode }} · {{ $kamar->kos->alamat }}</p><dl class="ui-description"><div><dt>Harga per bulan</dt><dd>Rp {{ number_format($kamar->harga, 0, ',', '.') }}</dd></div><div><dt>Durasi pilihan</dt><dd data-summary-duration>{{ old('durasi', 1) }} bulan</dd></div><div class="invoice-total"><dt>Total pembayaran</dt><dd data-summary-total>Rp {{ number_format($kamar->harga * (int) old('durasi', 1), 0, ',', '.') }}</dd></div></dl><p class="ui-help">Dibayar penuh melalui transfer. Rincian rekening dan upload bukti tersedia setelah pemesanan.</p></aside>
</div></main>
@endsection
