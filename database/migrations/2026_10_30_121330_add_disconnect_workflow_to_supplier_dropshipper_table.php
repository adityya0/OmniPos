<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('supplier_dropshipper', function (Blueprint $table) {
            $table->string('status', 30)->default('approved')->change();
            $table->timestamp('disconnect_requested_at')->nullable()->after('status');
            $table->string('disconnect_requested_by', 20)->nullable()->after('disconnect_requested_at'); // 'dropshipper' atau 'supplier'
            $table->text('disconnect_reason')->nullable()->after('disconnect_requested_by');
            $table->text('disconnect_rejection_reason')->nullable()->after('disconnect_reason');
            $table->timestamp('disconnect_approved_at')->nullable()->after('disconnect_rejection_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_dropshipper', function (Blueprint $table) {
            $table->dropColumn([
                'disconnect_requested_at',
                'disconnect_requested_by',
                'disconnect_reason',
                'disconnect_rejection_reason',
                'disconnect_approved_at',
            ]);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved')->change();
        });
    }
};
