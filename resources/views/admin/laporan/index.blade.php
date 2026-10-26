@extends('layouts.app')
@section('content')
<div class="m-shell"><aside class="m-sidebar">@include('admin._sidebar')</aside><main class="m-main">
<div class="m-topbar"><div><h1 class="m-page-title">Laporan</h1><p class="m-page-subtitle">Penerimaan transfer terverifikasi dan sisa tagihan.</p></div><a class="m-btn m-btn-secondary" href="{{ route('admin.laporan.csv', request()->query()) }}"><iconify-icon icon="lucide:download"></iconify-icon>Ekspor CSV</a></div>
<form method="GET" class="m-card m-card-body mb-4"><div class="row g-3 align-items-end">
<div class="col-md-3"><label class="d-block">Mulai<input type="date" name="mulai" class="m-form-control mt-2" value="{{ $mulai->toDateString() }}"></label></div>
<div class="col-md-3"><label class="d-block">Akhir<input type="date" name="akhir" class="m-form-control mt-2" value="{{ $akhir->toDateString() }}"></label></div>
<div class="col-md-3"><label class="d-block">Cabang<select name="kos_id" class="m-form-control mt-2"><option value="">Semua cabang</option>@foreach($kos as $item)<option value="{{ $item->id }}" @selected(request('kos_id') == $item->id)>{{ $item->nama }}</option>@endforeach</select></label></div>
<div class="col-md-3"><label class="d-block">Lokasi<select name="wilayah_id" class="m-form-control mt-2"><option value="">Semua lokasi</option>@foreach($wilayah as $item)<option value="{{ $item->id }}" @selected(request('wilayah_id') == $item->id)>{{ $item->nama }}</option>@endforeach</select></label></div>
<div><button class="m-btn m-btn-primary">Terapkan filter</button></div>
</div></form>
<div class="row g-3 mb-4"><div class="col-md-6"><div class="m-card m-card-body"><span class="m-label">Pembayaran diterima pada periode</span><div class="m-kpi-value">Rp {{ number_format($diterima, 0, ',', '.') }}</div></div></div><div class="col-md-6"><div class="m-card m-card-body"><span class="m-label">Sisa tagihan yang diterbitkan pada periode</span><div class="m-kpi-value">Rp {{ number_format($piutang, 0, ',', '.') }}</div></div></div></div>
<div class="m-card m-table-wrap"><table class="m-table"><thead><tr><th>Referensi</th><th>Penghuni / cabang</th><th>Nominal</th><th>Status</th><th>Sisa</th></tr></thead><tbody>@forelse($tagihan as $item)<tr><td>{{ $item->nomor_referensi }}</td><td>{{ $item->penyewaan->penghuni->nama }}<br><small class="text-muted">{{ $item->penyewaan->kamar->kos->nama }}</small></td><td>Rp {{ number_format($item->nominal, 0, ',', '.') }}</td><td>{{ str_replace('_', ' ', $item->status) }}</td><td>Rp {{ number_format(max(0, $item->nominal - $item->diterima), 0, ',', '.') }}</td></tr>@empty<tr><td colspan="5" class="m-empty">Tidak ada tagihan untuk filter ini.</td></tr>@endforelse</tbody></table></div>{{ $tagihan->links() }}
</main></div>
@endsection
