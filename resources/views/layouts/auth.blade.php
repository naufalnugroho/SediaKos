<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Masuk') — SediaKos</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/landing.css', 'resources/js/app.js'])
</head>
<body class="auth-page auth-centered {{ request()->routeIs('register') ? 'auth-register' : '' }}">
    <header class="auth-header"><a class="auth-brand" href="{{ route('home') }}" aria-label="SediaKos, kembali ke beranda"><svg viewBox="0 0 28 14" fill="none" aria-hidden="true"><path d="M1.5 12.5L8.5 3L15.5 12.5M13 12.5L19.5 3L26.5 12.5" stroke="#243CE6" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"/></svg><span>SediaKos</span></a><a class="auth-back" href="{{ route('home') }}">Kembali ke beranda</a></header>
    <main class="auth-main">
        @hasSection('heading')<h1 class="auth-heading">@yield('heading')</h1>@endif
        <div class="auth-card">
        @if(session('success'))<div class="auth-notice" role="status">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="auth-error" role="alert"><strong>Periksa kembali data Anda.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        @yield('content')
        </div>
        @yield('after-card')
    </main>
    <footer class="auth-footer">© {{ date('Y') }} SediaKos <span>Jatiwaringin · Kalibata · Margonda</span></footer>
</body>
</html>
