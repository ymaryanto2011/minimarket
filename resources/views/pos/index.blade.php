@extends('layouts.app')

@section('title', 'Kasir POS')
@section('page_title', 'Kasir POS')
@section('page_subtitle', 'Transaksi Penjualan Real-time')

{{-- Fullscreen toggle button injected into layout header --}}
@section('header_actions')
<button id="btn-fullscreen" onclick="toggleFullscreen()"
    class="flex items-center gap-2 px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 active:scale-95 transition text-sm font-medium select-none"
    title="Layar Penuh (F11)">
    <svg id="icon-fs-enter" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
    </svg>
    <svg id="icon-fs-exit" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25" />
    </svg>
    <span id="fs-label">Layar Penuh</span>
</button>
@endsection

@section('content')
{{-- =====================================================================
     MAIN POS INTERFACE
===================================================================== --}}
<div class="flex flex-col" style="height:100%">

    {{-- ── MAIN ROW ────────────────────────────────────────────────── --}}
    <div class="flex flex-1 overflow-hidden">

        {{-- ════════════════════════════════════════════════════════
             LEFT PANEL  65% — Scan + Cart
        ════════════════════════════════════════════════════════ --}}
        <div class="flex flex-col bg-white border-r border-gray-200" style="width:65%; min-width:0">

            {{-- Barcode / Search Bar --}}
            <div class="p-3 bg-gray-50 border-b flex-shrink-0">
                <div class="relative">
                    <div class="flex gap-2">
                        <div class="flex-1 relative">
                            <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <input type="text" id="barcode-input" autocomplete="off" spellcheck="false"
                                class="w-full pl-10 pr-4 py-2.5 border-2 border-blue-400 rounded-lg text-sm
                                          focus:outline-none focus:border-blue-600 bg-white"
                                placeholder="Scan Barcode / Ketik nama barang... [F2]">
                        </div>
                        <button onclick="clearCart()" id="btn-clear"
                            class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium transition"
                            title="F12 – Batalkan Transaksi">
                            <svg class="w-4 h-4 inline -mt-0.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>Batal [F12]
                        </button>
                    </div>
                    {{-- Search Dropdown --}}
                    <div id="search-results"
                        class="absolute left-0 right-0 top-full mt-1 z-50 bg-white border border-gray-200
                                rounded-xl shadow-2xl max-h-72 overflow-y-auto hidden">
                    </div>
                </div>
            </div>

            {{-- Cart Table --}}
            <div class="flex-1 overflow-y-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 border-b-2 border-gray-200 sticky top-0 z-10">
                        <tr>
                            <th class="py-2 px-3 text-left text-xs text-gray-500 font-semibold w-8">#</th>
                            <th class="py-2 px-3 text-left text-xs text-gray-500 font-semibold">NAMA BARANG</th>
                            <th class="py-2 px-3 text-right text-xs text-gray-500 font-semibold w-28">HARGA</th>
                            <th class="py-2 px-3 text-center text-xs text-gray-500 font-semibold w-36">QTY</th>
                            <th class="py-2 px-3 text-right text-xs text-gray-500 font-semibold w-28">SUBTOTAL</th>
                            <th class="py-2 px-3 w-8"></th>
                        </tr>
                    </thead>
                    <tbody id="cart-body">
                        <tr id="row-empty">
                            <td colspan="6" class="text-center py-16 text-gray-400">
                                <svg class="w-14 h-14 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="font-medium">Keranjang kosong</p>
                                <p class="text-xs mt-1">Scan barcode atau cari nama barang di atas</p>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot id="cart-foot" class="hidden bg-gray-50 border-t-2 border-gray-300 sticky bottom-0">
                        <tr>
                            <td colspan="3" class="py-2 px-3 text-right text-xs text-gray-500">
                                <span id="foot-count">0</span> jenis barang
                            </td>
                            <td class="py-2 px-3 text-center font-bold text-gray-600 text-xs">
                                Total: <span id="foot-qty">0</span> pcs
                            </td>
                            <td class="py-2 px-3 text-right font-bold text-gray-700">
                                <span id="foot-subtotal">Rp 0</span>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>{{-- / LEFT PANEL --}}

        {{-- ════════════════════════════════════════════════════════
             RIGHT PANEL  35% — Payment
        ════════════════════════════════════════════════════════ --}}
        <div class="flex flex-col bg-gray-50" style="width:35%; min-width:260px">

            {{-- Invoice header --}}
            <div class="px-4 py-2.5 border-b bg-white flex-shrink-0">
                <div class="flex justify-between text-xs text-gray-500">
                    <span>No: <strong id="inv-no" class="text-gray-800">AUTO</strong></span>
                    <span id="pos-clock" class="font-mono font-medium text-gray-700"></span>
                </div>
                <p class="text-xs text-gray-400 mt-0.5">
                    Kasir: <strong class="text-gray-600">{{ $storeProfile->name ?? config('app.name') }}</strong>
                </p>
            </div>

            {{-- Scrollable payment body --}}
            <div class="flex-1 overflow-y-auto p-3 space-y-3">

                {{-- Subtotal / Discount / Tax --}}
                <div class="bg-white rounded-xl border p-3 space-y-2.5 shadow-sm">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-semibold" id="s-subtotal">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-sm items-center">
                        <span class="text-gray-500">Diskon (Rp)</span>
                        <input type="number" id="discount-input" value="0" min="0"
                            class="w-28 text-right border border-gray-300 rounded-lg px-2 py-1 text-sm
                                      focus:outline-none focus:border-blue-400"
                            oninput="recalculate()">
                    </div>
                    <div class="flex justify-between text-sm items-center border-t pt-2">
                        <label class="text-gray-500 flex items-center gap-1.5 cursor-pointer select-none">
                            <input type="checkbox" id="tax-toggle" onchange="recalculate()"
                                class="w-3.5 h-3.5 rounded accent-orange-500">
                            Pajak PPN (11%)
                        </label>
                        <span class="font-medium text-orange-500" id="s-tax">Rp 0</span>
                    </div>
                </div>

                {{-- TOTAL (hero) --}}
                <div class="bg-gradient-to-br from-blue-600 to-blue-800 text-white rounded-xl p-4 text-center shadow-lg">
                    <p class="text-xs uppercase tracking-widest opacity-70 mb-1">Total Pembayaran</p>
                    <p id="s-total" class="text-5xl font-extrabold tracking-tight leading-none">Rp 0</p>
                </div>

                {{-- Payment method --}}
                <div class="bg-white rounded-xl border p-3 shadow-sm">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Metode Pembayaran</p>
                    <div class="grid grid-cols-3 gap-1.5">
                        <button onclick="selectMethod('cash')" data-method="cash"
                            class="pay-btn active-method py-2 rounded-lg border-2 text-sm font-semibold transition">Tunai</button>
                        <button onclick="selectMethod('transfer')" data-method="transfer"
                            class="pay-btn py-2 rounded-lg border-2 text-sm font-semibold transition">Transfer</button>
                        <button onclick="selectMethod('qris')" data-method="qris"
                            class="pay-btn py-2 rounded-lg border-2 text-sm font-semibold transition">QRIS</button>
                    </div>
                </div>

                {{-- Paid amount + change --}}
                <div class="bg-white rounded-xl border p-3 shadow-sm space-y-2.5">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Nominal Bayar</p>
                        <input type="number" id="paid-input" value="" min="0"
                            class="w-full border-2 border-gray-300 rounded-lg px-3 py-3 text-2xl font-bold text-right
                                      focus:outline-none focus:border-blue-500 transition"
                            oninput="recalculate()" placeholder="0">
                    </div>
                    {{-- Quick cash --}}
                    <div id="quick-cash" class="grid grid-cols-3 gap-1">
                        <button onclick="setExact()" class="qc-btn bg-emerald-100 text-emerald-700 hover:bg-emerald-200 font-bold">Pas</button>
                        <button onclick="addPaid(50000)" class="qc-btn bg-gray-100 text-gray-700 hover:bg-gray-200">+50K</button>
                        <button onclick="addPaid(100000)" class="qc-btn bg-gray-100 text-gray-700 hover:bg-gray-200">+100K</button>
                        <button onclick="addPaid(20000)" class="qc-btn bg-gray-100 text-gray-700 hover:bg-gray-200">+20K</button>
                        <button onclick="addPaid(10000)" class="qc-btn bg-gray-100 text-gray-700 hover:bg-gray-200">+10K</button>
                        <button onclick="addPaid(5000)" class="qc-btn bg-gray-100 text-gray-700 hover:bg-gray-200">+5K</button>
                    </div>
                    {{-- Change --}}
                    <div class="flex justify-between items-center border-t pt-2.5">
                        <span class="text-sm font-semibold text-gray-600">Kembalian</span>
                        <span id="s-change" class="text-3xl font-extrabold text-emerald-600">Rp 0</span>
                    </div>
                </div>

            </div>{{-- / scrollable body --}}

            {{-- Pay Button (sticky bottom) --}}
            <div class="p-3 border-t bg-white flex-shrink-0">
                <button id="btn-pay" onclick="processCheckout()"
                    class="w-full py-4 rounded-xl text-white text-lg font-bold shadow-lg
                               bg-emerald-500 hover:bg-emerald-600 active:scale-[.98] transition
                               disabled:bg-gray-300 disabled:cursor-not-allowed disabled:shadow-none"
                    title="Enter / F1 — Bayar & Cetak" disabled>
                    <svg class="w-5 h-5 inline -mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Bayar &amp; Cetak &nbsp;[Enter / F1]
                </button>
            </div>
        </div>{{-- / RIGHT PANEL --}}

    </div>{{-- / MAIN ROW --}}

    {{-- ── SHORTCUTS BAR ──────────────────────────────────────────── --}}
    <div class="bg-gray-900 text-gray-400 px-4 py-1.5 flex flex-wrap gap-x-5 gap-y-1 text-xs flex-shrink-0">
        <span><kbd class="pos-kbd">F1</kbd> Bayar</span>
        <span><kbd class="pos-kbd">F2</kbd> Fokus Barcode</span>
        <span><kbd class="pos-kbd">F3</kbd> Hold</span>
        <span><kbd class="pos-kbd">F11</kbd> Layar Penuh</span>
        <span><kbd class="pos-kbd">F12</kbd> Batal Transaksi</span>
        <span><kbd class="pos-kbd">Enter</kbd> Konfirmasi</span>
        <span><kbd class="pos-kbd">Esc</kbd> Tutup Dropdown</span>
        <span class="ml-auto text-gray-600" id="trx-counter">Transaksi: 0</span>
    </div>

