@extends('layouts.app')

@section('title', 'Penyesuaian Stok')
@section('page_title', 'Penyesuaian Stok')
@section('page_subtitle', 'Koreksi stok fisik dengan sistem')

@section('content')
<div class="card max-w-4xl">
    <div class="card-header">Form Penyesuaian Stok</div>
    <p class="text-sm text-gray-600 mb-4">Placeholder untuk proses adjustment stok jika terdapat selisih opname.</p>

    <form class="space-y-4">
        <div>
            <label class="block text-sm font-medium mb-1">Barang</label>
            <input type="text" class="input-field" placeholder="Cari barang">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Stok Sistem</label>
                <input type="number" class="input-field" value="145">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Stok Fisik</label>
                <input type="number" class="input-field" value="142">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Selisih</label>
                <input type="number" class="input-field" value="-3" readonly>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Catatan</label>
            <textarea class="input-field" rows="3" placeholder="Alasan penyesuaian"></textarea>
        </div>
    </form>

    <div class="flex gap-2 justify-end mt-4">
        <a href="{{ route('stock.index') }}" class="btn-secondary">Kembali</a>
        <button type="button" class="btn-primary">Proses Penyesuaian</button>
    </div>
</div>
@endsection