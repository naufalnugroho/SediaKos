@extends('layouts.app')
@section('content')
<div class="m-login"><div class="m-login-card m-shell-card"><div class="m-card m-card-body">
    <div class="m-brand mb-4"><iconify-icon icon="lucide:loader-circle" aria-hidden="true"></iconify-icon> KosKita</div>
    <div class="m-label">Area aman</div><h1 class="m-page-title mt-2 mb-2">Login Admin</h1><p class="m-page-subtitle mb-4">Kelola data kos, wilayah, dan fasilitas dari satu tempat.</p>
    <form method="POST" action="{{ route('login.store') }}">@csrf
        <div class="mb-3"><label class="m-label d-block mb-2" for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" class="m-form-control" required autofocus></div>
        <div class="mb-3"><label class="m-label d-block mb-2" for="password">Password</label><input id="password" type="password" name="password" class="m-form-control" required></div>
        <label class="d-flex align-items-center gap-2 mb-4 text-muted small"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
        <button class="m-btn m-btn-primary w-100" type="submit"><iconify-icon icon="lucide:log-in" aria-hidden="true"></iconify-icon> Masuk</button>
    </form>
</div></div></div>
@endsection