<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProductImportTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new ProductImportDataSheet(),
            new ProductImportRefSheet(),
        ];
    }
}

// ─── Sheet 1: Data Template ───────────────────────────────────────────────────
class ProductImportDataSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    public function title(): string
    {
        return 'Import Barang';
    }

    public function array(): array
    {
        return [
            // Row 1: Title
            ['TEMPLATE IMPORT MASTER BARANG & STOK AWAL', '', '', '', '', '', '', '', '', '', '', ''],
            // Row 2: Instructions
            ['Petunjuk: Isi data mulai baris ke-5. Jangan ubah baris 1-4. Kode Produk boleh dikosongkan (otomatis). Kategori & Satuan wajib diisi. Lihat sheet "Referensi" untuk daftar kategori & satuan yang ada.', '', '', '', '', '', '', '', '', '', '', ''],
            // Row 3: blank spacer
            ['', '', '', '', '', '', '', '', '', '', '', ''],
            // Row 4: Headers
            [
                'Kode Produk',
                'Nama Barang',
                'Kategori',
                'Barcode',
                'Satuan',
                'Harga Jual Eceran',
                'Harga Jual Grosir',
                'Min Qty Grosir',
                'Stok Awal',
                'Stok Minimum',
                'Deskripsi',
                'Status',
            ],
            // Row 5: Sample 1
            [
                '',
                'Indomie Goreng',
                'Mie Instan',
                '8992388024735',
                'pcs',
                3500,
                3200,
                10,
                120,
                24,
                'Mie goreng rasa spesial',
                'Aktif',
            ],
            // Row 6: Sample 2
            [
                '',
                'Beras Premium 5kg',
                'Bahan Pokok',
                '8996001234567',
                'kg',
                72000,
                68000,
                5,
                200,
                20,
                '',
                'Aktif',
            ],
            // Row 7: Sample 3
            [
                'MNM-001',
                'Aqua 600ml',
                'Minuman',
                '8999999123456',
                'botol',
                3000,
                2700,
                12,
                48,
                12,
                'Air mineral botol',
                'Aktif',
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 16,
            'B' => 28,
            'C' => 18,
            'D' => 18,
            'E' => 10,
            'F' => 20,
            'G' => 20,
            'H' => 16,
            'I' => 12,
            'J' => 14,
            'K' => 28,
            'L' => 12,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Merge title
        $sheet->mergeCells('A1:L1');
        $sheet->mergeCells('A2:L2');
        $sheet->mergeCells('A3:L3');

        // Title style
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A8A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Instruction style
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 9, 'color' => ['argb' => 'FF374151']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFDBEAFE']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(36);

        // Header row style
        $sheet->getStyle('A4:L4')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFBFDBFE']],
            ],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(20);

        // Sample data style (zebra)
        $sheet->getStyle('A5:L7')->applyFromArray([
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFD1D5DB']],
            ],
        ]);
        $sheet->getStyle('A5:L5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFAFAFA');
        $sheet->getStyle('A6:L6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFEFF6FF');
        $sheet->getStyle('A7:L7')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFAFAFA');

        // Mark price columns as number format
        $sheet->getStyle('F5:G7')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('H5:J7')->getNumberFormat()->setFormatCode('#,##0');

        // Note for Kode Produk column
        $sheet->getComment('A4')->getText()->createTextRun('Boleh dikosongkan. Akan digenerate otomatis berdasarkan kategori.');

        return [];
    }
}

// ─── Sheet 2: Reference ──────────────────────────────────────────────────────
class ProductImportRefSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    public function title(): string
    {
        return 'Referensi';
    }

    public function array(): array
    {
        $rows = [
            ['REFERENSI DATA', '', ''],
            ['', '', ''],
            ['KATEGORI YANG ADA DI SISTEM', '', 'CONTOH SATUAN'],
            ['Nama Kategori', 'Kode', 'Nama Satuan'],
        ];

        $categories = Category::orderBy('name')->get();
        $contohSatuan = ['pcs', 'kg', 'gram', 'liter', 'ml', 'botol', 'pack', 'dus', 'karton', 'lusin', 'meter', 'lembar'];

        $max = max($categories->count(), count($contohSatuan));
        for ($i = 0; $i < $max; $i++) {
            $cat  = $categories->get($i);
            $rows[] = [
                $cat?->name ?? '',
                $cat?->code ?? '',
                $contohSatuan[$i] ?? '',
            ];
        }

        $rows[] = ['', '', ''];
        $rows[] = ['', '', ''];
        $rows[] = ['STATUS VALID:', '', ''];
        $rows[] = ['Aktif', '', ''];
        $rows[] = ['Nonaktif', '', ''];

        return $rows;
    }

    public function columnWidths(): array
    {
        return ['A' => 30, 'B' => 12, 'C' => 18];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A8A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getStyle('A3:C3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FF1D4ED8']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFDBEAFE']],
        ]);

        $sheet->getStyle('A4:C4')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        return [];
    }
}
