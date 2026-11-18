<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductTier;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosPricingModeAndSettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create([
            'name' => 'Owner Toko Retail',
            'email' => 'retail@omnipos.test',
            'role' => 'admin_supplier',
        ]);

        $this->supplier = Supplier::create([
            'owner_id' => $this->owner->id,
            'store_name' => 'Toko Retail Jaya',
            'warehouse_address' => 'Jl. Grosir Raya No. 12',
            'contact_phone' => '081234567890',
            'shipping_origin_city' => 'Jakarta Barat',
            'pos_pricing_mode' => 'global_filter',
        ]);
    }

    public function test_settings_page_is_displayed_with_pos_pricing_mode(): void
    {
        $response = $this->actingAs($this->owner)->get(route('admin.settings.index'));

        $response->assertOk();
        $response->assertSee('Mode Tampilan Tingkatan Harga Kasir POS');
        $response->assertSee('Mode 1: Filter Tingkatan Harga Global');
        $response->assertSee('Mode 2: Tombol Multi-Level di Setiap Kartu');
    }

    public function test_supplier_can_update_pos_pricing_mode_via_settings(): void
    {
        $response = $this->actingAs($this->owner)
            ->put(route('admin.settings.pos-pricing-mode'), [
                'pos_pricing_mode' => 'card_buttons',
            ]);

        $response->assertRedirect(route('admin.settings.index', ['tab' => 'pos']));
        $this->supplier->refresh();
        $this->assertSame('card_buttons', $this->supplier->pos_pricing_mode);
    }

    public function test_supplier_can_update_pos_pricing_mode_via_ajax_endpoint(): void
    {
        $response = $this->actingAs($this->owner)
            ->postJson(route('admin.pos.pricing-mode'), [
                'pos_pricing_mode' => 'card_buttons',
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'pos_pricing_mode' => 'card_buttons',
        ]);

        $this->supplier->refresh();
        $this->assertSame('card_buttons', $this->supplier->pos_pricing_mode);
    }

    public function test_pos_page_renders_with_multi_level_tiers_and_mode(): void
    {
        $category = Category::create([
            'name' => 'Mainan',
            'slug' => 'mainan',
            'supplier_id' => $this->supplier->id,
        ]);

        $product = Product::create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $category->id,
            'name' => 'Lego Edukasi',
            'sku' => 'LEGO-001',
            'cost_price' => 20000,
            'selling_price' => 50000,
            'dropship_margin' => 10000,
            'stock' => 100,
            'is_active' => true,
        ]);

        ProductTier::create([
            'product_id' => $product->id,
            'tier_level' => 1,
            'name' => 'Eceran',
            'unit_multiplier' => 1,
            'price' => 50000,
        ]);

        ProductTier::create([
            'product_id' => $product->id,
            'tier_level' => 2,
            'name' => 'Grosir Lusin',
            'unit_multiplier' => 12,
            'price' => 540000,
        ]);

        $response = $this->actingAs($this->owner)->get(route('admin.pos.index'));

        $response->assertOk();
        $response->assertSee('Filter Harga:');
        $response->assertDontSee('Tampilan Harga:');
        $response->assertSee('Cari Produk / Scan Barcode');
        $response->assertSee('LEGO-001');
    }

    public function test_supplier_can_update_store_profile(): void
    {
        $response = $this->actingAs($this->owner)
            ->put(route('admin.settings.store-profile'), [
                'store_name' => 'Toko Retail Jaya Makmur',
                'contact_phone' => '08999999999',
                'email' => 'toko@retailjaya.test',
                'shipping_origin_city' => 'Surabaya',
                'warehouse_address' => 'Jl. Industri No. 5, Surabaya',
                'operating_hours' => '08:00 - 20:00',
                'cut_off_time' => '16:00 WIB',
            ]);

        $response->assertRedirect(route('admin.settings.index', ['tab' => 'store']));
        $this->supplier->refresh();
        $this->assertSame('Toko Retail Jaya Makmur', $this->supplier->store_name);
        $this->assertSame('08999999999', $this->supplier->contact_phone);
        $this->assertSame('Surabaya', $this->supplier->shipping_origin_city);
    }

    public function test_supplier_can_bulk_update_tier_levels_across_all_products(): void
    {
        $category = Category::create([
            'name' => 'Mainan',
            'slug' => 'mainan-tier',
            'supplier_id' => $this->supplier->id,
        ]);

        $prod1 = Product::create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $category->id,
            'name' => 'Produk A',
            'sku' => 'SKU-A',
            'cost_price' => 10000,
            'selling_price' => 20000,
            'dropship_margin' => 5000,
            'stock' => 50,
            'is_active' => true,
        ]);

        $prod2 = Product::create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $category->id,
            'name' => 'Produk B',
            'sku' => 'SKU-B',
            'cost_price' => 15000,
            'selling_price' => 30000,
            'dropship_margin' => 7000,
            'stock' => 80,
            'is_active' => true,
        ]);

        ProductTier::create(['product_id' => $prod1->id, 'tier_level' => 1, 'name' => 'Eceran', 'unit_multiplier' => 1, 'price' => 20000]);
        ProductTier::create(['product_id' => $prod1->id, 'tier_level' => 2, 'name' => 'Lusin Lama', 'unit_multiplier' => 12, 'price' => 216000]);

        ProductTier::create(['product_id' => $prod2->id, 'tier_level' => 1, 'name' => 'Eceran', 'unit_multiplier' => 1, 'price' => 30000]);
        ProductTier::create(['product_id' => $prod2->id, 'tier_level' => 2, 'name' => 'Lusin Lama', 'unit_multiplier' => 12, 'price' => 324000]);

        $response = $this->actingAs($this->owner)
            ->post(route('admin.products.tiers.bulk-update'), [
                'tiers' => [
                    ['tier_level' => 1, 'name' => 'Satuan Retail', 'unit_multiplier' => 1],
                    ['tier_level' => 2, 'name' => 'Grosir Lusinan Baru', 'unit_multiplier' => 12],
                    ['tier_level' => 3, 'name' => 'Partai Karton Besar', 'unit_multiplier' => 72],
                ],
            ]);

        $response->assertRedirect(route('admin.products.index'));

        // Cek bahwa tier level 1, 2, dan 3 terupdate di Produk A & B
        $prod1Tiers = $prod1->fresh()->tiers()->orderBy('tier_level')->get();
        $this->assertCount(3, $prod1Tiers);
        $this->assertSame('Satuan Retail', $prod1Tiers[0]->name);
        $this->assertSame('Grosir Lusinan Baru', $prod1Tiers[1]->name);
        $this->assertSame('Partai Karton Besar', $prod1Tiers[2]->name);
        $this->assertEquals(72, $prod1Tiers[2]->unit_multiplier);

        $prod2Tiers = $prod2->fresh()->tiers()->orderBy('tier_level')->get();
        $this->assertCount(3, $prod2Tiers);
        $this->assertSame('Satuan Retail', $prod2Tiers[0]->name);
        $this->assertSame('Grosir Lusinan Baru', $prod2Tiers[1]->name);
        $this->assertSame('Partai Karton Besar', $prod2Tiers[2]->name);
    }
}

