<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('cancel_reason')->nullable()->after('notes');
            $table->string('cancelled_by')->nullable()->after('cancel_reason'); // 'admin' | 'dropshipper'
            $table->string('cancel_stage')->nullable()->after('cancelled_by'); // stage saat dibatalkan
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['cancel_reason', 'cancelled_by', 'cancel_stage']);
        });
    }
};
