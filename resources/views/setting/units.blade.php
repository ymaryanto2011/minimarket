@extends('layouts.app')
@section('title', 'Satuan Barang')
@section('page_title', 'Pengaturan Satuan')
@section('page_subtitle', 'Kelola master satuan/unit barang')

@section('content')
{{-- Flash messages handled globally by toast system in layouts/app.blade.php --}}

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Tambah -->
    <div class="card">
        <h3 class="text-lg font-bold mb-4">Tambah Satuan</h3>
        <form method="POST" action="{{ route('setting.units.store') }}">
            @csrf
            @error('name') <p class="text-red-500 text-sm mb-2">{{ $message }}</p> @enderror
            <div class="flex gap-2">
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama satuan (pcs, kg, liter...)" class="input-field flex-1" required>
                <button type="submit" class="btn-primary whitespace-nowrap">+ Tambah</button>
            </div>
        </form>
    </div>

    <!-- Daftar Satuan -->
    <div class="lg:col-span-2 card">
        <h3 class="text-lg font-bold mb-4">Daftar Satuan ({{ $units->count() }})</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b bg-gray-50">
                        <th class="text-left py-2 px-3">#</th>
                        <th class="text-left py-2 px-3">Nama Satuan</th>
                        <th class="text-center py-2 px-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($units as $i => $unit)
                    <tr class="border-b hover:bg-gray-50" x-data="{ editing: false }">
                        <td class="py-2 px-3 text-gray-500">{{ $i + 1 }}</td>
                        <td class="py-2 px-3">
                            <span x-show="!editing">{{ $unit->name }}</span>
                            <form x-show="editing" method="POST" action="{{ route('setting.units.update', $unit) }}" class="flex gap-2">
                                @csrf @method('PUT')
                                <input type="text" name="name" value="{{ $unit->name }}" class="input-field py-1 flex-1" required>
                                <button type="submit" class="btn-primary text-xs py-1 px-2">Simpan</button>
                                <button type="button" @click="editing = false" class="btn-secondary text-xs py-1 px-2">Batal</button>
                            </form>
                        </td>
                        <td class="py-2 px-3 text-center">
                            <button @click="editing = !editing" x-show="!editing" class="btn-primary text-xs py-1 px-2">Edit</button>
                            <form method="POST" action="{{ route('setting.units.destroy', $unit) }}" class="inline" x-show="!editing"
                                onsubmit="return confirm('Hapus satuan {{ $unit->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger text-xs py-1 px-2">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center py-6 text-gray-500">Belum ada satuan. Tambahkan satuan di atas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection