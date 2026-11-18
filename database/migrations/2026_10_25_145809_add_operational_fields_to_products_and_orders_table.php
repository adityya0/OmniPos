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
        Schema::table('products', function (Blueprint $table) {
            $table->integer('safety_stock')->default(5)->after('stock');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_code')->nullable()->after('invoice_number');
            $table->string('shipping_receipt_number')->nullable()->after('order_code');
            $table->string('status')->default('selesai')->after('payment_status'); // menunggu_packing, dipacking, dikirim, selesai, dibatalkan
            $table->dateTime('marketplace_deadline')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('safety_stock');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_code', 'shipping_receipt_number', 'status', 'marketplace_deadline']);
        });
    }
};