</div>{{-- / POS WRAPPER --}}


{{-- =====================================================================
     RECEIPT  (invisible — shown only on print)
===================================================================== --}}
<div id="receipt-wrap" class="hidden">
    <div id="receipt">
        <div style="text-align:center;border-bottom:2px dashed #000;padding-bottom:6px;margin-bottom:6px">
            <strong id="r-store-name" style="font-size:14px">{{ $storeProfile->name ?? config('app.name') }}</strong><br>
            <span id="r-store-addr">{{ $storeProfile->address ?? '' }}</span><br>
            <span id="r-store-phone">{{ ($storeProfile->phone ?? '') ? 'Telp: '.($storeProfile->phone ?? '') : '' }}</span>
        </div>
        <table style="width:100%;font-size:11px;margin-bottom:6px">
            <tr>
                <td>No. Trans</td>
                <td style="text-align:right" id="r-inv"></td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td style="text-align:right" id="r-date"></td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td style="text-align:right">{{ auth()->user()->name ?? 'Admin' }}</td>
            </tr>
        </table>
        <div style="border-top:1px dashed #000;border-bottom:1px dashed #000;padding:4px 0;margin-bottom:6px">
            <table id="r-items" style="width:100%;font-size:10px;border-collapse:collapse"></table>
        </div>
        <table id="r-summary" style="width:100%;font-size:11px;margin-bottom:6px"></table>
        <div style="border-top:1px dashed #000;padding-top:6px;text-align:center;font-size:10px;line-height:1.6">
            <p>--- Terima Kasih Atas Kunjungan Anda ---</p>
            <p>Barang yang sudah dibeli tidak dapat</p>
            <p>ditukar / dikembalikan.</p>
        </div>
    </div>
