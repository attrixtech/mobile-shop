<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Customer;
use App\Models\ImeiRecord;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockBalance;
use App\Models\Tenant;
use App\Models\User;
use App\Services\AccountingService;
use App\Services\GstService;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosSaleTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Company $company;
    protected Branch $branch;
    protected User $user;
    protected Customer $customer;
    protected Product $product;
    protected ProductVariant $variant;
    protected ImeiRecord $imei;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'Demo Mobiles',
            'slug' => 'demomobiles',
            'status' => 'active',
        ]);

        $this->company = Company::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Demo Mobiles Pvt Ltd',
            'legal_name' => 'Demo Mobiles Pvt Ltd',
            'gstin' => '27ABCDE1234F1Z5',
            'state' => 'Maharashtra',
            'state_code' => '27',
            'invoice_prefix' => 'DEMO',
        ]);

        $this->branch = Branch::create([
            'tenant_id' => $this->tenant->id,
            'company_id' => $this->company->id,
            'name' => 'Main Showroom',
            'code' => 'MS01',
            'state' => 'Maharashtra',
            'state_code' => '27',
            'is_main' => true,
            'is_active' => true,
        ]);

        $this->user = User::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'name' => 'Cashier Demo',
            'email' => 'cashier@demomobiles.com',
            'password' => bcrypt('password'),
            'role' => 'cashier',
            'is_active' => true,
        ]);

        $this->customer = Customer::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Amit Kumar',
            'phone' => '9876543210',
            'state' => 'Maharashtra',
            'state_code' => '27',
            'customer_type' => 'retail',
        ]);

        $this->product = Product::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'OnePlus 12 5G',
            'type' => 'serialized',
            'hsn_code' => '85171300',
            'purchase_price' => 50000,
            'selling_price' => 64999,
            'gst_rate' => 18,
            'is_active' => true,
        ]);

        $this->variant = ProductVariant::create([
            'tenant_id' => $this->tenant->id,
            'product_id' => $this->product->id,
            'name' => '16GB / 512GB Silky Black',
            'sku' => 'OP12-16-512',
            'purchase_price' => 50000,
            'selling_price' => 64999,
        ]);

        // Stock and IMEI
        StockBalance::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'product_id' => $this->product->id,
            'variant_id' => $this->variant->id,
            'quantity' => 5,
        ]);

        $this->imei = ImeiRecord::create([
            'tenant_id' => $this->tenant->id,
            'product_id' => $this->product->id,
            'variant_id' => $this->variant->id,
            'branch_id' => $this->branch->id,
            'imei1' => '861234567890123',
            'imei2' => '861234567890124',
            'status' => 'in_stock',
            'cost_price' => 50000,
            'selling_price' => 64999,
        ]);

        // Seed Chart of Accounts
        $accountingService = app(AccountingService::class);
        $accountingService->getOrCreateAccounts($this->tenant->id, $this->branch->id);
    }

    public function test_pos_sale_processes_correctly_with_imei_and_gst(): void
    {
        $saleService = app(SaleService::class);

        $saleData = [
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'user_id' => $this->user->id,
            'customer_name' => $this->customer->name,
            'customer_phone' => $this->customer->phone,
            'customer_gstin' => null,
            'customer_state' => $this->customer->state,
            'customer_state_code' => $this->customer->state_code,
            'discount_amount' => 0,
            'notes' => 'Test POS Transaction',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'variant_id' => $this->variant->id,
                    'quantity' => 1,
                    'unit_price' => 64999,
                    'imeis' => [$this->imei->imei1],
                ],
            ],
            'payments' => [
                [
                    'payment_method' => 'upi',
                    'amount' => 76698.82,
                    'reference_no' => 'UPI-TXN-998811',
                ],
            ],
        ];

        $sale = $saleService->processSale($saleData);

        // Assert sale record
        $this->assertNotNull($sale);
        $this->assertEquals(76698.82, (float) $sale->grand_total);
        $this->assertEquals('paid', $sale->payment_status);

        // Assert IMEI was marked as sold
        $this->imei->refresh();
        $this->assertEquals('sold', $this->imei->status);
        $this->assertEquals($this->customer->id, $this->imei->customer_id);

        // Assert Stock balance decreased
        $balance = StockBalance::where('branch_id', $this->branch->id)
            ->where('product_id', $this->product->id)
            ->first();
        $this->assertEquals(4, $balance->quantity);

        // Assert Intra-state GST (CGST + SGST)
        $this->assertGreaterThan(0, (float) $sale->cgst);
        $this->assertGreaterThan(0, (float) $sale->sgst);
        $this->assertEquals(0, (float) $sale->igst);
    }

    public function test_selling_unavailable_imei_throws_exception(): void
    {
        $this->expectException(\Exception::class);

        // Mark IMEI as already sold
        $this->imei->update(['status' => 'sold']);

        $saleService = app(SaleService::class);
        $saleService->processSale([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'user_id' => $this->user->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'variant_id' => $this->variant->id,
                    'quantity' => 1,
                    'unit_price' => 64999,
                    'imeis' => [$this->imei->imei1],
                ],
            ],
            'payments' => [
                ['payment_method' => 'cash', 'amount' => 64999],
            ],
        ]);
    }
}
