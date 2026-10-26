@extends('layouts.app')

@section('content')
<div class="m-main mx-auto" style="max-width: 1440px;">
    <section class="m-hero mb-4">
        <div class="m-label mb-3">Pencarian kos yang lebih sederhana</div>
        <h1>Temukan tempat tinggal yang terasa seperti rumah.</h1>
        <p class="mt-3 mb-4">Kelola dan temukan informasi kos berdasarkan wilayah, fasilitas, dan kebutuhan Anda.</p>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('public.kos.index') }}" class="m-btn m-btn-primary"><iconify-icon icon="lucide:search" aria-hidden="true"></iconify-icon> Mulai cari kos</a>
            <a href="{{ route('login') }}" class="m-btn m-btn-secondary"><iconify-icon icon="lucide:layout-dashboard" aria-hidden="true"></iconify-icon> Area admin</a>
        </div>
    </section>
    <div class="m-kpi-grid">
        <div class="m-shell-card"><div class="m-card m-card-body"><div class="m-label">Wilayah</div><div class="m-kpi-value">{{ $jumlahWilayah }}</div><div class="m-page-subtitle">Area yang tersedia</div></div></div>
        <div class="m-shell-card"><div class="m-card m-card-body"><div class="m-label">Kos aktif</div><div class="m-kpi-value">{{ $jumlahKosAktif }}</div><div class="m-page-subtitle">Informasi terverifikasi</div></div></div>
        <div class="m-shell-card"><div class="m-card m-card-body"><div class="m-label">Fasilitas</div><div class="m-kpi-value">{{ $jumlahFasilitas }}</div><div class="m-page-subtitle">Pilihan untuk penghuni</div></div></div>
        <div class="m-shell-card"><div class="m-card m-card-body"><div class="m-label">Untuk siapa</div><div class="m-kpi-value"><span class="m-status m-status-info"><iconify-icon icon="lucide:users"></iconify-icon> Semua</span></div><div class="m-page-subtitle">Pengunjung dan admin</div></div></div>
    </div>
    <section class="mt-5">
        <div class="d-flex justify-content-between align-items-end gap-3 mb-3">
            <div><div class="m-label">Pilihan terbaru</div><h2 class="m-page-title mt-2">Kos yang bisa Anda lihat</h2></div>
            <a href="{{ route('public.kos.index') }}" class="m-btn m-btn-secondary">Lihat semua</a>
        </div>
        <div class="row g-3">
            @forelse($kosUnggulan as $item)
                <div class="col-md-6 col-xl-4">
                    <div class="m-shell-card h-100"><article class="m-card m-card-body h-100">
                        <div class="m-label">{{ $item->wilayah->nama }}</div>
                        <h3 class="m-card-title mt-2">{{ $item->nama }}</h3>
                        <p class="m-page-subtitle mt-2">{{ $item->alamat }}</p>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <span class="m-status m-status-{{ $item->kamar_tersedia_count ? 'success' : 'warning' }}"><iconify-icon icon="lucide:{{ $item->kamar_tersedia_count ? 'circle-check' : 'circle-alert' }}"></iconify-icon>{{ $item->kamar_tersedia_count }} kamar tersedia</span>
                            <a href="{{ route('public.kos.show', $item) }}" class="m-btn m-btn-secondary">Detail</a>
                        </div>
                    </article></div>
                </div>
            @empty
                <div class="col-12"><div class="m-shell-card"><div class="m-card m-card-body m-empty">Belum ada kos aktif.</div></div></div>
            @endforelse
        </div>
    </section>
</div>
@endsection