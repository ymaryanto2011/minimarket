<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Product;
use App\Models\StoreProfile;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\StockMovement;
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
        $isCustom = (bool) $request->input('is_custom', false);

        $baseRules = [
            'to_name'              => 'required|string|max:255',
            'date'                 => 'required|date',
            'valid_until'          => 'required|date|after_or_equal:date',
            'notes'                => 'nullable|string',
            'discount'             => 'nullable|numeric|min:0',
            'tax_rate'             => 'nullable|numeric|min:0|max:100',
            'items'                          => 'required|array|min:1',
            'items.*.product_name'           => 'required|string|max:255',
            'items.*.unit_label'             => 'nullable|string|max:50',
            'items.*.conversion_qty'         => 'nullable|numeric|min:0.0001',
            'items.*.qty'                    => 'required|integer|min:1',
            'items.*.unit_price'             => 'required|numeric|min:0',
            'items.*.discount_pct'           => 'nullable|numeric|min:0|max:100',
        ];

        if ($isCustom) {
            $baseRules['items.*.product_id'] = 'nullable|exists:products,id';
        } else {
            $baseRules['items.*.product_id'] = 'required|exists:products,id';
        }

        $data = $request->validate($baseRules);

        $quotation = null;
        DB::transaction(function () use ($data, $isCustom, &$quotation) {
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
                'is_custom'    => $isCustom,
                'created_by'   => null,
            ]);

            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $discPct   = $item['discount_pct'] ?? 0;
                $lineTotal = $item['qty'] * $item['unit_price'] * (1 - $discPct / 100);
                $subtotal += $lineTotal;
                $quotation->items()->create([
                    'product_id'     => $item['product_id'] ?? null,
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
        $isCustom = (bool) $request->input('is_custom', $quotation->is_custom);

        $baseRules = [
            'to_name'              => 'required|string|max:255',
            'date'                 => 'required|date',
            'valid_until'          => 'required|date',
            'notes'                => 'nullable|string',
            'discount'             => 'nullable|numeric|min:0',
            'tax_rate'             => 'nullable|numeric|min:0|max:100',
            'status'               => 'required|in:draft,submit,approved,paid,rejected,expired,cancelled',
            'items'                          => 'required|array|min:1',
            'items.*.product_name'           => 'required|string|max:255',
            'items.*.unit_label'             => 'nullable|string|max:50',
            'items.*.conversion_qty'         => 'nullable|numeric|min:0.0001',
            'items.*.qty'                    => 'required|integer|min:1',
            'items.*.unit_price'             => 'required|numeric|min:0',
            'items.*.discount_pct'           => 'nullable|numeric|min:0|max:100',
        ];

        if ($isCustom) {
            $baseRules['items.*.product_id'] = 'nullable|exists:products,id';
        } else {
            $baseRules['items.*.product_id'] = 'required|exists:products,id';
        }

        $data = $request->validate($baseRules);

        DB::transaction(function () use ($data, $isCustom, $quotation) {
            $quotation->items()->delete();
            $subtotal = 0;

            foreach ($data['items'] as $item) {
                $discPct   = $item['discount_pct'] ?? 0;
                $lineTotal = $item['qty'] * $item['unit_price'] * (1 - $discPct / 100);
                $subtotal += $lineTotal;
                $quotation->items()->create([
                    'product_id'     => $item['product_id'] ?? null,
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
                'is_custom'   => $isCustom,
            ]);
        });

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Penawaran berhasil diperbarui.']);
        }
        return redirect()->route('quotation.index')->with('success', 'Penawaran berhasil diperbarui.');
    }

    public function convertToTransaction(Quotation $quotation)
    {
        if ($quotation->status !== 'paid') {
            return response()->json(['success' => false, 'message' => 'Hanya penawaran berstatus Lunas yang bisa dikonversi.'], 422);
        }
        if ($quotation->is_custom) {
            return response()->json(['success' => false, 'message' => 'Penawaran custom tidak bisa dikonversi ke transaksi.'], 422);
        }
        if ($quotation->transaction_id) {
            return response()->json(['success' => false, 'message' => 'Penawaran ini sudah pernah dikonversi ke transaksi.'], 422);
        }

        $quotation->load('items.product');

        // Check stock availability before starting DB transaction
        foreach ($quotation->items as $item) {
            if (!$item->product_id) continue;
            $stockQty = (int) round($item->qty * ($item->conversion_qty ?: 1));
            $product  = Product::find($item->product_id);
            if (!$product || $product->stock < $stockQty) {
                return response()->json([
                    'success' => false,
                    'message' => "Stok tidak cukup untuk barang: {$item->product_name} (tersedia: " . ($product->stock ?? 0) . ", dibutuhkan: {$stockQty})",
                ], 422);
            }
        }

        $transaction = null;
        DB::transaction(function () use ($quotation, &$transaction) {
            $transaction = Transaction::create([
                'invoice_no'     => Transaction::generateInvoiceNo(),
                'cashier_id'     => null,
                'subtotal'       => $quotation->subtotal,
                'discount'       => $quotation->discount,
                'tax'            => $quotation->tax_amount,
                'total'          => $quotation->total,
                'payment_method' => 'transfer',
                'paid_amount'    => $quotation->total,
                'change_amount'  => 0,
                'status'         => 'paid',
            ]);

            foreach ($quotation->items as $item) {
                if (!$item->product_id) continue;

                $product  = Product::lockForUpdate()->find($item->product_id);
                $stockQty = (int) round($item->qty * ($item->conversion_qty ?: 1));

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id'     => $item->product_id,
                    'product_name'   => $item->product_name,
                    'price'          => $item->unit_price,
                    'qty'            => $item->qty,
                    'discount'       => round($item->unit_price * $item->qty * ($item->discount_pct / 100), 2),
                    'subtotal'       => $item->total,
                    'price_type'     => 'retail',
                ]);

                StockMovement::create([
                    'product_id'   => $item->product_id,
                    'type'         => 'out',
                    'qty'          => $stockQty,
                    'stock_before' => $product->stock,
                    'stock_after'  => $product->stock - $stockQty,
                    'reference'    => $transaction->invoice_no,
                    'note'         => 'Dari Penawaran: ' . $quotation->quotation_no,
                    'user_id'      => null,
                ]);

                $product->decrement('stock', $stockQty);
            }

            $quotation->update(['transaction_id' => $transaction->id]);
        });

        return response()->json([
            'success'    => true,
            'message'    => 'Penawaran berhasil dicatat sebagai penjualan. Invoice: ' . $transaction->invoice_no,
            'invoice_no' => $transaction->invoice_no,
        ]);
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
