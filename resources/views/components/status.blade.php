@props(['value'])
@php
    $labels = ['menunggu_pembayaran' => 'Menunggu pembayaran', 'menunggu_verifikasi' => 'Menunggu verifikasi', 'aktif' => 'Aktif', 'selesai' => 'Selesai', 'batal' => 'Dibatalkan', 'dibatalkan' => 'Dibatalkan', 'kedaluwarsa' => 'Kedaluwarsa', 'valid' => 'Pembayaran diterima', 'ditolak' => 'Bukti ditolak', 'lunas' => 'Lunas', 'dibayar_sebagian' => 'Dibayar sebagian', 'belum_dibayar' => 'Belum dibayar', 'terlambat' => 'Terlambat', 'tersedia' => 'Tersedia', 'dipesan' => 'Dipesan', 'terisi' => 'Terisi', 'pemeliharaan' => 'Pemeliharaan'];
    $tone = match($value) { 'aktif', 'valid', 'lunas', 'tersedia' => 'success', 'menunggu_pembayaran', 'menunggu_verifikasi', 'dibayar_sebagian', 'dipesan' => 'warning', 'ditolak', 'terlambat' => 'danger', default => 'neutral' };
@endphp
<span {{ $attributes->class(['ui-status', 'ui-status-'.$tone]) }}><span aria-hidden="true"></span>{{ $labels[$value] ?? ucfirst(str_replace('_', ' ', $value)) }}</span>
