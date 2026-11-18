<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_analytics_calculates_velocity_fast_slow_and_dynamic_restock(): void
    {
        $owner = User::factory()->create(['role' => 'admin_supplier']);
        $supplier = Supplier::create([
            'owner_id' => $owner->id,
            'store_name' => 'Toko Analitik',
            'warehouse_address' => 'Jl. Gudang No. 1',
            'contact_phone' => '081234567890',
            'stock_lead_time_days' => 3,
            'stock_target_days' => 14,
        ]);

        $category = Category::create([
            'supplier_id' => $supplier->id,
            'name' => 'Mainan',
        ]);

        // Produk Fast Moving dengan stok tipis (stok 20, terjual 28 dalam 14 hari => 2/hari, ROP = 2*3+5 = 11; buat stok 10 agar kritis)
        $fastProduct = Product::create([
            'supplier_id' => $supplier->id,
            'category_id' => $category->id,
            'sku' => 'FAST-001',
            'name' => 'Robot Fast Moving',
            'cost_price' => 10000,
            'selling_price' => 25000,
            'dropship_price' => 18000,
            'stock' => 10,
            'safety_stock' => 5,
            'is_active' => true,
        ]);

        // Produk Slow Moving (0 terjual)
        $slowProduct = Product::create([
            'supplier_id' => $supplier->id,
            'category_id' => $category->id,
            'sku' => 'SLOW-001',
            'name' => 'Boneka Slow Moving',
            'cost_price' => 15000,
            'selling_price' => 30000,
            'dropship_price' => 22000,
            'stock' => 50,
            'safety_stock' => 5,
            'is_active' => true,
        ]);

        $order = Order::create([
            'supplier_id' => $supplier->id,
            'user_id' => $owner->id,
            'transaction_type' => 'pos_offline',
            'invoice_number' => 'INV-TEST-001',
            'customer_name' => 'Pelanggan',
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'status' => 'selesai',
            'subtotal' => 700000,
            'final_amount' => 700000,
            'total_cost' => 280000,
            'net_profit' => 420000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $fastProduct->id,
            'product_name' => $fastProduct->name,
            'product_sku' => $fastProduct->sku,
            'quantity' => 28,
            'unit_price' => 25000,
            'cost_price' => 10000,
            'subtotal' => 700000,
        ]);

        $response = $this->actingAs($owner)
            ->get(route('admin.stock-analytics.index', ['days' => 14]));

        $response->assertOk();
        $response->assertSee('Analisis Stok');
        $response->assertSee('Robot Fast Moving');
        $response->assertSee('Boneka Slow Moving');
        $response->assertViewHas('stats', function ($stats) {
            // Fast product: 2/hari, target = 2*14+5 = 33, stok = 10 => saran restock = 23
            return $stats['needs_restock'] === 1
                && $stats['fast_moving'] === 1
                && $stats['slow_moving'] === 1
                && (int) $stats['estimated_cost'] === 230000;
        });
    }

    public function test_supplier_can_update_stock_forecast_parameters(): void
    {
        $owner = User::factory()->create(['role' => 'admin_supplier']);
        $supplier = Supplier::create([
            'owner_id' => $owner->id,
            'store_name' => 'Toko Parameter',
            'warehouse_address' => 'Jl. Gudang No. 2',
            'contact_phone' => '081234567891',
        ]);

        $response = $this->actingAs($owner)
            ->put(route('admin.stock-analytics.parameters'), [
                'stock_lead_time_days' => 5,
                'stock_target_days' => 21,
                'days' => 14,
            ]);

        $response->assertRedirect(route('admin.stock-analytics.index', ['days' => 14]));
        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'stock_lead_time_days' => 5,
            'stock_target_days' => 21,
        ]);
    }
}
