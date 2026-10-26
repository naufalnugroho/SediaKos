<nav class="landing-nav" aria-label="Navigasi utama"><div class="landing-nav-inner">
    <a class="landing-brand" href="{{ route('home') }}"><svg viewBox="0 0 28 14" fill="none" aria-hidden="true"><path d="M1.5 12.5L8.5 3L15.5 12.5M13 12.5L19.5 3L26.5 12.5" stroke="#243CE6" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"/></svg>SediaKos</a>
    <div class="landing-nav-links"><a href="{{ route('home') }}">Beranda</a><a href="{{ route('home') }}#lokasi">Lokasi kos</a><a href="{{ route('home') }}#cara-sewa">Cara sewa</a></div>
    <div class="landing-nav-actions">@auth
        <a href="{{ route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'penyewa.index') }}">{{ auth()->user()->isAdmin() ? 'Dashboard owner' : 'Sewa saya' }}</a>
        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Keluar</button></form>
    @else<a href="{{ route('login') }}">Masuk</a><a href="{{ route('register') }}">Daftar</a>@endauth</div>
</div></nav>
