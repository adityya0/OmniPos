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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('transaction_type', ['pos_offline', 'dropship'])->default('pos_offline');
            $table->string('customer_name')->nullable()->default('Pelanggan Offline');
            $table->decimal('total_cost', 14, 2)->default(0); // Total HPP
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('final_amount', 14, 2)->default(0); // Total omset penjualan
            $table->decimal('net_profit', 14, 2)->default(0); // Laba bersih (final_amount - total_cost)
            $table->enum('payment_method', ['cash', 'transfer', 'qris'])->default('cash');
            $table->enum('payment_status', ['paid', 'pending', 'cancelled'])->default('paid');
            $table->decimal('cash_amount', 14, 2)->nullable();
            $table->decimal('change_amount', 14, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
