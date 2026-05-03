@extends('layouts.app')

@section('title', 'Edit Penawaran')
@section('page_title', 'Edit Penawaran')
@section('page_subtitle', 'Ubah detail penawaran yang sudah dibuat')

@section('content')
<div class="card max-w-5xl">
    <div class="card-header">Edit Penawaran</div>
    <p class="text-sm text-gray-600 mb-4">Placeholder edit untuk penawaran dengan ID: <span class="font-semibold">{{ $id ?? '-' }}</span></p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Nomor Penawaran</label>
            <input type="text" class="input-field" value="PW-20260502-0001">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Kepada</label>
            <input type="text" class="input-field" value="PT ABC Distributor">
        </div>
    </div>

    <div class="mt-4 rounded-lg border border-dashed border-gray-300 p-4 text-sm text-gray-600">
        Placeholder tabel item untuk proses edit penawaran.
    </div>

    <div class="flex gap-2 justify-end mt-4">
        <a href="{{ route('quotation.index') }}" class="btn-secondary">Kembali</a>
        <button type="button" class="btn-primary">Update Penawaran</button>
    </div>
</div>
@endsection