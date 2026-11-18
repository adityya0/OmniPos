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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('email')->nullable()->after('contact_phone');
            $table->string('receipt_header_title')->nullable()->after('email');
            $table->string('receipt_logo')->nullable()->after('receipt_header_title');
            $table->string('receipt_icon', 50)->default('shopping-bag')->after('receipt_logo');
            $table->boolean('receipt_show_logo')->default(true)->after('receipt_icon');
            $table->boolean('receipt_show_header')->default(true)->after('receipt_show_logo');
            $table->boolean('receipt_show_address')->default(true)->after('receipt_show_header');
            $table->boolean('receipt_show_phone')->default(true)->after('receipt_show_address');
            $table->boolean('receipt_show_email')->default(true)->after('receipt_show_phone');
            $table->boolean('receipt_show_invoice')->default(true)->after('receipt_show_email');
            $table->boolean('receipt_show_date')->default(true)->after('receipt_show_invoice');
            $table->boolean('receipt_show_cashier')->default(true)->after('receipt_show_date');
            $table->boolean('receipt_show_customer')->default(false)->after('receipt_show_cashier');
            $table->boolean('receipt_show_subtotal')->default(true)->after('receipt_show_customer');
            $table->boolean('receipt_show_discount')->default(true)->after('receipt_show_subtotal');
            $table->boolean('receipt_show_payment_method')->default(true)->after('receipt_show_discount');
            $table->boolean('receipt_show_change')->default(true)->after('receipt_show_payment_method');
            $table->text('receipt_footer_notes')->nullable()->after('receipt_show_change');
            $table->boolean('receipt_show_footer')->default(true)->after('receipt_footer_notes');
            $table->string('receipt_paper_size', 10)->default('58mm')->after('receipt_show_footer');
            $table->json('printer_bluetooth_config')->nullable()->after('receipt_paper_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn([
                'email',
                'receipt_header_title',
                'receipt_logo',
                'receipt_icon',
                'receipt_show_logo',
                'receipt_show_header',
                'receipt_show_address',
                'receipt_show_phone',
                'receipt_show_email',
                'receipt_show_invoice',
                'receipt_show_date',
                'receipt_show_cashier',
                'receipt_show_customer',
                'receipt_show_subtotal',
                'receipt_show_discount',
                'receipt_show_payment_method',
                'receipt_show_change',
                'receipt_footer_notes',
                'receipt_show_footer',
                'receipt_paper_size',
                'printer_bluetooth_config',
            ]);
        });
    }
};
