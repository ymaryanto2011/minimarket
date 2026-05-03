@extends("layouts.app")
@section("title", "Master Barang")
@section("page_title", "Master Barang")
@section("page_subtitle", "Kelola data produk")

@section("content")
{{-- Flash messages handled globally by toast system in layouts/app.blade.php --}}

<div class="card mb-4">
    <form method="GET" class="flex gap-3 flex-wrap">
        <input type="text" name="search" value="{{ request("search") }}" placeholder="Cari nama/kode/barcode..." class="input-field flex-1 min-w-48">
        <select name="category" class="input-field w-48">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request("category") == $cat->id ? "selected" : "" }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary">Cari</button>
        <a href="{{ route("master.create") }}" class="btn-success">+ Tambah Produk</a>
    </form>
</div>

<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="text-left py-2 px-3">Kode</th>
                    <th class="text-left py-2 px-3">Nama Produk</th>
                    <th class="text-left py-2 px-3">Kategori</th>
                    <th class="text-right py-2 px-3">Harga Eceran</th>
                    <th class="text-left py-2 px-3">Satuan</th>
                    <th class="text-right py-2 px-3">Stok</th>
                    <th class="text-center py-2 px-3">Status</th>
                    <th class="text-center py-2 px-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-2 px-3 font-mono text-xs">{{ $product->code }}</td>
                    <td class="py-2 px-3 font-medium">{{ $product->name }}</td>
                    <td class="py-2 px-3 text-gray-600">{{ $product->category->name }}</td>
                    <td class="py-2 px-3 text-right">Rp {{ number_format($product->retail_price, 0, ",", ".") }}</td>
                    <td class="py-2 px-3">
                        <span class="text-xs font-medium text-gray-700">{{ $product->unit }}</span>
                        @if($product->unitConversions->count() > 0)
                        <div class="flex flex-wrap gap-1 mt-0.5">
                            @foreach($product->unitConversions as $conv)
                            <span class="inline-flex items-center text-xs bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded" title="1 {{ $conv->unit_name }} = {{ $conv->conversion_qty }} {{ $product->unit }}, jual: Rp {{ number_format($conv->sell_price, 0, ',', '.') }}">
                                {{ $conv->unit_name }}
                            </span>
                            @endforeach
                        </div>
                        @endif
                    </td>
                    <td class="py-2 px-3 text-right {{ $product->isLowStock() ? "text-red-600 font-bold" : "" }}">
                        {{ $product->stock }} {{ $product->unit }}
                        @if($product->isLowStock()) <span class="text-xs">(min: {{ $product->min_stock }})</span> @endif
                    </td>
                    <td class="py-2 px-3 text-center">
                        @if($product->is_active)
                        <span class="badge-success">Aktif</span>
                        @else
                        <span class="badge-danger">Nonaktif</span>
                        @endif
                    </td>
                    <td class="py-2 px-3 text-center">
                        <a href="{{ route("master.edit", $product) }}" class="btn-primary text-xs py-1 px-2">Edit</a>
                        <form method="POST" action="{{ route("master.destroy", $product) }}" class="inline" onsubmit="return confirm(" Nonaktifkan produk ini?")">
                            @csrf @method("DELETE")
                            <button type="submit" class="btn-danger text-xs py-1 px-2">Nonaktif</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-6 text-gray-500">Tidak ada produk ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $products->links() }}</div>
</div>
@endsection