<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    public function index(Request $request)
    {
        $allProducts = Product::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'barcode', 'retail_price', 'unit']);

        return view('barcode.index', compact('allProducts'));
    }
}
