<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCancellationTest extends TestCase
{
    use RefreshDatabase;

    private function setupScenario(): array
    {
        $owner = User::factory()->create([
            'role' => 'admin_supplier',
            'name' => 'Owner Toko',
        ]);

        $supplier = Supplier::create([
            'owner_id' => $owner->id,
            'store_name' => 'Toko Mainan Sejahtera',
            'warehouse_address' => 'Bandung',
            'contact_phone' => '08123456789',
        ]);

        $owner->update(['supplier_id' => $supplier->id]);

        $dropshipper = User::factory()->create([
            'role' => 'dropshipper',
            'name' => 'Mitra Budi',
        ]);

        $product = Product::create([
            'supplier_id' => $supplier->id,
            'name' => 'Lego Transformer',
            'sku' => 'TOY-LEGO-01',
            'stock' => 10,
            'price' => 50000,
            'weight_gram' => 200,
        ]);

        return [$owner, $supplier, $dropshipper, $product];
    }

    public function test_admin_can_cancel_order_in_menunggu_packing_and_stock_is_restored(): void
    {
        [$owner, $supplier, $dropshipper, $product] = $this->setupScenario();

        $order = Order::create([
            'invoice_number' => Order::generateInvoiceNumber(),
            'order_code' => 'ORD-TEST-001',
            'status' => 'menunggu_packing',
            'supplier_id' => $supplier->id,
            'user_id' => $dropshipper->id,
            'transaction_type' => 'dropship',
            'customer_name' => 'Pak Joko',
            'total_cost' => 50000,
            'subtotal' => 50000,
            'final_amount' => 50000,
            'net_profit' => 10000,
            'payment_status' => 'paid',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'quantity' => 2,
            'unit_price' => 50000,
            'subtotal' => 100000,
        ]);

        $response = $this->actingAs($owner)->post("/admin/packing/{$order->id}/cancel", [
            'cancel_reason' => 'Permintaan pembeli / dropshipper di marketplace',
        ]);

        $response->assertRedirect(route('admin.packing.index'));
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('dibatalkan', $order->status);
        $this->assertEquals('Permintaan pembeli / dropshipper di marketplace', $order->cancel_reason);
        $this->assertEquals('admin', $order->cancelled_by);
        $this->assertEquals('Siap Kemas', $order->cancel_stage);

        // Stok bertambah dari 10 menjadi 12
        $product->refresh();
        $this->assertEquals(12, $product->stock);
    }

    public function test_admin_can_cancel_order_in_dikirim_stage_without_restoring_stock(): void
    {
        [$owner, $supplier, $dropshipper, $product] = $this->setupScenario();

        $order = Order::create([
            'invoice_number' => Order::generateInvoiceNumber(),
            'order_code' => 'ORD-TEST-002',
            'status' => 'dikirim',
            'shipping_receipt_number' => 'RESI123456',
            'courier_name' => 'J&T Express',
            'supplier_id' => $supplier->id,
            'user_id' => $dropshipper->id,
            'transaction_type' => 'dropship',
            'customer_name' => 'Bu Siti',
            'total_cost' => 50000,
            'subtotal' => 50000,
            'final_amount' => 50000,
            'net_profit' => 10000,
            'payment_status' => 'paid',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'quantity' => 3,
            'unit_price' => 50000,
            'subtotal' => 150000,
        ]);

        $response = $this->actingAs($owner)->post("/admin/packing/{$order->id}/cancel", [
            'cancel_reason' => 'Paket diretur kurir karena pembeli menolak bayar / cancel',
        ]);

        $response->assertRedirect(route('admin.packing.index'));
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('dibatalkan', $order->status);
        $this->assertEquals('Sudah Dikirim', $order->cancel_stage);

        // Stok tidak berubah otomatis karena sudah di kurir
        $product->refresh();
        $this->assertEquals(10, $product->stock);
    }

    public function test_tim_packing_cannot_cancel_order(): void
    {
        [$owner, $supplier, $dropshipper, $product] = $this->setupScenario();

        $staffPacking = User::factory()->create([
            'role' => 'tim_packing',
            'supplier_id' => $supplier->id,
        ]);

        $order = Order::create([
            'invoice_number' => Order::generateInvoiceNumber(),
            'order_code' => 'ORD-TEST-003',
            'status' => 'dipacking',
            'supplier_id' => $supplier->id,
            'user_id' => $dropshipper->id,
            'transaction_type' => 'dropship',
            'customer_name' => 'Pak Bambang',
            'total_cost' => 50000,
            'subtotal' => 50000,
            'final_amount' => 50000,
            'net_profit' => 10000,
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($staffPacking)->post("/admin/packing/{$order->id}/cancel", [
            'cancel_reason' => 'Ingin membatalkan',
        ]);

        $response->assertForbidden();
    }
}
