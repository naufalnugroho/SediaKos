@extends('layouts.app')
@section('content')
<div class="m-shell"><aside class="m-sidebar">@include('admin._sidebar')</aside><main class="m-main">
<div class="m-topbar"><h1 class="m-page-title">{{ $wilayah->exists ? 'Edit' : 'Tambah' }} lokasi</h1><a class="m-btn m-btn-secondary" href="{{ route('admin.wilayah.index') }}">Kembali</a></div>
<form class="m-card m-card-body" method="POST" action="{{ $wilayah->exists ? route('admin.wilayah.update', $wilayah) : route('admin.wilayah.store') }}">@csrf @if($wilayah->exists) @method('PUT') @endif
<label class="d-block mb-3">Nama lokasi<input class="m-form-control mt-2" name="nama" value="{{ old('nama', $wilayah->nama) }}" required maxlength="100"></label><button class="m-btn m-btn-primary">Simpan lokasi</button>
</form></main></div>
@endsection
