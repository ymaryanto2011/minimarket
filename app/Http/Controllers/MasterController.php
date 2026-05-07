<?php

namespace App\Http\Controllers;

use App\Exports\ProductImportTemplateExport;
use App\Imports\ProductImport;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\StockMovement;
use App\Models\ProductUnitConversion;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MasterController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'unitConversions']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%')
                    ->orWhere('barcode', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->orderBy('name')->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('master.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        return view('master.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'              => 'required|string|unique:products,code',
            'name'              => 'required|string|max:255',
            'category_id'       => 'required|exists:categories,id',
            'retail_price'      => 'required|numeric|min:0',
            'wholesale_price'   => 'required|numeric|min:0',
            'min_wholesale_qty' => 'required|integer|min:1',
            'stock'             => 'required|integer|min:0',
            'min_stock'         => 'required|integer|min:0',
            'unit'              => 'required|string|max:50',
            'barcode'           => 'nullable|string|max:100',
            'description'       => 'nullable|string',
            'is_active'         => 'boolean',
            // Konversi satuan
            'conversions'                    => 'nullable|array',
            'conversions.*.unit_name'        => 'required_with:conversions|string|max:50',
            'conversions.*.conversion_qty'   => 'required_with:conversions|numeric|min:0.0001',
            'conversions.*.sell_price'       => 'required_with:conversions|numeric|min:0',
            'conversions.*.buy_price'        => 'nullable|numeric|min:0',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $conversions = $data['conversions'] ?? [];
        unset($data['conversions']);

        $product = Product::create($data);

        foreach ($conversions as $conv) {
            if (!empty($conv['unit_name'])) {
                $product->unitConversions()->create([
                    'unit_name'      => $conv['unit_name'],
                    'conversion_qty' => $conv['conversion_qty'],
                    'sell_price'     => $conv['sell_price'],
                    'buy_price'      => $conv['buy_price'] ?? 0,
                ]);
            }
        }

        return redirect()->route('master.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function nextCode(Request $request)
    {
        $categoryId = $request->validate([
            'category_id' => 'required|exists:categories,id',
        ])['category_id'];

        $category = Category::findOrFail($categoryId);
        $prefix = strtoupper($category->code ?? 'XX');

        $max = Product::where('category_id', $categoryId)
            ->where('code', 'like', $prefix . '-%')
            ->get()
            ->map(function ($p) use ($prefix) {
                $suffix = substr($p->code, strlen($prefix) + 1);
                return is_numeric($suffix) ? (int) $suffix : 0;
            })
            ->max();

        $next = str_pad(($max ?? 0) + 1, 3, '0', STR_PAD_LEFT);

        return response()->json(['code' => $prefix . '-' . $next]);
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $product->load('unitConversions');
        return view('master.edit', compact('product', 'categories', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'code'              => 'required|string|unique:products,code,' . $product->id,
            'name'              => 'required|string|max:255',
            'category_id'       => 'required|exists:categories,id',
            'retail_price'      => 'required|numeric|min:0',
            'wholesale_price'   => 'required|numeric|min:0',
            'min_wholesale_qty' => 'required|integer|min:1',
            'min_stock'         => 'required|integer|min:0',
            'unit'              => 'required|string|max:50',
            'barcode'           => 'nullable|string|max:100',
            'description'       => 'nullable|string',
            // Konversi satuan
            'conversions'                    => 'nullable|array',
            'conversions.*.unit_name'        => 'required_with:conversions|string|max:50',
            'conversions.*.conversion_qty'   => 'required_with:conversions|numeric|min:0.0001',
            'conversions.*.sell_price'       => 'required_with:conversions|numeric|min:0',
            'conversions.*.buy_price'        => 'nullable|numeric|min:0',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $conversions = $data['conversions'] ?? [];
        unset($data['conversions']);

        $product->update($data);

        // Sync konversi: hapus semua lalu buat ulang
        $product->unitConversions()->delete();
        foreach ($conversions as $conv) {
            if (!empty($conv['unit_name'])) {
                $product->unitConversions()->create([
                    'unit_name'      => $conv['unit_name'],
                    'conversion_qty' => $conv['conversion_qty'],
                    'sell_price'     => $conv['sell_price'],
                    'buy_price'      => $conv['buy_price'] ?? 0,
                ]);
            }
        }

        return redirect()->route('master.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->update(['is_active' => false]);
        return redirect()->route('master.index')->with('success', 'Produk berhasil dinonaktifkan.');
    }

    // ── Download template Excel ───────────────────────────────────────────────
    public function importTemplate()
    {
        return Excel::download(
            new ProductImportTemplateExport(),
            'template_import_barang.xlsx'
        );
    }

    // ── Import dari Excel ─────────────────────────────────────────────────────
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes'    => 'Format file harus .xlsx, .xls, atau .csv.',
            'file.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        try {
            $import = new ProductImport();
            Excel::import($import, $request->file('file'));

            $r = $import->result;
            $msg = "Import selesai: {$r['imported']} produk baru, {$r['updated']} diperbarui, {$r['skipped']} dilewati.";

            if (!empty($r['errors'])) {
                $errorList = implode(' | ', array_slice($r['errors'], 0, 5));
                if (count($r['errors']) > 5) {
                    $errorList .= ' ... dan ' . (count($r['errors']) - 5) . ' error lainnya.';
                }
                return redirect()->route('master.index')
                    ->with('warning', $msg . ' Error: ' . $errorList);
            }

            return redirect()->route('master.index')->with('success', $msg);
        } catch (\Throwable $e) {
            return redirect()->route('master.index')
                ->with('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }
}
