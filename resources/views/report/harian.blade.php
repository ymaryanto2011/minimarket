@extends('layouts.app')

@section('title', 'Laporan Harian')
@section('page_title', 'Laporan Penjualan Harian')
@section('page_subtitle', 'Ringkasan performa penjualan per hari')

@section('content')
<div class="space-y-4">
    <div class="card">
        <div class="card-header">Filter Laporan Harian</div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Tanggal</label>
                <input type="date" class="input-field" value="2026-05-02">
            </div>
            <div class="flex items-end">
                <button class="btn-primary w-full">Tampilkan</button>
            </div>
            <div class="flex items-end">
                <a href="{{ route('report.index') }}" class="btn-secondary w-full text-center">Kembali ke Ringkasan</a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Hasil Laporan Harian</div>
        <p class="text-sm text-gray-600">Placeholder data transaksi harian dan rekap omzet.</p>
    </div>
</div>
@endsection