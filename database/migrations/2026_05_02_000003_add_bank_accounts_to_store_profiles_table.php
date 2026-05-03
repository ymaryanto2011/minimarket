<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_profiles', function (Blueprint $table) {
            $table->string('owner_name')->nullable()->after('email');
            $table->json('bank_accounts')->nullable()->after('owner_name');
        });
    }

    public function down(): void
    {
        Schema::table('store_profiles', function (Blueprint $table) {
            $table->dropColumn(['owner_name', 'bank_accounts']);
        });
    }
};
