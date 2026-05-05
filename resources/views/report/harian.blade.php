@extends('layouts.app')

@section('title', 'Laporan Harian')
@section('page_title', 'Laporan Penjualan Harian')
@section('page_subtitle', 'Ringkasan performa penjualan per hari')

@section('content')
<div class="space-y-4">

    {{-- Filter --}}
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('report.harian') }}" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" class="input-field">
            </div>
            <button type="submit" class="btn-primary">Tampilkan</button>
            <a href="{{ route('report.index') }}" class="btn-secondary">← Kembali</a>
        </form>
    </div>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-xs uppercase tracking-wide">Total Omzet</p>
            <p class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($total, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-xs uppercase tracking-wide">Transaksi</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $count }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-xs uppercase tracking-wide">Total Item</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ number_format($totalItems) }} unit</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-xs uppercase tracking-wide">Rata-rata/Transaksi</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">Rp {{ number_format($avgPerTrx, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Metode Pembayaran --}}
    @if($paymentBreakdown->count())
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="font-semibold text-gray-700 mb-3">Metode Pembayaran</h3>
        <div class="space-y-2">
            @foreach($paymentBreakdown as $method => $data)
            @php $pct = $total > 0 ? round($data['total'] / $total * 100) : 0; @endphp
            <div class="flex items-center gap-3">
                <span class="w-24 text-sm text-gray-600 capitalize">{{ $method ?: 'Lainnya' }}</span>
                <div class="flex-1 bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                </div>
                <span class="text-sm font-semibold text-gray-800 w-48 text-right">
                    Rp {{ number_format($data['total'], 0, ',', '.') }} ({{ $pct }}%)
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Tabel Transaksi --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-700">
                Detail Transaksi — {{ $date->translatedFormat('d F Y') }}
            </h3>
            <div class="flex gap-2">
                <a href="{{ route('report.harian.pdf', ['date' => $date->format('Y-m-d')]) }}"
                    target="_blank"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Export PDF
                </a>
                <a href="{{ route('report.harian.excel', ['date' => $date->format('Y-m-d')]) }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Excel
                </a>
            </div>
        </div>
        @if($transactions->isEmpty())
        <div class="py-12 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <p>Tidak ada transaksi pada tanggal ini.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="text-left py-3 px-4 font-semibold text-gray-600">No. Invoice</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600">Waktu</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600">Kasir</th>
                        <th class="text-right py-3 px-4 font-semibold text-gray-600">Item</th>
                        <th class="text-right py-3 px-4 font-semibold text-gray-600">Total</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600">Metode</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $trx)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4 font-mono text-xs text-gray-600">{{ $trx->invoice_no }}</td>
                        <td class="py-3 px-4 text-gray-600">{{ $trx->created_at->format('H:i') }}</td>
                        <td class="py-3 px-4 text-gray-700">{{ $trx->cashier?->name ?? 'Admin' }}</td>
                        <td class="py-3 px-4 text-right text-gray-600">{{ $trx->items->count() }} item</td>
                        <td class="py-3 px-4 text-right font-semibold text-gray-800">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                {{ $trx->payment_method === 'cash' ? 'bg-green-100 text-green-700' :
                                   ($trx->payment_method === 'card' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600') }}">
                                {{ ucfirst($trx->payment_method ?? '-') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 border-t-2 font-bold">
                        <td colspan="4" class="py-3 px-4 text-gray-700">Total ({{ $count }} transaksi)</td>
                        <td class="py-3 px-4 text-right text-green-700">Rp {{ number_format($total, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>

</div>
@endsection