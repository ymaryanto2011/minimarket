<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanHarianExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithMapping
{
    protected Carbon $date;

    public function __construct(Carbon $date)
    {
        $this->date = $date;
    }

    public function collection()
    {
        return Transaction::with('cashier', 'items')
            ->whereDate('created_at', $this->date)
            ->where('status', 'paid')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function map($transaction): array
    {
        return [
            $transaction->invoice_no,
            $transaction->created_at->format('H:i:s'),
            $transaction->cashier?->name ?? 'Admin',
            $transaction->items->count(),
            $transaction->items->sum('qty'),
            'Rp ' . number_format($transaction->subtotal, 0, ',', '.'),
            $transaction->discount > 0 ? 'Rp ' . number_format($transaction->discount, 0, ',', '.') : '-',
            $transaction->tax > 0 ? 'Rp ' . number_format($transaction->tax, 0, ',', '.') : '-',
            'Rp ' . number_format($transaction->total, 0, ',', '.'),
            ucfirst($transaction->payment_method ?? '-'),
        ];
    }

    public function headings(): array
    {
        return [
            'No. Invoice',
            'Waktu',
            'Kasir',
            'Jml Item',
            'Total Qty',
            'Subtotal',
            'Diskon',
            'Pajak',
            'Total',
            'Metode Bayar',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF1E3A5F']],
            ],
        ];
    }

    public function title(): string
    {
        return 'Laporan Harian ' . $this->date->format('d-m-Y');
    }
}
