@extends('layouts.app')

@section('title', 'Preview PDF Penawaran')
@section('page_title', 'Preview PDF Penawaran')
@section('page_subtitle', 'Simulasi tampilan dokumen sebelum diunduh')

@section('content')
<div class="space-y-4">
    <div class="card">
        <div class="card-header">Preview Penawaran #{{ $id ?? '-' }}</div>
        <p class="text-sm text-gray-600 mb-4">Halaman placeholder untuk preview PDF. Integrasi generator PDF dapat ditambahkan di tahap berikutnya.</p>

        <div class="rounded-lg border border-gray-200 p-6 bg-white">
            <h3 class="text-lg font-semibold">Dokumen Penawaran Barang</h3>
            <p class="text-sm text-gray-600 mt-1">Nomor: PW-20260502-0001</p>
            <p class="text-sm text-gray-600">Kepada: PT ABC Distributor</p>
            <hr class="my-4">
            <p class="text-sm text-gray-700">Area isi PDF placeholder.</p>
        </div>
    </div>

    <div class="flex gap-2 justify-end">
        <a href="{{ route('quotation.index') }}" class="btn-secondary">Kembali</a>
        <button type="button" class="btn-primary">Unduh PDF</button>
    </div>
</div>
@endsection