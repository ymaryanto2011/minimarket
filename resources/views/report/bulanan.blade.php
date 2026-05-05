@extends('layouts.app')

@section('title', 'Laporan Bulanan')
@section('page_title', 'Laporan Penjualan Bulanan')
@section('page_subtitle', 'Analisis tren penjualan tiap bulan')

@section('content')
@php
$monthNames = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
@endphp
<div class="space-y-4">

    {{-- Filter --}}
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('report.bulanan') }}" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                <select name="month" class="input-field">
                    @foreach($monthNames as $m => $name)
                    @if($m > 0)
                    <option value="{{ $m }}" @selected($m==$month)>{{ $name }}</option>
                    @endif
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                <select name="year" class="input-field">
                    @for($y = now()->year; $y >= now()->year - 4; $y--)
                    <option value="{{ $y }}" @selected($y==$year)>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="btn-primary">Tampilkan</button>
            <a href="{{ route('report.index') }}" class="btn-secondary">← Kembali</a>
        </form>
    </div>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-xs uppercase tracking-wide">Total Omzet</p>
            <p class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-xs uppercase tracking-wide">Transaksi</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $totalCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-xs uppercase tracking-wide">Total Item</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ number_format($totalItems) }} unit</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-xs uppercase tracking-wide">Hari Aktif</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">{{ $dailyData->count() }} hari</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Ringkasan Per Hari --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-5 py-4 border-b font-semibold text-gray-700">Ringkasan Per Hari</div>
            @if($dailyData->isEmpty())
            <p class="p-5 text-gray-400 text-sm">Tidak ada data.</p>
            @else
            <div class="overflow-y-auto max-h-64">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b">
                            <th class="text-left py-2 px-4 font-semibold text-gray-600">Tanggal</th>
                            <th class="text-right py-2 px-4 font-semibold text-gray-600">Trx</th>
                            <th class="text-right py-2 px-4 font-semibold text-gray-600">Omzet</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dailyData as $tanggal => $d)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 px-4 text-gray-700">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</td>
                            <td class="py-2 px-4 text-right text-gray-600">{{ $d['count'] }}</td>
                            <td class="py-2 px-4 text-right font-medium text-gray-800">Rp {{ number_format($d['total'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- Metode Pembayaran --}}
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="font-semibold text-gray-700 mb-3">Metode Pembayaran</h3>
            @if($paymentBreakdown->isEmpty())
            <p class="text-gray-400 text-sm">Tidak ada data.</p>
            @else
            <div class="space-y-2">
                @foreach($paymentBreakdown as $method => $data)
                @php $pct = $totalRevenue > 0 ? round($data['total'] / $totalRevenue * 100) : 0; @endphp
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
            @endif
        </div>
    </div>

    {{-- Tabel Detail Transaksi --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-700">
                Detail Transaksi — {{ $monthNames[$month] }} {{ $year }}
            </h3>
            <div class="flex gap-2">
                <a href="{{ route('report.bulanan.pdf', ['month' => $month, 'year' => $year]) }}"
                    target="_blank"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Export PDF
                </a>
                <a href="{{ route('report.bulanan.excel', ['month' => $month, 'year' => $year]) }}"
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
            <p>Tidak ada transaksi pada periode ini.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="text-left py-3 px-4 font-semibold text-gray-600">No. Invoice</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-600">Tanggal</th>
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
                        <td class="py-3 px-4 text-gray-600">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
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
                        <td colspan="4" class="py-3 px-4 text-gray-700">Total ({{ $totalCount }} transaksi)</td>
                        <td class="py-3 px-4 text-right text-green-700">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>

</div>
@endsection