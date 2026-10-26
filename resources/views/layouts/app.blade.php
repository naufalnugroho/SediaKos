<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'SediaKos' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite('resources/css/landing.css')
    @vite('resources/css/dashboard.css')
    @vite('resources/css/workspace.css')
</head>
<body class="{{ request()->routeIs('admin.*') ? 'admin-ui' : '' }}">
    @if(request()->routeIs('admin.*'))
        <header class="admin-header"><div class="admin-header-start"><button type="button" class="m-icon-btn admin-mobile-menu" data-sidebar-toggle aria-label="Buka menu pengelolaan" aria-expanded="false"><iconify-icon icon="lucide:panel-left"></iconify-icon></button><a href="{{ route('admin.dashboard') }}"><strong>SediaKos</strong></a><span class="admin-header-label text-muted">/ Workspace owner</span></div><div class="admin-header-actions"><a href="{{ route('home') }}">Lihat website</a><form method="POST" action="{{ route('logout') }}">@csrf<button class="m-btn m-btn-secondary">Keluar</button></form></div></header>
        <button type="button" class="admin-overlay" data-sidebar-close aria-label="Tutup menu"></button>
    @else
        @include('partials.navbar')
    @endif
    @include('partials.alerts')
    @yield('content')
    @unless(request()->routeIs('admin.*', 'home', 'home.alias'))
        <footer class="public-page-footer"><a href="{{ route('home') }}"><strong>SediaKos</strong> · Tempat baru untuk keseharian Anda.</a><span>© {{ date('Y') }} SediaKos</span></footer>
    @endunless
</body>
</html>
