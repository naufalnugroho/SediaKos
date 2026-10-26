@extends('layouts.app')
@section('content')
<div class="m-shell"><aside class="m-sidebar">@include('admin._sidebar')</aside><main class="m-main">
<div class="m-topbar"><div><h1 class="m-page-title">{{ $kos->exists ? 'Edit' : 'Tambah' }} cabang kos</h1><p class="m-page-subtitle">Informasi ini ditampilkan kepada calon penyewa.</p></div><a class="m-btn m-btn-secondary" href="{{ route('admin.kos.index') }}">Kembali</a></div>
<form method="POST" enctype="multipart/form-data" action="{{ $kos->exists ? route('admin.kos.update', $kos) : route('admin.kos.store') }}" class="m-card m-card-body">@csrf @if($kos->exists) @method('PUT') @endif
<div class="row g-3 mb-3">
@foreach(['nama' => 'Nama cabang', 'slug' => 'Slug (opsional)', 'kontak' => 'Nomor kontak'] as $field => $label)
<div class="col-md-6"><label class="d-block">{{ $label }}<input name="{{ $field }}" value="{{ old($field, $kos->$field) }}" class="m-form-control mt-2" @required($field === 'nama')></label></div>
@endforeach
<div class="col-md-6"><label class="d-block">Lokasi<select name="wilayah_id" class="m-form-control mt-2" required><option value="">Pilih lokasi</option>@foreach($wilayah as $item)<option value="{{ $item->id }}" @selected(old('wilayah_id', $kos->wilayah_id) == $item->id)>{{ $item->nama }}</option>@endforeach</select></label></div>
</div>
@foreach(['alamat' => 'Alamat lengkap', 'deskripsi' => 'Deskripsi', 'aturan' => 'Aturan kos'] as $field => $label)
<label class="d-block mb-3">{{ $label }}<textarea name="{{ $field }}" class="m-form-control mt-2" rows="3" @required($field === 'alamat')>{{ old($field, $kos->$field) }}</textarea></label>
@endforeach
<fieldset class="mb-3"><legend class="fs-6">Fasilitas</legend><div class="row g-2">@forelse($fasilitas as $item)<div class="col-md-4"><label><input type="checkbox" name="fasilitas[]" value="{{ $item->id }}" @checked(in_array($item->id, old('fasilitas', $kos->fasilitas->modelKeys())))> {{ $item->nama }}</label></div>@empty<p class="text-muted">Tambahkan fasilitas melalui menu Fasilitas terlebih dahulu.</p>@endforelse</div></fieldset>
<label class="d-block mb-3">Foto cabang<input type="file" name="photos[]" class="m-form-control mt-2" accept="image/jpeg,image/png,image/webp" multiple><small class="text-muted">Maksimal 10 foto per upload, JPG/PNG/WEBP, 5 MB per foto.</small></label>
<label class="d-block mb-4"><input type="hidden" name="status" value="0"><input type="checkbox" name="status" value="1" @checked(old('status', $kos->exists ? $kos->status : true))> Tampilkan cabang di website</label>
<div><button class="m-btn m-btn-primary">Simpan cabang</button></div>
</form></main></div>
@endsection
