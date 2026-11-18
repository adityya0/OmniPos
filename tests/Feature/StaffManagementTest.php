<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_supplier_can_view_staff_page(): void
    {
        $owner = User::factory()->create([
            'role' => 'admin_supplier',
            'name' => 'Owner Nurdin',
            'email' => 'nurdin@gmail.com',
        ]);

        $supplier = Supplier::create([
            'owner_id' => $owner->id,
            'store_name' => 'Toko Nurdin',
            'warehouse_address' => 'Bandung',
            'contact_phone' => '08123456789',
        ]);

        $owner->update(['supplier_id' => $supplier->id]);

        $staff = User::factory()->create([
            'name' => 'Rian Packing',
            'email' => 'rian@toko.test',
            'role' => 'tim_packing',
            'supplier_id' => $supplier->id,
        ]);

        $response = $this->actingAs($owner)->get('/admin/staff');
        $response->assertOk();
        $response->assertSee('Owner Nurdin');
        $response->assertSee('Rian Packing');
        $response->assertSee('Tim Packing');
        $response->assertSee('Tambah Staf Toko');
    }

    public function test_admin_supplier_can_create_new_staff(): void
    {
        $owner = User::factory()->create([
            'role' => 'admin_supplier',
        ]);

        $supplier = Supplier::create([
            'owner_id' => $owner->id,
            'store_name' => 'Toko Nurdin',
            'warehouse_address' => 'Bandung',
            'contact_phone' => '08123456789',
        ]);

        $owner->update(['supplier_id' => $supplier->id]);

        $response = $this->actingAs($owner)->post('/admin/staff', [
            'name' => 'Siti Media',
            'email' => 'siti@toko.test',
            'password' => 'secret123',
            'role' => 'tim_media',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'name' => 'Siti Media',
            'email' => 'siti@toko.test',
            'role' => 'tim_media',
            'supplier_id' => $supplier->id,
        ]);
    }

    public function test_admin_supplier_can_delete_staff(): void
    {
        $owner = User::factory()->create([
            'role' => 'admin_supplier',
        ]);

        $supplier = Supplier::create([
            'owner_id' => $owner->id,
            'store_name' => 'Toko Nurdin',
            'warehouse_address' => 'Bandung',
            'contact_phone' => '08123456789',
        ]);

        $owner->update(['supplier_id' => $supplier->id]);

        $staff = User::factory()->create([
            'name' => 'Staf Akan Dihapus',
            'email' => 'hapus@toko.test',
            'role' => 'tim_packing',
            'supplier_id' => $supplier->id,
        ]);

        $response = $this->actingAs($owner)->delete("/admin/staff/{$staff->id}");
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseMissing('users', [
            'id' => $staff->id,
        ]);
    }

    public function test_dropshipper_cannot_access_staff_page(): void
    {
        $dropshipper = User::factory()->create([
            'role' => 'dropshipper',
        ]);

        $response = $this->actingAs($dropshipper)->get('/admin/staff');
        $response->assertRedirect(route('dropshipper.catalog.index'));
    }
}
