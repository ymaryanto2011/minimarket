@extends('layouts.app')

@section('title', 'Penawaran Barang')
@section('page_title', 'Penawaran Barang')
@section('page_subtitle', 'Buat dan kelola penawaran harga untuk customer')

@section('content')
@php
$__quotationsJson = json_encode($quotations->map(function($q) {
return [
'id' => $q->id,
'quotation_no' => $q->quotation_no,
'to_name' => $q->to_name,
'date' => $q->date ? $q->date->format('Y-m-d') : null,
'valid_until' => $q->valid_until ? $q->valid_until->format('Y-m-d') : null,
'subtotal' => (float) $q->subtotal,
'discount' => (float) $q->discount,
'tax_rate' => (float) $q->tax_rate,
'tax_amount' => (float) $q->tax_amount,
'total' => (float) $q->total,
'notes' => $q->notes,
'status' => $q->status,
'created_by' => $q->created_by,
'creator' => $q->creator ? ['name' => $q->creator->name] : null,
'items' => $q->items->map(fn($i) => [
'id' => $i->id,
'product_id' => $i->product_id,
'product_name' => $i->product_name,
'unit_label' => $i->unit_label,
'conversion_qty' => (float) $i->conversion_qty,
'qty' => $i->qty,
'unit_price' => (float) $i->unit_price,
'discount_pct' => (float) $i->discount_pct,
'total' => (float) $i->total,
])->values()->all(),
];
})->values(), JSON_HEX_TAG);

$__productsJson = json_encode($products->map(fn($p) => [
'id' => $p->id,
'code' => $p->code,
'name' => $p->name,
'retail_price' => (float) $p->retail_price,
'wholesale_price' => (float) $p->wholesale_price,
'unit' => $p->unit,
'unit_conversions' => $p->allUnits(),
])->values(), JSON_HEX_TAG);

$__storeJson = json_encode($store ? [
'name' => $store->name,
'address' => $store->address,
'phone' => $store->phone,
'owner_name' => $store->owner_name,
'bank_accounts' => $store->bank_accounts ?? [],
] : null, JSON_HEX_TAG);
@endphp
<script>
    window.__quotations = <?= $__quotationsJson ?>;
    window.__products = <?= $__productsJson ?>;
    window.__store = <?= $__storeJson ?>;
    window.__nextNo = @json($nextNo);
    window.__csrfToken = @json(csrf_token());
    window.__routeStore = @json(route('quotation.store'));
    window.__routeUpdate = '/quotation/';
    window.__routeDelete = '/quotation/';
    window.__routePdf = '/quotation/';
</script>

<div x-data="quotationApp()" x-init="initApp()">

    {{-- Flash messages handled globally by toast system in layouts/app.blade.php --}}

    {{-- ===== Tabs Header ===== --}}
    <div class="bg-white rounded-lg shadow mb-4">
        <div class="flex items-center justify-between px-4 pt-1 border-b">
            <div class="flex overflow-x-auto">
                <button @click="activeTab='aktif'"
                    :class="activeTab==='aktif' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                    class="px-5 py-3 text-sm whitespace-nowrap transition-colors flex items-center gap-1.5">
                    Aktif
                    <span class="px-1.5 py-0.5 rounded-full text-xs font-medium"
                        :class="activeTab==='aktif' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600'"
                        x-text="activeCount"></span>
                </button>
                <button @click="activeTab='komplit'"
                    :class="activeTab==='komplit' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                    class="px-5 py-3 text-sm whitespace-nowrap transition-colors flex items-center gap-1.5">
                    Komplit
                    <span class="px-1.5 py-0.5 rounded-full text-xs font-medium"
                        :class="activeTab==='komplit' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600'"
                        x-text="komplitCount"></span>
                </button>
                <button @click="activeTab='semua'"
                    :class="activeTab==='semua' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                    class="px-5 py-3 text-sm whitespace-nowrap transition-colors flex items-center gap-1.5">
                    Semua
                    <span class="px-1.5 py-0.5 rounded-full text-xs font-medium"
                        :class="activeTab==='semua' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600'"
                        x-text="quotations.length"></span>
                </button>
            </div>
            <button @click="openCreate()" class="btn-primary text-sm ml-4 whitespace-nowrap flex items-center gap-1.5 my-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Penawaran
            </button>
        </div>
    </div>

    {{-- ===== Table ===== --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b">
            <input type="text" x-model="search" placeholder="Cari no. penawaran / nama customer..."
                class="input-field max-w-sm text-sm">
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b bg-gray-50">
                        <th class="text-left py-3 px-4 font-semibold text-gray-600">No. Penawaran</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600">Kepada</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600">Tanggal</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600">Valid Hingga</th>
                        <th class="text-right py-3 px-4 font-semibold text-gray-600">Total</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600">Status</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-600 w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="q in filteredQuotations" :key="q.id">
                        <tr class="border-b hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-gray-800 text-xs" x-text="q.quotation_no"></td>
                            <td class="py-3 px-4 text-gray-700 max-w-xs truncate" x-text="q.to_name"></td>
                            <td class="py-3 px-4 text-gray-500 text-xs whitespace-nowrap" x-text="fmtDate(q.date)"></td>
                            <td class="py-3 px-4 text-xs whitespace-nowrap"
                                :class="isExpired(q.valid_until, q.status) ? 'text-red-500 font-medium' : 'text-gray-500'"
                                x-text="fmtDate(q.valid_until)"></td>
                            <td class="py-3 px-4 text-right font-semibold text-gray-800 whitespace-nowrap" x-text="fmtRupiah(q.total)"></td>
                            <td class="py-3 px-4">
                                <span :class="statusBadge(q.status)" x-text="statusLabel(q.status)"></span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex justify-center items-center gap-0.5">
                                    {{-- View --}}
                                    <button @click="openView(q)" title="Lihat Detail"
                                        class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    {{-- Edit --}}
                                    <button @click="openEdit(q)" title="Edit"
                                        x-show="['draft','submit','approved'].includes(q.status)"
                                        class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    {{-- PDF --}}
                                    <a :href="`/quotation/${q.id}/pdf`" title="PDF" target="_blank"
                                        class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </a>
                                    {{-- Delete --}}
                                    <button @click="confirmDelete(q)" title="Hapus"
                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredQuotations.length === 0" x-cloak>
                        <td colspan="7" class="py-14 text-center text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Tidak ada data penawaran.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- MODAL CREATE / EDIT                                    --}}
    {{-- ===================================================== --}}
    <div x-show="formModal" x-cloak
        class="fixed inset-0 z-50 bg-black bg-opacity-60 flex items-start justify-center py-4 px-2 overflow-y-auto"
        @keydown.escape.window="formModal = false">

        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl my-auto" @click.stop>

            {{-- Header --}}
            <div class="flex items-start justify-between p-5 border-b">
                <div>
                    <h2 class="text-lg font-bold text-gray-800"
                        x-text="formMode === 'create' ? 'Buat Penawaran Baru' : 'Edit Penawaran'"></h2>
                    <p class="text-sm text-gray-500 mt-0.5"
                        x-text="formMode === 'create' ? 'Isi form untuk membuat penawaran baru' : ('Edit: ' + form.quotation_no)"></p>
                </div>
                <button @click="formModal = false" class="text-gray-400 hover:text-gray-600 transition ml-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Errors --}}
            <div x-show="formErrors.length > 0" class="mx-5 mt-4 bg-red-50 border border-red-200 rounded-lg p-3">
                <p class="text-sm font-semibold text-red-700 mb-1">Harap perbaiki:</p>
                <ul class="text-sm text-red-600 list-disc list-inside space-y-0.5">
                    <template x-for="err in formErrors">
                        <li x-text="err"></li>
                    </template>
                </ul>
            </div>

            <div class="p-5 space-y-5">

                {{-- === Header Fields === --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">No. Penawaran</label>
                        <input type="text" x-model="form.quotation_no"
                            class="input-field text-sm bg-gray-50 text-gray-400" readonly>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                        <select x-model="form.status" class="input-field text-sm"
                            :disabled="formMode === 'create'" :class="formMode === 'create' ? 'bg-gray-50 text-gray-400' : ''">
                            <option value="draft">Draft</option>
                            <option value="submit">Dikirim ke Customer</option>
                            <option value="approved">Disetujui</option>
                            <option value="paid">Lunas</option>
                            <option value="rejected">Ditolak</option>
                            <option value="expired">Kadaluarsa</option>
                            <option value="cancelled">Dibatalkan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" x-model="form.date" class="input-field text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Berlaku Hingga <span class="text-red-500">*</span></label>
                        <input type="date" x-model="form.valid_until" class="input-field text-sm" required>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Nama Customer / Penerima <span class="text-red-500">*</span></label>
                    <input type="text" x-model="form.to_name" class="input-field"
                        placeholder="Nama perusahaan / toko / customer" required>
                </div>

                {{-- === Items Table === --}}
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Detail Barang</label>
                        <button type="button" @click="addItem()"
                            class="text-blue-600 hover:text-blue-800 text-xs font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Baris
                        </button>
                    </div>
                    <div class="border rounded-lg overflow-x-auto">
                        <table class="w-full text-xs min-w-max">
                            <thead>
                                <tr class="bg-gray-700 text-white">
                                    <th class="text-center py-2 px-2 w-8">#</th>
                                    <th class="text-left py-2 px-3 min-w-48">Barang</th>
                                    <th class="py-2 px-3 w-28">Satuan</th>
                                    <th class="text-right py-2 px-3 w-20">Qty</th>
                                    <th class="text-right py-2 px-3 w-32">Harga Satuan</th>
                                    <th class="text-right py-2 px-3 w-20">Diskon %</th>
                                    <th class="text-right py-2 px-3 w-32">Jumlah</th>
                                    <th class="w-8"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, idx) in form.items" :key="idx">
                                    <tr class="border-t hover:bg-gray-50">
                                        <td class="py-1.5 px-2 text-center text-gray-400" x-text="idx + 1"></td>
                                        <td class="py-1.5 px-2">
                                            <input type="text"
                                                x-model="item.product_search"
                                                @focus="activeDropdownIdx = idx; positionDropdown($event.target)"
                                                @input="activeDropdownIdx = idx; positionDropdown($event.target)"
                                                @keydown.escape="activeDropdownIdx = -1"
                                                @blur="setTimeout(() => { activeDropdownIdx = -1 }, 200)"
                                                placeholder="Ketik kode / nama barang..."
                                                class="input-field text-xs py-1 w-full"
                                                autocomplete="off">
                                        </td>
                                        <td class="py-1.5 px-2">
                                            {{-- Unit dropdown: auto-populated from selected product's conversions --}}
                                            <select x-model="item.unit_label" @change="onUnitChange(idx)"
                                                class="input-field text-xs py-1 w-full"
                                                :disabled="!item.product_id">
                                                <template x-if="item.product_id">
                                                    <template x-for="u in getProductUnits(item.product_id)" :key="u.unit_name">
                                                        <option :value="u.unit_name" x-text="u.unit_name"
                                                            :selected="u.unit_name === item.unit_label"></option>
                                                    </template>
                                                </template>
                                                <template x-if="!item.product_id">
                                                    <option value="">—</option>
                                                </template>
                                            </select>
                                        </td>
                                        <td class="py-1.5 px-2">
                                            <input type="number" x-model.number="item.qty" @input="calcItem(idx)"
                                                min="1" class="input-field text-xs py-1 text-right w-full">
                                        </td>
                                        <td class="py-1.5 px-2">
                                            <input type="number" x-model.number="item.unit_price" @input="calcItem(idx)"
                                                min="0" step="100" class="input-field text-xs py-1 text-right w-full">
                                        </td>
                                        <td class="py-1.5 px-2">
                                            <input type="number" x-model.number="item.discount_pct" @input="calcItem(idx)"
                                                min="0" max="100" step="0.5" class="input-field text-xs py-1 text-right w-full">
                                        </td>
                                        <td class="py-1.5 px-2 text-right font-semibold text-gray-700 whitespace-nowrap"
                                            x-text="fmtRupiah(item.total || 0)"></td>
                                        <td class="py-1.5 px-2 text-center">
                                            <button @click="removeItem(idx)" x-show="form.items.length > 1"
                                                class="text-red-400 hover:text-red-600 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    {{-- Global product search dropdown (position:fixed avoids overflow-x-auto clipping) --}}
                    <div x-show="activeDropdownIdx >= 0 && formModal"
                        :style="`top:${dropdownTop}px; left:${dropdownLeft}px; width:${dropdownWidth}px`"
                        style="position:fixed; z-index:9999; background:#fff; border:1px solid #d1d5db; border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,.15); max-height:220px; overflow-y:auto">
                        <template x-for="p in filteredProducts(activeDropdownIdx)" :key="p.id">
                            <div @mousedown.prevent="form.items[activeDropdownIdx].product_id = String(p.id); onProductChange(activeDropdownIdx); activeDropdownIdx = -1"
                                style="padding:8px 12px; font-size:12px; cursor:pointer; border-bottom:1px solid #f3f4f6"
                                :style="activeDropdownIdx >= 0 && form.items[activeDropdownIdx] && String(form.items[activeDropdownIdx].product_id) === String(p.id) ? 'background:#eff6ff; color:#1d4ed8; font-weight:600' : 'color:#374151'"
                                x-text="'['+p.code+'] '+p.name">
                            </div>
                        </template>
                        <div x-show="filteredProducts(activeDropdownIdx).length === 0"
                            style="padding:12px; font-size:12px; color:#9ca3af; text-align:center">Barang tidak ditemukan</div>
                    </div>
                </div>

                {{-- === Totals & Notes === --}}
                <div class="flex flex-col md:flex-row gap-5">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Catatan</label>
                        <textarea x-model="form.notes" rows="4" class="input-field text-sm"
                            placeholder="Syarat & ketentuan, catatan khusus..."></textarea>
                    </div>
                    <div class="md:w-72 shrink-0">
                        <div class="border rounded-lg p-3 space-y-2 text-sm bg-gray-50">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 text-xs">Subtotal</span>
                                <span class="font-medium" x-text="fmtRupiah(subtotal)"></span>
                            </div>
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-gray-500 text-xs shrink-0">Potongan (Rp)</span>
                                <input type="number" x-model.number="form.discount" @input="calcTotals()"
                                    min="0" step="1000" class="input-field text-xs py-1 text-right w-32">
                            </div>
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-gray-500 text-xs shrink-0">Pajak (%)</span>
                                <input type="number" x-model.number="form.tax_rate" @input="calcTotals()"
                                    min="0" max="100" step="0.5" class="input-field text-xs py-1 text-right w-32">
                            </div>
                            <div class="flex justify-between text-xs text-gray-400 border-t pt-2">
                                <span x-text="'PPN / Pajak' + (form.tax_rate ? ` (${form.tax_rate}%)` : '')"></span>
                                <span x-text="fmtRupiah(taxAmount)"></span>
                            </div>
                            <div class="flex justify-between items-center border-t pt-2 font-bold">
                                <span class="text-gray-700">Total Bayar</span>
                                <span class="text-blue-600 text-base" x-text="fmtRupiah(grandTotal)"></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="flex justify-between items-center gap-3 p-5 border-t bg-gray-50 rounded-b-xl">
                <p class="text-xs text-gray-400">
                    <span x-show="formMode==='create'">Penawaran akan tersimpan sebagai <strong>Draft</strong></span>
                    <span x-show="formMode==='edit'">Perubahan akan disimpan langsung</span>
                </p>
                <div class="flex gap-3">
                    <button type="button" @click="formModal = false" class="btn-secondary">Batal</button>
                    <button type="button" @click="submitForm()" :disabled="submitting"
                        class="btn-primary" :class="submitting ? 'opacity-60 cursor-not-allowed' : ''">
                        <svg x-show="submitting" class="w-4 h-4 inline mr-1 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        <span x-text="submitting ? 'Menyimpan...' : (formMode === 'create' ? 'Simpan Draft' : 'Simpan Perubahan')"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- MODAL VIEW (Read-only)                                 --}}
    {{-- ===================================================== --}}
    <div x-show="viewModal" x-cloak
        class="fixed inset-0 z-50 bg-black bg-opacity-60 flex items-start justify-center py-4 px-2 overflow-y-auto"
        @keydown.escape.window="viewModal = false">

        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl my-auto" @click.stop>

            {{-- Modal toolbar --}}
            <div class="flex items-center justify-between p-4 border-b bg-gray-50 rounded-t-xl">
                <div class="flex items-center gap-2">
                    <span :class="statusBadge(viewData?.status)" x-text="statusLabel(viewData?.status)" class="text-xs"></span>
                    <span class="font-mono text-sm font-bold text-gray-700" x-text="viewData?.quotation_no"></span>
                </div>
                <div class="flex gap-2 items-center">
                    <a :href="`/quotation/${viewData?.id}/pdf`" target="_blank"
                        class="btn-secondary text-xs py-1.5 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        PDF
                    </a>
                    <button @click="viewModal = false" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div x-show="viewData" class="p-6 text-sm space-y-5">

                {{-- Store Header --}}
                <div class="text-center border-b pb-4">
                    <h1 class="text-xl font-bold text-gray-800" x-text="store?.name || 'Toko'"></h1>
                    <p class="text-gray-500 text-xs mt-0.5" x-text="store?.address"></p>
                    <p class="text-gray-500 text-xs" x-show="store?.phone" x-text="'Telp: ' + store?.phone"></p>
                </div>

                {{-- Title --}}
                <h2 class="text-center text-lg font-bold tracking-wide text-gray-700 uppercase">Penawaran Harga</h2>

                {{-- Info Grid --}}
                <div class="grid grid-cols-2 gap-3 text-xs border rounded-lg p-3 bg-gray-50">
                    <div>
                        <p class="text-gray-400">Kepada:</p>
                        <p class="font-semibold text-gray-800" x-text="viewData?.to_name"></p>
                    </div>
                    <div>
                        <p class="text-gray-400">No. Penawaran:</p>
                        <p class="font-mono font-semibold" x-text="viewData?.quotation_no"></p>
                    </div>
                    <div>
                        <p class="text-gray-400">Tanggal:</p>
                        <p class="font-semibold" x-text="fmtDate(viewData?.date)"></p>
                    </div>
                    <div>
                        <p class="text-gray-400">Berlaku Hingga:</p>
                        <p class="font-semibold" :class="isExpired(viewData?.valid_until, viewData?.status) ? 'text-red-500' : ''" x-text="fmtDate(viewData?.valid_until)"></p>
                    </div>
                    <div>
                        <p class="text-gray-400">Dibuat oleh:</p>
                        <p class="font-semibold" x-text="viewData?.creator?.name || 'Admin'"></p>
                    </div>
                </div>

                {{-- Items --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-xs border border-gray-200 border-collapse">
                        <thead>
                            <tr class="bg-gray-700 text-white">
                                <th class="text-center py-2 px-2 border border-gray-600 w-8">No</th>
                                <th class="text-left py-2 px-3 border border-gray-600">Nama Barang</th>
                                <th class="text-left py-2 px-2 border border-gray-600 w-16">Satuan</th>
                                <th class="text-right py-2 px-2 border border-gray-600 w-14">Qty</th>
                                <th class="text-right py-2 px-3 border border-gray-600 w-28">Harga Satuan</th>
                                <th class="text-right py-2 px-2 border border-gray-600 w-16">Diskon</th>
                                <th class="text-right py-2 px-3 border border-gray-600 w-28">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, idx) in (viewData?.items || [])">
                                <tr :class="idx % 2 === 0 ? '' : 'bg-gray-50'">
                                    <td class="text-center py-2 px-2 border border-gray-200" x-text="idx+1"></td>
                                    <td class="py-2 px-3 border border-gray-200 font-medium" x-text="item.product_name"></td>
                                    <td class="py-2 px-2 border border-gray-200 text-xs text-blue-700 font-medium" x-text="item.unit_label || '—'"></td>
                                    <td class="text-right py-2 px-2 border border-gray-200" x-text="item.qty"></td>
                                    <td class="text-right py-2 px-3 border border-gray-200" x-text="fmtRupiah(item.unit_price)"></td>
                                    <td class="text-right py-2 px-2 border border-gray-200 text-orange-600"
                                        x-text="(item.discount_pct || 0) > 0 ? (item.discount_pct + '%') : '—'"></td>
                                    <td class="text-right py-2 px-3 border border-gray-200 font-semibold" x-text="fmtRupiah(item.total)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Totals --}}
                <div class="flex justify-end">
                    <div class="w-64 text-xs space-y-1.5">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-medium" x-text="fmtRupiah(viewData?.subtotal)"></span>
                        </div>
                        <div class="flex justify-between" x-show="viewData?.discount > 0">
                            <span class="text-gray-500">Potongan</span>
                            <span class="text-red-500 font-medium" x-text="'- ' + fmtRupiah(viewData?.discount)"></span>
                        </div>
                        <div class="flex justify-between" x-show="viewData?.tax_rate > 0">
                            <span class="text-gray-500" x-text="'Pajak (' + viewData?.tax_rate + '%)'"></span>
                            <span x-text="fmtRupiah(viewData?.tax_amount)"></span>
                        </div>
                        <div class="flex justify-between font-bold text-sm border-t pt-2 mt-1">
                            <span>Total Pembayaran</span>
                            <span class="text-blue-700" x-text="fmtRupiah(viewData?.total)"></span>
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div x-show="viewData?.notes" class="border-l-4 border-gray-300 pl-3 text-xs text-gray-600 bg-gray-50 rounded-r py-2">
                    <p class="font-semibold text-gray-500 mb-0.5">Catatan:</p>
                    <p x-text="viewData?.notes"></p>
                </div>

                {{-- Footer: Signatures + Bank --}}
                <div class="border-t pt-4 grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">

                    {{-- Signature --}}
                    <div>
                        <p class="font-semibold text-gray-600 mb-1">Hormat kami,</p>
                        <div class="h-14"></div>
                        <div class="border-t border-gray-400 pt-1 inline-block min-w-32">
                            <p class="font-bold text-gray-800" x-text="store?.owner_name || store?.name || 'Pemilik Toko'"></p>
                            <p class="text-gray-400">Pemilik / Kasir</p>
                        </div>
                    </div>

                    {{-- Bank Accounts --}}
                    <div>
                        <p class="font-semibold text-gray-600 mb-2">Informasi Pembayaran:</p>
                        <template x-if="store?.bank_accounts?.length">
                            <div class="space-y-2">
                                <template x-for="(bank, bi) in store.bank_accounts" :key="bi">
                                    <div class="p-2 border rounded bg-gray-50">
                                        <p class="font-bold text-gray-700" x-text="bank.bank_name"></p>
                                        <p class="text-gray-600" x-text="'No. Rekening: ' + bank.account_no"></p>
                                        <p class="text-gray-600" x-text="'A/N: ' + bank.account_name"></p>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="!store?.bank_accounts?.length">
                            <p class="text-gray-400 italic">Belum ada info rekening.<br>
                                <a href="/setting/profile" class="text-blue-500 underline">Lengkapi di Pengaturan → Profil Toko</a>
                            </p>
                        </template>
                    </div>
                </div>

            </div>

            <div class="p-4 border-t flex justify-between items-center bg-gray-50 rounded-b-xl">
                <button x-show="['draft','submit','approved'].includes(viewData?.status)"
                    @click="viewModal=false; openEdit(viewData)"
                    class="btn-secondary text-sm flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Penawaran
                </button>
                <div x-show="!['draft','submit','approved'].includes(viewData?.status)"></div>
                <button @click="viewModal = false" class="btn-secondary">Tutup</button>
            </div>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- MODAL DELETE CONFIRM                                   --}}
    {{-- ===================================================== --}}
    <div x-show="deleteModal" x-cloak
        class="fixed inset-0 z-50 bg-black bg-opacity-60 flex items-center justify-center px-4"
        @keydown.escape.window="deleteModal = false">

        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6" @click.stop>
            <div class="text-center">
                <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">Hapus Penawaran?</h3>
                <p class="text-sm text-gray-500 mb-6"
                    x-text="`Penawaran ${deletingItem?.quotation_no} untuk ${deletingItem?.to_name} akan dihapus permanen dan tidak dapat dikembalikan.`"></p>
                <div class="flex gap-3 justify-center">
                    <button @click="deleteModal = false" class="btn-secondary">Batal</button>
                    <button @click="doDelete()" :disabled="submitting"
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold px-5 py-2 rounded-lg transition text-sm"
                        :class="submitting ? 'opacity-60 cursor-not-allowed' : ''">
                        <span x-show="!submitting">Ya, Hapus</span>
                        <span x-show="submitting">Menghapus...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('extra_js')
<script>
    function quotationApp() {
        return {
            quotations: window.__quotations || [],
            products: window.__products || [],
            store: window.__store,

            activeTab: 'aktif',
            search: '',

            formModal: false,
            formMode: 'create',
            viewModal: false,
            deleteModal: false,

            form: {},
            viewData: null,
            deletingItem: null,

            submitting: false,
            formErrors: [],

            subtotal: 0,
            taxAmount: 0,
            grandTotal: 0,

            activeDropdownIdx: -1,
            dropdownTop: 0,
            dropdownLeft: 0,
            dropdownWidth: 300,

            initApp() {
                /* nothing */
            },

            positionDropdown(el) {
                const rect = el.getBoundingClientRect();
                this.dropdownTop = rect.bottom + 2;
                this.dropdownLeft = rect.left;
                this.dropdownWidth = Math.max(300, rect.width);
            },

            filteredProducts(idx) {
                if (idx < 0 || !this.form.items || !this.form.items[idx]) return [];
                const search = (this.form.items[idx].product_search || '').toLowerCase();
                if (!search) return this.products;
                return this.products.filter(p => ('[' + p.code + '] ' + p.name).toLowerCase().includes(search));
            },

            /* ---- Computed counts ---- */
            get activeStatuses() {
                return ['draft', 'submit', 'approved'];
            },
            get komplitStatuses() {
                return ['paid'];
            },

            get activeCount() {
                return this.quotations.filter(q => this.activeStatuses.includes(q.status)).length;
            },
            get komplitCount() {
                return this.quotations.filter(q => this.komplitStatuses.includes(q.status)).length;
            },

            get filteredQuotations() {
                let list = this.quotations;
                if (this.activeTab === 'aktif') list = list.filter(q => this.activeStatuses.includes(q.status));
                if (this.activeTab === 'komplit') list = list.filter(q => this.komplitStatuses.includes(q.status));
                if (this.search.trim()) {
                    const s = this.search.toLowerCase();
                    list = list.filter(q => q.quotation_no.toLowerCase().includes(s) || q.to_name.toLowerCase().includes(s));
                }
                return list;
            },

            /* ---- Form helpers ---- */
            blankItem() {
                return {
                    product_id: '',
                    product_name: '',
                    product_search: '',
                    unit_label: '',
                    conversion_qty: 1,
                    qty: 1,
                    unit_price: 0,
                    discount_pct: 0,
                    total: 0
                };
            },

            /* Return the list of all available units for a product (base + conversions) */
            getProductUnits(pid) {
                const prod = this.products.find(p => String(p.id) === String(pid));
                return prod ? (prod.unit_conversions || [{
                    unit_name: prod.unit,
                    conversion_qty: 1,
                    sell_price: prod.retail_price,
                    buy_price: 0
                }]) : [];
            },

            defaultForm() {
                const today = new Date().toISOString().split('T')[0];
                const d = new Date();
                d.setDate(d.getDate() + 7);
                const nextWeek = d.toISOString().split('T')[0];
                return {
                    id: null,
                    quotation_no: window.__nextNo,
                    to_name: '',
                    date: today,
                    valid_until: nextWeek,
                    status: 'draft',
                    notes: '',
                    discount: 0,
                    tax_rate: 0,
                    items: [this.blankItem()],
                };
            },

            openCreate() {
                this.formMode = 'create';
                this.form = this.defaultForm();
                this.formErrors = [];
                this.calcTotals();
                this.formModal = true;
            },

            openEdit(q) {
                this.formMode = 'edit';
                this.form = {
                    id: q.id,
                    quotation_no: q.quotation_no,
                    to_name: q.to_name,
                    date: q.date ? q.date.split('T')[0] : q.date,
                    valid_until: q.valid_until ? q.valid_until.split('T')[0] : q.valid_until,
                    status: q.status,
                    notes: q.notes || '',
                    discount: parseFloat(q.discount) || 0,
                    tax_rate: parseFloat(q.tax_rate) || 0,
                    items: (q.items || []).map(it => {
                        const prod = this.products.find(p => String(p.id) === String(it.product_id));
                        return {
                            product_id: String(it.product_id || ''),
                            product_name: it.product_name || '',
                            product_search: prod ? `[${prod.code}] ${prod.name}` : (it.product_name || ''),
                            unit_label: it.unit_label || '',
                            conversion_qty: parseFloat(it.conversion_qty) || 1,
                            qty: it.qty,
                            unit_price: parseFloat(it.unit_price),
                            discount_pct: parseFloat(it.discount_pct) || 0,
                            total: parseFloat(it.total),
                        };
                    }),
                };
                if (!this.form.items.length) this.form.items = [this.blankItem()];
                this.formErrors = [];
                this.calcTotals();
                this.formModal = true;
            },

            openView(q) {
                this.viewData = q;
                this.viewModal = true;
            },

            addItem() {
                this.form.items.push(this.blankItem());
            },
            removeItem(idx) {
                this.form.items.splice(idx, 1);
                this.calcTotals();
            },

            onProductChange(idx) {
                const pid = parseInt(this.form.items[idx].product_id);
                const prod = this.products.find(p => p.id === pid);
                if (prod) {
                    this.form.items[idx].product_name = prod.name;
                    this.form.items[idx].product_search = `[${prod.code}] ${prod.name}`;
                    const units = prod.unit_conversions || [];
                    // Default to first unit (base unit)
                    const first = units[0] || {
                        unit_name: prod.unit,
                        conversion_qty: 1,
                        sell_price: prod.retail_price
                    };
                    this.form.items[idx].unit_label = first.unit_name;
                    this.form.items[idx].conversion_qty = parseFloat(first.conversion_qty) || 1;
                    this.form.items[idx].unit_price = parseFloat(first.sell_price) || parseFloat(prod.retail_price);
                }
                this.calcItem(idx);
            },

            onUnitChange(idx) {
                const pid = parseInt(this.form.items[idx].product_id);
                const prod = this.products.find(p => p.id === pid);
                if (!prod) return;
                const selectedUnit = this.form.items[idx].unit_label;
                const units = prod.unit_conversions || [];
                const found = units.find(u => u.unit_name === selectedUnit);
                if (found) {
                    this.form.items[idx].conversion_qty = parseFloat(found.conversion_qty) || 1;
                    this.form.items[idx].unit_price = parseFloat(found.sell_price) || 0;
                }
                this.calcItem(idx);
            },

            calcItem(idx) {
                const it = this.form.items[idx];
                it.total = (parseFloat(it.qty) || 0) * (parseFloat(it.unit_price) || 0) * (1 - (parseFloat(it.discount_pct) || 0) / 100);
                this.calcTotals();
            },

            calcTotals() {
                this.subtotal = this.form.items.reduce((s, it) => s + (parseFloat(it.total) || 0), 0);
                const disc = parseFloat(this.form.discount) || 0;
                const taxRate = parseFloat(this.form.tax_rate) || 0;
                const afterDisc = Math.max(0, this.subtotal - disc);
                this.taxAmount = afterDisc * taxRate / 100;
                this.grandTotal = afterDisc + this.taxAmount;
            },

            /* ---- AJAX form submit ---- */
            async submitForm() {
                this.formErrors = [];
                if (!this.form.to_name.trim()) this.formErrors.push('Nama customer wajib diisi.');
                if (!this.form.date) this.formErrors.push('Tanggal wajib diisi.');
                if (!this.form.valid_until) this.formErrors.push('Tanggal berlaku hingga wajib diisi.');
                if (!this.form.items.some(i => i.product_id)) this.formErrors.push('Minimal 1 barang harus dipilih.');
                if (this.formErrors.length) return;

                this.submitting = true;
                const payload = {
                    to_name: this.form.to_name,
                    date: this.form.date,
                    valid_until: this.form.valid_until,
                    status: this.form.status,
                    notes: this.form.notes,
                    discount: this.form.discount,
                    tax_rate: this.form.tax_rate,
                    items: this.form.items.filter(i => i.product_id).map(i => ({
                        product_id: i.product_id,
                        product_name: i.product_name,
                        unit_label: i.unit_label || '',
                        conversion_qty: parseFloat(i.conversion_qty) || 1,
                        qty: i.qty,
                        unit_price: i.unit_price,
                        discount_pct: i.discount_pct,
                    })),
                };

                const isCreate = this.formMode === 'create';
                const url = isCreate ? window.__routeStore : (window.__routeUpdate + this.form.id);
                const method = isCreate ? 'POST' : 'PUT';

                try {
                    const res = await fetch(url, {
                        method,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': window.__csrfToken,
                        },
                        body: JSON.stringify(payload),
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        window.location.reload();
                    } else {
                        this.formErrors = data.errors ?
                            Object.values(data.errors).flat() : [data.message || 'Terjadi kesalahan.'];
                    }
                } catch (e) {
                    this.formErrors = ['Gagal terhubung ke server.'];
                } finally {
                    this.submitting = false;
                }
            },

            /* ---- Delete ---- */
            confirmDelete(q) {
                this.deletingItem = q;
                this.deleteModal = true;
            },

            async doDelete() {
                this.submitting = true;
                try {
                    const res = await fetch(window.__routeDelete + this.deletingItem.id, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': window.__csrfToken
                        },
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        window.location.reload();
                    }
                } catch (e) {
                    /* ignore */
                } finally {
                    this.submitting = false;
                }
            },

            /* ---- Helpers ---- */
            isExpired(d, status) {
                if (!d || ['paid', 'approved', 'rejected', 'cancelled'].includes(status)) return false;
                return new Date(d) < new Date();
            },

            statusLabel(s) {
                return {
                    draft: 'Draft',
                    submit: 'Dikirim',
                    approved: 'Disetujui',
                    paid: 'Lunas',
                    rejected: 'Ditolak',
                    expired: 'Kadaluarsa',
                    cancelled: 'Dibatalkan'
                } [s] || s;
            },

            statusBadge(s) {
                const m = {
                    draft: 'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700',
                    submit: 'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700',
                    approved: 'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700',
                    paid: 'inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-700',
                    rejected: 'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700',
                    expired: 'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-700',
                    cancelled: 'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-rose-100 text-rose-700',
                };
                return m[s] || '';
            },

            fmtRupiah(n) {
                return 'Rp ' + Math.round(parseFloat(n) || 0).toLocaleString('id-ID');
            },

            fmtDate(d) {
                if (!d) return '—';
                return new Date(d).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            },
        };
    }
</script>
@endsection