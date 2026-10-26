@extends('layouts.app')
@section('content')
<div class="m-main mx-auto" style="max-width:1440px">
    <div class="m-topbar"><div><h1 class="m-page-title">Cari Kos</h1><p class="m-page-subtitle">Temukan kos aktif sesuai lokasi dan anggaran.</p></div></div>
    <form class="m-shell-card mb-4" method="GET"><div class="m-card m-card-body"><div class="row g-2">
        <div class="col-lg-3"><input class="m-form-control" name="q" value="{{ request('q') }}" placeholder="Cari nama kos..."></div>
        <div class="col-lg-2"><select class="m-form-control" name="wilayah_id"><option value="">Semua wilayah</option>@foreach($wilayah as $item)<option value="{{ $item->id }}" @selected(request('wilayah_id') == $item->id)>{{ $item->nama }}</option>@endforeach</select></div>
        <div class="col-lg-2"><select class="m-form-control" name="fasilitas_id"><option value="">Semua fasilitas</option>@foreach($fasilitas as $item)<option value="{{ $item->id }}" @selected(request('fasilitas_id') == $item->id)>{{ $item->nama }}</option>@endforeach</select></div>
        <div class="col-lg-2"><input class="m-form-control" type="number" name="max_harga" value="{{ request('max_harga') }}" placeholder="Harga maks."></div>
        <div class="col-lg-2"><select class="m-form-control" name="sort"><option value="">Terbaru</option><option value="termurah" @selected(request('sort') === 'termurah')>Termurah</option></select></div>
        <div class="col-lg-1"><button class="m-btn m-btn-primary w-100" aria-label="Cari"><iconify-icon icon="lucide:search"></iconify-icon></button></div>
    </div><label class="d-inline-flex align-items-center gap-2 mt-3 small text-muted"><input type="checkbox" name="tersedia" value="1" @checked(request('tersedia'))> Hanya kamar tersedia</label></div></form>
    <div class="row g-3">@forelse($kos as $item)<div class="col-md-6 col-xl-4"><div class="m-shell-card h-100"><article class="m-card h-100 overflow-hidden"><div class="p-3"><div class="m-label">{{ $item->wilayah->nama }}</div><h2 class="m-card-title mt-2">{{ $item->nama }}</h2><p class="m-page-subtitle mt-2">{{ \Illuminate\Support\Str::limit($item->alamat, 80) }}</p><div class="d-flex justify-content-between align-items-center mt-3"><span class="m-status m-status-{{ $item->kamar_tersedia_count ? 'success' : 'warning' }}"><iconify-icon icon="lucide:circle-check"></iconify-icon>{{ $item->kamar_tersedia_count }} tersedia</span><a href="{{ route('public.kos.show', $item) }}" class="m-btn m-btn-secondary">Detail</a></div></div></article></div></div>@empty<div class="m-empty">Kos tidak ditemukan.</div>@endforelse</div>{{ $kos->links() }}
</div>
@endsection