@extends('layouts.app')

@section('title', 'Stok Masuk')
@section('page_title', 'Stok Masuk')
@section('page_subtitle', 'Catat penambahan stok barang')

@section('content')
<div class="card max-w-4xl">
    <div class="card-header">Input Stok Masuk</div>
    <p class="text-sm text-gray-600 mb-4">Halaman placeholder untuk pencatatan stok masuk dari supplier.</p>

    <form class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Tanggal</label>
            <input type="date" class="input-field">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Supplier</label>
            <input type="text" class="input-field" placeholder="Nama supplier">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Barang</label>
            <input type="text" class="input-field" placeholder="Pilih barang">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Jumlah Masuk</label>
            <input type="number" class="input-field" placeholder="0">
        </div>
    </form>

    <div class="flex gap-2 justify-end mt-4">
        <a href="{{ route('stock.index') }}" class="btn-secondary">Kembali</a>
        <button type="button" class="btn-primary">Simpan</button>
    </div>
</div>
@endsection