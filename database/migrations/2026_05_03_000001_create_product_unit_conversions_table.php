<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_unit_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('unit_name');                             // e.g. "dus", "pack"
            $table->decimal('conversion_qty', 10, 4);               // 1 unit ini = N satuan dasar
            $table->decimal('sell_price', 15, 2)->default(0);       // harga jual per unit ini
            $table->decimal('buy_price', 15, 2)->default(0);        // harga beli per unit ini
            $table->timestamps();

            $table->unique(['product_id', 'unit_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_unit_conversions');
    }
};
