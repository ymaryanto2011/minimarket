<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            // Satuan yang dipakai saat input penawaran (mis: "dus" atau "botol")
            $table->string('unit_label')->nullable()->after('product_name');
            // Berapa satuan dasar dalam 1 unit ini (mis: 1 dus = 24 botol → 24)
            $table->decimal('conversion_qty', 10, 4)->default(1)->after('unit_label');
        });
    }

    public function down(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->dropColumn(['unit_label', 'conversion_qty']);
        });
    }
};
