@extends('layouts.app')

@section('title', 'Laporan Bulanan')
@section('page_title', 'Laporan Penjualan Bulanan')
@section('page_subtitle', 'Analisis tren penjualan tiap bulan')

@section('content')
<div class="space-y-4">
    <div class="card">
        <div class="card-header">Filter Laporan Bulanan</div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Bulan</label>
                <input type="month" class="input-field" value="2026-05">
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
        <div class="card-header">Hasil Laporan Bulanan</div>
        <p class="text-sm text-gray-600">Placeholder grafik tren penjualan bulanan dan perbandingan omzet.</p>
    </div>
</div>
@endsection