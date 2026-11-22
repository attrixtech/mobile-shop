<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Customer;
use App\Models\RepairJob;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepairJobTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Company $company;
    protected Branch $branch;
    protected User $technician;
    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'QuickFix Mobiles',
            'slug' => 'quickfix',
            'status' => 'active',
        ]);

        $this->company = Company::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'QuickFix Mobiles Service Center',
            'invoice_prefix' => 'QF',
        ]);

        $this->branch = Branch::create([
            'tenant_id' => $this->tenant->id,
            'company_id' => $this->company->id,
            'name' => 'Central Service Hub',
            'code' => 'SH01',
            'is_main' => true,
        ]);

        $this->technician = User::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'name' => 'Rahul Verma',
            'email' => 'rahul@quickfix.com',
            'password' => bcrypt('password'),
            'role' => 'repair_technician',
            'is_active' => true,
        ]);

        $this->customer = Customer::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Kavita Singh',
            'phone' => '9123456780',
            'customer_type' => 'retail',
        ]);
    }

    public function test_can_create_repair_job_card_with_diagnostics(): void
    {
        $this->actingAs($this->technician);
        session([
            'active_tenant_id' => $this->tenant->id,
            'active_branch_id' => $this->branch->id,
        ]);

        $response = $this->post(route('repairs.store'), [
            'customer_id' => $this->customer->id,
            'brand' => 'Apple',
            'model' => 'iPhone 13',
            'imei' => '352987112233445',
            'reported_problem' => 'Cracked OLED screen, touch unresponsive in upper right quadrant',
            'device_condition' => 'Minor dent on lower left bezel, scratch on camera ring',
            'accessories_received' => 'Original silicone case',
            'warranty_status' => 'out_of_warranty',
            'estimated_cost' => 8500,
            'advance_paid' => 1000,
            'technician_id' => $this->technician->id,
            'estimated_delivery_at' => now()->addDays(2)->toDateString(),
        ]);

        $response->assertRedirect();

        $job = RepairJob::where('imei', '352987112233445')->first();
        $this->assertNotNull($job);
        $this->assertEquals('received', $job->status);
        $this->assertEquals(8500, (float) $job->estimated_cost);
        $this->assertEquals(1000, (float) $job->advance_paid);
        $this->assertEquals($this->technician->id, $job->technician_id);
    }

    public function test_can_update_repair_status(): void
    {
        $this->actingAs($this->technician);
        session([
            'active_tenant_id' => $this->tenant->id,
            'active_branch_id' => $this->branch->id,
        ]);

        $job = RepairJob::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'job_number' => 'REP-TEST-001',
            'brand' => 'Samsung',
            'model' => 'Galaxy A54',
            'reported_problem' => 'Charging port loose',
            'estimated_cost' => 1500,
            'status' => 'received',
            'payment_status' => 'pending',
        ]);

        $response = $this->post(route('repairs.status', $job->id), [
            'status' => 'ready',
            'final_cost' => 1500,
            'notes' => 'Charging sub-board replaced and tested successfully',
        ]);

        $response->assertRedirect();
        $job->refresh();
        $this->assertEquals('ready', $job->status);
        $this->assertEquals(1500, (float) $job->final_cost);
    }
}
