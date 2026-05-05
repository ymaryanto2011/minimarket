<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Harian {{ $date->format('d-m-Y') }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #111827;
            background: #f3f4f6;
        }

        .toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            background: #1e3a5f;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .toolbar-title {
            color: #fff;
            font-size: 14px;
            font-weight: 600;
        }

        .toolbar-actions {
            display: flex;
            gap: 10px;
        }

        .btn-tool {
            padding: 7px 18px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-print {
            background: #16a34a;
            color: #fff;
        }

        .btn-back {
            background: #fff;
            color: #1e3a5f;
        }

        .page-wrapper {
            padding: 72px 24px 24px;
        }

        .paper {
            background: #fff;
            width: 794px;
            margin: 0 auto;
            padding: 40px 48px 48px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .15);
            border-radius: 4px;
        }

        .kop {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #1e3a5f;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }

        .store-name {
            font-size: 20px;
            font-weight: 700;
            color: #1e3a5f;
        }

        .store-info {
            font-size: 10px;
            color: #6b7280;
            margin-top: 3px;
            line-height: 1.5;
        }

        .doc-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e3a5f;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .doc-sub {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .summary-card {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px 12px;
            background: #f9fafb;
        }

        .summary-label {
            font-size: 9px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-value {
            font-size: 14px;
            font-weight: 700;
            color: #1e3a5f;
            margin-top: 3px;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        table.items thead tr {
            background: #1e3a5f;
            color: #fff;
        }

        table.items thead th {
            padding: 8px 10px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }

        table.items thead th.right {
            text-align: right;
        }

        table.items tbody td {
            padding: 7px 10px;
            font-size: 11px;
            border-bottom: 1px solid #e5e7eb;
        }

        table.items tbody tr:nth-child(even) td {
            background: #f9fafb;
        }

        table.items tbody td.right {
            text-align: right;
        }

        table.items tfoot td {
            padding: 8px 10px;
            font-weight: 700;
            font-size: 12px;
            border-top: 2px solid #1e3a5f;
            background: #f0f4ff;
        }

        table.items tfoot td.right {
            text-align: right;
            color: #16a34a;
        }

        .payment-section {
            margin-bottom: 20px;
        }

        .payment-title {
            font-size: 10px;
            text-transform: uppercase;
            color: #9ca3af;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .payment-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            padding: 4px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .print-footer {
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
            margin-top: 16px;
        }

        @media print {
            body {
                background: #fff;
            }

            .toolbar {
                display: none !important;
            }

            .page-wrapper {
                padding: 0;
            }

            .paper {
                box-shadow: none;
                border-radius: 0;
                width: 100%;
                padding: 24px 32px;
            }
        }
    </style>
</head>

<body>

    <div class="toolbar">
        <span class="toolbar-title">Laporan Harian — {{ $date->translatedFormat('d F Y') }}</span>
        <div class="toolbar-actions">
            <a href="{{ route('report.harian', ['date' => $date->format('Y-m-d')]) }}" class="btn-tool btn-back">← Kembali</a>
            <button onclick="window.print()" class="btn-tool btn-print">🖨 Cetak / Simpan PDF</button>
        </div>
    </div>

    <div class="page-wrapper">
        <div class="paper">

            {{-- Kop --}}
            <div class="kop">
                <div>
                    <div class="store-name">{{ $store->name ?? 'Nama Toko' }}</div>
                    <div class="store-info">
                        @if($store?->address){{ $store->address }}<br>@endif
                        @if($store?->phone)Telp: {{ $store->phone }}@endif
                        @if($store?->email) &nbsp;|&nbsp; {{ $store->email }}@endif
                    </div>
                </div>
                <div style="text-align:right">
                    <div class="doc-title">Laporan Penjualan Harian</div>
                    <div class="doc-sub">{{ $date->translatedFormat('d F Y') }}</div>
                </div>
            </div>

            {{-- Ringkasan --}}
            <div class="summary-grid">
                <div class="summary-card">
                    <div class="summary-label">Total Omzet</div>
                    <div class="summary-value" style="color:#16a34a">Rp {{ number_format($total, 0, ',', '.') }}</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Transaksi</div>
                    <div class="summary-value">{{ $count }}</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Total Item</div>
                    <div class="summary-value">{{ number_format($totalItems) }}</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Rata-rata/Trx</div>
                    <div class="summary-value">Rp {{ number_format($avgPerTrx, 0, ',', '.') }}</div>
                </div>
            </div>

            {{-- Metode Pembayaran --}}
            @if($paymentBreakdown->count())
            <div class="payment-section">
                <div class="payment-title">Metode Pembayaran</div>
                @foreach($paymentBreakdown as $method => $data)
                <div class="payment-row">
                    <span style="text-transform:capitalize">{{ $method ?: 'Lainnya' }} ({{ $data['count'] }} trx)</span>
                    <span style="font-weight:600">Rp {{ number_format($data['total'], 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Tabel Transaksi --}}
            @if($transactions->isNotEmpty())
            <table class="items">
                <thead>
                    <tr>
                        <th style="width:28px">No</th>
                        <th>No. Invoice</th>
                        <th>Waktu</th>
                        <th>Kasir</th>
                        <th class="right" style="width:55px">Item</th>
                        <th class="right" style="width:120px">Total</th>
                        <th style="width:80px">Metode</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $i => $trx)
                    <tr>
                        <td style="text-align:center">{{ $i + 1 }}</td>
                        <td style="font-family:monospace; font-size:10px">{{ $trx->invoice_no }}</td>
                        <td>{{ $trx->created_at->format('H:i') }}</td>
                        <td>{{ $trx->cashier?->name ?? 'Admin' }}</td>
                        <td class="right">{{ $trx->items->count() }}</td>
                        <td class="right" style="font-weight:600">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                        <td style="text-transform:capitalize">{{ $trx->payment_method ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="">Total ({{ $count }} transaksi)</td>
                        <td class="right">Rp {{ number_format($total, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            @else
            <p style="text-align:center; color:#9ca3af; padding: 32px 0;">Tidak ada transaksi pada tanggal ini.</p>
            @endif

            <div class="print-footer">
                Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }} &nbsp;|&nbsp; {{ $store->name ?? 'Minimarket POS' }}
            </div>

        </div>
    </div>

</body>

</html>