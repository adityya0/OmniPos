<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
use App\Models\ProductTier;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedInteger('tier_level')->default(1);
            $table->string('name')->default('Eceran');
            $table->unsignedInteger('unit_multiplier')->default(1);
            $table->decimal('price', 14, 2)->default(0);
            $table->timestamps();
        });

        // Buat default 3 level untuk semua produk yang sudah ada di database
        foreach (Product::all() as $product) {
            $basePrice = floatval($product->selling_price);
            ProductTier::create([
                'product_id' => $product->id,
                'tier_level' => 1,
                'name' => 'Eceran (Satuan)',
                'unit_multiplier' => 1,
                'price' => $basePrice,
            ]);

            ProductTier::create([
                'product_id' => $product->id,
                'tier_level' => 2,
                'name' => 'Grosir (Lusin)',
                'unit_multiplier' => 12,
                'price' => max(0, round($basePrice * 12 * 0.9, -3)),
            ]);

            ProductTier::create([
                'product_id' => $product->id,
                'tier_level' => 3,
                'name' => 'Grosir Besar (Dus)',
                'unit_multiplier' => 72,
                'price' => max(0, round($basePrice * 72 * 0.8, -3)),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_tiers');
    }
};

