@extends('layouts.auth')
@section('title', 'Masuk akun')
@section('heading', 'Selamat datang kembali')
@section('content')
<form class="auth-form" method="POST" action="{{ route('login.store') }}">@csrf
    <div class="auth-field"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email Anda" autocomplete="email" required autofocus @if($errors->has('email')) aria-invalid="true" @endif></div>
    <div class="auth-field"><label for="password">Password</label><div class="auth-password"><input id="password" type="password" name="password" placeholder="Masukkan password Anda" autocomplete="current-password" required><button type="button" data-password-toggle aria-controls="password" aria-label="Tampilkan password" aria-pressed="false"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button></div></div>
    <label class="auth-remember"><input type="checkbox" name="remember" value="1" @checked(old('remember'))> Ingat saya di perangkat ini</label>
    <button class="auth-submit" type="submit">Masuk</button>
</form>
<p class="auth-helper">Penyewa dan owner masuk melalui halaman yang sama. Anda akan diarahkan sesuai akun Anda.</p>
@endsection
@section('after-card')
<p class="auth-prompt">Belum punya akun? <a href="{{ route('register') }}">Daftar penyewa</a></p>
@endsection
