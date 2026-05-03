@extends('layouts.app')

@section('title', 'Buat Penawaran')
@section('page_title', 'Buat Penawaran Baru')
@section('page_subtitle', 'Susun penawaran harga untuk customer/supplier')

@section('content')
<div class="card max-w-5xl">
    <div class="card-header">Form Penawaran Baru</div>
    <p class="text-sm text-gray-600 mb-4">Halaman placeholder. Nantinya daftar item dan perhitungan total diambil dari data barang.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Nomor Penawaran</label>
            <input type="text" class="input-field" value="PW-20260502-0005">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Kepada</label>
            <input type="text" class="input-field" placeholder="Nama perusahaan/toko">
        </div>
    </div>

    <div class="mt-4 rounded-lg border border-dashed border-gray-300 p-4 text-sm text-gray-600">
        Placeholder tabel item penawaran.
    </div>

    <div class="flex gap-2 justify-end mt-4">
        <a href="{{ route('quotation.index') }}" class="btn-secondary">Kembali</a>
        <button type="button" class="btn-primary">Simpan Draft</button>
    </div>
</div>
@endsection