@extends('layouts.app')

@section('title', 'Stok Barang')
@section('page_title', 'Manajemen Stok Barang')
@section('page_subtitle', 'Pantau dan kelola stok inventaris')

@section('extra_css')
<style>
    [x-cloak] {
        display: none !important
    }
</style>
@endsection

@section('content')

{{-- Product stock data for Alpine.js --}}
<script>
    window.__stockMap = {
        !!json_encode($allProducts - > pluck('stock', 'id'), JSON_HEX_TAG) !!
    };
</script>

<div class="space-y-4"
    x-data="{
        tab: '{{ old('form', request('tab', 'saat-ini')) }}',
        adjProductId: '{{ old('product_id', '') }}',
        adjCurrentStock: null,
        adjNewStock: '{{ old('new_stock', '') }}',
        get adjDiff() {
            if (this.adjNewStock === '' || this.adjCurrentStock === null) return '';
            return parseInt(this.adjNewStock) - this.adjCurrentStock;
        },
        initAdj() {
            if (this.adjProductId && window.__stockMap[this.adjProductId] !== undefined) {
                this.adjCurrentStock = window.__stockMap[this.adjProductId];
            }
        },
        onAdjProductChange(id) {
            if (id && window.__stockMap[id] !== undefined) {
                this.adjCurrentStock = window.__stockMap[id];
            } else {
                this.adjCurrentStock = null;
            }
            this.adjNewStock = '';
        },
        openMasuk() { this.tab = 'masuk'; },
        openPenyesuaian(productId, stock) {
            this.adjProductId = String(productId);
            this.adjCurrentStock = stock;
            this.adjNewStock = '';
            this.tab = 'penyesuaian';
        }
    }"
    x-init="initAdj()">

    {{-- Flash messages handled globally by toast system in layouts/app.blade.php --}}

    {{-- Tab Navigation --}}
    <div class="bg-white rounded-lg shadow">
        <div class="flex border-b border-gray-200 px-4 overflow-x-auto">
            <button @click="tab = 'saat-ini'"
                :class="tab === 'saat-ini' ? 'border-b-2 border-blue-600 text-blue-600 font-medium' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-3 text-sm whitespace-nowrap -mb-px transition-colors">Stok Saat Ini</button>
            <button @click="tab = 'masuk'"
                :class="tab === 'masuk' ? 'border-b-2 border-blue-600 text-blue-600 font-medium' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-3 text-sm whitespace-nowrap -mb-px transition-colors">Stok Masuk</button>
            <button @click="tab = 'penyesuaian'"
                :class="tab === 'penyesuaian' ? 'border-b-2 border-blue-600 text-blue-600 font-medium' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-3 text-sm whitespace-nowrap -mb-px transition-colors">Penyesuaian Stok</button>
            <button @click="tab = 'histori'"
                :class="tab === 'histori' ? 'border-b-2 border-blue-600 text-blue-600 font-medium' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-3 text-sm whitespace-nowrap -mb-px transition-colors">Histori Mutasi</button>
        </div>
    </div>

    {{-- ===== TAB: STOK SAAT INI ===== --}}
    <div x-show="tab === 'saat-ini'" x-cloak>
        @if($lowStockProducts->count())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <div>
                <p class="font-bold text-red-800">{{ $lowStockProducts->count() }} Item dengan Stok di Bawah Minimum</p>
                <p class="text-sm text-red-700">{{ $lowStockProducts->map(fn($p) => "{$p->name} ({$p->stock} unit)")->join(', ') }}</p>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-lg shadow p-4 flex flex-wrap justify-between items-center gap-3">
            <form method="GET" action="{{ route('stock.index') }}" class="flex gap-2 flex-1 min-w-0">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari kode / nama barang..."
                    class="input-field flex-1 max-w-xs">
                <button type="submit" class="btn-secondary text-sm px-3">Cari</button>
                @if(request('search'))
                <a href="{{ route('stock.index') }}" class="btn-secondary text-sm px-3">Reset</a>
                @endif
            </form>
            <div class="flex gap-2">
                <button @click="openMasuk()" class="btn-primary text-sm">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Stok Masuk
                </button>
                <button @click="tab = 'penyesuaian'" class="btn-secondary text-sm">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Penyesuaian
                </button>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b font-semibold text-gray-700">Stok Barang Saat Ini</div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="text-left py-3 px-4 font-semibold text-gray-600">Kode</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600">Nama Barang</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600">Satuan</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-600">Stok Saat Ini</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-600">Stok Minimum</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600">Status</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600">Last Update</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4 font-mono text-xs text-gray-500">{{ $product->code }}</td>
                            <td class="py-3 px-4 font-medium text-gray-800">{{ $product->name }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ $product->unit }}</td>
                            <td class="py-3 px-4 text-right font-bold {{ $product->isLowStock() ? 'text-red-600' : 'text-gray-800' }}">
                                {{ number_format($product->stock) }}
                            </td>
                            <td class="py-3 px-4 text-right text-gray-500">{{ number_format($product->min_stock) }}</td>
                            <td class="py-3 px-4">
                                @if($product->isLowStock())
                                <span class="badge-danger">Minimum!</span>
                                @else
                                <span class="badge-success">Aman</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-xs text-gray-500 whitespace-nowrap">{{ $product->updated_at->format('d M Y, H:i') }}</td>
                            <td class="py-3 px-4 text-center">
                                <button @click="openPenyesuaian({{ $product->id }}, {{ $product->stock }})"
                                    class="text-blue-600 hover:text-blue-800 text-xs font-medium">Detail</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-gray-400">Tidak ada data produk ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($products->hasPages())
            <div class="px-4 py-3 border-t">
                {{ $products->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- ===== TAB: STOK MASUK ===== --}}
    <div x-show="tab === 'masuk'" x-cloak>
        <div class="bg-white rounded-lg shadow max-w-3xl">
            <div class="px-6 py-4 border-b">
                <h3 class="font-semibold text-gray-700">Input Stok Masuk</h3>
                <p class="text-sm text-gray-500 mt-0.5">Catat penambahan stok barang dari supplier</p>
            </div>
            <div class="p-6">
                @if($errors->any() && old('form') === 'masuk')
                <div class="mb-5 bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-sm font-medium text-red-700 mb-1">Harap perbaiki kesalahan berikut:</p>
                    <ul class="text-sm text-red-600 list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('stock.storeMasuk') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="form" value="masuk">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}"
                                class="input-field" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. Referensi / PO</label>
                            <input type="text" name="reference" value="{{ old('reference') }}"
                                placeholder="Contoh: PO-20260502-001" class="input-field">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Pilih Barang <span class="text-red-500">*</span>
                        </label>
                        <select name="product_id" class="input-field" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach($allProducts as $p)
                            <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>
                                [{{ $p->code }}] {{ $p->name }} — Stok saat ini: {{ $p->stock }} {{ $p->unit }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Jumlah Masuk <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="qty" value="{{ old('qty') }}"
                                min="1" placeholder="0" class="input-field" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea name="note" rows="3"
                            placeholder="Catatan tambahan (opsional)"
                            class="input-field">{{ old('note') }}</textarea>
                    </div>

                    <div class="flex gap-3 justify-end pt-1">
                        <button type="button" @click="tab = 'saat-ini'" class="btn-secondary">Batal</button>
                        <button type="submit" class="btn-primary">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Stok Masuk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===== TAB: PENYESUAIAN STOK ===== --}}
    <div x-show="tab === 'penyesuaian'" x-cloak>
        <div class="bg-white rounded-lg shadow max-w-3xl">
            <div class="px-6 py-4 border-b">
                <h3 class="font-semibold text-gray-700">Form Penyesuaian Stok</h3>
                <p class="text-sm text-gray-500 mt-0.5">Koreksi stok fisik dengan data sistem (stock opname)</p>
            </div>
            <div class="p-6">
                @if($errors->any() && old('form') === 'penyesuaian')
                <div class="mb-5 bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-sm font-medium text-red-700 mb-1">Harap perbaiki kesalahan berikut:</p>
                    <ul class="text-sm text-red-600 list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('stock.storePenyesuaian') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="form" value="penyesuaian">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Pilih Barang <span class="text-red-500">*</span>
                        </label>
                        <select name="product_id" class="input-field" required
                            x-model="adjProductId"
                            @change="onAdjProductChange($event.target.value)">
                            <option value="">-- Pilih Barang --</option>
                            @foreach($allProducts as $p)
                            <option value="{{ $p->id }}">
                                [{{ $p->code }}] {{ $p->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stok Sistem</label>
                            <input type="number" :value="adjCurrentStock !== null ? adjCurrentStock : ''"
                                placeholder="—" class="input-field bg-gray-50 text-gray-600" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Stok Fisik Baru <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="new_stock" x-model="adjNewStock"
                                min="0" placeholder="0" required
                                :disabled="adjCurrentStock === null"
                                class="input-field"
                                :class="adjCurrentStock === null ? 'opacity-50 cursor-not-allowed' : ''">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Selisih</label>
                            <input type="number" :value="adjDiff"
                                placeholder="—" class="input-field bg-gray-50 font-semibold"
                                :class="adjDiff !== '' ? (adjDiff < 0 ? 'text-red-600' : (adjDiff > 0 ? 'text-green-600' : 'text-gray-600')) : ''"
                                readonly>
                            <p class="text-xs mt-1" x-show="adjDiff !== ''"
                                :class="adjDiff < 0 ? 'text-red-500' : (adjDiff > 0 ? 'text-green-600' : 'text-gray-400')">
                                <span x-show="adjDiff < 0">Stok berkurang <strong x-text="Math.abs(adjDiff)"></strong> unit</span>
                                <span x-show="adjDiff > 0">Stok bertambah <strong x-text="adjDiff"></strong> unit</span>
                                <span x-show="adjDiff == 0">Tidak ada perubahan stok</span>
                            </p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan / Alasan Penyesuaian</label>
                        <textarea name="note" rows="3"
                            placeholder="Contoh: Hasil stock opname, barang rusak/hilang, dll."
                            class="input-field">{{ old('note') }}</textarea>
                    </div>

                    <div class="flex gap-3 justify-end pt-1">
                        <button type="button" @click="tab = 'saat-ini'" class="btn-secondary">Batal</button>
                        <button type="submit" class="btn-primary"
                            :disabled="adjCurrentStock === null"
                            :class="adjCurrentStock === null ? 'opacity-50 cursor-not-allowed' : ''">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Proses Penyesuaian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===== TAB: HISTORI MUTASI ===== --}}
    <div x-show="tab === 'histori'" x-cloak>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <div>
                    <h3 class="font-semibold text-gray-700">Histori Mutasi Stok</h3>
                    <p class="text-sm text-gray-500 mt-0.5">50 transaksi terbaru</p>
                </div>
                <span class="text-sm text-gray-400">{{ $movements->count() }} data</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="text-left py-3 px-4 font-semibold text-gray-600">Tanggal</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600">Barang</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600">Tipe</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-600">Qty</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-600">Stok Sebelum</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-600">Stok Sesudah</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600">Referensi</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $m)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4 text-xs text-gray-500 whitespace-nowrap">
                                {{ $m->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="py-3 px-4 font-medium text-gray-800">{{ $m->product->name ?? '—' }}</td>
                            <td class="py-3 px-4">
                                @if($m->type === 'in')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                    </svg>
                                    Masuk
                                </span>
                                @elseif($m->type === 'out')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                    </svg>
                                    Keluar
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-700">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Penyesuaian
                                </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-bold {{ $m->qty < 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ $m->qty > 0 ? '+' : '' }}{{ number_format($m->qty) }}
                            </td>
                            <td class="py-3 px-4 text-right text-gray-500">{{ number_format($m->stock_before) }}</td>
                            <td class="py-3 px-4 text-right font-semibold text-gray-800">{{ number_format($m->stock_after) }}</td>
                            <td class="py-3 px-4 text-xs text-gray-500">{{ $m->reference ?? '—' }}</td>
                            <td class="py-3 px-4 text-xs text-gray-500 max-w-xs truncate" title="{{ $m->note }}">
                                {{ $m->note ?? '—' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-gray-400">Belum ada riwayat mutasi stok.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection