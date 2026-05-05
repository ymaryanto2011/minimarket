<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanBulananExport implements WithMultipleSheets
{
    protected int $month;
    protected int $year;

    public function __construct(int $month, int $year)
    {
        $this->month = $month;
        $this->year  = $year;
    }

    public function sheets(): array
    {
        return [
            new LaporanBulananDetailSheet($this->month, $this->year),
            new LaporanBulananRingkasanSheet($this->month, $this->year),
        ];
    }
}

class LaporanBulananDetailSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithMapping
{
    protected int $month;
    protected int $year;

    public function __construct(int $month, int $year)
    {
        $this->month = $month;
        $this->year  = $year;
    }

    public function collection()
    {
        return Transaction::with('cashier', 'items')
            ->whereMonth('created_at', $this->month)
            ->whereYear('created_at', $this->year)
            ->where('status', 'paid')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function map($transaction): array
    {
        return [
            $transaction->invoice_no,
            $transaction->created_at->format('d/m/Y'),
            $transaction->created_at->format('H:i'),
            $transaction->cashier?->name ?? 'Admin',
            $transaction->items->count(),
            $transaction->items->sum('qty'),
            (float) $transaction->subtotal,
            (float) $transaction->discount,
            (float) $transaction->tax,
            (float) $transaction->total,
            ucfirst($transaction->payment_method ?? '-'),
        ];
    }

    public function headings(): array
    {
        return [
            'No. Invoice',
            'Tanggal',
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
        return 'Detail Transaksi';
    }
}

class LaporanBulananRingkasanSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected int $month;
    protected int $year;

    public function __construct(int $month, int $year)
    {
        $this->month = $month;
        $this->year  = $year;
    }

    public function collection()
    {
        $transactions = Transaction::whereMonth('created_at', $this->month)
            ->whereYear('created_at', $this->year)
            ->where('status', 'paid')
            ->get();

        $daily = $transactions->groupBy(fn($t) => $t->created_at->format('Y-m-d'))
            ->map(fn($g) => [
                'date'  => $g->first()->created_at->format('d/m/Y'),
                'count' => $g->count(),
                'total' => $g->sum('total'),
            ])
            ->sortKeys()
            ->values();

        return $daily->map(fn($d) => [$d['date'], $d['count'], (float) $d['total']]);
    }

    public function headings(): array
    {
        return ['Tanggal', 'Jumlah Transaksi', 'Omzet'];
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
        return 'Ringkasan Harian';
    }
}
