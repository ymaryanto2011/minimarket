@extends("layouts.app")
@section("title", "Master Barang")
@section("page_title", "Master Barang")
@section("page_subtitle", "Kelola data produk")

@section("content")
{{-- Flash messages handled globally by toast system in layouts/app.blade.php --}}

<div class="card mb-4">
    <div class="flex flex-wrap items-center gap-3">
        {{-- Search form --}}
        <form method="GET" class="flex gap-3 flex-wrap flex-1 min-w-0">
            <input type="text" name="search" value="{{ request("search") }}" placeholder="Cari nama/kode/barcode..." class="input-field flex-1 min-w-48">
            <select name="category" class="input-field w-48">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request("category") == $cat->id ? "selected" : "" }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary">Cari</button>
        </form>

        {{-- Action buttons (outside form to avoid layout clipping) --}}
        <div class="flex gap-2 flex-shrink-0">
            <a href="{{ route("master.create") }}" class="btn-success">+ Tambah Produk</a>
            <button type="button" onclick="document.getElementById('modal-import').classList.remove('hidden')"
                style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-weight:600;font-size:0.875rem;background:#059669;color:#fff;border:none;cursor:pointer;transition:background .2s;"
                onmouseover="this.style.background='#047857'" onmouseout="this.style.background='#059669'">
                <svg style="width:16px;height:16px;flex-shrink:0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Import Excel
            </button>
        </div>
    </div>
</div>

{{-- ── Import Modal ──────────────────────────────────────────────────────── --}}
<div id="modal-import"
    class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
    onclick="if(event.target===this)this.classList.add('hidden')">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6" onclick="event.stopPropagation()">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Import Master Barang</h3>
                    <p class="text-xs text-gray-500">Upload file Excel untuk input barang secara massal</p>
                </div>
            </div>
            <button onclick="document.getElementById('modal-import').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Panduan singkat --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-5 text-sm text-blue-800 space-y-1.5">
            <p class="font-semibold text-blue-700 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                Petunjuk Pengisian
            </p>
            <ul class="list-disc list-inside space-y-1 text-xs">
                <li>Unduh template terlebih dahulu, isi data mulai baris ke-5</li>
                <li><strong>Kode Produk</strong> boleh dikosongkan — akan digenerate otomatis</li>
                <li><strong>Kategori</strong> & <strong>Satuan</strong> wajib diisi. Kategori baru akan dibuat otomatis</li>
                <li>Jika kode sudah ada, data produk akan <strong>diperbarui</strong> (tidak duplikat)</li>
                <li>Stok awal dicatat sebagai mutasi stok masuk</li>
                <li>Format file: <strong>.xlsx</strong>, <strong>.xls</strong>, atau <strong>.csv</strong> (maks. 5 MB)</li>
            </ul>
        </div>

        {{-- Download template --}}
        <a href="{{ route('master.import-template') }}"
            class="flex items-center gap-3 border-2 border-dashed border-emerald-300 rounded-xl px-4 py-3 mb-5 hover:bg-emerald-50 transition group">
            <div class="w-9 h-9 rounded-lg bg-emerald-100 group-hover:bg-emerald-200 flex items-center justify-center flex-shrink-0 transition">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
            </div>
            <div>
                <p class="font-semibold text-emerald-700 text-sm">Unduh Template Excel</p>
                <p class="text-xs text-gray-500">template_import_barang.xlsx — sudah berisi contoh data & referensi</p>
            </div>
        </a>

        {{-- Upload form --}}
        <form method="POST" action="{{ route('master.import') }}" enctype="multipart/form-data" id="form-import">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Pilih File Excel</label>
                <div class="relative border-2 border-dashed border-gray-300 rounded-xl px-4 py-5 text-center hover:border-blue-400 transition"
                    id="drop-zone"
                    ondragover="event.preventDefault();this.classList.add('border-blue-400','bg-blue-50')"
                    ondragleave="this.classList.remove('border-blue-400','bg-blue-50')"
                    ondrop="handleDrop(event)">
                    <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-sm text-gray-600" id="drop-label">Seret file ke sini atau <span class="text-blue-600 font-semibold cursor-pointer" onclick="document.getElementById('import-file').click()">klik untuk memilih</span></p>
                    <p class="text-xs text-gray-400 mt-1">.xlsx, .xls, .csv — maks 5 MB</p>
                    <input type="file" id="import-file" name="file" accept=".xlsx,.xls,.csv" class="hidden"
                        onchange="updateDropLabel(this)">
                </div>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="document.getElementById('modal-import').classList.add('hidden')"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium transition">
                    Batal
                </button>
                <button type="submit" id="btn-import-submit"
                    class="inline-flex items-center gap-2 px-5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Proses Import
                </button>
            </div>
        </form>
    </div>
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

@section('extra_js')
<script>
    function updateDropLabel(input) {
        const label = document.getElementById('drop-label');
        if (input.files && input.files[0]) {
            label.innerHTML = '<span class="text-emerald-700 font-semibold">' + input.files[0].name + '</span>';
        }
    }

    function handleDrop(e) {
        e.preventDefault();
        const zone = document.getElementById('drop-zone');
        zone.classList.remove('border-blue-400', 'bg-blue-50');
        const file = e.dataTransfer.files[0];
        if (!file) return;
        const input = document.getElementById('import-file');
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        updateDropLabel(input);
    }
    document.getElementById('form-import').addEventListener('submit', function() {
        const btn = document.getElementById('btn-import-submit');
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Memproses...';
    });
</script>
@endsection