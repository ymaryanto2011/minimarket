<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->boolean('is_custom')->default(false)->after('status');
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->onDelete('set null')->after('is_custom');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropForeign(['transaction_id']);
            $table->dropColumn(['is_custom', 'transaction_id']);
        });
    }
};
