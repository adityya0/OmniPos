<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductTier;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTierBulkUpdateTest extends TestCase
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
            'store_name' => 'Toko Mainan Sejahtera',
            'warehouse_address' => 'Jl. Grosir Raya No. 10',
            'contact_phone' => '08123456789',
        ]);

        $this->supplierUser->update(['supplier_id' => $this->supplier->id]);

        $this->category = Category::create([
            'supplier_id' => $this->supplier->id,
            'name' => 'Mainan Edukatif',
            'slug' => 'mainan-edukatif',
        ]);
    }

    public function test_products_index_renders_with_edit_category_level_button_and_scripts(): void
    {
        $product = Product::create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'name' => 'Robot Transformer',
            'sku' => 'ROBOT-01',
            'cost_price' => 25000,
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

        $response = $this->actingAs($this->supplierUser)->get(route('admin.products.index'));

        $response->assertOk();
        $response->assertSee('Edit Kategori Level');
        $response->assertSee('open-tier-modal');
        $response->assertSee('function productInventory()', false);
        $response->assertSee('Kelola');
    }

    public function test_bulk_update_tiers_updates_all_products_in_supplier_store(): void
    {
        $p1 = Product::create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'name' => 'Produk 1',
            'sku' => 'P1',
            'cost_price' => 10000,
            'selling_price' => 20000,
            'dropship_margin' => 5000,
            'stock' => 50,
            'is_active' => true,
        ]);

        $p2 = Product::create([
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->category->id,
            'name' => 'Produk 2',
            'sku' => 'P2',
            'cost_price' => 15000,
            'selling_price' => 30000,
            'dropship_margin' => 5000,
            'stock' => 30,
            'is_active' => true,
        ]);

        ProductTier::create([
            'product_id' => $p1->id,
            'tier_level' => 1,
            'name' => 'Eceran (Satuan)',
            'unit_multiplier' => 1,
            'price' => 20000,
        ]);

        ProductTier::create([
            'product_id' => $p2->id,
            'tier_level' => 1,
            'name' => 'Eceran (Satuan)',
            'unit_multiplier' => 1,
            'price' => 30000,
        ]);

        $response = $this->actingAs($this->supplierUser)
            ->post(route('admin.products.tiers.bulk-update'), [
                'tiers' => [
                    ['tier_level' => 1, 'name' => 'Eceran', 'unit_multiplier' => 1],
                    ['tier_level' => 2, 'name' => 'Grosir Lusin', 'unit_multiplier' => 12],
                    ['tier_level' => 3, 'name' => 'Grosir Karton', 'unit_multiplier' => 48],
                ],
            ]);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        // Check product 1 has updated level 1 and new level 2, 3
        $this->assertDatabaseHas('product_tiers', [
            'product_id' => $p1->id,
            'tier_level' => 1,
            'name' => 'Eceran',
            'unit_multiplier' => 1,
        ]);

        $this->assertDatabaseHas('product_tiers', [
            'product_id' => $p1->id,
            'tier_level' => 2,
            'name' => 'Grosir Lusin',
            'unit_multiplier' => 12,
        ]);

        $this->assertDatabaseHas('product_tiers', [
            'product_id' => $p2->id,
            'tier_level' => 3,
            'name' => 'Grosir Karton',
            'unit_multiplier' => 48,
        ]);
    }
}