</div>

@endsection

{{-- =====================================================================
     EXTRA CSS
===================================================================== --}}
@section('extra_css')
<style>
    /* ── POS content area: remove padding & prevent outer scroll ── */
    #content-area {
        padding: 0 !important;
        overflow: hidden !important;
    }

    /* ── Fullscreen: hide sidebar with smooth animation ── */
    body.fullscreen-mode #app-sidebar {
        width: 0 !important;
        min-width: 0 !important;
        overflow: hidden !important;
        padding: 0 !important;
    }

    /* ── Quick-cash buttons ── */
    .qc-btn {
        border-radius: 6px;
        padding: 5px 0;
        font-size: 11px;
        font-weight: 600;
        text-align: center;
        cursor: pointer;
        transition: background .15s;
    }

    /* ── Payment method buttons ── */
    .pay-btn {
        border-color: #e5e7eb;
        color: #4b5563;
    }

    .pay-btn.active-method {
        border-color: #2563eb !important;
        background: #eff6ff !important;
        color: #1d4ed8 !important;
    }

    .pay-btn:hover:not(.active-method) {
        background: #f9fafb;
    }

    /* ── Keyboard shortcut badge ── */
    .pos-kbd {
        display: inline-block;
        background: #4b5563;
        border: 1px solid #6b7280;
        border-bottom-width: 2px;
        border-radius: 4px;
        padding: 0 5px;
        font-family: ui-monospace, monospace;
        font-size: 10px;
        color: #f3f4f6;
        line-height: 18px;
    }

    /* ── Search dropdown item ── */
    .si {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f3f4f6;
    }

    .si:last-child {
        border-bottom: none;
    }

    .si:hover {
        background: #eff6ff;
    }

    /* ── Print: show only receipt ── */
    @media print {
        * {
            visibility: hidden !important;
        }

        #receipt-wrap,
        #receipt-wrap * {
            visibility: visible !important;
        }

        #receipt-wrap {
            display: block !important;
            position: fixed;
            top: 0;
            left: 0;
            width: 80mm;
            padding: 3mm;
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            color: #000;
            background: #fff;
        }

        @page {
            size: 80mm auto;
            margin: 0;
        }
    }
