

<?php $__env->startSection('title', 'Cetak Barcode'); ?>
<?php $__env->startSection('page_title', 'Cetak Barcode'); ?>
<?php $__env->startSection('page_subtitle', 'Generate dan print label barcode barang'); ?>

<?php $__env->startSection('content'); ?>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Panel -->
    <div class="lg:col-span-1">
        <div class="card space-y-4">
            <div class="card-header">Setting Barcode</div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Barang</label>
                <select class="input-field">
                    <option>-- Pilih Barang --</option>
                    <option data-code="B001">B001 - Indomie Mie Goreng</option>
                    <option data-code="B002">B002 - Teh Botol Sosro</option>
                    <option data-code="B003">B003 - Kopi Instant</option>
                    <option data-code="B004">B004 - Gula 1 kg</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Label</label>
                <input type="number" class="input-field" value="10" min="1">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Format Barcode</label>
                <select class="input-field">
                    <option>CODE128</option>
                    <option>EAN-13</option>
                    <option>UPC-A</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tata Letak Label</label>
                <select class="input-field">
                    <option>1 Kolom x 1 Baris</option>
                    <option>2 Kolom x 3 Baris</option>
                    <option>3 Kolom x 5 Baris</option>
                </select>
            </div>

            <div class="border-t pt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tampilkan di Label</label>
                <div class="space-y-2">
                    <div class="flex items-center">
                        <input type="checkbox" id="show-code" checked class="w-4 h-4">
                        <label for="show-code" class="ml-2 text-sm">Kode Barcode</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="show-name" checked class="w-4 h-4">
                        <label for="show-name" class="ml-2 text-sm">Nama Barang</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="show-price" class="w-4 h-4">
                        <label for="show-price" class="ml-2 text-sm">Harga Jual</label>
                    </div>
                </div>
            </div>

            <div class="flex gap-2">
                <button class="btn-primary flex-1">Preview</button>
                <button class="btn-success flex-1">Cetak</button>
            </div>
        </div>
    </div>

    <!-- Preview Panel -->
    <div class="lg:col-span-2">
        <div class="card">
            <div class="card-header">Preview Label</div>

            <!-- Barcode Preview -->
            <div class="bg-gray-50 p-6 rounded-lg border-2 border-dashed border-gray-300">
                <div class="grid grid-cols-1 gap-4 text-center">
                    <!-- Single Label Preview -->
                    <div style="width: 100px; margin: 0 auto;">
                        <div class="bg-white p-3 border border-gray-300 rounded text-center">
                            <!-- Barcode SVG Placeholder -->
                            <div class="bg-gray-800 text-white py-8 text-sm font-mono mb-2">
                                ■■■■■■■■■
                            </div>
                            <p class="text-xs font-bold">B001</p>
                            <p class="text-xs text-gray-600">Indomie Mie</p>
                            <p class="text-xs text-gray-600">Goreng</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 space-y-3">
                <h3 class="font-bold text-gray-800">Informasi Label</h3>
                <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                    <p class="text-sm"><strong>Ukuran Label:</strong> 40mm x 30mm</p>
                    <p class="text-sm"><strong>Total Label:</strong> 10 lembar</p>
                    <p class="text-sm"><strong>Estimasi Kertas:</strong> A4 (1 halaman)</p>
                    <p class="text-sm text-gray-600">Gunakan printer label atau gunting manual di garis putus-putus</p>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="font-bold text-gray-800 mb-3">Histori Cetak Barcode</h3>
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Barang</th>
                            <th class="text-left py-2">Qty</th>
                            <th class="text-left py-2">Tanggal</th>
                            <th class="text-left py-2">User</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b">
                            <td class="py-2">Indomie Mie Goreng</td>
                            <td class="py-2">10</td>
                            <td class="py-2">02 Mei 2026, 11:30</td>
                            <td class="py-2">Admin User</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2">Teh Botol Sosro</td>
                            <td class="py-2">5</td>
                            <td class="py-2">02 Mei 2026, 10:15</td>
                            <td class="py-2">Admin User</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\Minimarket\resources\views/barcode/index.blade.php ENDPATH**/ ?>