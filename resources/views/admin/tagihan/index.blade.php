@extends('layouts.app')
@section('content')
<div class="m-shell"><aside class="m-sidebar">@include('admin._sidebar')</aside><main class="m-main">
<div class="m-topbar"><div><h1 class="m-page-title">Tagihan</h1><p class="m-page-subtitle">Lihat rincian, riwayat pembayaran, dan bukti transfer setiap tagihan.</p></div><a href="{{ route('admin.tagihan.create') }}" class="m-btn m-btn-primary">Buat tagihan manual</a></div>
<form method="GET" class="m-card m-card-body mb-4"><div class="row g-3 align-items-end"><div class="col-md-6"><label class="ui-field mb-0">Cari tagihan<input class="m-form-control" name="q" value="{{ request('q') }}" placeholder="Nomor tagihan atau nama penghuni"></label></div><div class="col-md-4"><label class="ui-field mb-0">Status<select class="m-form-control" name="status"><option value="">Semua status</option>@foreach(['belum_dibayar' => 'Belum dibayar', 'dibayar_sebagian' => 'Dibayar sebagian', 'lunas' => 'Lunas', 'terlambat' => 'Terlambat', 'dibatalkan' => 'Dibatalkan'] as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select></label></div><div class="col-md-2"><button class="m-btn m-btn-primary w-100">Terapkan</button></div></div></form>
<div class="m-card m-table-wrap"><table class="m-table"><thead><tr><th>Referensi</th><th>Penghuni / kamar</th><th>Nominal</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
@forelse($tagihan as $item)<tr><td><a class="ui-link ui-reference" href="{{ route('admin.tagihan.show', $item) }}">{{ $item->nomor_referensi }}</a></td><td>{{ $item->penyewaan->penghuni->nama }}<br><small class="text-muted">{{ $item->penyewaan->kamar->kos->nama }} / {{ $item->penyewaan->kamar->kode }}</small></td><td>Rp {{ number_format($item->nominal, 0, ',', '.') }}</td><td><x-status :value="$item->status" /></td><td><a class="m-btn m-btn-secondary" href="{{ route('admin.tagihan.show', $item) }}">Detail tagihan</a></td></tr>
@empty<tr><td colspan="5" class="m-empty">Belum ada tagihan. Tagihan pesanan online akan muncul otomatis.</td></tr>@endforelse
</tbody></table></div><div class="mt-4">{{ $tagihan->links() }}</div>
</main></div>
@endsection
