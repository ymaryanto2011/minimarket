@extends("layouts.app")
@section("title", "Tambah Barang")
@section("page_title", "Tambah Barang")
@section("page_subtitle", "Form input data barang baru")

@section("content")
<div class="max-w-3xl"
    x-data="{
        conversions: @json(old('conversions', [])),
        addRow() { this.conversions.push({ unit_name:'', conversion_qty:'', sell_price:'', buy_price:'' }); },
        removeRow(i) { this.conversions.splice(i, 1); }
     }">

    <form method="POST" action="{{ route("master.store") }}">
        @csrf
        @if($errors->any())
        <div class="alert-danger mb-4">
            <ul class="list-disc pl-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        {{-- ── Informasi Dasar ────────────────────────────── --}}
        <div class="card mb-4">
            <h3 class="font-semibold text-gray-700 mb-3">Informasi Produk</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Kode Produk *</label>
                    <input type="text" name="code" value="{{ old('code') }}" class="input-field" required>
                </div>
                <div>
                    <label class="form-label">Nama Produk *</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="input-field" required>
                </div>
                <div>
                    <label class="form-label">Kategori *</label>
                    <select name="category_id" class="input-field" required>
                        <option value="">Pilih kategori</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Barcode</label>
                    <input type="text" name="barcode" value="{{ old('barcode') }}" class="input-field">
                </div>
                <div>
                    <label class="form-label">Stok Awal *</label>
                    <input type="number" name="stock" value="{{ old('stock', 0) }}" class="input-field" min="0" required>
                </div>
                <div>
                    <label class="form-label">Stok Minimum *</label>
                    <input type="number" name="min_stock" value="{{ old('min_stock', 5) }}" class="input-field" min="0" required>
                </div>
            </div>
            <div class="mt-4">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" rows="2" class="input-field">{{ old('description') }}</textarea>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <label for="is_active" class="form-label mb-0">Produk Aktif</label>
            </div>
        </div>

        {{-- ── Satuan Dasar & Harga Eceran ─────────────────── --}}
        <div class="card mb-4">
            <h3 class="font-semibold text-gray-700 mb-1">Satuan Dasar &amp; Harga Eceran</h3>
            <p class="text-xs text-gray-500 mb-3">Satuan terkecil produk ini — stok selalu dihitung dalam satuan ini.</p>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Satuan Dasar *</label>
                    <select name="unit" class="input-field" required>
                        <option value="">Pilih satuan</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->name }}" {{ old('unit') == $unit->name ? 'selected' : '' }}>{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Harga Jual Eceran *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">Rp</span>
                        <input type="number" name="retail_price" value="{{ old('retail_price', 0) }}" class="input-field pl-10" min="0" required>
                    </div>
                </div>
                <div>
                    <label class="form-label">Harga Grosir (satuan dasar) *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">Rp</span>
                        <input type="number" name="wholesale_price" value="{{ old('wholesale_price', 0) }}" class="input-field pl-10" min="0" required>
                    </div>
                </div>
                <div>
                    <label class="form-label">Min. Qty Grosir *</label>
                    <input type="number" name="min_wholesale_qty" value="{{ old('min_wholesale_qty', 1) }}" class="input-field" min="1" required>
                </div>
            </div>
        </div>

        {{-- ── Konversi Satuan & Harga per Satuan ─────────── --}}
        <div class="card mb-4">
            <div class="flex items-center justify-between mb-1">
                <div>
                    <h3 class="font-semibold text-gray-700">Satuan Lain &amp; Harga (Opsional)</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Misal: 1 dus = 24 botol — tambahkan satuan lebih besar beserta harganya.</p>
                </div>
                <button type="button" @click="addRow()" class="btn-secondary text-xs py-1.5 px-3 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Satuan
                </button>
            </div>

            <div x-show="conversions.length === 0" class="text-center py-6 text-gray-400 text-sm border border-dashed rounded-lg mt-3">
                Belum ada satuan lain. Klik "+ Tambah Satuan" untuk menambahkan.
            </div>

            <div x-show="conversions.length > 0" class="mt-3 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b">
                            <th class="text-left py-2 px-3 font-semibold">Satuan</th>
                            <th class="text-left py-2 px-3 font-semibold">Isi (= berapa satuan dasar)</th>
                            <th class="text-left py-2 px-3 font-semibold">Harga Jual</th>
                            <th class="text-left py-2 px-3 font-semibold">Harga Beli</th>
                            <th class="py-2 px-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, i) in conversions" :key="i">
                            <tr class="border-b">
                                <td class="py-2 px-3">
                                    <select :name="`conversions[${i}][unit_name]`" x-model="row.unit_name" class="input-field text-sm">
                                        <option value="">Pilih satuan</option>
                                        @foreach($units as $unit)
                                        <option value="{{ $unit->name }}">{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="py-2 px-3">
                                    <input type="number" :name="`conversions[${i}][conversion_qty]`" x-model="row.conversion_qty"
                                        class="input-field text-sm w-28" min="0.0001" step="0.0001" placeholder="cth: 24">
                                </td>
                                <td class="py-2 px-3">
                                    <div class="relative">
                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-500 text-xs">Rp</span>
                                        <input type="number" :name="`conversions[${i}][sell_price]`" x-model="row.sell_price"
                                            class="input-field text-sm pl-8" min="0" placeholder="0">
                                    </div>
                                </td>
                                <td class="py-2 px-3">
                                    <div class="relative">
                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-500 text-xs">Rp</span>
                                        <input type="number" :name="`conversions[${i}][buy_price]`" x-model="row.buy_price"
                                            class="input-field text-sm pl-8" min="0" placeholder="0">
                                    </div>
                                </td>
                                <td class="py-2 px-3 text-center">
                                    <button type="button" @click="removeRow(i)" class="text-red-500 hover:text-red-700" title="Hapus baris">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary">Simpan Produk</button>
            <a href="{{ route('master.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection