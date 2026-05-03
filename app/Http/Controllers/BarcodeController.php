<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    public function index(Request $request)
    {
        $products = collect();

        if ($request->filled('search')) {
            $products = Product::where('is_active', true)
                ->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('code', 'like', '%' . $request->search . '%')
                        ->orWhere('barcode', 'like', '%' . $request->search . '%');
                })
                ->orderBy('name')
                ->get();
        }

        return view('barcode.index', compact('products'));
    }
}
