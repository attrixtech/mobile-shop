<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenantA;
    protected Tenant $tenantB;
    protected User $userA;
    protected User $userB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenantA = Tenant::create([
            'name' => 'Shop Alpha',
            'slug' => 'alpha',
            'status' => 'active',
        ]);

        $this->tenantB = Tenant::create([
            'name' => 'Shop Beta',
            'slug' => 'beta',
            'status' => 'active',
        ]);

        $this->userA = User::create([
            'tenant_id' => $this->tenantA->id,
            'name' => 'Alpha Owner',
            'email' => 'owner@alpha.com',
            'password' => bcrypt('password'),
            'role' => 'store_owner',
            'is_active' => true,
        ]);

        $this->userB = User::create([
            'tenant_id' => $this->tenantB->id,
            'name' => 'Beta Owner',
            'email' => 'owner@beta.com',
            'password' => bcrypt('password'),
            'role' => 'store_owner',
            'is_active' => true,
        ]);

        // Seed data for Tenant A
        Product::create([
            'tenant_id' => $this->tenantA->id,
            'name' => 'Alpha Exclusive Smartphone',
            'type' => 'serialized',
            'purchase_price' => 10000,
            'selling_price' => 15000,
        ]);

        Customer::create([
            'tenant_id' => $this->tenantA->id,
            'name' => 'Alpha VIP Customer',
            'phone' => '9999999991',
        ]);

        // Seed data for Tenant B
        Product::create([
            'tenant_id' => $this->tenantB->id,
            'name' => 'Beta Special Earphones',
            'type' => 'standard',
            'purchase_price' => 500,
            'selling_price' => 999,
        ]);

        Customer::create([
            'tenant_id' => $this->tenantB->id,
            'name' => 'Beta VIP Customer',
            'phone' => '9999999992',
        ]);
    }

    public function test_tenant_a_cannot_view_tenant_b_products(): void
    {
        $this->actingAs($this->userA);
        session(['active_tenant_id' => $this->tenantA->id]);

        $response = $this->get(route('products.index'));
        $response->assertStatus(200);

        // Alpha product should be visible
        $response->assertSee('Alpha Exclusive Smartphone');

        // Beta product MUST NOT be visible
        $response->assertDontSee('Beta Special Earphones');
    }

    public function test_tenant_a_cannot_view_tenant_b_customers(): void
    {
        $this->actingAs($this->userA);
        session(['active_tenant_id' => $this->tenantA->id]);

        $response = $this->get(route('customers.index'));
        $response->assertStatus(200);

        // Alpha customer should be visible
        $response->assertSee('Alpha VIP Customer');

        // Beta customer MUST NOT be visible
        $response->assertDontSee('Beta VIP Customer');
    }
}
