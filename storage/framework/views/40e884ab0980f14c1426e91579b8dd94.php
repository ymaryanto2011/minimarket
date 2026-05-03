
<?php $__env->startSection('title', 'Kategori Barang'); ?>
<?php $__env->startSection('page_title', 'Master Kategori Barang'); ?>
<?php $__env->startSection('page_subtitle', 'Kelola kategori produk'); ?>

<?php $__env->startSection('content'); ?>


<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Tambah -->
    <div class="card">
        <h3 class="text-lg font-bold mb-4">Tambah Kategori</h3>
        <form method="POST" action="<?php echo e(route('categories.store')); ?>">
            <?php echo csrf_field(); ?>
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-sm mb-2"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <div class="space-y-3">
                <input type="text" name="name" value="<?php echo e(old('name')); ?>" placeholder="Nama kategori..." class="input-field w-full" required>
                <button type="submit" class="btn-primary w-full">+ Tambah Kategori</button>
            </div>
        </form>
    </div>

    <!-- Daftar Kategori -->
    <div class="lg:col-span-2 card">
        <h3 class="text-lg font-bold mb-4">Daftar Kategori (<?php echo e($categories->count()); ?>)</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b bg-gray-50">
                        <th class="text-left py-2 px-3">#</th>
                        <th class="text-left py-2 px-3">Nama Kategori</th>
                        <th class="text-right py-2 px-3">Jumlah Produk</th>
                        <th class="text-center py-2 px-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="border-b hover:bg-gray-50" x-data="{ editing: false }">
                        <td class="py-2 px-3 text-gray-500"><?php echo e($i + 1); ?></td>
                        <td class="py-2 px-3">
                            <span x-show="!editing" class="font-medium"><?php echo e($category->name); ?></span>
                            <form x-show="editing" method="POST" action="<?php echo e(route('categories.update', $category)); ?>" class="flex gap-2">
                                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                <input type="text" name="name" value="<?php echo e($category->name); ?>" class="input-field py-1 flex-1" required>
                                <button type="submit" class="btn-primary text-xs py-1 px-2">Simpan</button>
                                <button type="button" @click="editing = false" class="btn-secondary text-xs py-1 px-2">Batal</button>
                            </form>
                        </td>
                        <td class="py-2 px-3 text-right">
                            <a href="<?php echo e(route('master.index', ['category' => $category->id])); ?>" class="badge-info hover:underline">
                                <?php echo e($category->products_count); ?> produk
                            </a>
                        </td>
                        <td class="py-2 px-3 text-center">
                            <button @click="editing = !editing" x-show="!editing" class="btn-primary text-xs py-1 px-2">Edit</button>
                            <?php if($category->products_count == 0): ?>
                            <form method="POST" action="<?php echo e(route('categories.destroy', $category)); ?>" class="inline" x-show="!editing"
                                onsubmit="return confirm('Hapus kategori <?php echo e($category->name); ?>?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn-danger text-xs py-1 px-2">Hapus</button>
                            </form>
                            <?php else: ?>
                            <span class="text-xs text-gray-400 ml-1" x-show="!editing">Ada produk</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="text-center py-6 text-gray-500">Belum ada kategori. Tambahkan kategori di sebelah kiri.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\Minimarket\resources\views/master/categories.blade.php ENDPATH**/ ?>