@extends('layouts.app')
@section('title', 'Backup Database')
@section('page_title', 'Backup Database')
@section('page_subtitle', 'Unduh backup database lengkap dalam format SQL')

@section('content')
<div class="max-w-2xl space-y-6">

    {{-- Info Card --}}
    <div class="card">
        <div class="card-header">Backup Database Komplit</div>
        <div class="space-y-4">
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex gap-3">
                <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-sm text-blue-700">
                    <p class="font-semibold mb-1">Informasi Backup</p>
                    <ul class="space-y-0.5 text-blue-600 list-disc list-inside">
                        <li>Backup mencakup seluruh tabel & data database</li>
                        <li>Format: SQL — dapat diimport kembali via phpMyAdmin</li>
                        <li>File akan diunduh langsung ke komputer Anda</li>
                        <li>Disarankan backup rutin setiap hari atau sebelum update sistem</li>
                    </ul>
                </div>
            </div>

            {{-- DB Info --}}
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-gray-500 text-xs mb-0.5">Database</p>
                    <p class="font-semibold text-gray-800">{{ config('database.connections.mysql.database') }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-gray-500 text-xs mb-0.5">Host</p>
                    <p class="font-semibold text-gray-800">{{ config('database.connections.mysql.host') }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-gray-500 text-xs mb-0.5">Tanggal & Waktu</p>
                    <p class="font-semibold text-gray-800">{{ now()->format('d/m/Y H:i:s') }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-gray-500 text-xs mb-0.5">Diakses oleh</p>
                    <p class="font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                </div>
            </div>

            <a href="{{ route('setting.backup.download') }}"
                class="flex items-center justify-center gap-2 btn-success w-full py-3 text-base font-semibold rounded-xl"
                onclick="this.innerHTML='<svg class=\'w-5 h-5 animate-spin\' fill=\'none\' viewBox=\'0 0 24 24\'><circle class=\'opacity-25\' cx=\'12\' cy=\'12\' r=\'10\' stroke=\'currentColor\' stroke-width=\'4\'/><path class=\'opacity-75\' fill=\'currentColor\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z\'/></svg> Menyiapkan backup...'; setTimeout(()=>location.reload(),3000);">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Unduh Backup SQL Sekarang
            </a>
        </div>
    </div>

    {{-- Warning --}}
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex gap-3">
        <svg class="w-5 h-5 text-yellow-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
        </svg>
        <div class="text-sm text-yellow-700">
            <p class="font-semibold">Penting!</p>
            <p class="mt-0.5">Simpan file backup di tempat yang aman. Jangan bagikan file backup kepada pihak yang tidak berwenang karena berisi seluruh data transaksi dan pengguna.</p>
        </div>
    </div>

    {{-- Restore info --}}
    <div class="card">
        <div class="card-header">Cara Restore Backup</div>
        <ol class="list-decimal list-inside text-sm text-gray-600 space-y-2 mt-1">
            <li>Buka <strong>phpMyAdmin</strong> di browser (<code class="bg-gray-100 px-1 rounded">http://localhost/phpmyadmin</code>)</li>
            <li>Pilih database <strong>{{ config('database.connections.mysql.database') }}</strong></li>
            <li>Klik tab <strong>Import</strong></li>
            <li>Pilih file <code class="bg-gray-100 px-1 rounded">.sql</code> hasil backup</li>
            <li>Klik <strong>Go / Kirim</strong></li>
        </ol>
    </div>

</div>
@endsection