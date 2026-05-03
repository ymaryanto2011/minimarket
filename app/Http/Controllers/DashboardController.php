<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\StockMovement;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $todaySales = Transaction::whereDate('created_at', $today)
            ->where('status', 'paid')
            ->sum('total');

        $todayTransactions = Transaction::whereDate('created_at', $today)
            ->where('status', 'paid')
            ->count();

        $totalProducts = Product::where('is_active', true)->count();
        $lowStockProducts = Product::where('is_active', true)
            ->whereColumn('stock', '<=', 'min_stock')
            ->count();

        $recentTransactions = Transaction::with('cashier')
            ->where('status', 'paid')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $monthlySales = Transaction::where('status', 'paid')
            ->whereMonth('created_at', $today->month)
            ->whereYear('created_at', $today->year)
            ->sum('total');

        return view('dashboard.index', compact(
            'todaySales',
            'todayTransactions',
            'totalProducts',
            'lowStockProducts',
            'recentTransactions',
            'monthlySales'
        ));
    }
}
