
<?php $__env->startSection("title", "Setting Profil Toko"); ?>
<?php $__env->startSection("page_title", "Setting"); ?>
<?php $__env->startSection("page_subtitle", "Konfigurasi profil toko"); ?>

<?php $__env->startSection("extra_css"); ?>
<style>
    [x-cloak] {
        display: none !important
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection("content"); ?>


<form method="POST" action="<?php echo e(route("setting.profileUpdate")); ?>"
    x-data="{
        banks: <?php echo e(json_encode(old('bank_accounts', $store->bank_accounts ?? []) ?: [])); ?>,
        addBank() {
            if (this.banks.length < 5) this.banks.push({ bank_name:'', account_no:'', account_name:'' });
        },
        removeBank(i) { this.banks.splice(i, 1); }
    }">
    <?php echo csrf_field(); ?>
    <?php if($errors->any()): ?>
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
        <ul class="text-sm text-red-700 list-disc list-inside space-y-0.5">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>

    
    <div class="bg-white rounded-lg shadow p-6 mb-6 max-w-2xl">
        <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            Profil Toko
        </h3>
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Toko <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="<?php echo e(old('name', $store->name)); ?>" class="input-field" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pemilik</label>
                    <input type="text" name="owner_name" value="<?php echo e(old('owner_name', $store->owner_name)); ?>"
                        placeholder="Nama pemilik toko / direktur" class="input-field">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea name="address" rows="2" class="input-field"><?php echo e(old("address", $store->address)); ?></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                    <input type="text" name="phone" value="<?php echo e(old('phone', $store->phone)); ?>" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="<?php echo e(old('email', $store->email)); ?>" class="input-field">
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-lg shadow p-6 mb-6 max-w-2xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                Rekening Bank
            </h3>
            <button type="button" @click="addBank()" x-show="banks.length < 5"
                class="btn-secondary text-xs py-1.5 flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Rekening
            </button>
        </div>

        <p class="text-xs text-gray-400 mb-4">Rekening akan ditampilkan di dokumen penawaran (maksimal 5 rekening).</p>

        <div class="space-y-4">
            <template x-for="(bank, i) in banks" :key="i">
                <div class="border rounded-lg p-4 bg-gray-50 relative">
                    <div class="flex justify-between items-center mb-3">
                        <p class="text-xs font-semibold text-gray-600" x-text="`Rekening ${i + 1}`"></p>
                        <button type="button" @click="removeBank(i)"
                            class="text-red-400 hover:text-red-600 transition text-xs flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Hapus
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Nama Bank</label>
                            <input type="text" :name="`bank_accounts[${i}][bank_name]`" x-model="bank.bank_name"
                                placeholder="BCA, Mandiri, BRI..." class="input-field text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">No. Rekening</label>
                            <input type="text" :name="`bank_accounts[${i}][account_no]`" x-model="bank.account_no"
                                placeholder="1234567890" class="input-field text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Atas Nama</label>
                            <input type="text" :name="`bank_accounts[${i}][account_name]`" x-model="bank.account_name"
                                placeholder="Nama pemilik rekening" class="input-field text-sm">
                        </div>
                    </div>
                </div>
            </template>

            <div x-show="banks.length === 0" class="text-center py-8 text-gray-400 border-2 border-dashed rounded-lg">
                <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <p class="text-sm">Belum ada rekening bank.</p>
                <button type="button" @click="addBank()" class="mt-2 text-blue-500 text-sm hover:underline">+ Tambah Rekening</button>
            </div>
        </div>
    </div>

    <div class="max-w-2xl">
        <button type="submit" class="btn-primary">
            <svg class="w-4 h-4 inline mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Simpan Pengaturan
        </button>
    </div>
</form>
<?php $__env->stopSection(); ?>


<?php $__env->startSection("content"); ?>
<?php if(session("success")): ?>
<div class="alert-success mb-4"><?php echo e(session("success")); ?></div>
<?php endif; ?>

<div class="card max-w-xl">
    <h3 class="text-lg font-bold mb-4">Profil Toko</h3>
    <form method="POST" action="<?php echo e(route("setting.profileUpdate")); ?>">
        <?php echo csrf_field(); ?>
        <?php if($errors->any()): ?>
        <div class="alert-danger mb-4">
            <ul class="list-disc pl-4"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
        </div>
        <?php endif; ?>

        <div class="space-y-4">
            <div>
                <label class="form-label">Nama Toko *</label>
                <input type="text" name="name" value="<?php echo e(old("name", $store->name)); ?>" class="input-field" required>
            </div>
            <div>
                <label class="form-label">Alamat</label>
                <textarea name="address" rows="3" class="input-field"><?php echo e(old("address", $store->address)); ?></textarea>
            </div>
            <div>
                <label class="form-label">No. Telepon</label>
                <input type="text" name="phone" value="<?php echo e(old("phone", $store->phone)); ?>" class="input-field">
            </div>
            <div>
                <label class="form-label">Email</label>
                <input type="email" name="email" value="<?php echo e(old("email", $store->email)); ?>" class="input-field">
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="btn-primary">Simpan</button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make("layouts.app", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\Minimarket\resources\views/setting/index.blade.php ENDPATH**/ ?>