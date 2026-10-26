@extends('layouts.app')
@section('content')
<div class="m-shell"><aside class="m-sidebar">@include('admin._sidebar')</aside><main class="m-main">
<div class="m-topbar"><div><h1 class="m-page-title">Rekening pembayaran</h1><p class="m-page-subtitle">Rekening tujuan transfer yang dilihat penyewa pada halaman pembayaran.</p></div></div>
<form class="m-card m-card-body form-narrow" method="POST" action="{{ route('admin.rekening.update') }}">@csrf @method('PUT')
<div class="ui-note mb-4"><iconify-icon icon="lucide:info"></iconify-icon><p>Gunakan rekening milik pengelola. Periksa kembali nomor dan nama pemilik sebelum menyimpan perubahan.</p></div>
@foreach(['bank' => 'Nama bank', 'rekening' => 'Nomor rekening', 'pemilik' => 'Nama pemilik rekening'] as $field => $label)
<label class="ui-field">{{ $label }}<input class="m-form-control" name="{{ $field }}" value="{{ old($field, $rekening[$field] ?? '') }}" required @if($field === 'rekening') inputmode="numeric" @endif></label>
@endforeach
<button class="m-btn m-btn-primary">Simpan rekening</button>
</form></main></div>
@endsection
