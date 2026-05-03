<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->decimal('tax_rate', 5, 2)->default(0)->after('discount');
            $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_rate');
        });

        // Extend status enum to include paid & cancelled
        DB::statement("ALTER TABLE quotations MODIFY COLUMN status ENUM('draft','submit','approved','paid','rejected','expired','cancelled') NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['tax_rate', 'tax_amount']);
        });
        DB::statement("ALTER TABLE quotations MODIFY COLUMN status ENUM('draft','submit','approved','rejected','expired') NOT NULL DEFAULT 'draft'");
    }
};
