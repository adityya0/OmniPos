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
            $table->string('shipping_origin_city')->nullable()->default('Lewi Goong, Kab. Garut, Jawa Barat')->after('warehouse_address');
            $table->string('operating_hours')->nullable()->default('Senin - Sabtu: 08:00 - 17:00 WIB')->after('contact_phone');
            $table->string('cut_off_time')->nullable()->default('15:00 WIB')->after('operating_hours');
            $table->string('packing_sla')->nullable()->default('< 24 Jam Kerja')->after('cut_off_time');
            $table->string('supported_couriers')->nullable()->default('J&T Express, JNE (Reg/YES), SiCepat, Shopee Xpress (SPX), Anteraja, GoSend')->after('packing_sla');
            $table->string('packing_standard')->nullable()->default('Standard Bubble Wrap Tebal + Dus Pelindung + Label Fragile')->after('supported_couriers');
            $table->text('return_policy')->nullable()->after('packing_standard');
            $table->string('pic_name')->nullable()->default('Admin CS & Fulfillment Toko')->after('return_policy');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('dropship_store_name')->nullable()->after('phone');
            $table->text('address')->nullable()->after('dropship_store_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_origin_city',
                'operating_hours',
                'cut_off_time',
                'packing_sla',
                'supported_couriers',
                'packing_standard',
                'return_policy',
                'pic_name',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'dropship_store_name',
                'address',
            ]);
        });
    }
};
