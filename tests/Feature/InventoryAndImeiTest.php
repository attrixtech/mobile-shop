<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Company;
use App\Models\ImeiRecord;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockBalance;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Tenant;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryAndImeiTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Company $company;
    protected Branch $branch1;
    protected Branch $branch2;
    protected Product $product;
    protected ProductVariant $variant;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'Apex Retail',
            'slug' => 'apexretail',
            'status' => 'active',
        ]);

        $this->company = Company::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Apex Retail Pvt Ltd',
            'invoice_prefix' => 'APEX',
        ]);

        $this->branch1 = Branch::create([
            'tenant_id' => $this->tenant->id,
            'company_id' => $this->company->id,
            'name' => 'Andheri Store',
            'code' => 'AND01',
            'is_main' => true,
        ]);

        $this->branch2 = Branch::create([
            'tenant_id' => $this->tenant->id,
            'company_id' => $this->company->id,
            'name' => 'Bandra Store',
            'code' => 'BND01',
            'is_main' => false,
        ]);

        $this->user = User::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Inventory Head',
            'email' => 'inv@apex.com',
            'password' => bcrypt('password'),
            'role' => 'inventory_manager',
        ]);

        $this->product = Product::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Samsung S24 Ultra',
            'type' => 'serialized',
            'purchase_price' => 95000,
            'selling_price' => 129999,
            'gst_rate' => 18,
        ]);

        $this->variant = ProductVariant::create([
            'tenant_id' => $this->tenant->id,
            'product_id' => $this->product->id,
            'name' => '12GB / 256GB Titanium Gray',
            'sku' => 'S24U-12-256',
            'purchase_price' => 95000,
            'selling_price' => 129999,
        ]);
    }

    public function test_cannot_register_duplicate_primary_imei(): void
    {
        $this->expectException(\Exception::class);

        $inventoryService = app(InventoryService::class);

        // Register first time
        $inventoryService->registerImei(
            $this->tenant->id,
            $this->branch1->id,
            $this->product->id,
            $this->variant->id,
            '359876543210987',
            null,
            null,
            null,
            null,
            95000
        );

        // Attempt duplicate registration
        $inventoryService->registerImei(
            $this->tenant->id,
            $this->branch2->id,
            $this->product->id,
            $this->variant->id,
            '359876543210987', // Identical IMEI
            null,
            null,
            null,
            null,
            95000
        );
    }

    public function test_inter_branch_transfer_updates_stock_and_imei_location(): void
    {
        $inventoryService = app(InventoryService::class);

        // Register IMEI in branch 1
        $imei = $inventoryService->registerImei(
            $this->tenant->id,
            $this->branch1->id,
            $this->product->id,
            $this->variant->id,
            '359876543210999',
            null,
            null,
            null,
            null,
            95000
        );

        // Add initial stock balance to branch 1
        $inventoryService->adjustStock(
            $this->tenant->id,
            $this->branch1->id,
            $this->product->id,
            $this->variant->id,
            1,
            'purchase',
            'Purchase',
            null,
            'Initial stock',
            $this->user->id
        );

        $balance1 = StockBalance::where('branch_id', $this->branch1->id)->where('product_id', $this->product->id)->first();
        $this->assertEquals(1, $balance1->quantity);

        // Create Stock Transfer record
        $transfer = StockTransfer::create([
            'tenant_id' => $this->tenant->id,
            'transfer_number' => 'TRF-001',
            'from_branch_id' => $this->branch1->id,
            'to_branch_id' => $this->branch2->id,
            'status' => 'dispatched',
            'requested_by' => $this->user->id,
        ]);

        StockTransferItem::create([
            'transfer_id' => $transfer->id,
            'product_id' => $this->product->id,
            'variant_id' => $this->variant->id,
            'quantity' => 1,
            'imeis' => [$imei->imei1],
        ]);

        // Execute inter-branch transfer
        $transfer->load('items');
        $inventoryService->executeTransfer($transfer, $this->user->id);

        $this->assertEquals('received', $transfer->status);

        // Branch 1 stock should now be 0
        $balance1->refresh();
        $this->assertEquals(0, $balance1->quantity);

        // Branch 2 stock should now be 1
        $balance2 = StockBalance::where('branch_id', $this->branch2->id)->where('product_id', $this->product->id)->first();
        $this->assertNotNull($balance2);
        $this->assertEquals(1, $balance2->quantity);

        // IMEI branch should be updated to branch 2
        $imei->refresh();
        $this->assertEquals($this->branch2->id, $imei->branch_id);
    }
}
