<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\StockMovement;
use App\Models\StoreProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $storeProfile = StoreProfile::first();
        return view('pos.index', compact('storeProfile'));
    }

    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $products = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%")
                    ->orWhere('barcode', 'like', "%{$q}%");
            })
            ->select('id', 'code', 'name', 'retail_price', 'wholesale_price', 'min_wholesale_qty', 'stock', 'unit')
            ->limit(20)
            ->get();

        return response()->json($products);
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'items'          => 'required|array|min:1',
            'items.*.id'     => 'required|exists:products,id',
            'items.*.qty'    => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,transfer,qris',
            'paid_amount'    => 'required|numeric|min:0',
            'discount'       => 'nullable|numeric|min:0',
        ]);

        $discount = $data['discount'] ?? 0;

        $trx = DB::transaction(function () use ($data, $discount) {
            $subtotal = 0;
            $lines = [];

            foreach ($data['items'] as $item) {
                $product = Product::lockForUpdate()->find($item['id']);
                if ($product->stock < $item['qty']) {
                    abort(422, "Stok {$product->name} tidak cukup.");
                }

                $isWholesale = $item['qty'] >= $product->min_wholesale_qty;
                $price = $isWholesale ? $product->wholesale_price : $product->retail_price;
                $lineTotal = $price * $item['qty'];
                $subtotal += $lineTotal;

                $lines[] = [
                    'product'    => $product,
                    'qty'        => $item['qty'],
                    'price'      => $price,
                    'subtotal'   => $lineTotal,
                    'price_type' => $isWholesale ? 'wholesale' : 'retail',
                ];
            }

            $total = max(0, $subtotal - $discount);
            $change = $data['paid_amount'] - $total;

            $transaction = Transaction::create([
                'invoice_no'     => Transaction::generateInvoiceNo(),
                'cashier_id'     => null,
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'tax'            => 0,
                'total'          => $total,
                'payment_method' => $data['payment_method'],
                'paid_amount'    => $data['paid_amount'],
                'change_amount'  => $change,
                'status'         => 'paid',
            ]);

            foreach ($lines as $line) {
                $transaction->items()->create([
                    'product_id'   => $line['product']->id,
                    'product_name' => $line['product']->name,
                    'price'        => $line['price'],
                    'qty'          => $line['qty'],
                    'discount'     => 0,
                    'subtotal'     => $line['subtotal'],
                    'price_type'   => $line['price_type'],
                ]);

                StockMovement::create([
                    'product_id'   => $line['product']->id,
                    'type'         => 'out',
                    'qty'          => $line['qty'],
                    'stock_before' => $line['product']->stock,
                    'stock_after'  => $line['product']->stock - $line['qty'],
                    'reference'    => $transaction->invoice_no,
                    'user_id'      => null,
                ]);

                $line['product']->decrement('stock', $line['qty']);
            }

            return $transaction;
        });

        return response()->json([
            'message'    => 'Transaksi berhasil.',
            'invoice_no' => $trx->invoice_no ?? '-',
        ]);
    }
}
