

<?php $__env->startSection('title', 'Laporan Penjualan'); ?>
<?php $__env->startSection('page_title', 'Laporan Penjualan'); ?>
<?php $__env->startSection('page_subtitle', 'Pantau omzet dan transaksi'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4" x-data="{ type: 'harian', date: '<?php echo e(now()->format('Y-m-d')); ?>', month: <?php echo e(now()->month); ?>, year: <?php echo e(now()->year); ?> }">

    
    <div class="bg-white rounded-lg shadow p-5">
        <h3 class="font-semibold text-gray-700 mb-4">Pilih Jenis Laporan</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Laporan</label>
                <select x-model="type" class="input-field">
                    <option value="harian">Laporan Harian</option>
                    <option value="bulanan">Laporan Bulanan</option>
                </select>
            </div>
            <div x-show="type === 'harian'">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" x-model="date" class="input-field" :max="'<?php echo e(now()->format('Y-m-d')); ?>'">
            </div>
            <div x-show="type === 'bulanan'" class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                    <select x-model="month" class="input-field">
                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Maret</option>
                        <option value="4">April</option>
                        <option value="5">Mei</option>
                        <option value="6">Juni</option>
                        <option value="7">Juli</option>
                        <option value="8">Agustus</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                    <select x-model="year" class="input-field">
                        <?php for($y = now()->year; $y >= now()->year - 4; $y--): ?>
                        <option value="<?php echo e($y); ?>"><?php echo e($y); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            <div class="flex gap-2">
                <button @click="
                    if(type==='harian') window.location.href='<?php echo e(route('report.harian')); ?>?date='+date;
                    else window.location.href='<?php echo e(route('report.bulanan')); ?>?month='+month+'&year='+year;
                " class="btn-primary flex-1">
                    Generate Laporan
                </button>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <a href="<?php echo e(route('report.harian', ['date' => now()->format('Y-m-d')])); ?>"
            class="bg-white rounded-lg shadow p-5 hover:shadow-md transition group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center group-hover:bg-blue-200 transition">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-800">Laporan Harian</p>
                    <p class="text-sm text-gray-500">Detail transaksi per hari + Export PDF & Excel</p>
                </div>
                <svg class="w-5 h-5 text-gray-400 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>

        <a href="<?php echo e(route('report.bulanan', ['month' => now()->month, 'year' => now()->year])); ?>"
            class="bg-white rounded-lg shadow p-5 hover:shadow-md transition group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center group-hover:bg-green-200 transition">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-800">Laporan Bulanan</p>
                    <p class="text-sm text-gray-500">Ringkasan & tren bulanan + Export PDF & Excel</p>
                </div>
                <svg class="w-5 h-5 text-gray-400 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>
    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\Minimarket\resources\views/report/index.blade.php ENDPATH**/ ?>