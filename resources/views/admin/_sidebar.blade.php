<a class="m-brand" href="{{ route('admin.dashboard') }}"><iconify-icon icon="lucide:building-2"></iconify-icon>SediaKos</a>
<div class="m-sidebar-label">Pengelolaan kos</div>
<nav aria-label="Menu owner">
@foreach([
    ['dashboard', 'layout-dashboard', 'Ringkasan'],
    ['kos.index', 'building-2', 'Cabang kos'],
    ['kamar.index', 'door-open', 'Kamar'],
    ['fasilitas.index', 'wifi', 'Fasilitas'],
    ['wilayah.index', 'map-pin', 'Lokasi'],
    ['penghuni.index', 'users', 'Penghuni'],
    ['penyewaan.index', 'key-round', 'Penyewaan'],
    ['verifikasi.index', 'badge-check', 'Verifikasi pembayaran'],
    ['tagihan.index', 'receipt', 'Tagihan'],
    ['laporan.index', 'chart-no-axes-combined', 'Laporan'],
    ['rekening.edit', 'landmark', 'Rekening pembayaran'],
] as [$path, $icon, $label])
    <a class="m-nav-link {{ request()->routeIs('admin.'.explode('.', $path)[0].'*') ? 'active' : '' }}" href="{{ route('admin.'.$path) }}"><iconify-icon icon="lucide:{{ $icon }}" aria-hidden="true"></iconify-icon>{{ $label }}</a>
@endforeach
</nav>
<div class="admin-account"><strong>{{ auth()->user()->name }}</strong><span class="text-muted">{{ auth()->user()->email }}</span></div>
