

<?php $__env->startSection('title', 'Preview PDF Penawaran'); ?>
<?php $__env->startSection('page_title', 'Preview PDF Penawaran'); ?>
<?php $__env->startSection('page_subtitle', 'Simulasi tampilan dokumen sebelum diunduh'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">
    <div class="card">
        <div class="card-header">Preview Penawaran #<?php echo e($id ?? '-'); ?></div>
        <p class="text-sm text-gray-600 mb-4">Halaman placeholder untuk preview PDF. Integrasi generator PDF dapat ditambahkan di tahap berikutnya.</p>

        <div class="rounded-lg border border-gray-200 p-6 bg-white">
            <h3 class="text-lg font-semibold">Dokumen Penawaran Barang</h3>
            <p class="text-sm text-gray-600 mt-1">Nomor: PW-20260502-0001</p>
            <p class="text-sm text-gray-600">Kepada: PT ABC Distributor</p>
            <hr class="my-4">
            <p class="text-sm text-gray-700">Area isi PDF placeholder.</p>
        </div>
    </div>

    <div class="flex gap-2 justify-end">
        <a href="<?php echo e(route('quotation.index')); ?>" class="btn-secondary">Kembali</a>
        <button type="button" class="btn-primary">Unduh PDF</button>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\Minimarket\resources\views/quotation/pdf.blade.php ENDPATH**/ ?>