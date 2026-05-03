
<?php $__env->startSection("title", "Master Barang"); ?>
<?php $__env->startSection("page_title", "Master Barang"); ?>
<?php $__env->startSection("page_subtitle", "Kelola data produk"); ?>

<?php $__env->startSection("content"); ?>


<div class="card mb-4">
    <form method="GET" class="flex gap-3 flex-wrap">
        <input type="text" name="search" value="<?php echo e(request("search")); ?>" placeholder="Cari nama/kode/barcode..." class="input-field flex-1 min-w-48">
        <select name="category" class="input-field w-48">
            <option value="">Semua Kategori</option>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($cat->id); ?>" <?php echo e(request("category") == $cat->id ? "selected" : ""); ?>><?php echo e($cat->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <button type="submit" class="btn-primary">Cari</button>
        <a href="<?php echo e(route("master.create")); ?>" class="btn-success">+ Tambah Produk</a>
    </form>
</div>

<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="text-left py-2 px-3">Kode</th>
                    <th class="text-left py-2 px-3">Nama Produk</th>
                    <th class="text-left py-2 px-3">Kategori</th>
                    <th class="text-right py-2 px-3">Harga Eceran</th>
                    <th class="text-left py-2 px-3">Satuan</th>
                    <th class="text-right py-2 px-3">Stok</th>
                    <th class="text-center py-2 px-3">Status</th>
                    <th class="text-center py-2 px-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-2 px-3 font-mono text-xs"><?php echo e($product->code); ?></td>
                    <td class="py-2 px-3 font-medium"><?php echo e($product->name); ?></td>
                    <td class="py-2 px-3 text-gray-600"><?php echo e($product->category->name); ?></td>
                    <td class="py-2 px-3 text-right">Rp <?php echo e(number_format($product->retail_price, 0, ",", ".")); ?></td>
                    <td class="py-2 px-3">
                        <span class="text-xs font-medium text-gray-700"><?php echo e($product->unit); ?></span>
                        <?php if($product->unitConversions->count() > 0): ?>
                        <div class="flex flex-wrap gap-1 mt-0.5">
                            <?php $__currentLoopData = $product->unitConversions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="inline-flex items-center text-xs bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded" title="1 <?php echo e($conv->unit_name); ?> = <?php echo e($conv->conversion_qty); ?> <?php echo e($product->unit); ?>, jual: Rp <?php echo e(number_format($conv->sell_price, 0, ',', '.')); ?>">
                                <?php echo e($conv->unit_name); ?>

                            </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td class="py-2 px-3 text-right <?php echo e($product->isLowStock() ? "text-red-600 font-bold" : ""); ?>">
                        <?php echo e($product->stock); ?> <?php echo e($product->unit); ?>

                        <?php if($product->isLowStock()): ?> <span class="text-xs">(min: <?php echo e($product->min_stock); ?>)</span> <?php endif; ?>
                    </td>
                    <td class="py-2 px-3 text-center">
                        <?php if($product->is_active): ?>
                        <span class="badge-success">Aktif</span>
                        <?php else: ?>
                        <span class="badge-danger">Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-2 px-3 text-center">
                        <a href="<?php echo e(route("master.edit", $product)); ?>" class="btn-primary text-xs py-1 px-2">Edit</a>
                        <form method="POST" action="<?php echo e(route("master.destroy", $product)); ?>" class="inline" onsubmit="return confirm(" Nonaktifkan produk ini?")">
                            <?php echo csrf_field(); ?> <?php echo method_field("DELETE"); ?>
                            <button type="submit" class="btn-danger text-xs py-1 px-2">Nonaktif</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" class="text-center py-6 text-gray-500">Tidak ada produk ditemukan.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-4"><?php echo e($products->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make("layouts.app", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\Minimarket\resources\views/master/index.blade.php ENDPATH**/ ?>