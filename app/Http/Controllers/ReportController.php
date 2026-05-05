<?php

namespace App\Http\Controllers;

use App\Exports\LaporanHarianExport;
use App\Exports\LaporanBulananExport;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

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

        $total        = $transactions->sum('total');
        $count        = $transactions->count();
        $totalItems   = $transactions->sum(fn($t) => $t->items->sum('qty'));
        $avgPerTrx    = $count > 0 ? $total / $count : 0;

        $paymentBreakdown = $transactions->groupBy('payment_method')
            ->map(fn($g) => ['count' => $g->count(), 'total' => $g->sum('total')])
            ->sortByDesc('total');

        return view('report.harian', compact(
            'transactions',
            'total',
            'count',
            'date',
            'totalItems',
            'avgPerTrx',
            'paymentBreakdown'
        ));
    }

    public function bulanan(Request $request)
    {
        $month = $request->filled('month') ? (int) $request->month : now()->month;
        $year  = $request->filled('year')  ? (int) $request->year  : now()->year;

        $transactions = Transaction::with('cashier', 'items')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', 'paid')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalRevenue = $transactions->sum('total');
        $totalCount   = $transactions->count();
        $totalItems   = $transactions->sum(fn($t) => $t->items->sum('qty'));

        $dailyData = $transactions->groupBy(fn($t) => $t->created_at->format('Y-m-d'))
            ->map(fn($g) => ['count' => $g->count(), 'total' => $g->sum('total')])
            ->sortKeys();

        $paymentBreakdown = $transactions->groupBy('payment_method')
            ->map(fn($g) => ['count' => $g->count(), 'total' => $g->sum('total')])
            ->sortByDesc('total');

        return view('report.bulanan', compact(
            'transactions',
            'totalRevenue',
            'totalCount',
            'dailyData',
            'month',
            'year',
            'totalItems',
            'paymentBreakdown'
        ));
    }

    // ── Export Excel Harian ─────────────────────────────────────────────
    public function exportHarianExcel(Request $request)
    {
        $date = $request->filled('date') ? Carbon::parse($request->date) : Carbon::today();
        $filename = 'laporan-harian-' . $date->format('Y-m-d') . '.xlsx';
        return Excel::download(new LaporanHarianExport($date), $filename);
    }

    // ── Export PDF Harian (print view) ──────────────────────────────────
    public function exportHarianPdf(Request $request)
    {
        $date = $request->filled('date') ? Carbon::parse($request->date) : Carbon::today();

        $transactions = Transaction::with('cashier', 'items')
            ->whereDate('created_at', $date)
            ->where('status', 'paid')
            ->orderBy('created_at', 'desc')
            ->get();

        $total      = $transactions->sum('total');
        $count      = $transactions->count();
        $totalItems = $transactions->sum(fn($t) => $t->items->sum('qty'));
        $avgPerTrx  = $count > 0 ? $total / $count : 0;

        $paymentBreakdown = $transactions->groupBy('payment_method')
            ->map(fn($g) => ['count' => $g->count(), 'total' => $g->sum('total')])
            ->sortByDesc('total');

        $store = \App\Models\StoreProfile::first();

        return view('report.pdf-harian', compact(
            'transactions',
            'total',
            'count',
            'date',
            'totalItems',
            'avgPerTrx',
            'paymentBreakdown',
            'store'
        ));
    }

    // ── Export Excel Bulanan ────────────────────────────────────────────
    public function exportBulananExcel(Request $request)
    {
        $month = $request->filled('month') ? (int) $request->month : now()->month;
        $year  = $request->filled('year')  ? (int) $request->year  : now()->year;
        $filename = 'laporan-bulanan-' . $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '.xlsx';
        return Excel::download(new LaporanBulananExport($month, $year), $filename);
    }

    // ── Export PDF Bulanan (print view) ─────────────────────────────────
    public function exportBulananPdf(Request $request)
    {
        $month = $request->filled('month') ? (int) $request->month : now()->month;
        $year  = $request->filled('year')  ? (int) $request->year  : now()->year;

        $transactions = Transaction::with('cashier', 'items')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', 'paid')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalRevenue = $transactions->sum('total');
        $totalCount   = $transactions->count();
        $totalItems   = $transactions->sum(fn($t) => $t->items->sum('qty'));

        $dailyData = $transactions->groupBy(fn($t) => $t->created_at->format('Y-m-d'))
            ->map(fn($g) => ['count' => $g->count(), 'total' => $g->sum('total')])
            ->sortKeys();

        $paymentBreakdown = $transactions->groupBy('payment_method')
            ->map(fn($g) => ['count' => $g->count(), 'total' => $g->sum('total')])
            ->sortByDesc('total');

        $store = \App\Models\StoreProfile::first();

        return view('report.pdf-bulanan', compact(
            'transactions',
            'totalRevenue',
            'totalCount',
            'dailyData',
            'month',
            'year',
            'totalItems',
            'paymentBreakdown',
            'store'
        ));
    }
}
