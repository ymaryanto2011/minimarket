<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Product;
use App\Models\StoreProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $quotations = Quotation::with(['items', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();

        $products = Product::where('is_active', true)
            ->with('unitConversions')
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'retail_price', 'wholesale_price', 'unit']);

        $store  = StoreProfile::first();
        $nextNo = Quotation::generateQuotationNo();

        return view('quotation.index', compact('quotations', 'products', 'store', 'nextNo'));
    }

    public function show(Quotation $quotation)
    {
        $quotation->load(['items.product', 'creator']);
        $store = StoreProfile::first();
        return response()->json(['quotation' => $quotation, 'store' => $store]);
    }

    public function create()
    {
        return redirect()->route('quotation.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'to_name'              => 'required|string|max:255',
            'date'                 => 'required|date',
            'valid_until'          => 'required|date|after_or_equal:date',
            'notes'                => 'nullable|string',
            'discount'             => 'nullable|numeric|min:0',
            'tax_rate'             => 'nullable|numeric|min:0|max:100',
            'items'                          => 'required|array|min:1',
            'items.*.product_id'             => 'required|exists:products,id',
            'items.*.product_name'           => 'required|string|max:255',
            'items.*.unit_label'             => 'nullable|string|max:50',
            'items.*.conversion_qty'         => 'nullable|numeric|min:0.0001',
            'items.*.qty'                    => 'required|integer|min:1',
            'items.*.unit_price'             => 'required|numeric|min:0',
            'items.*.discount_pct'           => 'nullable|numeric|min:0|max:100',
        ]);

        $quotation = null;
        DB::transaction(function () use ($data, &$quotation) {
            $quotation = Quotation::create([
                'quotation_no' => Quotation::generateQuotationNo(),
                'to_name'      => $data['to_name'],
                'date'         => $data['date'],
                'valid_until'  => $data['valid_until'],
                'notes'        => $data['notes'] ?? null,
                'discount'     => $data['discount'] ?? 0,
                'tax_rate'     => $data['tax_rate'] ?? 0,
                'tax_amount'   => 0,
                'subtotal'     => 0,
                'total'        => 0,
                'status'       => 'draft',
                'created_by'   => null,
            ]);

            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $discPct   = $item['discount_pct'] ?? 0;
                $lineTotal = $item['qty'] * $item['unit_price'] * (1 - $discPct / 100);
                $subtotal += $lineTotal;
                $quotation->items()->create([
                    'product_id'     => $item['product_id'],
                    'product_name'   => $item['product_name'],
                    'unit_label'     => $item['unit_label'] ?? null,
                    'conversion_qty' => $item['conversion_qty'] ?? 1,
                    'qty'            => $item['qty'],
                    'unit_price'     => $item['unit_price'],
                    'discount_pct'   => $discPct,
                    'total'          => $lineTotal,
                ]);
            }

            $discount      = $data['discount'] ?? 0;
            $taxRate       = $data['tax_rate'] ?? 0;
            $afterDiscount = max(0, $subtotal - $discount);
            $taxAmount     = $afterDiscount * $taxRate / 100;

            $quotation->update([
                'subtotal'   => $subtotal,
                'tax_amount' => $taxAmount,
                'total'      => $afterDiscount + $taxAmount,
            ]);
        });

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Penawaran berhasil dibuat.']);
        }
        return redirect()->route('quotation.index')->with('success', 'Penawaran berhasil dibuat.');
    }

    public function edit(Quotation $quotation)
    {
        return redirect()->route('quotation.index');
    }

    public function update(Request $request, Quotation $quotation)
    {
        $data = $request->validate([
            'to_name'              => 'required|string|max:255',
            'date'                 => 'required|date',
            'valid_until'          => 'required|date',
            'notes'                => 'nullable|string',
            'discount'             => 'nullable|numeric|min:0',
            'tax_rate'             => 'nullable|numeric|min:0|max:100',
            'status'               => 'required|in:draft,submit,approved,paid,rejected,expired,cancelled',
            'items'                          => 'required|array|min:1',
            'items.*.product_id'             => 'required|exists:products,id',
            'items.*.product_name'           => 'required|string|max:255',
            'items.*.unit_label'             => 'nullable|string|max:50',
            'items.*.conversion_qty'         => 'nullable|numeric|min:0.0001',
            'items.*.qty'                    => 'required|integer|min:1',
            'items.*.unit_price'             => 'required|numeric|min:0',
            'items.*.discount_pct'           => 'nullable|numeric|min:0|max:100',
        ]);

        DB::transaction(function () use ($data, $quotation) {
            $quotation->items()->delete();
            $subtotal = 0;

            foreach ($data['items'] as $item) {
                $discPct   = $item['discount_pct'] ?? 0;
                $lineTotal = $item['qty'] * $item['unit_price'] * (1 - $discPct / 100);
                $subtotal += $lineTotal;
                $quotation->items()->create([
                    'product_id'     => $item['product_id'],
                    'product_name'   => $item['product_name'],
                    'unit_label'     => $item['unit_label'] ?? null,
                    'conversion_qty' => $item['conversion_qty'] ?? 1,
                    'qty'            => $item['qty'],
                    'unit_price'     => $item['unit_price'],
                    'discount_pct'   => $discPct,
                    'total'          => $lineTotal,
                ]);
            }

            $discount      = $data['discount'] ?? 0;
            $taxRate       = $data['tax_rate'] ?? 0;
            $afterDiscount = max(0, $subtotal - $discount);
            $taxAmount     = $afterDiscount * $taxRate / 100;

            $quotation->update([
                'to_name'     => $data['to_name'],
                'date'        => $data['date'],
                'valid_until' => $data['valid_until'],
                'notes'       => $data['notes'] ?? null,
                'discount'    => $discount,
                'tax_rate'    => $taxRate,
                'subtotal'    => $subtotal,
                'tax_amount'  => $taxAmount,
                'total'       => $afterDiscount + $taxAmount,
                'status'      => $data['status'],
            ]);
        });

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Penawaran berhasil diperbarui.']);
        }
        return redirect()->route('quotation.index')->with('success', 'Penawaran berhasil diperbarui.');
    }

    public function destroy(Quotation $quotation)
    {
        $quotation->items()->delete();
        $quotation->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Penawaran berhasil dihapus.']);
        }
        return redirect()->route('quotation.index')->with('success', 'Penawaran berhasil dihapus.');
    }

    public function pdf(Quotation $quotation)
    {
        $quotation->load(['items.product', 'creator']);
        $store = StoreProfile::first();
        return view('quotation.pdf', compact('quotation', 'store'));
    }
}
