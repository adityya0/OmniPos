<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PosAndInventoryTest extends TestCase
{
    use RefreshDatabase;

    private User $supplierUser;
    private Supplier $supplier;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->supplierUser = User::create([
            'name' => 'Owner Toko',
            'email' => 'owner@posdropship.test',
            'password' => bcrypt('password123'),
            'role' => 'admin_supplier',
        ]);

        $this->supplier = Supplier::create([
            'owner_id' => $this->supplierUser->id,
            'store_name' => 'Toko Maju Jaya',
            'warehouse_address' => 'Jl. Pergudangan Utama No. 1',
            'contact_phone' => '08123456789',
        ]);

        $this->supplierUser->update(['supplier_id' => $this->supplier->id]);

        $this->category = Category::create([
            'supplier_id' => $this->supplier->id,
            'name' => 'Pakaian Pria',
            'slug' => 'pakaian-pria',
        ]);
    }

    public function test_can_generate_unique_sku_via_ajax(): void
    {
        $response = $this->actingAs($this->supplierUser)
            ->getJson(route('admin.products.generate-sku', [
                'name' => 'Kemeja Casual',
                'category_id' => $this->category->id,
            ]));

        $response->assertStatus(200);
        $response->assertJsonStructure(['sku']);
        $this->assertStringStartsWith('KEM-PAK-', $response->json('sku'));
    }

    public function test_media_or_admin_can_create_product_with_dimensions(): void
    {
        $response = $this->actingAs($this->supplierUser)
            ->post(route('admin.products.store'), [
                'name' => 'Kaos Polos Premium',
                'category_id' => $this->category->id,
                'sku' => 'PAK-KAO-1001',
                'description' => 'Bahan katun combed 30s adem',
                'length' => 20,
                'width' => 15,
                'height' => 3,
                'weight' => 200,
                'cost_price' => 35000,
                'selling_price' => 65000,
                'dropship_price' => 45000,
                'stock' => 50,
            ]);

        $response->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'sku' => 'PAK-KAO-1001',
            'name' => 'Kaos Polos Premium',
            'stock' => 50,
            'cost_price' => 35000,
            'selling_price' => 65000,
        ]);
    }

    public function test_pos_checkout_decrements_stock_and_calculates_profit(): void
    {
        $product = Product::create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'sku' => 'PAK-CEL-2002',
            'name' => 'Celana Chino Slimfit',
            'length' => 30,
            'width' => 20,
            'height' => 5,
            'weight' => 400,
            'cost_price' => 60000,
            'selling_price' => 120000,
            'dropship_price' => 85000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->supplierUser)
            ->postJson(route('admin.pos.checkout'), [
                'customer_name' => 'Budi Offline',
                'payment_method' => 'cash',
                'cash_amount' => 250000,
                'discount' => 0,
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 2,
                    ]
                ]
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verifikasi stok berkurang secara realtime (10 - 2 = 8)
        $product->refresh();
        $this->assertEquals(8, $product->stock);

        // Verifikasi transaksi order
        $this->assertDatabaseHas('orders', [
            'supplier_id' => $this->supplier->id,
            'customer_name' => 'Budi Offline',
            'subtotal' => 240000,
            'final_amount' => 240000,
            'total_cost' => 120000, // 2 x 60.000
            'net_profit' => 120000, // 240.000 - 120.000
            'change_amount' => 10000, // 250.000 - 240.000
        ]);
    }

    public function test_dropshipper_can_see_realtime_stock(): void
    {
        $dropshipper = User::create([
            'name' => 'Dropshipper Mitra',
            'email' => 'dropship@test.com',
            'password' => bcrypt('password123'),
            'role' => 'dropshipper',
        ]);

        $product = Product::create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'sku' => 'PAK-JAK-3003',
            'name' => 'Jaket Bomber Waterproof',
            'cost_price' => 100000,
            'selling_price' => 180000,
            'dropship_price' => 135000,
            'stock' => 12,
            'is_active' => true,
        ]);

        $response = $this->actingAs($dropshipper)
            ->get(route('dropshipper.catalog.index'));

        $response->assertStatus(200);
        $response->assertSee('Jaket Bomber Waterproof');
        $response->assertSee('Stok: 12');
    }

    public function test_reports_page_displays_accumulated_sales_and_profit(): void
    {
        $product = Product::create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'sku' => 'PAK-TSH-4004',
            'name' => 'T-Shirt Oversize',
            'cost_price' => 40000,
            'selling_price' => 90000,
            'dropship_price' => 60000,
            'stock' => 20,
            'is_active' => true,
        ]);

        // Simulasikan transaksi kasir
        $this->actingAs($this->supplierUser)
            ->postJson(route('admin.pos.checkout'), [
                'customer_name' => 'Pelanggan Kasir',
                'payment_method' => 'cash',
                'cash_amount' => 90000,
                'discount' => 0,
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 1]
                ]
            ]);

        $response = $this->actingAs($this->supplierUser)
            ->get(route('admin.reports.index'));

        $response->assertStatus(200);
        $response->assertSee('Laporan Akumulasi Penjualan');
        $response->assertSee('90.000'); // Omset
        $response->assertSee('50.000'); // Laba Bersih
    }

    public function test_user_can_download_product_import_template_csv(): void
    {
        $response = $this->actingAs($this->supplierUser)
            ->get(route('admin.products.template'));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=template_import_produk.csv');
    }

    public function test_user_can_import_products_from_csv(): void
    {
        $csvContent = "nama_produk,kategori,hpp_modal,harga_jual_pos,harga_dropship,stok,berat_gram,panjang_cm,lebar_cm,tinggi_cm,deskripsi\n"
            . "Sepatu Sneaker Pria,Sepatu,120000,250000,190000,30,800,30,20,12,Bahan kanvas premium\n"
            . "Tas Ransel Laptop,Tas & Aksesoris,85000,165000,125000,15,500,40,30,15,Water resistant\n";

        $file = UploadedFile::fake()->createWithContent('products.csv', $csvContent);

        $response = $this->actingAs($this->supplierUser)
            ->post(route('admin.products.import'), [
                'file' => $file,
            ]);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'supplier_id' => $this->supplier->id,
            'name' => 'Sepatu Sneaker Pria',
            'stock' => 30,
            'selling_price' => 250000,
        ]);

        $this->assertDatabaseHas('products', [
            'supplier_id' => $this->supplier->id,
            'name' => 'Tas Ransel Laptop',
            'stock' => 15,
            'dropship_price' => 125000,
        ]);
    }

    public function test_user_can_bulk_delete_products(): void
    {
        $product1 = Product::create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'sku' => 'BULK-001',
            'name' => 'Produk Hapus 1',
            'cost_price' => 10000,
            'selling_price' => 20000,
            'dropship_price' => 15000,
            'stock' => 5,
        ]);

        $product2 = Product::create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'sku' => 'BULK-002',
            'name' => 'Produk Hapus 2',
            'cost_price' => 10000,
            'selling_price' => 20000,
            'dropship_price' => 15000,
            'stock' => 10,
        ]);

        $response = $this->actingAs($this->supplierUser)
            ->post(route('admin.products.bulk-delete'), [
                'ids' => [$product1->id, $product2->id],
            ]);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('products', ['id' => $product1->id]);
        $this->assertDatabaseMissing('products', ['id' => $product2->id]);
    }

    public function test_user_can_delete_invoice_and_restores_product_stock(): void
    {
        $product = Product::create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'sku' => 'DEL-INV-01',
            'name' => 'Produk Invoice Test',
            'cost_price' => 20000,
            'selling_price' => 50000,
            'dropship_price' => 35000,
            'stock' => 15,
        ]);

        // Buat checkout penjualan 3 unit -> sisa stok jadi 12
        $checkoutResponse = $this->actingAs($this->supplierUser)
            ->postJson(route('admin.pos.checkout'), [
                'customer_name' => 'Budi Santoso',
                'payment_method' => 'cash',
                'cash_amount' => 150000,
                'discount' => 0,
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 3]
                ]
            ]);

        $orderId = $checkoutResponse->json('order.id');
        $this->assertEquals(12, $product->fresh()->stock);

        // Hapus invoice transaksi
        $response = $this->actingAs($this->supplierUser)
            ->delete(route('admin.reports.orders.destroy', $orderId));

        $response->assertRedirect(route('admin.reports.index', ['tab' => 'transactions']));
        $response->assertSessionHas('success');

        // Invoice harus terhapus dan stok produk harus kembali menjadi 15
        $this->assertDatabaseMissing('orders', ['id' => $orderId]);
        $this->assertEquals(15, $product->fresh()->stock);
    }

    public function test_user_can_bulk_delete_invoices_and_restores_product_stock(): void
    {
        $product = Product::create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'sku' => 'BULK-INV-02',
            'name' => 'Produk Bulk Invoice Test',
            'cost_price' => 15000,
            'selling_price' => 30000,
            'dropship_price' => 25000,
            'stock' => 20,
        ]);

        // Buat order 1 (2 unit)
        $res1 = $this->actingAs($this->supplierUser)
            ->postJson(route('admin.pos.checkout'), [
                'customer_name' => 'Customer A',
                'payment_method' => 'cash',
                'cash_amount' => 60000,
                'discount' => 0,
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 2]
                ]
            ]);

        // Buat order 2 (3 unit)
        $res2 = $this->actingAs($this->supplierUser)
            ->postJson(route('admin.pos.checkout'), [
                'customer_name' => 'Customer B',
                'payment_method' => 'qris',
                'cash_amount' => 90000,
                'discount' => 0,
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 3]
                ]
            ]);

        $this->assertEquals(15, $product->fresh()->stock);

        $order1Id = $res1->json('order.id');
        $order2Id = $res2->json('order.id');

        // Hapus massal kedua invoice
        $response = $this->actingAs($this->supplierUser)
            ->post(route('admin.reports.orders.bulk-delete'), [
                'ids' => [$order1Id, $order2Id],
            ]);

        $response->assertRedirect(route('admin.reports.index', ['tab' => 'transactions']));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('orders', ['id' => $order1Id]);
        $this->assertDatabaseMissing('orders', ['id' => $order2Id]);

        // Stok produk harus pulih kembali menjadi 20
        $this->assertEquals(20, $product->fresh()->stock);
    }

    public function test_admin_supplier_can_view_operational_dashboard_with_all_metrics(): void
    {
        // 1. Buat produk low stock
        $product = Product::create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'sku' => 'DASH-001',
            'name' => 'Produk Stok Kritis',
            'cost_price' => 10000,
            'selling_price' => 20000,
            'dropship_price' => 15000,
            'stock' => 2,
            'safety_stock' => 5,
        ]);

        // 2. Transaksi POS hari ini
        $this->actingAs($this->supplierUser)
            ->postJson(route('admin.pos.checkout'), [
                'customer_name' => 'Pembeli Offline',
                'payment_method' => 'cash',
                'cash_amount' => 20000,
                'discount' => 0,
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 1]
                ]
            ]);

        // 3. Pesanan dropship menunggu packing
        Order::create([
            'order_code' => 'DRP-TEST-99',
            'invoice_number' => 'INV-DRP-99',
            'shipping_receipt_number' => 'SPXID9999',
            'supplier_id' => $this->supplier->id,
            'user_id' => $this->supplierUser->id,
            'transaction_type' => 'dropship',
            'customer_name' => 'Pembeli Dropship',
            'status' => 'menunggu_packing',
            'marketplace_deadline' => now()->addHours(3),
            'payment_status' => 'paid',
            'final_amount' => 30000,
        ]);

        $response = $this->actingAs($this->supplierUser)
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
            ->component('Dashboard')
            ->has('todayPosSales')
            ->has('pendingPackingCount')
            ->has('lowStockCount')
            ->has('activeDropshippersCount')
            ->has('urgentOrders')
            ->has('lowStockProducts')
            ->has('recentPosSales')
        );
    }

    public function test_can_create_product_with_custom_multi_level_tiers(): void
    {
        $response = $this->actingAs($this->supplierUser)
            ->post(route('admin.products.store'), [
                'name' => 'Mainan Masak-Masakan Chef Set',
                'category_id' => $this->category->id,
                'sku' => 'TOY-MSK-999',
                'description' => 'Set masak lengkap',
                'length' => 20,
                'width' => 15,
                'height' => 10,
                'weight' => 500,
                'cost_price' => 30000,
                'dropship_price' => 45000,
                'stock' => 120,
                'tiers' => [
                    ['level' => 1, 'name' => 'Satuan / Eceran', 'multiplier' => 1, 'price' => 60000],
                    ['level' => 2, 'name' => 'Lusin Grosir', 'multiplier' => 12, 'price' => 600000],
                    ['level' => 3, 'name' => 'Karton Besar', 'multiplier' => 60, 'price' => 2700000],
                ]
            ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('sku', 'TOY-MSK-999')->firstOrFail();
        $this->assertEquals(60000, $product->selling_price);
        $this->assertCount(3, $product->tiers);
        $this->assertEquals('Lusin Grosir', $product->tiers[1]->name);
        $this->assertEquals(12, $product->tiers[1]->unit_multiplier);
        $this->assertEquals(600000, $product->tiers[1]->price);
    }

    public function test_pos_checkout_with_tier_decrements_accurate_physical_stock(): void
    {
        $product = Product::create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'sku' => 'TOY-BLC-888',
            'name' => 'Building Blocks 100pcs',
            'length' => 25,
            'width' => 15,
            'height' => 10,
            'weight' => 400,
            'cost_price' => 20000,
            'selling_price' => 45000,
            'dropship_price' => 30000,
            'stock' => 100,
        ]);

        $product->tiers()->createMany([
            ['tier_level' => 1, 'name' => 'Eceran', 'unit_multiplier' => 1, 'price' => 45000],
            ['tier_level' => 2, 'name' => 'Lusin', 'unit_multiplier' => 12, 'price' => 480000],
        ]);

        // Pembeli beli 2 Lusin (2 x 12 = 24 pcs stok fisik terpotong)
        $response = $this->actingAs($this->supplierUser)
            ->postJson(route('admin.pos.checkout'), [
                'customer_name' => 'Grosir Toko Berkah',
                'payment_method' => 'cash',
                'cash_amount' => 1000000,
                'discount' => 0,
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 2,
                        'tier_name' => 'Lusin',
                        'unit_multiplier' => 12,
                        'unit_price' => 480000,
                    ]
                ]
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Sisa stok harus 100 - 24 = 76
        $product->refresh();
        $this->assertEquals(76, $product->stock);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'tier_name' => 'Lusin',
            'unit_multiplier' => 12,
            'quantity' => 2,
            'unit_price' => 480000,
            'subtotal' => 960000,
        ]);
    }

    public function test_omnichannel_stock_integration_between_pos_and_dropshipper(): void
    {
        $dropshipper = User::create([
            'name' => 'Dropshipper Mitra Integrasi',
            'email' => 'mitra_stock@test.com',
            'password' => bcrypt('password123'),
            'role' => 'dropshipper',
        ]);

        $product = Product::create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'sku' => 'INT-STK-999',
            'name' => 'Produk Integrasi Stok',
            'cost_price' => 10000,
            'selling_price' => 25000,
            'dropship_price' => 18000,
            'stock' => 20,
            'safety_stock' => 5,
            'is_active' => true,
        ]);

        // 1. Dropshipper memesan 4 unit online
        $orderResponse = $this->actingAs($dropshipper)
            ->post(route('dropshipper.orders.store'), [
                'product_id' => $product->id,
                'quantity' => 4,
                'customer_name' => 'Budi Marketplace',
                'customer_phone' => '08123456789',
                'courier_name' => 'J&T Express',
                'shipping_receipt_number' => 'JT-99887766',
            ]);

        $orderResponse->assertSessionHasNoErrors();
        $product->refresh();

        // Stok berkurang 4 secara realtime (20 - 4 = 16)
        $this->assertEquals(16, $product->stock);
        // Booking dropship adalah 4, fisik di gudang masih 20
        $this->assertEquals(4, $product->pending_dropship_qty);
        $this->assertEquals(20, $product->total_physical_stock);

        // 2. Kasir POS offline menjual 6 unit ke pelanggan langsung di toko
        $posResponse = $this->actingAs($this->supplierUser)
            ->postJson(route('admin.pos.checkout'), [
                'customer_name' => 'Pelanggan Offline Toko',
                'payment_method' => 'cash',
                'cash_amount' => 200000,
                'discount' => 0,
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 6,
                        'unit_price' => 25000,
                    ]
                ]
            ]);

        $posResponse->assertStatus(200);
        $posResponse->assertJson(['success' => true]);

        $product->refresh();
        // Stok berkurang lagi 6 (16 - 6 = 10)
        $this->assertEquals(10, $product->stock);

        // 3. Dropshipper melihat stok terkini di katalog (10 pcs)
        $catalogResponse = $this->actingAs($dropshipper)
            ->get(route('dropshipper.catalog.index'));
        $catalogResponse->assertStatus(200);
        $catalogResponse->assertSee('Stok: 10');

        // 4. Pembatalan pesanan dropship mengembalikan 4 unit ke stok
        $dropshipOrder = Order::where('shipping_receipt_number', 'JT-99887766')->first();
        $this->assertNotNull($dropshipOrder);

        $cancelResponse = $this->actingAs($this->supplierUser)
            ->post(route('admin.packing.cancel', $dropshipOrder), [
                'cancel_reason' => 'Permintaan pembatalan dari pembeli online'
            ]);

        $cancelResponse->assertSessionHasNoErrors();
        $product->refresh();

        // Stok kembali bertambah 4 (10 + 4 = 14)
        $this->assertEquals(14, $product->stock);
        $this->assertEquals(0, $product->pending_dropship_qty);
    }
}
