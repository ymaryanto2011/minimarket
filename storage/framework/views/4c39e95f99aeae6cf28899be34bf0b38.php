

<?php $__env->startSection("title", "Dashboard"); ?>
<?php $__env->startSection("page_title", "Dashboard"); ?>
<?php $__env->startSection("page_subtitle", "Ringkasan aktivitas toko hari ini"); ?>

<?php $__env->startSection("content"); ?>
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Omzet Hari Ini</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">Rp <?php echo e(number_format($todaySales, 0, ",", ".")); ?></p>
                <p class="text-blue-600 text-sm mt-1">Omzet Bulan: Rp <?php echo e(number_format($monthlySales, 0, ",", ".")); ?></p>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Transaksi Hari Ini</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo e($todayTransactions); ?></p>
                <p class="text-green-600 text-sm mt-1">Transaksi berhasil</p>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Produk Aktif</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo e($totalProducts); ?></p>
                <p class="text-purple-600 text-sm mt-1">Produk tersedia</p>
            </div>
            <div class="bg-purple-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4v10"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Stok Minimum Alert</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo e($lowStockProducts); ?></p>
                <?php if($lowStockProducts > 0): ?>
                    <p class="text-red-600 text-sm mt-1"><a href="<?php echo e(route("stock.index")); ?>?filter=low">Periksa segera</a></p>
                <?php else: ?>
                    <p class="text-green-600 text-sm mt-1">Stok aman</p>
                <?php endif; ?>
            </div>
            <div class="bg-red-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<div class="mt-6 card">
    <div class="card-header">5 Transaksi Terakhir</div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2 px-2">No Invoice</th>
                    <th class="text-left py-2 px-2">Waktu</th>
                    <th class="text-left py-2 px-2">Kasir</th>
                    <th class="text-right py-2 px-2">Total</th>
                    <th class="text-left py-2 px-2">Metode</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $recentTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-2 px-2 font-mono"><?php echo e($trx->invoice_no); ?></td>
                    <td class="py-2 px-2"><?php echo e($trx->created_at->format("H:i")); ?></td>
                    <td class="py-2 px-2"><?php echo e($trx->cashier?->name ?? "-"); ?></td>
                    <td class="py-2 px-2 text-right font-bold">Rp <?php echo e(number_format($trx->total, 0, ",", ".")); ?></td>
                    <td class="py-2 px-2"><span class="badge-info"><?php echo e(ucfirst($trx->payment_method)); ?></span></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="text-center py-4 text-gray-500">Belum ada transaksi hari ini.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("layouts.app", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\Minimarket\resources\views/dashboard/index.blade.php ENDPATH**/ ?>