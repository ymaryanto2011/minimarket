<?php

namespace App\Http\Controllers;

use App\Exports\StokBarangExport;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->where('is_active', true);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('filter') && $request->filter === 'low') {
            $query->whereColumn('stock', '<=', 'min_stock');
        }

        $products = $query->orderBy('name')->paginate(15)->withQueryString();

        $allProducts = Product::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'stock', 'unit']);

        $lowStockProducts = Product::where('is_active', true)
            ->whereColumn('stock', '<=', 'min_stock')
            ->orderBy('name')
            ->get(['name', 'stock']);

        $movements = StockMovement::with('product', 'user')
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return view('stock.index', compact('products', 'allProducts', 'lowStockProducts', 'movements'));
    }

    public function masuk()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('stock.masuk', compact('products'));
    }

    public function storeMasuk(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty'        => 'required|integer|min:1',
            'reference'  => 'nullable|string|max:100',
            'note'       => 'nullable|string',
        ]);

        DB::transaction(function () use ($data) {
            $product = Product::lockForUpdate()->find($data['product_id']);
            StockMovement::create([
                'product_id'  => $product->id,
                'type'        => 'in',
                'qty'         => $data['qty'],
                'stock_before' => $product->stock,
                'stock_after'  => $product->stock + $data['qty'],
                'reference'   => $data['reference'] ?? null,
                'note'        => $data['note'] ?? null,
                'user_id'     => null,
            ]);
            $product->increment('stock', $data['qty']);
        });

        return redirect()->route('stock.index', ['tab' => 'masuk'])->with('success', 'Stok masuk berhasil dicatat.');
    }

    public function penyesuaian()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('stock.penyesuaian', compact('products'));
    }

    public function storePenyesuaian(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'new_stock'  => 'required|integer|min:0',
            'note'       => 'nullable|string',
        ]);

        DB::transaction(function () use ($data) {
            $product = Product::lockForUpdate()->find($data['product_id']);
            $diff = $data['new_stock'] - $product->stock;
            StockMovement::create([
                'product_id'  => $product->id,
                'type'        => 'adjustment',
                'qty'         => $diff,
                'stock_before' => $product->stock,
                'stock_after'  => $data['new_stock'],
                'note'        => $data['note'] ?? null,
                'user_id'     => null,
            ]);
            $product->update(['stock' => $data['new_stock']]);
        });

        return redirect()->route('stock.index', ['tab' => 'penyesuaian'])->with('success', 'Penyesuaian stok berhasil.');
    }

    public function exportExcel()
    {
        $filename = 'stok-barang-' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new StokBarangExport(), $filename);
    }
}
