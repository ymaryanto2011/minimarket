<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('report.index');
    }

    public function harian(Request $request)
    {
        $date = $request->filled('date') ? Carbon::parse($request->date) : Carbon::today();

        $transactions = Transaction::with('cashier', 'items')
            ->whereDate('created_at', $date)
            ->where('status', 'paid')
            ->orderBy('created_at', 'desc')
            ->get();

        $total = $transactions->sum('total');
        $count = $transactions->count();

        return view('report.harian', compact('transactions', 'total', 'count', 'date'));
    }

    public function bulanan(Request $request)
    {
        $month = $request->filled('month') ? (int) $request->month : now()->month;
        $year  = $request->filled('year')  ? (int) $request->year  : now()->year;

        $transactions = Transaction::with('cashier')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', 'paid')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalRevenue = $transactions->sum('total');
        $totalCount = $transactions->count();

        // Daily breakdown
        $dailyData = $transactions->groupBy(fn($t) => $t->created_at->format('Y-m-d'))
            ->map(fn($g) => ['count' => $g->count(), 'total' => $g->sum('total')])
            ->sortKeys();

        return view('report.bulanan', compact('transactions', 'totalRevenue', 'totalCount', 'dailyData', 'month', 'year'));
    }
}