</style>
@endsection

{{-- =====================================================================
     EXTRA JS
===================================================================== --}}
@section('extra_js')
<script>
    /* ====================================================================
   STATE
==================================================================== */
    let cart = [];
    let payMethod = 'cash';
    let trxCount = 0;
    const CSRF = '{{ csrf_token() }}';

    /* ====================================================================
       CLOCK (update every second)
    ==================================================================== */
    function tick() {
        const now = new Date();
        const pad = n => String(n).padStart(2, '0');
        const str = `${pad(now.getDate())}/${pad(now.getMonth()+1)}/${now.getFullYear()} ` +
            `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
        const el = document.getElementById('pos-clock');
        if (el) el.textContent = str;
        const hdr = document.getElementById('current-time');
        if (hdr) hdr.textContent = now.toLocaleString('id-ID', {
            weekday: 'short',
            day: 'numeric',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }
    tick();
    setInterval(tick, 1000);

    /* ====================================================================
       FULLSCREEN
    ==================================================================== */
    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(() => {});
        } else {
            document.exitFullscreen();
        }
    }
    document.addEventListener('fullscreenchange', () => {
        const on = !!document.fullscreenElement;
        document.body.classList.toggle('fullscreen-mode', on);
        const lbl = document.getElementById('fs-label');
        const iEnter = document.getElementById('icon-fs-enter');
        const iExit = document.getElementById('icon-fs-exit');
        if (lbl) lbl.textContent = on ? 'Keluar Penuh' : 'Layar Penuh';
        if (iEnter) iEnter.classList.toggle('hidden', on);
        if (iExit) iExit.classList.toggle('hidden', !on);
    });

    /* ====================================================================
       BARCODE / SEARCH
    ==================================================================== */
    let debounce = null;

    document.getElementById('barcode-input').addEventListener('input', function() {
        clearTimeout(debounce);
        const q = this.value.trim();
        if (!q) {
            hideDropdown();
            return;
        }
        debounce = setTimeout(() => doSearch(q, false), 280);
    });

    document.getElementById('barcode-input').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const q = this.value.trim();
            if (q) doSearch(q, true);
        }
        if (e.key === 'Escape') hideDropdown();
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            const first = document.querySelector('#search-results .si');
            if (first) first.focus();
        }
    });

    async function doSearch(q, autoAdd) {
        try {
            const res = await fetch(`/pos/search?q=${encodeURIComponent(q)}`);
            const data = await res.json();
            if (autoAdd && data.length === 1) {
                addToCart(data[0]);
                document.getElementById('barcode-input').value = '';
                hideDropdown();
            } else {
                renderDropdown(data);
            }
        } catch {
            /* network error */
        }
    }

    function renderDropdown(products) {
        const el = document.getElementById('search-results');
        if (!products.length) {
            el.innerHTML = '<div class="px-4 py-3 text-sm text-gray-400">Barang tidak ditemukan</div>';
        } else {
            el.innerHTML = products.map(p => `
            <div class="si" tabindex="0"
                 onclick="pickProduct(${p.id})"
                 onkeydown="if(event.key==='Enter')pickProduct(${p.id})">
                <div>
                    <p class="text-sm font-semibold">${esc(p.name)}</p>
                    <p class="text-xs text-gray-400">${esc(p.code||'')} &bull; Stok: ${p.stock} ${esc(p.unit||'')}</p>
                </div>
                <div class="text-right ml-3 shrink-0">
                    <p class="text-sm font-bold text-blue-600">${rp(p.retail_price)}</p>
                    ${p.wholesale_price && p.wholesale_price < p.retail_price
                        ? `<p class="text-xs text-purple-500">Grosir&ge;${p.min_wholesale_qty}: ${rp(p.wholesale_price)}</p>`
                        : ''}
                </div>
            </div>`).join('');
            el._data = products;
        }
        el.classList.remove('hidden');
    }

    function pickProduct(id) {
        const el = document.getElementById('search-results');
        const p = (el._data || []).find(x => x.id == id);
        if (p) {
            addToCart(p);
            document.getElementById('barcode-input').value = '';
            hideDropdown();
            focusBarcode();
        }
    }

    function hideDropdown() {
        document.getElementById('search-results').classList.add('hidden');
    }

    document.addEventListener('click', e => {
        if (!e.target.closest('#barcode-input') && !e.target.closest('#search-results')) hideDropdown();
    });

    function focusBarcode() {
        const el = document.getElementById('barcode-input');
        if (el) {
            el.focus();
            el.select();
        }
    }

    /* ====================================================================
       CART MANAGEMENT
    ==================================================================== */
    function addToCart(product) {
        const existing = cart.find(i => i.id === product.id);
        if (existing) {
            if (existing.qty >= product.stock) {
                showToast(`Stok ${product.name} tidak cukup (tersisa: ${product.stock})`, 'warning');
                return;
            }
            existing.qty++;
            existing.price = calcPrice(existing, existing.qty);
        } else {
            cart.push({
                id: product.id,
                code: product.code || '',
                name: product.name,
                unit: product.unit || '',
                retail_price: product.retail_price,
                wholesale_price: product.wholesale_price || product.retail_price,
                min_wholesale_qty: product.min_wholesale_qty || 9999,
                price: product.retail_price,
                qty: 1,
                stock: product.stock,
            });
        }
        renderCart();
        recalculate();
    }

    function calcPrice(item, qty) {
        return (item.wholesale_price < item.retail_price && qty >= item.min_wholesale_qty) ?
            item.wholesale_price :
            item.retail_price;
    }

    function updateQty(id, delta) {
        const item = cart.find(i => i.id == id);
        if (!item) return;
        const nq = item.qty + delta;
        if (nq <= 0) {
            removeItem(id);
            return;
        }
        if (nq > item.stock) {
            showToast(`Stok ${item.name} tidak cukup (tersisa: ${item.stock})`, 'warning');
            return;
        }
        item.qty = nq;
        item.price = calcPrice(item, nq);
        renderCart();
        recalculate();
    }

    function setQty(id, val) {
        const item = cart.find(i => i.id == id);
        if (!item) return;
        const qty = parseInt(val);
        if (!qty || qty <= 0) {
            removeItem(id);
            return;
        }
        if (qty > item.stock) {
            showToast(`Stok ${item.name} tidak cukup (tersisa: ${item.stock})`, 'warning');
            return;
        }
        item.qty = qty;
        item.price = calcPrice(item, qty);
        renderCart();
        recalculate();
    }

    function removeItem(id) {
        cart = cart.filter(i => i.id != id);
        renderCart();
        recalculate();
    }

    function clearCart() {
        if (cart.length > 0 && !confirm('Batalkan semua item?')) return;
        cart = [];
        document.getElementById('discount-input').value = '0';
        document.getElementById('paid-input').value = '';
        document.getElementById('tax-toggle').checked = false;
        renderCart();
        recalculate();
        focusBarcode();
    }

    function renderCart() {
        const tbody = document.getElementById('cart-body');
        const foot = document.getElementById('cart-foot');
        const empty = document.getElementById('row-empty');

        if (cart.length === 0) {
            tbody.innerHTML = '';
            if (empty) {
                empty.style.display = '';
                tbody.appendChild(empty);
            }
            foot.classList.add('hidden');
            return;
        }
        if (empty) empty.style.display = 'none';
        foot.classList.remove('hidden');

        let tQty = 0,
            tSub = 0;
        tbody.innerHTML = cart.map((item, i) => {
            const sub = item.price * item.qty;
            tQty += item.qty;
            tSub += sub;
            const ws = item.qty >= item.min_wholesale_qty && item.wholesale_price < item.retail_price;
            return `
        <tr class="border-b hover:bg-blue-50/40 transition-colors">
            <td class="py-1.5 px-3 text-xs text-gray-400">${i+1}</td>
            <td class="py-1.5 px-3">
                <p class="font-semibold text-sm leading-tight">${esc(item.name)}</p>
                <p class="text-xs text-gray-400">${esc(item.code)}${item.unit?' &bull; '+esc(item.unit):''}</p>
                ${ws?'<span class="text-xs bg-purple-100 text-purple-700 px-1 rounded">Grosir</span>':''}
            </td>
            <td class="py-1.5 px-3 text-right text-sm">${rp(item.price)}</td>
            <td class="py-1.5 px-3">
                <div class="flex items-center justify-center gap-1">
                    <button onclick="updateQty(${item.id},-1)"
                            class="w-7 h-7 bg-gray-100 hover:bg-red-100 rounded font-bold text-sm leading-none transition">&minus;</button>
                    <input type="number" value="${item.qty}" min="1" max="${item.stock}"
                           class="w-14 text-center border border-gray-300 rounded text-sm py-0.5 font-bold"
                           onchange="setQty(${item.id},this.value)" onclick="this.select()">
                    <button onclick="updateQty(${item.id},1)"
                            class="w-7 h-7 bg-gray-100 hover:bg-green-100 rounded font-bold text-sm leading-none transition">+</button>
                </div>
            </td>
            <td class="py-1.5 px-3 text-right font-bold text-sm">${rp(sub)}</td>
            <td class="py-1.5 px-3 text-center">
                <button onclick="removeItem(${item.id})"
                        class="text-red-300 hover:text-red-600 transition">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                    </svg>
                </button>
            </td>
        </tr>`;
        }).join('');

        document.getElementById('foot-count').textContent = cart.length;
        document.getElementById('foot-qty').textContent = tQty;
        document.getElementById('foot-subtotal').textContent = rp(tSub);
    }

    /* ====================================================================
       RECALCULATE
    ==================================================================== */
    function recalculate() {
        const subtotal = cart.reduce((s, i) => s + i.price * i.qty, 0);
        const discount = parseFloat(document.getElementById('discount-input').value) || 0;
        const taxOn = document.getElementById('tax-toggle').checked;
        const afterDisc = Math.max(0, subtotal - discount);
        const tax = taxOn ? Math.round(afterDisc * 0.11) : 0;
        const total = afterDisc + tax;
        const paid = parseFloat(document.getElementById('paid-input').value) || 0;
        const change = paid - total;

        document.getElementById('s-subtotal').textContent = rp(subtotal);
        document.getElementById('s-tax').textContent = rp(tax);
        document.getElementById('s-total').textContent = rp(total);

        const chEl = document.getElementById('s-change');
        chEl.textContent = change >= 0 ? rp(change) : `Kurang ${rp(Math.abs(change))}`;
        chEl.className = `text-3xl font-extrabold ${change >= 0 ? 'text-emerald-600' : 'text-red-500'}`;

        const canPay = cart.length > 0 && (payMethod !== 'cash' || paid >= total);
        document.getElementById('btn-pay').disabled = !canPay;
    }

    /* ====================================================================
       PAYMENT METHOD
    ==================================================================== */
    function selectMethod(method) {
        payMethod = method;
        document.querySelectorAll('.pay-btn').forEach(b => b.classList.remove('active-method'));
        const active = document.querySelector(`.pay-btn[data-method="${method}"]`);
        if (active) active.classList.add('active-method');
        document.getElementById('quick-cash').style.display = method === 'cash' ? 'grid' : 'none';
        if (method !== 'cash') setExact();
        recalculate();
    }

    function setExact() {
        const subtotal = cart.reduce((s, i) => s + i.price * i.qty, 0);
        const discount = parseFloat(document.getElementById('discount-input').value) || 0;
        const taxOn = document.getElementById('tax-toggle').checked;
        const afterDisc = Math.max(0, subtotal - discount);
        const tax = taxOn ? Math.round(afterDisc * 0.11) : 0;
        document.getElementById('paid-input').value = afterDisc + tax;
        recalculate();
    }

    function addPaid(amount) {
        const cur = parseFloat(document.getElementById('paid-input').value) || 0;
        document.getElementById('paid-input').value = cur + amount;
        recalculate();
    }

    /* ====================================================================
       CHECKOUT
    ==================================================================== */
    async function processCheckout() {
        if (!cart.length) {
            focusBarcode();
            return;
        }

        const subtotal = cart.reduce((s, i) => s + i.price * i.qty, 0);
        const discount = parseFloat(document.getElementById('discount-input').value) || 0;
        const taxOn = document.getElementById('tax-toggle').checked;
        const afterDisc = Math.max(0, subtotal - discount);
        const tax = taxOn ? Math.round(afterDisc * 0.11) : 0;
        const total = afterDisc + tax;
        const paid = parseFloat(document.getElementById('paid-input').value) || 0;

        if (payMethod === 'cash' && paid < total) {
            showToast('Nominal bayar kurang. Silakan masukkan jumlah yang cukup.', 'warning');
            document.getElementById('paid-input').focus();
            return;
        }

        const btn = document.getElementById('btn-pay');
        btn.disabled = true;
        btn.textContent = 'Memproses\u2026';

        try {
            const res = await fetch('/pos/checkout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    items: cart.map(i => ({
                        id: i.id,
                        qty: i.qty
                    })),
                    payment_method: payMethod,
                    paid_amount: paid,
                    discount: discount,
                }),
            });

            if (!res.ok) {
                const err = await res.json().catch(() => ({
                    message: 'Transaksi gagal'
                }));
                throw new Error(err.message || 'Transaksi gagal');
            }

            const result = await res.json();
            trxCount++;
            document.getElementById('trx-counter').textContent = `Transaksi: ${trxCount}`;

            buildReceipt(result.invoice_no, total, discount, tax, paid);
            window.print();

            cart = [];
            document.getElementById('discount-input').value = '0';
            document.getElementById('paid-input').value = '';
            document.getElementById('tax-toggle').checked = false;
            renderCart();
            recalculate();
            focusBarcode();

        } catch (err) {
            showToast('Error: ' + err.message, 'error');
            recalculate();
        }
    }

    /* ====================================================================
       RECEIPT BUILDER
    ==================================================================== */
    function buildReceipt(invoiceNo, total, discount, tax, paid) {
        const now = new Date();
        const dateStr = now.toLocaleString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        const subtotal = cart.reduce((s, i) => s + i.price * i.qty, 0);

        document.getElementById('r-inv').textContent = invoiceNo || '\u2014';
        document.getElementById('r-date').textContent = dateStr;

        document.getElementById('r-items').innerHTML = `
        <tr style="border-bottom:1px dashed #000;font-weight:bold">
            <td style="padding:2px 0">Barang</td>
            <td style="text-align:right;padding:2px 0">Qty</td>
            <td style="text-align:right;padding:2px 0">Harga</td>
            <td style="text-align:right;padding:2px 0">Sub</td>
        </tr>
        ${cart.map(i => `
        <tr>
            <td style="padding:2px 0;word-break:break-all">${esc(i.name)}</td>
            <td style="text-align:right;white-space:nowrap;padding:2px 0">${i.qty}&nbsp;${esc(i.unit)}</td>
            <td style="text-align:right;white-space:nowrap;padding:2px 0">${rp(i.price)}</td>
            <td style="text-align:right;white-space:nowrap;padding:2px 0">${rp(i.price*i.qty)}</td>
        </tr>`).join('')}`;

        const mtd = {
            cash: 'Tunai',
            transfer: 'Transfer',
            qris: 'QRIS'
        };
        document.getElementById('r-summary').innerHTML = `
        <tr><td>Subtotal</td><td style="text-align:right">${rp(subtotal)}</td></tr>
        ${discount > 0 ? `<tr><td>Diskon</td><td style="text-align:right">-${rp(discount)}</td></tr>` : ''}
        ${tax > 0      ? `<tr><td>Pajak PPN 11%</td><td style="text-align:right">${rp(tax)}</td></tr>` : ''}
        <tr style="font-weight:bold;border-top:1px solid #000">
            <td style="padding-top:3px">TOTAL</td>
            <td style="text-align:right;padding-top:3px">${rp(total)}</td>
        </tr>
        <tr><td>Metode</td><td style="text-align:right">${mtd[payMethod]||payMethod}</td></tr>
        <tr><td>Bayar</td><td style="text-align:right">${rp(paid)}</td></tr>
        <tr style="font-weight:bold">
            <td>Kembalian</td>
            <td style="text-align:right">${rp(Math.max(0, paid - total))}</td>
        </tr>`;

        document.getElementById('receipt-wrap').style.display = 'block';
    }

    /* ====================================================================
       KEYBOARD SHORTCUTS
    ==================================================================== */
    document.addEventListener('keydown', e => {
        const tag = e.target.tagName;
        if (e.key === 'F1') {
            e.preventDefault();
            processCheckout();
        }
        if (e.key === 'F2') {
            e.preventDefault();
            focusBarcode();
        }
        if (e.key === 'F3') {
            e.preventDefault();
            showToast('Fitur Hold belum tersedia.', 'info');
        }
        if (e.key === 'F11') {
            e.preventDefault();
            toggleFullscreen();
        }
        if (e.key === 'F12') {
            e.preventDefault();
            clearCart();
        }
        if (e.key === 'Escape') {
            // If fullscreen, prevent exit fullscreen, just close dropdown
            if (document.fullscreenElement) {
                e.preventDefault();
                hideDropdown();
                // Optionally, close modal here if needed
            } else {
                hideDropdown();
            }
        }
        if (e.key === 'Enter' && tag !== 'INPUT' && tag !== 'BUTTON' && tag !== 'SELECT') {
            e.preventDefault();
            processCheckout();
        }
    });

    /* ====================================================================
       HELPERS
    ==================================================================== */
    function rp(n) {
        return 'Rp\u00a0' + Math.round(n || 0).toLocaleString('id-ID');
    }

    function esc(s) {
        return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    /* init */
    focusBarcode();
    recalculate();
</script>
@endsection