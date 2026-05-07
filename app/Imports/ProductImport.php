<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Unit;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class ProductImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    /** Baris pertama data (setelah heading) */
    public int $headingRow = 4;

    /** Hasil: ['imported'=>n, 'skipped'=>n, 'errors'=>[...]] */
    public array $result = ['imported' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];

    public function headingRow(): int
    {
        return $this->headingRow;
    }

    public function collection(Collection $rows)
    {
        // Cache categories & units agar tidak query berulang
        $categoryCache = Category::pluck('id', 'name')->map(fn($v) => $v)->toArray();
        // normalize keys to lowercase
        $categoryByLower = [];
        foreach ($categoryCache as $name => $id) {
            $categoryByLower[Str::lower(trim($name))] = $id;
        }

        $rowNum = $this->headingRow + 1; // untuk pesan error (1-based)

        foreach ($rows as $row) {
            $rowNum++;

            try {
                // ── Baca kolom ────────────────────────────────────────────
                $namaBarang   = trim((string) ($row['nama_barang']  ?? $row['nama_produk'] ?? ''));
                $kategori     = trim((string) ($row['kategori']     ?? ''));
                $satuan       = trim((string) ($row['satuan']       ?? ''));
                $hargaEceran  = $this->parseNumber($row['harga_jual_eceran'] ?? $row['harga_eceran'] ?? 0);
                $hargaGrosir  = $this->parseNumber($row['harga_jual_grosir'] ?? $row['harga_grosir'] ?? $hargaEceran);
                $minGrosir    = (int) ($row['min_qty_grosir']       ?? 1);
                $stokAwal     = (int) ($row['stok_awal']            ?? 0);
                $stokMin      = (int) ($row['stok_minimum']         ?? 0);
                $barcode      = trim((string) ($row['barcode']      ?? ''));
                $deskripsi    = trim((string) ($row['deskripsi']    ?? ''));
                $kode         = trim((string) ($row['kode_produk']  ?? $row['kode'] ?? ''));
                $statusRaw    = Str::lower(trim((string) ($row['status'] ?? 'aktif')));
                $isActive     = in_array($statusRaw, ['aktif', 'active', '1', 'ya', 'yes', 'true']);

                // ── Validasi wajib ────────────────────────────────────────
                if ($namaBarang === '') {
                    $this->result['skipped']++;
                    continue;
                }
                if ($kategori === '') {
                    $this->result['errors'][] = "Baris {$rowNum}: Kategori kosong untuk produk '{$namaBarang}'.";
                    $this->result['skipped']++;
                    continue;
                }
                if ($satuan === '') {
                    $this->result['errors'][] = "Baris {$rowNum}: Satuan kosong untuk produk '{$namaBarang}'.";
                    $this->result['skipped']++;
                    continue;
                }

                // ── Resolve / buat kategori ───────────────────────────────
                $catKey = Str::lower($kategori);
                if (!isset($categoryByLower[$catKey])) {
                    $catCode = strtoupper(Str::limit(preg_replace('/[^A-Za-z0-9]/', '', $kategori), 4, ''));
                    $cat = Category::create(['name' => $kategori, 'code' => $catCode ?: 'XX']);
                    $categoryByLower[$catKey] = $cat->id;
                }
                $categoryId = $categoryByLower[$catKey];

                // ── Auto-generate kode jika kosong ────────────────────────
                if ($kode === '') {
                    $kode = $this->generateCode($categoryId, $kategori);
                }

                // ── Simpan / update produk ────────────────────────────────
                DB::beginTransaction();

                $existing = Product::where('code', $kode)->first();

                if ($existing) {
                    // Update data produk (tidak mengubah stok lewat field langsung)
                    $existing->update([
                        'name'              => $namaBarang,
                        'category_id'       => $categoryId,
                        'unit'              => $satuan,
                        'retail_price'      => $hargaEceran,
                        'wholesale_price'   => $hargaGrosir,
                        'min_wholesale_qty' => $minGrosir > 0 ? $minGrosir : 1,
                        'min_stock'         => $stokMin,
                        'barcode'           => $barcode ?: null,
                        'description'       => $deskripsi ?: null,
                        'is_active'         => $isActive,
                    ]);
                    // Jika stok awal > 0 dan berbeda, buat penyesuaian
                    if ($stokAwal > 0 && $existing->stock != $stokAwal) {
                        $before = $existing->stock;
                        $existing->update(['stock' => $stokAwal]);
                        StockMovement::create([
                            'product_id'   => $existing->id,
                            'type'         => 'adjustment',
                            'qty'          => $stokAwal - $before,
                            'stock_before' => $before,
                            'stock_after'  => $stokAwal,
                            'reference'    => 'IMPORT-EXCEL',
                            'note'         => 'Penyesuaian stok dari import Excel',
                            'user_id'      => Auth::id(),
                        ]);
                    }
                    $this->result['updated']++;
                } else {
                    $product = Product::create([
                        'code'              => $kode,
                        'name'              => $namaBarang,
                        'category_id'       => $categoryId,
                        'unit'              => $satuan,
                        'retail_price'      => $hargaEceran,
                        'wholesale_price'   => $hargaGrosir,
                        'min_wholesale_qty' => $minGrosir > 0 ? $minGrosir : 1,
                        'stock'             => $stokAwal,
                        'min_stock'         => $stokMin,
                        'barcode'           => $barcode ?: null,
                        'description'       => $deskripsi ?: null,
                        'is_active'         => $isActive,
                    ]);
                    // Catat stok awal sebagai stock-in
                    if ($stokAwal > 0) {
                        StockMovement::create([
                            'product_id'   => $product->id,
                            'type'         => 'in',
                            'qty'          => $stokAwal,
                            'stock_before' => 0,
                            'stock_after'  => $stokAwal,
                            'reference'    => 'IMPORT-EXCEL',
                            'note'         => 'Stok awal dari import Excel',
                            'user_id'      => Auth::id(),
                        ]);
                    }
                    $this->result['imported']++;
                }

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                $this->result['errors'][] = "Baris {$rowNum}: " . $e->getMessage();
                $this->result['skipped']++;
            }
        }
    }

    // ── Auto-generate kode produk ─────────────────────────────────────────────
    private function generateCode(int $categoryId, string $kategori): string
    {
        $prefix = strtoupper(Str::limit(preg_replace('/[^A-Za-z0-9]/', '', $kategori), 4, ''));
        if (empty($prefix)) $prefix = 'XX';

        $max = Product::where('category_id', $categoryId)
            ->where('code', 'like', $prefix . '-%')
            ->get()
            ->map(fn($p) => (int) substr($p->code, strlen($prefix) + 1))
            ->max() ?? 0;

        return $prefix . '-' . str_pad($max + 1, 3, '0', STR_PAD_LEFT);
    }

    private function parseNumber(mixed $val): float
    {
        if (is_numeric($val)) return (float) $val;
        // Handle "Rp 10.000" or "10,000"
        $clean = preg_replace('/[^0-9,.]/', '', (string) $val);
        $clean = str_replace(',', '', $clean);
        return (float) $clean;
    }
}
