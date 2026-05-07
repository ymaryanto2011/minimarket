<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;

// ─── Public: Auth ─────────────────────────────────────────────────────────────
Route::get('/login',  [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// PWA Offline Fallback (public)
Route::get('/offline', fn() => view('offline'))->name('offline');

// ─── Protected: All authenticated users ────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // POS — accessible by all roles
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::get('/pos/search', [PosController::class, 'search'])->name('pos.search');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

    // ── Admin + Supervisor routes ─────────────────────────────────────────────
    Route::middleware('role:admin,supervisor')->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Stok Barang
        Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
        Route::get('/stock/masuk', [StockController::class, 'masuk'])->name('stock.masuk');
        Route::post('/stock/masuk', [StockController::class, 'storeMasuk'])->name('stock.storeMasuk');
        Route::get('/stock/penyesuaian', [StockController::class, 'penyesuaian'])->name('stock.penyesuaian');
        Route::post('/stock/penyesuaian', [StockController::class, 'storePenyesuaian'])->name('stock.storePenyesuaian');
        Route::get('/stock/export-excel', [StockController::class, 'exportExcel'])->name('stock.exportExcel');

        // Penawaran
        Route::get('/quotation', [QuotationController::class, 'index'])->name('quotation.index');
        Route::get('/quotation/create', [QuotationController::class, 'create'])->name('quotation.create');
        Route::post('/quotation', [QuotationController::class, 'store'])->name('quotation.store');
        Route::get('/quotation/{quotation}/edit', [QuotationController::class, 'edit'])->name('quotation.edit');
        Route::put('/quotation/{quotation}', [QuotationController::class, 'update'])->name('quotation.update');
        Route::delete('/quotation/{quotation}', [QuotationController::class, 'destroy'])->name('quotation.destroy');
        Route::get('/quotation/{quotation}/pdf', [QuotationController::class, 'pdf'])->name('quotation.pdf');
        Route::post('/quotation/{quotation}/convert-to-transaction', [QuotationController::class, 'convertToTransaction'])->name('quotation.convert');
        Route::get('/quotation/{quotation}', [QuotationController::class, 'show'])->name('quotation.show');

        // Laporan
        Route::get('/report', [ReportController::class, 'index'])->name('report.index');
        Route::get('/report/harian', [ReportController::class, 'harian'])->name('report.harian');
        Route::get('/report/bulanan', [ReportController::class, 'bulanan'])->name('report.bulanan');
        Route::get('/report/harian/excel', [ReportController::class, 'exportHarianExcel'])->name('report.harian.excel');
        Route::get('/report/harian/pdf', [ReportController::class, 'exportHarianPdf'])->name('report.harian.pdf');
        Route::get('/report/bulanan/excel', [ReportController::class, 'exportBulananExcel'])->name('report.bulanan.excel');
        Route::get('/report/bulanan/pdf', [ReportController::class, 'exportBulananPdf'])->name('report.bulanan.pdf');

        // Barcode
        Route::get('/barcode', [BarcodeController::class, 'index'])->name('barcode.index');
    });

    // ── Admin only routes ─────────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {

        // Master Barang
        Route::get('/master', [MasterController::class, 'index'])->name('master.index');
        Route::get('/master/create', [MasterController::class, 'create'])->name('master.create');
        Route::post('/master', [MasterController::class, 'store'])->name('master.store');
        Route::get('/master/next-code', [MasterController::class, 'nextCode'])->name('master.next-code');
        Route::get('/master/import-template', [MasterController::class, 'importTemplate'])->name('master.import-template');
        Route::post('/master/import', [MasterController::class, 'import'])->name('master.import');
        Route::get('/master/{product}/edit', [MasterController::class, 'edit'])->name('master.edit');
        Route::put('/master/{product}', [MasterController::class, 'update'])->name('master.update');
        Route::delete('/master/{product}', [MasterController::class, 'destroy'])->name('master.destroy');

        // Master Kategori
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Setting
        Route::get('/setting/profile', [SettingController::class, 'profile'])->name('setting.profile');
        Route::post('/setting/profile', [SettingController::class, 'profileUpdate'])->name('setting.profileUpdate');

        // Setting - Satuan
        Route::get('/setting/units', [UnitController::class, 'index'])->name('setting.units');
        Route::post('/setting/units', [UnitController::class, 'store'])->name('setting.units.store');
        Route::put('/setting/units/{unit}', [UnitController::class, 'update'])->name('setting.units.update');
        Route::delete('/setting/units/{unit}', [UnitController::class, 'destroy'])->name('setting.units.destroy');

        // Manajemen User
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');
        Route::post('/users/{user}/set-password', [UserController::class, 'setPassword'])->name('users.setPassword');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Backup Database
        Route::get('/setting/backup', [BackupController::class, 'index'])->name('setting.backup');
        Route::get('/setting/backup/download', [BackupController::class, 'download'])->name('setting.backup.download');
    });
});
