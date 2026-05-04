<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penawaran {{ $quotation->quotation_no }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #1f2937;
            background: #f3f4f6;
        }

        /* ── Toolbar (tidak tercetak) ── */
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

        .btn-print:hover {
            background: #15803d;
        }

        .btn-back {
            background: #fff;
            color: #1e3a5f;
        }

        .btn-back:hover {
            background: #e2e8f0;
        }

        /* ── Kertas dokumen ── */
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

        /* ── Kop Surat ── */
        .kop {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #1e3a5f;
            padding-bottom: 16px;
            margin-bottom: 16px;
        }

        .kop-left .store-name {
            font-size: 20px;
            font-weight: 700;
            color: #1e3a5f;
        }

        .kop-left .store-info {
            font-size: 10px;
            color: #6b7280;
            margin-top: 3px;
            line-height: 1.5;
        }

        .kop-right {
            text-align: right;
        }

        .doc-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e3a5f;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .doc-no {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
            font-family: 'Courier New', monospace;
        }

        /* ── Info Grid ── */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 24px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }

        .info-item .label {
            font-size: 9px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .info-item .value {
            font-size: 12px;
            font-weight: 600;
            color: #111827;
        }

        .info-item .value.expired {
            color: #dc2626;
        }

        /* ── Tabel Barang ── */
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
            letter-spacing: 0.3px;
        }

        table.items thead th.right {
            text-align: right;
        }

        table.items thead th.center {
            text-align: center;
        }

        table.items tbody td {
            padding: 7px 10px;
            font-size: 11px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }

        table.items tbody tr:nth-child(even) td {
            background: #f9fafb;
        }

        table.items tbody td.right {
            text-align: right;
        }

        table.items tbody td.center {
            text-align: center;
        }

        table.items tbody td.discount {
            color: #d97706;
        }

        /* ── Totals ── */
        .totals-wrap {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .totals-box {
            width: 260px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            overflow: hidden;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 12px;
            font-size: 11px;
            border-bottom: 1px solid #f3f4f6;
        }

        .totals-row:last-child {
            border-bottom: none;
        }

        .totals-row.grand {
            background: #1e3a5f;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            padding: 10px 12px;
        }

        .totals-row .label-t {
            color: #6b7280;
        }

        .totals-row.grand .label-t {
            color: #bfdbfe;
        }

        .totals-row .neg {
            color: #dc2626;
            font-weight: 600;
        }

        /* ── Catatan ── */
        .notes-box {
            border-left: 3px solid #d1d5db;
            padding: 8px 12px;
            background: #f9fafb;
            border-radius: 0 4px 4px 0;
            margin-bottom: 24px;
        }

        .notes-box .notes-title {
            font-size: 9px;
            text-transform: uppercase;
            color: #9ca3af;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .notes-box .notes-text {
            font-size: 11px;
            color: #374151;
            line-height: 1.5;
            white-space: pre-line;
        }

        /* ── TTD & Rekening ── */
        .footer-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }

        .ttd-label {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 48px;
        }

        .ttd-name {
            font-size: 12px;
            font-weight: 700;
            color: #111827;
            border-top: 1px solid #6b7280;
            padding-top: 4px;
            display: inline-block;
            min-width: 140px;
        }

        .ttd-role {
            font-size: 10px;
            color: #9ca3af;
        }

        .bank-title {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .bank-item {
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 8px 10px;
            margin-bottom: 8px;
            background: #f9fafb;
        }

        .bank-name {
            font-weight: 700;
            font-size: 11px;
            color: #1e3a5f;
        }

        .bank-detail {
            font-size: 10px;
            color: #374151;
            margin-top: 2px;
        }

        /* ── Status badge ── */
        .status-badge {
            display: inline-block;
            font-size: 9px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-draft {
            background: #f3f4f6;
            color: #374151;
        }

        .status-submit {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .status-approved {
            background: #dcfce7;
            color: #15803d;
        }

        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background: #fee2e2;
            color: #b91c1c;
        }

        .status-expired {
            background: #ffedd5;
            color: #c2410c;
        }

        .status-cancelled {
            background: #ffe4e6;
            color: #be123c;
        }

        /* ── Print ── */
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

    {{-- ── Toolbar Aksi (tidak tercetak) ── --}}
    <div class="toolbar">
        <span class="toolbar-title">
            Preview Penawaran &mdash; {{ $quotation->quotation_no }}
        </span>
        <div class="toolbar-actions">
            <a href="{{ route('quotation.index') }}" class="btn-tool btn-back">
                ← Kembali
            </a>
            <button onclick="window.print()" class="btn-tool btn-print">
                🖨 Cetak / Simpan PDF
            </button>
        </div>
    </div>

    {{-- ── Halaman Dokumen ── --}}
    <div class="page-wrapper">
        <div class="paper">

            {{-- Kop Surat --}}
            <div class="kop">
                <div class="kop-left">
                    <div class="store-name">{{ $store->name ?? 'Nama Toko' }}</div>
                    <div class="store-info">
                        @if($store?->address){{ $store->address }}<br>@endif
                        @if($store?->phone)Telp: {{ $store->phone }}@endif
                        @if($store?->email) &nbsp;|&nbsp; {{ $store->email }}@endif
                    </div>
                </div>
                <div class="kop-right">
                    <div class="doc-title">Penawaran Harga</div>
                    <div class="doc-no">{{ $quotation->quotation_no }}</div>
                    @php
                    $statusLabels = [
                    'draft' => 'Draft',
                    'submit' => 'Dikirim',
                    'approved' => 'Disetujui',
                    'paid' => 'Lunas',
                    'rejected' => 'Ditolak',
                    'expired' => 'Kadaluarsa',
                    'cancelled' => 'Dibatalkan',
                    ];
                    @endphp
                    <div style="margin-top:8px">
                        <span class="status-badge status-{{ $quotation->status }}">
                            {{ $statusLabels[$quotation->status] ?? $quotation->status }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Info Grid --}}
            @php
            $isExpired = $quotation->valid_until < now() && !in_array($quotation->status, ['paid','approved','rejected','cancelled']);
                @endphp
                <div class="info-grid">
                    <div class="info-item">
                        <div class="label">Kepada</div>
                        <div class="value">{{ $quotation->to_name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">No. Penawaran</div>
                        <div class="value">{{ $quotation->quotation_no }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Tanggal</div>
                        <div class="value">{{ $quotation->date->translatedFormat('d F Y') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Berlaku Hingga</div>
                        <div class="value {{ $isExpired ? 'expired' : '' }}">
                            {{ $quotation->valid_until->translatedFormat('d F Y') }}
                            @if($isExpired) <small>(Kadaluarsa)</small>@endif
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="label">Dibuat Oleh</div>
                        <div class="value">{{ $quotation->creator?->name ?? 'Admin' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Dibuat Tanggal</div>
                        <div class="value">{{ $quotation->created_at->translatedFormat('d F Y') }}</div>
                    </div>
                </div>

                {{-- Tabel Barang --}}
                <table class="items">
                    <thead>
                        <tr>
                            <th class="center" style="width:32px">No</th>
                            <th>Nama Barang</th>
                            <th class="center" style="width:60px">Satuan</th>
                            <th class="right" style="width:48px">Qty</th>
                            <th class="right" style="width:110px">Harga Satuan</th>
                            <th class="right" style="width:60px">Diskon</th>
                            <th class="right" style="width:110px">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotation->items as $i => $item)
                        <tr>
                            <td class="center">{{ $i + 1 }}</td>
                            <td>{{ $item->product_name }}</td>
                            <td class="center" style="color:#2563eb; font-weight:600">{{ $item->unit_label ?: '—' }}</td>
                            <td class="right">{{ $item->qty }}</td>
                            <td class="right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="right discount">
                                {{ $item->discount_pct > 0 ? $item->discount_pct . '%' : '—' }}
                            </td>
                            <td class="right" style="font-weight:600">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Totals --}}
                <div class="totals-wrap">
                    <div class="totals-box">
                        <div class="totals-row">
                            <span class="label-t">Subtotal</span>
                            <span>Rp {{ number_format($quotation->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if($quotation->discount > 0)
                        <div class="totals-row">
                            <span class="label-t">Potongan</span>
                            <span class="neg">- Rp {{ number_format($quotation->discount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        @if($quotation->tax_rate > 0)
                        <div class="totals-row">
                            <span class="label-t">Pajak ({{ rtrim(rtrim(number_format($quotation->tax_rate, 2, ',', '.'), '0'), ',') }}%)</span>
                            <span>Rp {{ number_format($quotation->tax_amount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="totals-row grand">
                            <span class="label-t">Total Pembayaran</span>
                            <span>Rp {{ number_format($quotation->total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Catatan --}}
                @if($quotation->notes)
                <div class="notes-box">
                    <div class="notes-title">Catatan / Syarat &amp; Ketentuan</div>
                    <div class="notes-text">{{ $quotation->notes }}</div>
                </div>
                @endif

                {{-- TTD & Rekening --}}
                <div class="footer-grid">
                    <div>
                        <div class="ttd-label">Hormat kami,</div>
                        <div class="ttd-name">{{ $store?->owner_name ?? $store?->name ?? 'Pemilik Toko' }}</div>
                        <div class="ttd-role">Pemilik / Kasir</div>
                    </div>
                    <div>
                        <div class="bank-title">Informasi Pembayaran</div>
                        @if($store?->bank_accounts && count($store->bank_accounts) > 0)
                        @foreach($store->bank_accounts as $bank)
                        <div class="bank-item">
                            <div class="bank-name">{{ $bank['bank_name'] ?? '' }}</div>
                            <div class="bank-detail">No. Rekening: {{ $bank['account_no'] ?? '' }}</div>
                            <div class="bank-detail">A/N: {{ $bank['account_name'] ?? '' }}</div>
                        </div>
                        @endforeach
                        @else
                        <p style="font-size:10px; color:#9ca3af; font-style:italic">
                            Belum ada info rekening. Lengkapi di Pengaturan → Profil Toko.
                        </p>
                        @endif
                    </div>
                </div>

        </div>{{-- /paper --}}
    </div>{{-- /page-wrapper --}}

</body>

</html>