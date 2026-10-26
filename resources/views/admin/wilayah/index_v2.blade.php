@extends('layouts.app')
@section('content')
<div class="m-shell"><aside class="m-sidebar">@include('admin._sidebar')</aside><section class="m-main">
    <div class="m-topbar"><div><h1 class="m-page-title">Wilayah</h1><p class="m-page-subtitle">Kelola area pencarian kos.</p></div><a href="{{ route('admin.wilayah.create') }}" class="m-btn m-btn-primary"><iconify-icon icon="lucide:plus"></iconify-icon> Tambah wilayah</a></div>
    <div class="m-shell-card"><div class="m-card m-card-body m-table-wrap"><table class="m-table"><thead><tr><th>Nama</th><th>Jumlah kos</th><th>Aksi</th></tr></thead><tbody>@forelse($wilayah as $item)<tr><td>{{ $item->nama }}</td><td>{{ $item->kos_count }}</td><td><a href="{{ route('admin.wilayah.edit', $item) }}" class="m-btn m-btn-secondary">Edit</a><form class="d-inline" method="POST" action="{{ route('admin.wilayah.destroy', $item) }}" onsubmit="return confirm('Hapus wilayah ini?')">@csrf @method('DELETE')<button class="m-btn m-btn-danger">Hapus</button></form></td></tr>@empty<tr><td colspan="3" class="m-empty">Belum ada wilayah.</td></tr>@endforelse</tbody></table></div></div>{{ $wilayah->links() }}
</section></div>
@endsection