@extends('layouts.auth')
@section('title', 'Daftar penyewa')
@section('heading', 'Daftar penyewa')
@section('content')
<form class="auth-form" method="POST" action="{{ route('register.store') }}">@csrf
    <div class="auth-field"><label for="name">Nama lengkap</label><input id="name" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required autocomplete="name" maxlength="255"></div>
    <div class="auth-field"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email Anda" required autocomplete="email"></div>
    <div class="auth-field"><label for="password">Password</label><input id="password" type="password" name="password" placeholder="Buat password" required autocomplete="new-password" minlength="8" aria-describedby="password-hint"><p id="password-hint">Gunakan minimal 8 karakter.</p></div>
    <div class="auth-field"><label for="password_confirmation">Ulangi password</label><input id="password_confirmation" type="password" name="password_confirmation" placeholder="Masukkan kembali password" required autocomplete="new-password" minlength="8"></div>
    <button class="auth-submit" type="submit">Buat akun</button>
</form>
@endsection
@section('after-card')
<p class="auth-prompt">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
@endsection
