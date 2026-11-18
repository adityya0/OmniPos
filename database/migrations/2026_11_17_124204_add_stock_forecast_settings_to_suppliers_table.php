<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->unsignedSmallInteger('stock_lead_time_days')->default(3)->after('pos_pricing_mode');
            $table->unsignedSmallInteger('stock_target_days')->default(14)->after('stock_lead_time_days');
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['stock_lead_time_days', 'stock_target_days']);
        });
    }
};
