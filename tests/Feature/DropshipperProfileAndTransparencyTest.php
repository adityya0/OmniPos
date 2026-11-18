<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\SupplierDropshipper;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DropshipperProfileAndTransparencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_dropshipper_can_access_profile_and_rules_page(): void
    {
        $dropshipper = User::factory()->create([
            'role' => 'dropshipper',
            'name' => 'Reseller Sejati',
            'email' => 'reseller@test.com',
            'phone' => '081299998888',
            'dropship_store_name' => 'Toko Reseller Sukses',
        ]);

        $supplierOwner = User::factory()->create([
            'role' => 'admin_supplier',
        ]);

        $supplier = Supplier::create([
            'owner_id' => $supplierOwner->id,
            'store_name' => 'Gudang Mainan Jaya',
            'warehouse_address' => 'Jl. Sentosa No. 12, Garut',
            'shipping_origin_city' => 'Kab. Garut, Jawa Barat',
            'contact_phone' => '08123456789',
            'operating_hours' => 'Senin - Sabtu: 08:00 - 17:00 WIB',
            'cut_off_time' => '15:00 WIB',
            'packing_sla' => '< 24 Jam Kerja',
            'supported_couriers' => 'J&T, JNE, SiCepat, SPX Express',
            'packing_standard' => 'Bubble Wrap Tebal + Dus Pelindung',
            'return_policy' => 'Wajib video unboxing.',
            'pic_name' => 'Pak Budi CS',
        ]);

        SupplierDropshipper::create([
            'supplier_id' => $supplier->id,
            'dropshipper_id' => $dropshipper->id,
            'status' => 'approved',
            'wallet_balance' => 0,
        ]);

        // Uji rute /dropshipper/profile
        $response = $this->actingAs($dropshipper)->get('/dropshipper/profile');
        $response->assertOk();
        $response->assertSeeText('Reseller Sejati');
        $response->assertSeeText('Toko Reseller Sukses');
        $response->assertSeeText('Ketentuan & Kebijakan Operasional Dropshipper');
        $response->assertSeeText('Batas Kemitraan Toko Penyuplai');
        $response->assertSeeText('Gudang Mainan Jaya');

        // Uji rute /profile juga merender view profil dropshipper
        $response2 = $this->actingAs($dropshipper)->get('/profile');
        $response2->assertOk();
        $response2->assertSeeText('Profil & Ketentuan Kemitraan');
    }

    public function test_dropshipper_can_update_profile_info(): void
    {
        $dropshipper = User::factory()->create([
            'role' => 'dropshipper',
            'name' => 'Nama Lama',
            'email' => 'old@test.com',
        ]);

        $response = $this->actingAs($dropshipper)->patch('/profile', [
            'name' => 'Nama Baru Reseller',
            'email' => 'baru@test.com',
            'phone' => '081234567890',
            'dropship_store_name' => 'Toko Mega Berkah',
            'address' => 'Jl. Gatot Subroto No. 5',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profile');

        $dropshipper->refresh();
        $this->assertSame('Nama Baru Reseller', $dropshipper->name);
        $this->assertSame('baru@test.com', $dropshipper->email);
        $this->assertSame('081234567890', $dropshipper->phone);
        $this->assertSame('Toko Mega Berkah', $dropshipper->dropship_store_name);
        $this->assertSame('Jl. Gatot Subroto No. 5', $dropshipper->address);
    }

    public function test_dropshipper_suppliers_page_displays_transparency_details(): void
    {
        $dropshipper = User::factory()->create([
            'role' => 'dropshipper',
        ]);

        $supplierOwner = User::factory()->create([
            'role' => 'admin_supplier',
        ]);

        $supplier = Supplier::create([
            'owner_id' => $supplierOwner->id,
            'store_name' => 'Cita Laksana Toys Pusat',
            'warehouse_address' => 'Lewi Goong',
            'shipping_origin_city' => 'Kab. Garut, Jawa Barat',
            'contact_phone' => '081234578',
            'operating_hours' => 'Senin - Sabtu: 08:00 - 17:00 WIB',
            'cut_off_time' => '15:00 WIB',
            'packing_sla' => '< 24 Jam Kerja',
            'supported_couriers' => 'J&T Express, JNE, SiCepat, SPX Express, GoSend',
            'packing_standard' => 'Bubble Wrap Tebal + Dus Pelindung',
            'return_policy' => 'Wajib video unboxing utuh.',
            'pic_name' => 'Kak Rina CS',
        ]);

        $response = $this->actingAs($dropshipper)->get('/dropshipper/suppliers');
        $response->assertOk();
        $response->assertSee('Cita Laksana Toys Pusat');
        $response->assertSee('Kab. Garut, Jawa Barat');
        $response->assertSee('15:00 WIB');
        $response->assertSee('Detail Toko');
        $response->assertSee('Titik Asal Pengiriman Gudang');
    }
}
