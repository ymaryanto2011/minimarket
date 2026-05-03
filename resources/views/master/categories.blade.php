@extends('layouts.app')
@section('title', 'Kategori Barang')
@section('page_title', 'Master Kategori Barang')
@section('page_subtitle', 'Kelola kategori produk')

@section('content')
{{-- Flash messages handled globally by toast system in layouts/app.blade.php --}}

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Tambah -->
    <div class="card">
        <h3 class="text-lg font-bold mb-4">Tambah Kategori</h3>
        <form method="POST" action="{{ route('categories.store') }}">
            @csrf
            @error('name') <p class="text-red-500 text-sm mb-2">{{ $message }}</p> @enderror
            <div class="space-y-3">
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama kategori..." class="input-field w-full" required>
                <button type="submit" class="btn-primary w-full">+ Tambah Kategori</button>
            </div>
        </form>
    </div>

    <!-- Daftar Kategori -->
    <div class="lg:col-span-2 card">
        <h3 class="text-lg font-bold mb-4">Daftar Kategori ({{ $categories->count() }})</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b bg-gray-50">
                        <th class="text-left py-2 px-3">#</th>
                        <th class="text-left py-2 px-3">Nama Kategori</th>
                        <th class="text-right py-2 px-3">Jumlah Produk</th>
                        <th class="text-center py-2 px-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $i => $category)
                    <tr class="border-b hover:bg-gray-50" x-data="{ editing: false }">
                        <td class="py-2 px-3 text-gray-500">{{ $i + 1 }}</td>
                        <td class="py-2 px-3">
                            <span x-show="!editing" class="font-medium">{{ $category->name }}</span>
                            <form x-show="editing" method="POST" action="{{ route('categories.update', $category) }}" class="flex gap-2">
                                @csrf @method('PUT')
                                <input type="text" name="name" value="{{ $category->name }}" class="input-field py-1 flex-1" required>
                                <button type="submit" class="btn-primary text-xs py-1 px-2">Simpan</button>
                                <button type="button" @click="editing = false" class="btn-secondary text-xs py-1 px-2">Batal</button>
                            </form>
                        </td>
                        <td class="py-2 px-3 text-right">
                            <a href="{{ route('master.index', ['category' => $category->id]) }}" class="badge-info hover:underline">
                                {{ $category->products_count }} produk
                            </a>
                        </td>
                        <td class="py-2 px-3 text-center">
                            <button @click="editing = !editing" x-show="!editing" class="btn-primary text-xs py-1 px-2">Edit</button>
                            @if($category->products_count == 0)
                            <form method="POST" action="{{ route('categories.destroy', $category) }}" class="inline" x-show="!editing"
                                onsubmit="return confirm('Hapus kategori {{ $category->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger text-xs py-1 px-2">Hapus</button>
                            </form>
                            @else
                            <span class="text-xs text-gray-400 ml-1" x-show="!editing">Ada produk</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-6 text-gray-500">Belum ada kategori. Tambahkan kategori di sebelah kiri.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection