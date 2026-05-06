@extends('layouts.app')

@section('title', 'Cetak Barcode')
@section('page_title', 'Cetak Barcode')
@section('page_subtitle', 'Generate dan print label barcode barang')

@section('extra_css')
<style>
    .label-card {
        border: 1px dashed #9ca3af;
        background: #fff;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 6px 4px 4px;
        box-sizing: border-box;
    }

    .label-card svg {
        max-width: 100%;
        height: auto;
        display: block;
    }

    .label-code {
        font-family: monospace;
        font-size: 9px;
        margin-top: 2px;
        text-align: center;
    }

    .label-name {
        font-size: 9px;
        text-align: center;
        margin-top: 1px;
        word-break: break-word;
        max-width: 100%;
        line-height: 1.2;
    }

    .label-price {
        font-size: 9px;
        font-weight: bold;
        text-align: center;
        margin-top: 1px;
    }
</style>
@endsection

@section('content')
<div x-data="barcodeApp()" x-init="init()">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ==================== FORM PANEL ==================== --}}
        <div class="lg:col-span-1 space-y-4">
            <div class="card space-y-4">
                <div class="card-header">Setting Barcode</div>

                {{-- Pilih Barang --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Barang</label>
                    <select x-model="productId" @change="onProductChange()" class="input-field">
                        <option value="">-- Pilih Barang --</option>
                        @foreach($allProducts as $p)
                        <option value="{{ $p->id }}"
                            data-barcode="{{ $p->barcode }}"
                            data-code="{{ $p->code }}"
                            data-name="{{ $p->name }}"
                            data-price="{{ intval($p->retail_price) }}">
                            {{ $p->code }} — {{ $p->name }}
                        </option>
                        @endforeach
                    </select>
                    <p x-show="selectedProduct" x-cloak class="text-xs text-gray-500 mt-1">
                        Barcode: <span x-text="barcodeVal()" class="font-mono font-semibold text-gray-700"></span>
                    </p>
                </div>

                {{-- Jumlah Label --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Label</label>
                    <input type="number" x-model.number="qty" min="1" max="500" class="input-field">
                </div>

                {{-- Format Barcode --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Format Barcode</label>
                    <select x-model="format" class="input-field">
                        <option value="CODE128">CODE128 (Universal)</option>
                        <option value="EAN13">EAN-13 (13 digit angka)</option>
                        <option value="CODE39">CODE39</option>
                        <option value="UPC">UPC-A (12 digit angka)</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">EAN-13 & UPC hanya untuk barcode angka</p>
                </div>

                {{-- Ukuran Kertas --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ukuran Kertas</label>
                    <select x-model="paperSize" @change="onPaperChange()" class="input-field">
                        <option value="A4">A4 (210 × 297 mm)</option>
                        <option value="A5">A5 (148 × 210 mm)</option>
                        <option value="Thermal58">Thermal 58 mm</option>
                        <option value="Thermal80">Thermal 80 mm</option>
                    </select>
                </div>

                {{-- Tata Letak --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Kolom per Halaman</label>
                    <select x-model.number="cols" class="input-field">
                        <template x-for="opt in layoutOptions" :key="opt.value">
                            <option :value="opt.value" x-text="opt.label"></option>
                        </template>
                    </select>
                </div>

                {{-- Tampilkan di Label --}}
                <div class="border-t pt-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tampilkan di Label</label>
                    <div class="space-y-1.5">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" x-model="showCode" class="w-4 h-4 rounded">
                            <span class="text-sm">Kode Barcode</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" x-model="showName" class="w-4 h-4 rounded">
                            <span class="text-sm">Nama Barang</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" x-model="showPrice" class="w-4 h-4 rounded">
                            <span class="text-sm">Harga Jual</span>
                        </label>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex gap-2 pt-1">
                    <button @click="generatePreview()" class="btn-primary flex-1">
                        <svg class="w-4 h-4 inline -mt-0.5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Preview
                    </button>
                    <button @click="printBarcodes()" class="btn-success flex-1">
                        <svg class="w-4 h-4 inline -mt-0.5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak
                    </button>
                </div>
            </div>

            {{-- Info Label --}}
            <div class="card">
                <div class="card-header">Info Label</div>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Kertas</dt>
                        <dd class="font-medium" x-text="paperLabels[paperSize]"></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Ukuran label</dt>
                        <dd class="font-medium" x-text="labelSizeLabel()"></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Total label</dt>
                        <dd class="font-medium" x-text="qty + ' lembar'"></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Estimasi halaman</dt>
                        <dd class="font-medium" x-text="estimasiHalaman() + ' halaman'"></dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- ==================== PREVIEW PANEL ==================== --}}
        <div class="lg:col-span-2">
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <div class="card-header" style="padding:0;border:none;margin:0;">Preview Label</div>
                    <span x-show="previewLabels.length" x-cloak
                        class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium"
                        x-text="previewLabels.length + ' label (maks 30 ditampilkan)'"></span>
                </div>

                {{-- Empty state --}}
                <div x-show="!previewLabels.length"
                    class="bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 flex flex-col items-center justify-center py-16 text-gray-400">
                    <svg class="w-12 h-12 mb-3 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75z" />
                    </svg>
                    <p class="text-sm font-medium">Pilih barang dan klik Preview</p>
                    <p class="text-xs mt-1 opacity-70">Label barcode akan tampil di sini</p>
                </div>

                {{-- Labels grid --}}
                <div x-show="previewLabels.length" x-cloak>
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 overflow-auto" style="max-height:420px;">
                        <div style="display:flex; flex-wrap:wrap; gap:6px;" id="preview-container">
                            <template x-for="(lbl, idx) in previewLabels" :key="idx">
                                <div class="label-card" :style="previewLabelStyle()">
                                    <svg :id="`bc-${idx}`" class="barcode-svg"></svg>
                                    <div x-show="showCode" class="label-code" x-text="lbl.barcodeVal"></div>
                                    <div x-show="showName" class="label-name" x-text="lbl.name"></div>
                                    <div x-show="showPrice" class="label-price" x-text="'Rp ' + rupiah(lbl.price)"></div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Format warning --}}
                    <div x-show="formatError" x-cloak
                        class="mt-3 bg-yellow-50 border border-yellow-200 rounded-lg p-3 flex items-start gap-2">
                        <svg class="w-4 h-4 text-yellow-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-yellow-700">Format tidak cocok</p>
                            <p class="text-xs text-yellow-600 mt-0.5" x-text="formatErrorMsg"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra_js')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
    function barcodeApp() {
        return {
            /* ── form state ─────────────────────────── */
            productId: '',
            qty: 10,
            format: 'CODE128',
            paperSize: 'A4',
            cols: 3,
            showCode: true,
            showName: true,
            showPrice: false,

            /* ── runtime ────────────────────────────── */
            selectedProduct: null,
            previewLabels: [],
            formatError: false,
            formatErrorMsg: '',
            layoutOptions: [],

            /* ── static maps ────────────────────────── */
            paperLabels: {
                A4: 'A4 (210 × 297 mm)',
                A5: 'A5 (148 × 210 mm)',
                Thermal58: 'Thermal 58 mm',
                Thermal80: 'Thermal 80 mm',
            },

            // [width, height] in mm per paper & cols
            labelSizes: {
                A4: {
                    1: [180, 50],
                    2: [96, 40],
                    3: [60, 35],
                    4: [44, 28]
                },
                A5: {
                    1: [128, 50],
                    2: [60, 40],
                    3: [38, 30]
                },
                Thermal58: {
                    1: [50, 28],
                    2: [23, 28]
                },
                Thermal80: {
                    1: [72, 30],
                    2: [33, 30]
                },
            },

            // rows-per-page estimate per paper & cols
            rowsPerPage: {
                A4: {
                    1: 6,
                    2: 8,
                    3: 10,
                    4: 12
                },
                A5: {
                    1: 4,
                    2: 6,
                    3: 8
                },
                Thermal58: {
                    1: 99,
                    2: 99
                },
                Thermal80: {
                    1: 99,
                    2: 99
                },
            },

            /* ── lifecycle ───────────────────────────── */
            init() {
                this.onPaperChange();
            },

            /* ── handlers ───────────────────────────── */
            onProductChange() {
                const sel = document.querySelector('select[x-model="productId"]');
                const opt = sel ? sel.options[sel.selectedIndex] : null;
                if (!opt || !this.productId) {
                    this.selectedProduct = null;
                    return;
                }
                this.selectedProduct = {
                    code: opt.dataset.code,
                    barcode: opt.dataset.barcode,
                    name: opt.dataset.name,
                    price: opt.dataset.price,
                };
            },

            onPaperChange() {
                const allOpts = [{
                        value: 1,
                        label: '1 Kolom'
                    },
                    {
                        value: 2,
                        label: '2 Kolom'
                    },
                    {
                        value: 3,
                        label: '3 Kolom'
                    },
                    {
                        value: 4,
                        label: '4 Kolom'
                    },
                ];
                const maxCols = {
                    A4: 4,
                    A5: 3,
                    Thermal58: 2,
                    Thermal80: 2
                };
                const max = maxCols[this.paperSize] || 4;
                this.layoutOptions = allOpts.filter(o => o.value <= max);
                if (this.cols > max) this.cols = max;
            },

            /* ── helpers ─────────────────────────────── */
            barcodeVal() {
                if (!this.selectedProduct) return '';
                return this.selectedProduct.barcode || this.selectedProduct.code;
            },

            labelSizeLabel() {
                const sizes = this.labelSizes[this.paperSize] || this.labelSizes.A4;
                const sz = sizes[this.cols] || sizes[Object.keys(sizes)[0]];
                return sz ? `${sz[0]} × ${sz[1]} mm` : '—';
            },

            estimasiHalaman() {
                const rpp = ((this.rowsPerPage[this.paperSize] || {})[this.cols]) || 10;
                return Math.max(1, Math.ceil(this.qty / (this.cols * rpp)));
            },

            previewLabelStyle() {
                const sizes = this.labelSizes[this.paperSize] || this.labelSizes.A4;
                const sz = sizes[this.cols] || sizes[Object.keys(sizes)[0]];
                if (!sz) return '';
                // 1 mm ≈ 2.2 px (screen preview, slightly scaled down)
                return `width:${Math.round(sz[0]*2.2)}px; min-height:${Math.round(sz[1]*2.2)}px;`;
            },

            rupiah(val) {
                return parseInt(val).toLocaleString('id-ID');
            },

            /* ── generate preview ─────────────────────── */
            generatePreview() {
                if (!this.selectedProduct) {
                    if (window.toastApp) window.toastApp.add('Pilih barang terlebih dahulu!', 'warning');
                    return;
                }
                this.formatError = false;
                const val = this.barcodeVal();
                const max = Math.min(this.qty, 30); // cap at 30 for screen

                this.previewLabels = Array.from({
                    length: max
                }, () => ({
                    barcodeVal: val,
                    name: this.selectedProduct.name,
                    price: this.selectedProduct.price,
                }));

                this.$nextTick(() => this.renderBarcodes());
            },

            renderBarcodes() {
                const val = this.barcodeVal();
                let errored = false;
                document.querySelectorAll('.barcode-svg').forEach(svg => {
                    try {
                        JsBarcode(svg, val, {
                            format: this.format,
                            width: 1.2,
                            height: 36,
                            displayValue: false,
                            margin: 2
                        });
                    } catch (_) {
                        try {
                            JsBarcode(svg, val, {
                                format: 'CODE128',
                                width: 1.2,
                                height: 36,
                                displayValue: false,
                                margin: 2
                            });
                        } catch (__) {
                            /* silent */ }
                        if (!errored) {
                            errored = true;
                            this.formatError = true;
                            this.formatErrorMsg = `Format ${this.format} tidak cocok dengan nilai "${val}". Barcode ditampilkan dengan CODE128.`;
                        }
                    }
                });
            },

            /* ── print ────────────────────────────────── */
            printBarcodes() {
                if (!this.selectedProduct) {
                    if (window.toastApp) window.toastApp.add('Pilih barang terlebih dahulu!', 'warning');
                    return;
                }

                const p = this.selectedProduct;
                const code = this.barcodeVal();
                const qty = parseInt(this.qty);
                const cols = parseInt(this.cols);
                const fmt = this.format;
                const showCode = this.showCode;
                const showName = this.showName;
                const showPrice = this.showPrice;

                const pageSizes = {
                    A4: '210mm 297mm',
                    A5: '148mm 210mm',
                    Thermal58: '58mm auto',
                    Thermal80: '80mm auto'
                };
                const pageSize = pageSizes[this.paperSize] || '210mm 297mm';
                const marginMm = this.paperSize.startsWith('Thermal') ? 2 : 5;

                const sizes = this.labelSizes[this.paperSize] || this.labelSizes.A4;
                const sz = sizes[cols] || sizes[Object.keys(sizes)[0]];
                const lblW = sz[0],
                    lblH = sz[1];

                let labelsHtml = '';
                for (let i = 0; i < qty; i++) {
                    labelsHtml += `<div class="lbl" id="l${i}">
                    <svg class="bc" id="bc${i}"></svg>
                    ${showCode  ? `<div class="lc">${code}</div>` : ''}
                    ${showName  ? `<div class="ln">${p.name}</div>` : ''}
                    ${showPrice ? `<div class="lp">Rp ${parseInt(p.price).toLocaleString('id-ID')}</div>` : ''}
                </div>`;
                }

                const win = window.open('', '_blank');
                if (!win) {
                    if (window.toastApp) window.toastApp.add('Popup diblokir browser. Aktifkan popup untuk mencetak.', 'error');
                    return;
                }

                win.document.write(`<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Barcode — ${p.name}</title>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"><\/script>
<style>
@page { size:${pageSize}; margin:${marginMm}mm; }
*   { box-sizing:border-box; margin:0; padding:0; }
body{ font-family:Arial,sans-serif; background:#fff; }
.wrap { display:flex; flex-wrap:wrap; gap:1.5mm; }
.lbl {
    width:${lblW}mm; min-height:${lblH}mm;
    border:.3pt dashed #aaa;
    display:flex; flex-direction:column;
    align-items:center; justify-content:center;
    padding:1.5mm 1mm 1mm;
    overflow:hidden; page-break-inside:avoid;
}
.lbl .bc { max-width:100%; height:auto; display:block; }
.lc { font-family:monospace; font-size:6pt; margin-top:1mm; text-align:center; }
.ln { font-size:6.5pt; text-align:center; margin-top:.5mm; line-height:1.2; word-break:break-word; max-width:100%; }
.lp { font-size:7pt; font-weight:bold; text-align:center; margin-top:.5mm; }
@media print{ body{ -webkit-print-color-adjust:exact; print-color-adjust:exact; } }
</style>
</head>
<body>
<div class="wrap">${labelsHtml}</div>
<script>
window.onload = function(){
    document.querySelectorAll('.bc').forEach(function(s){
        try{ JsBarcode(s,'${code}',{format:'${fmt}',width:1.1,height:28,displayValue:false,margin:1}); }
        catch(e){ JsBarcode(s,'${code}',{format:'CODE128',width:1.1,height:28,displayValue:false,margin:1}); }
    });
    setTimeout(function(){ window.print(); }, 700);
};
<\/script>
</body>
</html>`);
                win.document.close();
            },
        };
    }
</script>
@endsection