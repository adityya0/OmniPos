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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('sku')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('length', 8, 2)->default(0); // cm
            $table->decimal('width', 8, 2)->default(0);  // cm
            $table->decimal('height', 8, 2)->default(0); // cm
            $table->decimal('weight', 8, 2)->default(0); // gram
            $table->decimal('cost_price', 14, 2)->default(0); // HPP modal
            $table->decimal('selling_price', 14, 2)->default(0); // Harga POS / eceran
            $table->decimal('dropship_price', 14, 2)->default(0); // Harga mitra dropshipper
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
