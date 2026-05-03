

<?php $__env->startSection('title', 'Laporan Penjualan'); ?>
<?php $__env->startSection('page_title', 'Laporan Penjualan'); ?>
<?php $__env->startSection('page_subtitle', 'Pantau omzet dan transaksi'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">
    <!-- Filter -->
    <div class="card space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Laporan</label>
                <select class="input-field">
                    <option>Laporan Harian</option>
                    <option>Laporan Bulanan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                <input type="date" class="input-field" value="2026-05-02">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                <input type="date" class="input-field" value="2026-05-02">
            </div>
            <div class="flex items-end">
                <button class="btn-primary w-full">Generate Laporan</button>
            </div>
        </div>
    </div>

    <!-- Ringkasan Laporan Harian -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="card">
            <p class="text-gray-600 text-sm">Total Omzet</p>
            <p class="text-3xl font-bold text-green-600 mt-2">Rp 5.234.000</p>
        </div>
        <div class="card">
            <p class="text-gray-600 text-sm">Jumlah Transaksi</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">42</p>
        </div>
        <div class="card">
            <p class="text-gray-600 text-sm">Total Item Terjual</p>
            <p class="text-3xl font-bold text-purple-600 mt-2">187 unit</p>
        </div>
        <div class="card">
            <p class="text-gray-600 text-sm">Rata-rata/Transaksi</p>
            <p class="text-3xl font-bold text-orange-600 mt-2">Rp 124.619</p>
        </div>
    </div>

    <!-- Breakdown Metode Pembayaran -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="card">
            <div class="card-header">Breakdown Metode Pembayaran</div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span>Tunai</span>
                    <div class="flex items-center">
                        <div class="w-24 bg-gray-200 rounded-full h-2 mr-3">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 65%;"></div>
                        </div>
                        <span class="font-bold">Rp 3.402.000 (65%)</span>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span>Kartu</span>
                    <div class="flex items-center">
                        <div class="w-24 bg-gray-200 rounded-full h-2 mr-3">
                            <div class="bg-green-600 h-2 rounded-full" style="width: 35%;"></div>
                        </div>
                        <span class="font-bold">Rp 1.832.000 (35%)</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Top 5 Produk Terjual</div>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span>1. Indomie Mie Goreng</span>
                    <span class="font-bold">45 unit</span>
                </div>
                <div class="flex justify-between">
                    <span>2. Teh Botol Sosro</span>
                    <span class="font-bold">38 unit</span>
                </div>
                <div class="flex justify-between">
                    <span>3. Kopi Instant</span>
                    <span class="font-bold">32 unit</span>
                </div>
                <div class="flex justify-between">
                    <span>4. Gula 1 kg</span>
                    <span class="font-bold">28 unit</span>
                </div>
                <div class="flex justify-between">
                    <span>5. Minyak Goreng</span>
                    <span class="font-bold">24 unit</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Transaksi Harian -->
    <div class="card overflow-x-auto">
        <div class="card-header">Detail Transaksi Harian (02 Mei 2026)</div>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="text-left py-3 px-4 font-semibold">No. Invoice</th>
                    <th class="text-left py-3 px-4 font-semibold">Waktu</th>
                    <th class="text-left py-3 px-4 font-semibold">Item</th>
                    <th class="text-left py-3 px-4 font-semibold">Qty</th>
                    <th class="text-left py-3 px-4 font-semibold">Total</th>
                    <th class="text-left py-3 px-4 font-semibold">Metode</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4 font-mono">INV-20260502-001</td>
                    <td class="py-3 px-4">14:35</td>
                    <td class="py-3 px-4 text-xs">Indomie, Teh</td>
                    <td class="py-3 px-4">12</td>
                    <td class="py-3 px-4 font-bold">Rp 125.000</td>
                    <td class="py-3 px-4"><span class="badge-success">Tunai</span></td>
                </tr>
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4 font-mono">INV-20260502-002</td>
                    <td class="py-3 px-4">14:28</td>
                    <td class="py-3 px-4 text-xs">Kopi Instant</td>
                    <td class="py-3 px-4">5</td>
                    <td class="py-3 px-4 font-bold">Rp 87.500</td>
                    <td class="py-3 px-4"><span class="badge-warning">Kartu</span></td>
                </tr>
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4 font-mono">INV-20260502-003</td>
                    <td class="py-3 px-4">14:15</td>
                    <td class="py-3 px-4 text-xs">Gula, Minyak</td>
                    <td class="py-3 px-4">8</td>
                    <td class="py-3 px-4 font-bold">Rp 250.000</td>
                    <td class="py-3 px-4"><span class="badge-success">Tunai</span></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Export -->
    <div class="flex gap-2 justify-end">
        <button class="btn-secondary">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Export PDF
        </button>
        <button class="btn-secondary">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Export Excel
        </button>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\Minimarket\resources\views/report/index.blade.php ENDPATH**/ ?>