@extends('layouts.app')
@section('content')
<div class="m-shell"><aside class="m-sidebar">@include('admin._sidebar')</aside><main class="m-main">
<div class="m-topbar"><h1 class="m-page-title">{{ $fasilitas->exists ? 'Edit' : 'Tambah' }} fasilitas</h1><a class="m-btn m-btn-secondary" href="{{ route('admin.fasilitas.index') }}">Kembali</a></div>
<form class="m-card m-card-body" method="POST" action="{{ $fasilitas->exists ? route('admin.fasilitas.update', $fasilitas) : route('admin.fasilitas.store') }}">@csrf @if($fasilitas->exists) @method('PUT') @endif
<label class="d-block mb-3">Nama fasilitas<input class="m-form-control mt-2" name="nama" value="{{ old('nama', $fasilitas->nama) }}" required maxlength="100"></label><button class="m-btn m-btn-primary">Simpan fasilitas</button>
</form></main></div>
@endsection
