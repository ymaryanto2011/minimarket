<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StokBarangExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithMapping
{
    public function collection()
    {
        return Product::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function map($product): array
    {
        return [
            $product->code,
            $product->barcode ?? '-',
            $product->name,
            $product->category?->name ?? '-',
            $product->unit,
            (int) $product->stock,
            (int) $product->min_stock,
            $product->stock <= $product->min_stock ? 'Minimum!' : 'Aman',
            'Rp ' . number_format($product->retail_price ?? 0, 0, ',', '.'),
            'Rp ' . number_format($product->wholesale_price ?? 0, 0, ',', '.'),
            $product->updated_at->format('d/m/Y H:i'),
        ];
    }

    public function headings(): array
    {
        return [
            'Kode',
            'Barcode',
            'Nama Barang',
            'Kategori',
            'Satuan',
            'Stok Saat Ini',
            'Stok Minimum',
            'Status',
            'Harga Jual Eceran',
            'Harga Grosir',
            'Terakhir Update',
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
        return 'Stok Barang';
    }
}
