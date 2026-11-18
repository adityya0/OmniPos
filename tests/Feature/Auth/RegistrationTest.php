<?php

namespace Tests\Feature\Auth;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_dropshipper_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register.dropshipper'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Akun Dropshipper');
    }

    public function test_supplier_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register.supplier'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Akun Supplier / Toko');
    }

    public function test_dropshipper_can_register(): void
    {
        $response = $this->post(route('register.dropshipper'), [
            'name' => 'Budi Dropshipper',
            'email' => 'budi@dropship.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'budi@dropship.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('dropshipper', $user->role);
        $this->assertTrue($user->isDropshipper());
    }

    public function test_supplier_can_register_with_store_and_transaction(): void
    {
        $response = $this->post(route('register.supplier'), [
            'name' => 'Hendra Owner',
            'email' => 'hendra@tokoberkah.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'store_name' => 'Toko Berkah Utama',
            'warehouse_address' => 'Jl. Pergudangan No. 10 Jakarta',
            'contact_phone' => '081298765432',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'hendra@tokoberkah.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('admin_supplier', $user->role);
        $this->assertTrue($user->isAdminSupplier());

        $supplier = Supplier::where('owner_id', $user->id)->first();
        $this->assertNotNull($supplier);
        $this->assertEquals('Toko Berkah Utama', $supplier->store_name);
        $this->assertEquals('081298765432', $supplier->contact_phone);

        // Verify user supplier_id is set to the created supplier
        $this->assertEquals($supplier->id, $user->supplier_id);
        $this->assertEquals($supplier->id, $user->ownedSupplier->id);
    }
}
