@extends('layouts.app')
@section('content')
<main class="m-main mx-auto" style="max-width:800px">
    <article class="m-card m-card-body">
        <h1 class="m-page-title mb-3">Masuk dengan akun penyewa</h1>
        <p>Anda sedang masuk sebagai <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->email }}).
            Akun ini digunakan untuk pengelolaan kos. Untuk memesan kamar, gunakan akun penyewa.</p>
        <p>Keluar terlebih dahulu, lalu masuk menggunakan akun penyewa atau daftar akun baru. Anda juga bisa membuka website di jendela samaran untuk memakai akun penyewa sambil tetap membuka dashboard owner.</p>
        <div class="d-flex flex-wrap gap-2">
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="m-btn m-btn-primary" type="submit">Keluar dan ganti akun</button></form>
            <a class="m-btn m-btn-secondary" href="{{ route('public.kos.show', $kamar->kos_id) }}">Kembali ke detail kos</a>
        </div>
    </article>
</main>
@endsection
