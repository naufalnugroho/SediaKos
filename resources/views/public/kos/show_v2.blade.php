@extends('layouts.app')
@section('content')
<div class="m-main mx-auto" style="max-width:1200px">
    <a href="{{ route('public.kos.index') }}" class="m-btn m-btn-secondary mb-3"><iconify-icon icon="lucide:arrow-left"></iconify-icon> Kembali</a>
    <div class="m-shell-card"><article class="m-card m-card-body">
        @if($kos->fotoKos->isNotEmpty())<div class="row g-2 mb-4" aria-label="Galeri foto {{ $kos->nama }}">@foreach($kos->fotoKos as $foto)<div class="col-6 col-md-3"><img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($foto->path) }}" alt="Foto {{ $kos->nama }}" class="w-100 rounded-3" style="height:160px;object-fit:cover"></div>@endforeach</div>@endif
        <div class="m-label">{{ $kos->wilayah->nama }}</div><h1 class="m-page-title mt-2">{{ $kos->nama }}</h1><p class="m-page-subtitle mt-2">{{ $kos->alamat }}</p><p class="mt-4">{{ $kos->deskripsi ?: 'Belum ada deskripsi.' }}</p>
        <h2 class="m-card-title mt-4">Fasilitas</h2><div class="d-flex flex-wrap gap-2 mt-2">@forelse($kos->fasilitas as $item)<span class="m-status m-status-info"><iconify-icon icon="lucide:badge-check"></iconify-icon>{{ $item->nama }}</span>@empty<span class="text-muted">Belum ada fasilitas.</span>@endforelse</div>
        <h2 class="m-card-title mt-4">Kamar tersedia</h2><div class="m-table-wrap mt-2"><table class="m-table"><thead><tr><th>Kode</th><th>Harga</th><th>Status</th></tr></thead><tbody>@forelse($kos->kamar as $item)<tr><td>{{ $item->kode }}</td><td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td><td><span class="m-status m-status-success"><iconify-icon icon="lucide:circle-check"></iconify-icon>Tersedia</span></td></tr>@empty<tr><td colspan="3" class="m-empty">Belum ada kamar tersedia.</td></tr>@endforelse</tbody></table></div>
    </article></div>
</div>
@endsection