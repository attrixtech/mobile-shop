<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\CashRegister;
use App\Models\Category;
use App\Models\Company;
use App\Models\Customer;
use App\Models\ExchangeTransaction;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ImeiMovement;
use App\Models\ImeiRecord;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Purchase;
use App\Models\RepairJob;
use App\Models\Sale;
use App\Models\StockBalance;
use App\Models\SubscriptionPlan;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\UsedDevice;
use App\Models\User;
use App\Services\AccountingService;
use App\Services\InventoryService;
use App\Services\PurchaseService;
use App\Services\SaleService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Subscription Plans
        $planFree = SubscriptionPlan::create([
            'name' => 'Free Trial',
            'slug' => 'free',
            'price' => 0.00,
            'billing_interval' => 'monthly',
            'branch_limit' => 1,
            'user_limit' => 1,
            'product_limit' => 100,
            'features' => ['pos', 'basic_inventory', 'basic_reports'],
            'is_active' => true,
        ]);

        $planStarter = SubscriptionPlan::create([
            'name' => 'Starter',
            'slug' => 'starter',
            'price' => 499.00,
            'billing_interval' => 'monthly',
            'branch_limit' => 1,
            'user_limit' => 3,
            'product_limit' => 1000,
            'features' => ['pos', 'imei_tracking', 'gst_billing', 'customer_crm', 'basic_inventory'],
            'is_active' => true,
        ]);

        $planPro = SubscriptionPlan::create([
            'name' => 'Professional',
            'slug' => 'professional',
            'price' => 999.00,
            'billing_interval' => 'monthly',
            'branch_limit' => 3,
            'user_limit' => 10,
            'product_limit' => 10000,
            'features' => ['all_starter', 'repair_management', 'warranty_tracking', 'exchange_management', 'advanced_reports', 'suppliers'],
            'is_active' => true,
        ]);

        $planBiz = SubscriptionPlan::create([
            'name' => 'Business',
            'slug' => 'business',
            'price' => 1999.00,
            'billing_interval' => 'monthly',
            'branch_limit' => 10,
            'user_limit' => 25,
            'product_limit' => 50000,
            'features' => ['all_features', 'full_accounting', 'multi_branch_transfers', 'public_storefront', 'advanced_analytics', 'api_access'],
            'is_active' => true,
        ]);

        // 2. Platform Super Admin (No tenant, platform level)
        User::create([
            'name' => 'Platform Super Admin',
            'email' => 'superadmin@webotapp.com',
            'phone' => '+91 98200 12345',
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password123'),
        ]);

        // 3. Demo Tenant
        $tenant = Tenant::create([
            'name' => 'Webotapp Flagship Mobile Care & Retail',
            'slug' => 'webotapp-flagship',
            'domain' => 'flagship.mobileshop.test',
            'plan_id' => $planBiz->id,
            'status' => 'active',
            'trial_ends_at' => null,
            'subscription_ends_at' => now()->addYear(),
        ]);

        // Company
        $company = Company::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'name' => 'Webotapp Mobiles & Electronics',
            'legal_name' => 'Webotapp Retail India Pvt Ltd',
            'gstin' => '27AABCU9603R1ZM',
            'pan' => 'AABCU9603R',
            'email' => 'contact@webotappmobiles.com',
            'phone' => '+91 98201 54321',
            'address' => 'Shop 12-14, Ground Floor, Crystal Plaza, New Link Road',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'state_code' => '27',
            'pincode' => '400053',
            'invoice_prefix' => 'WEB',
            'currency' => 'INR',
        ]);

        // Branches
        $branch1 = Branch::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'company_id' => $company->id,
            'name' => 'Andheri West Mega Store',
            'code' => 'B001',
            'phone' => '+91 98201 54321',
            'email' => 'andheri@webotappmobiles.com',
            'address' => 'Shop 12-14, Crystal Plaza, Andheri West',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'state_code' => '27',
            'pincode' => '400053',
            'is_main' => true,
            'is_active' => true,
        ]);

        $branch2 = Branch::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'company_id' => $company->id,
            'name' => 'Bandra Retail Hub',
            'code' => 'B002',
            'phone' => '+91 98201 98765',
            'email' => 'bandra@webotappmobiles.com',
            'address' => '45 Hill Road, Near Bandra Station',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'state_code' => '27',
            'pincode' => '400050',
            'is_main' => false,
            'is_active' => true,
        ]);

        // Users
        $owner = User::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branch1->id,
            'name' => 'Rajesh Sharma (Owner)',
            'email' => 'owner@webotapp.com',
            'phone' => '+91 98200 55501',
            'role' => 'business_owner',
            'is_active' => true,
            'password' => Hash::make('password123'),
        ]);

        $manager = User::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branch1->id,
            'name' => 'Vikram Malhotra (Store Manager)',
            'email' => 'manager@webotapp.com',
            'phone' => '+91 98200 55502',
            'role' => 'store_manager',
            'is_active' => true,
            'password' => Hash::make('password123'),
        ]);

        $sales = User::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branch1->id,
            'name' => 'Sneha Kulkarni (Sales / Cashier)',
            'email' => 'sales@webotapp.com',
            'phone' => '+91 98200 55503',
            'role' => 'sales_executive',
            'is_active' => true,
            'password' => Hash::make('password123'),
        ]);

        $technician = User::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branch1->id,
            'name' => 'Mohd Imran (Lead Technician)',
            'email' => 'tech@webotapp.com',
            'phone' => '+91 98200 55504',
            'role' => 'repair_technician',
            'is_active' => true,
            'password' => Hash::make('password123'),
        ]);

        // Initialize Chart of Accounts
        $accountingService = app(AccountingService::class);
        $accounts = $accountingService->getOrCreateAccounts($tenant->id, $branch1->id);

        // 4. Categories & Brands
        $catSmartphones = Category::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'name' => 'Smartphones',
            'slug' => 'smartphones',
            'description' => 'Flagship, mid-range and budget Android & iOS mobile phones',
            'is_active' => true,
        ]);

        $catAccessories = Category::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'name' => 'Accessories',
            'slug' => 'accessories',
            'description' => 'Chargers, Cables, Power Banks, Audio, and Protection',
            'is_active' => true,
        ]);

        $catRepairs = Category::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'name' => 'Repair Services & Spares',
            'slug' => 'repairs',
            'description' => 'Mobile screen replacements, battery repairs and spare parts',
            'is_active' => true,
        ]);

        $brandApple = Brand::withoutGlobalScope('tenant')->create(['tenant_id' => $tenant->id, 'name' => 'Apple', 'slug' => 'apple']);
        $brandSamsung = Brand::withoutGlobalScope('tenant')->create(['tenant_id' => $tenant->id, 'name' => 'Samsung', 'slug' => 'samsung']);
        $brandOnePlus = Brand::withoutGlobalScope('tenant')->create(['tenant_id' => $tenant->id, 'name' => 'OnePlus', 'slug' => 'oneplus']);
        $brandRedmi = Brand::withoutGlobalScope('tenant')->create(['tenant_id' => $tenant->id, 'name' => 'Xiaomi Redmi', 'slug' => 'redmi']);
        $brandBoat = Brand::withoutGlobalScope('tenant')->create(['tenant_id' => $tenant->id, 'name' => 'boAt', 'slug' => 'boat']);

        // 5. Products & Variants
        // Product 1: iPhone 15 Pro
        $iphone = Product::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'category_id' => $catSmartphones->id,
            'brand_id' => $brandApple->id,
            'name' => 'Apple iPhone 15 Pro',
            'code' => 'IPH15P',
            'sku' => 'IPH-15P-BASE',
            'barcode' => '8901234567890',
            'type' => 'serialized',
            'description' => 'A17 Pro chip, Titanium design, 48MP main camera system with 3x optical zoom.',
            'processor' => 'Apple A17 Pro (3nm)',
            'battery' => '3274 mAh Li-Ion',
            'display_size' => '6.1 inch Super Retina XDR OLED 120Hz',
            'os' => 'iOS 17',
            'network' => '5G / 4G VoLTE',
            'warranty_months' => 12,
            'purchase_price' => 112000.00,
            'selling_price' => 129900.00,
            'mrp' => 134900.00,
            'wholesale_price' => 122000.00,
            'hsn_code' => '85171300',
            'gst_rate' => 18.00,
            'min_stock' => 2,
            'reorder_qty' => 5,
            'is_active' => true,
            'is_featured' => true,
        ]);

        $varIphone1 = ProductVariant::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'product_id' => $iphone->id,
            'name' => '128GB - Natural Titanium',
            'sku' => 'IPH-15P-128-NAT',
            'ram' => '8GB',
            'storage' => '128GB',
            'color' => 'Natural Titanium',
            'purchase_price' => 112000.00,
            'selling_price' => 129900.00,
            'mrp' => 134900.00,
        ]);

        $varIphone2 = ProductVariant::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'product_id' => $iphone->id,
            'name' => '256GB - Blue Titanium',
            'sku' => 'IPH-15P-256-BLU',
            'ram' => '8GB',
            'storage' => '256GB',
            'color' => 'Blue Titanium',
            'purchase_price' => 121000.00,
            'selling_price' => 139900.00,
            'mrp' => 144900.00,
        ]);

        // Product 2: Samsung Galaxy S24 Ultra
        $samsung = Product::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'category_id' => $catSmartphones->id,
            'brand_id' => $brandSamsung->id,
            'name' => 'Samsung Galaxy S24 Ultra 5G',
            'code' => 'SM-S928B',
            'sku' => 'SAM-S24U-BASE',
            'barcode' => '8901234567891',
            'type' => 'serialized',
            'description' => 'Galaxy AI, Snapdragon 8 Gen 3, S-Pen included, 200MP camera.',
            'processor' => 'Snapdragon 8 Gen 3 for Galaxy',
            'battery' => '5000 mAh 45W Fast Charging',
            'display_size' => '6.8 inch Dynamic LTPO AMOLED 2X 120Hz',
            'os' => 'Android 14, One UI 6.1',
            'network' => '5G / 4G VoLTE',
            'warranty_months' => 12,
            'purchase_price' => 110000.00,
            'selling_price' => 129999.00,
            'mrp' => 134999.00,
            'wholesale_price' => 119000.00,
            'hsn_code' => '85171300',
            'gst_rate' => 18.00,
            'min_stock' => 2,
            'reorder_qty' => 4,
            'is_active' => true,
            'is_featured' => true,
        ]);

        $varSamsung1 = ProductVariant::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'product_id' => $samsung->id,
            'name' => '12GB RAM / 256GB - Titanium Gray',
            'sku' => 'SAM-S24U-256-GRY',
            'ram' => '12GB',
            'storage' => '256GB',
            'color' => 'Titanium Gray',
            'purchase_price' => 110000.00,
            'selling_price' => 129999.00,
            'mrp' => 134999.00,
        ]);

        // Product 3: OnePlus 12
        $oneplus = Product::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'category_id' => $catSmartphones->id,
            'brand_id' => $brandOnePlus->id,
            'name' => 'OnePlus 12 5G',
            'code' => 'CPH2581',
            'sku' => 'OP-12-BASE',
            'barcode' => '8901234567892',
            'type' => 'serialized',
            'description' => 'Snapdragon 8 Gen 3, 4th Gen Hasselblad Camera, 5400 mAh battery with 100W SuperVOOC.',
            'processor' => 'Snapdragon 8 Gen 3',
            'battery' => '5400 mAh',
            'display_size' => '6.82 inch 2K ProXDR 120Hz',
            'os' => 'OxygenOS 14',
            'network' => '5G / 4G VoLTE',
            'warranty_months' => 12,
            'purchase_price' => 54000.00,
            'selling_price' => 64999.00,
            'mrp' => 69999.00,
            'wholesale_price' => 59000.00,
            'hsn_code' => '85171300',
            'gst_rate' => 18.00,
            'min_stock' => 3,
            'reorder_qty' => 6,
            'is_active' => true,
            'is_featured' => true,
        ]);

        $varOneplus1 = ProductVariant::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'product_id' => $oneplus->id,
            'name' => '12GB RAM / 256GB - Silky Black',
            'sku' => 'OP-12-256-BLK',
            'ram' => '12GB',
            'storage' => '256GB',
            'color' => 'Silky Black',
            'purchase_price' => 54000.00,
            'selling_price' => 64999.00,
            'mrp' => 69999.00,
        ]);

        // Product 4: Redmi Note 13 Pro
        $redmi = Product::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'category_id' => $catSmartphones->id,
            'brand_id' => $brandRedmi->id,
            'name' => 'Redmi Note 13 Pro 5G',
            'code' => 'RED-N13P',
            'sku' => 'RED-N13P-BASE',
            'barcode' => '8901234567893',
            'type' => 'serialized',
            'description' => '200MP Camera with OIS, 1.5K 120Hz AMOLED, Snapdragon 7s Gen 2.',
            'processor' => 'Snapdragon 7s Gen 2',
            'battery' => '5100 mAh 67W Turbo Charge',
            'display_size' => '6.67 inch AMOLED 120Hz',
            'os' => 'MIUI 14',
            'network' => '5G / 4G VoLTE',
            'warranty_months' => 12,
            'purchase_price' => 20500.00,
            'selling_price' => 25999.00,
            'mrp' => 28999.00,
            'wholesale_price' => 23000.00,
            'hsn_code' => '85171300',
            'gst_rate' => 18.00,
            'min_stock' => 5,
            'reorder_qty' => 10,
            'is_active' => true,
            'is_featured' => false,
        ]);

        // Product 5: Apple 20W USB-C Power Adapter (Non-serialized accessory)
        $charger = Product::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'category_id' => $catAccessories->id,
            'brand_id' => $brandApple->id,
            'name' => 'Apple 20W USB-C Power Adapter',
            'code' => 'MHJE3HN/A',
            'sku' => 'ACC-APL-20W',
            'barcode' => '194252157015',
            'type' => 'standard',
            'description' => 'Original Apple 20W USB-C fast charging adapter for iPhone & iPad.',
            'purchase_price' => 1400.00,
            'selling_price' => 1900.00,
            'mrp' => 1900.00,
            'wholesale_price' => 1650.00,
            'hsn_code' => '85044030',
            'gst_rate' => 18.00,
            'min_stock' => 10,
            'reorder_qty' => 25,
            'is_active' => true,
            'is_featured' => false,
        ]);

        // Product 6: boAt Airdopes 141 (Non-serialized accessory)
        $boatEarbuds = Product::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'category_id' => $catAccessories->id,
            'brand_id' => $brandBoat->id,
            'name' => 'boAt Airdopes 141 TWS Earbuds',
            'code' => 'AD141',
            'sku' => 'ACC-BOAT-141',
            'barcode' => '8907605114101',
            'type' => 'standard',
            'description' => '42H Playtime, ENx Tech, ASAP Charge, IPX4 Water Resistance.',
            'purchase_price' => 850.00,
            'selling_price' => 1299.00,
            'mrp' => 4490.00,
            'wholesale_price' => 1050.00,
            'hsn_code' => '85183000',
            'gst_rate' => 18.00,
            'min_stock' => 5,
            'reorder_qty' => 15,
            'is_active' => true,
            'is_featured' => false,
        ]);

        // Product 7: Screen Replacement Service (Service product)
        $screenService = Product::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'category_id' => $catRepairs->id,
            'brand_id' => $brandApple->id,
            'name' => 'iPhone Display Glass & OLED Replacement Service',
            'code' => 'SRV-SCR-IPH',
            'sku' => 'SRV-SCR-01',
            'barcode' => 'SRV89001',
            'type' => 'service',
            'description' => 'Professional genuine OLED panel fitting with TrueTone calibration.',
            'purchase_price' => 4500.00,
            'selling_price' => 7500.00,
            'mrp' => 8500.00,
            'hsn_code' => '998713',
            'gst_rate' => 18.00,
            'min_stock' => 0,
            'reorder_qty' => 0,
            'is_active' => true,
        ]);

        // 6. Suppliers
        $supIngram = Supplier::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'name' => 'Ingram Micro India Pvt Ltd',
            'business_name' => 'Ingram Micro Distributing',
            'gstin' => '27AAACI1580E1ZT',
            'contact_person' => 'Sunil Deshmukh',
            'phone' => '+91 22 6678 9000',
            'email' => 'orders@ingrammicro.co.in',
            'address' => 'Godrej Coliseum, Somaiya Hospital Road, Sion East',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'state_code' => '27',
            'pincode' => '400022',
            'opening_balance' => 0,
            'current_balance' => 0,
            'is_active' => true,
        ]);

        $supRedington = Supplier::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'name' => 'Redington India Limited',
            'business_name' => 'Redington Distribution Hub',
            'gstin' => '27AAACR4063F1ZB',
            'contact_person' => 'Kavita Menon',
            'phone' => '+91 22 4001 8000',
            'email' => 'sales.west@redington.co.in',
            'address' => 'Redington Centre, Guindy / Andheri Branch',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'state_code' => '27',
            'pincode' => '400093',
            'opening_balance' => 0,
            'current_balance' => 0,
            'is_active' => true,
        ]);

        // 7. Customers
        $custRahul = Customer::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'name' => 'Rahul Sharma',
            'phone' => '9820011223',
            'email' => 'rahul.sharma@example.com',
            'gstin' => null,
            'address' => 'Flat 402, Sea Green Apts, Juhu Tara Road',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'state_code' => '27',
            'pincode' => '400049',
            'customer_type' => 'retail',
            'loyalty_points' => 150,
            'credit_limit' => 20000.00,
        ]);

        $custPriya = Customer::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'name' => 'Priya Patel',
            'phone' => '9820044556',
            'email' => 'priya.patel@example.com',
            'gstin' => null,
            'address' => '102 Sunrise Heights, Lokhandwala',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'state_code' => '27',
            'pincode' => '400053',
            'customer_type' => 'retail',
            'loyalty_points' => 25,
            'credit_limit' => 10000.00,
        ]);

        $custApex = Customer::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'name' => 'Apex Tech Solutions LLP',
            'phone' => '9820077889',
            'email' => 'procurement@apextech.com',
            'gstin' => '27AAFFA1234F1Z5',
            'address' => 'Unit 705, Nesco IT Park, Goregaon East',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'state_code' => '27',
            'pincode' => '400063',
            'customer_type' => 'corporate',
            'loyalty_points' => 500,
            'credit_limit' => 150000.00,
        ]);

        // 8. Seed Initial Stock & IMEIs using Services
        $inventoryService = app(InventoryService::class);
        $purchaseService = app(PurchaseService::class);
        $saleService = app(SaleService::class);

        // Purchases with IMEIs
        $purchase1 = $purchaseService->createPurchase([
            'tenant_id' => $tenant->id,
            'branch_id' => $branch1->id,
            'supplier_id' => $supIngram->id,
            'supplier_invoice_no' => 'ING-2026-9041',
            'invoice_date' => now()->subDays(5)->toDateString(),
            'items' => [
                [
                    'product_id' => $iphone->id,
                    'variant_id' => $varIphone1->id,
                    'quantity' => 3,
                    'unit_cost' => 112000.00,
                    'tax_rate' => 18.00,
                    'imeis' => ['356789101234561', '356789101234562', '356789101234563'],
                ],
                [
                    'product_id' => $samsung->id,
                    'variant_id' => $varSamsung1->id,
                    'quantity' => 2,
                    'unit_cost' => 110000.00,
                    'tax_rate' => 18.00,
                    'imeis' => ['358901234567891', '358901234567892'],
                ],
            ],
            'paid_amount' => 450000.00,
            'payment_method' => 'bank_transfer',
            'payment_reference' => 'NEFT-ING-88219',
            'user_id' => $manager->id,
        ]);

        $purchase2 = $purchaseService->createPurchase([
            'tenant_id' => $tenant->id,
            'branch_id' => $branch1->id,
            'supplier_id' => $supRedington->id,
            'supplier_invoice_no' => 'RED-2026-4412',
            'invoice_date' => now()->subDays(3)->toDateString(),
            'items' => [
                [
                    'product_id' => $oneplus->id,
                    'variant_id' => $varOneplus1->id,
                    'quantity' => 3,
                    'unit_cost' => 54000.00,
                    'tax_rate' => 18.00,
                    'imeis' => ['864501234567811', '864501234567812', '864501234567813'],
                ],
                [
                    'product_id' => $charger->id,
                    'variant_id' => null,
                    'quantity' => 20,
                    'unit_cost' => 1400.00,
                    'tax_rate' => 18.00,
                    'imeis' => [],
                ],
                [
                    'product_id' => $boatEarbuds->id,
                    'variant_id' => null,
                    'quantity' => 15,
                    'unit_cost' => 850.00,
                    'tax_rate' => 18.00,
                    'imeis' => [],
                ],
            ],
            'paid_amount' => 200000.00,
            'payment_method' => 'bank_transfer',
            'payment_reference' => 'RTGS-RED-44211',
            'user_id' => $manager->id,
        ]);

        // Branch 2 stock purchase
        $purchase3 = $purchaseService->createPurchase([
            'tenant_id' => $tenant->id,
            'branch_id' => $branch2->id,
            'supplier_id' => $supRedington->id,
            'supplier_invoice_no' => 'RED-2026-4488',
            'invoice_date' => now()->subDays(2)->toDateString(),
            'items' => [
                [
                    'product_id' => $redmi->id,
                    'variant_id' => null,
                    'quantity' => 5,
                    'unit_cost' => 20500.00,
                    'tax_rate' => 18.00,
                    'imeis' => ['869901234567891', '869901234567892', '869901234567893', '869901234567894', '869901234567895'],
                ],
                [
                    'product_id' => $charger->id,
                    'variant_id' => null,
                    'quantity' => 10,
                    'unit_cost' => 1400.00,
                    'tax_rate' => 18.00,
                    'imeis' => [],
                ],
            ],
            'paid_amount' => 116500.00,
            'payment_method' => 'bank_transfer',
            'payment_reference' => 'NEFT-RED-55214',
            'user_id' => $owner->id,
        ]);

        // 9. Process Demo Sales via SaleService
        // Sale 1: Rahul buys iPhone 15 Pro (IMEI: 356789101234561) + 20W Charger with UPI
        $sale1 = $saleService->processSale([
            'tenant_id' => $tenant->id,
            'branch_id' => $branch1->id,
            'customer_id' => $custRahul->id,
            'items' => [
                [
                    'product_id' => $iphone->id,
                    'variant_id' => $varIphone1->id,
                    'quantity' => 1,
                    'unit_price' => 129900.00,
                    'discount' => 1000.00,
                    'imeis' => ['356789101234561'],
                ],
                [
                    'product_id' => $charger->id,
                    'variant_id' => null,
                    'quantity' => 1,
                    'unit_price' => 1900.00,
                    'discount' => 0.00,
                    'imeis' => [],
                ],
            ],
            'payments' => [
                [
                    'payment_method' => 'upi',
                    'amount' => 154344.00,
                    'reference_no' => 'UPI-ICICI-9948291',
                ],
            ],
            'user_id' => $sales->id,
            'notes' => 'Customer opted for UPI on POS counter 1',
        ]);

        // Sale 2: Priya Patel buys OnePlus 12 with Mobile Exchange
        // First record an Exchange Transaction
        $exchange1 = ExchangeTransaction::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branch1->id,
            'customer_id' => $custPriya->id,
            'old_brand' => 'Apple',
            'old_model' => 'iPhone 12 64GB',
            'imei1' => '359988776655443',
            'condition_screen' => 'Minor Scratches',
            'condition_battery' => 'Healthy (84%)',
            'condition_body' => 'Like New',
            'functional_status' => 'Fully Functional',
            'accessories_received' => 'Original Box + Cable',
            'estimated_value' => 18000.00,
            'approved_value' => 18000.00,
            'notes' => 'Tested and approved by Vikram Malhotra',
            'evaluated_by' => $manager->id,
            'status' => 'approved',
        ]);

        // Create used phone entry
        UsedDevice::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branch1->id,
            'exchange_id' => $exchange1->id,
            'brand' => 'Apple',
            'model' => 'iPhone 12 64GB Blue',
            'imei1' => '359988776655443',
            'grading' => 'B_good',
            'purchase_cost' => 18000.00,
            'repair_cost' => 0.00,
            'expected_selling_price' => 24000.00,
            'status' => 'in_stock',
            'notes' => 'Exchanged device ready for resale display',
        ]);

        $sale2 = $saleService->processSale([
            'tenant_id' => $tenant->id,
            'branch_id' => $branch1->id,
            'customer_id' => $custPriya->id,
            'exchange_id' => $exchange1->id,
            'items' => [
                [
                    'product_id' => $oneplus->id,
                    'variant_id' => $varOneplus1->id,
                    'quantity' => 1,
                    'unit_price' => 64999.00,
                    'discount' => 1000.00,
                    'imeis' => ['864501234567811'],
                ],
            ],
            'payments' => [
                [
                    'payment_method' => 'card',
                    'amount' => 57518.82, // Total after ₹18,000 exchange allowance
                    'reference_no' => 'HDFC-POS-7741',
                ],
            ],
            'user_id' => $sales->id,
            'notes' => 'Exchange sale completed with ₹18,000 buyback voucher applied',
        ]);

        // 10. Repair Center Sample Job
        $repairJob1 = RepairJob::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branch1->id,
            'customer_id' => $custRahul->id,
            'job_number' => 'REP-' . date('Ymd') . '-001',
            'brand' => 'Apple',
            'model' => 'iPhone 13',
            'imei' => '358899001122334',
            'device_condition' => 'Cracked glass upper-right corner, touch responsive',
            'reported_problem' => 'Glass cracked after falling on pavement, display flickering slightly.',
            'accessories_received' => 'Spigen protective case',
            'warranty_status' => 'out_of_warranty',
            'technician_id' => $technician->id,
            'estimated_cost' => 6500.00,
            'final_cost' => 6500.00,
            'advance_paid' => 1000.00,
            'status' => 'under_repair',
            'received_at' => now()->subDay(),
            'estimated_delivery_at' => now()->addDay(),
            'created_by' => $sales->id,
        ]);

        $repairJob1->items()->create([
            'type' => 'part',
            'description' => 'iPhone 13 Premium OLED Panel',
            'product_id' => $screenService->id,
            'quantity' => 1,
            'unit_price' => 5000.00,
            'total_amount' => 5000.00,
        ]);

        $repairJob1->items()->create([
            'type' => 'labor',
            'description' => 'Precision Screen Fitting, TrueTone Program & Water Seal',
            'product_id' => null,
            'quantity' => 1,
            'unit_price' => 1500.00,
            'total_amount' => 1500.00,
        ]);

        $repairJob1->histories()->create([
            'status' => 'received',
            'notes' => 'Device received at reception with ₹1,000 advance cash',
            'changed_by' => $sales->id,
        ]);

        $repairJob1->histories()->create([
            'status' => 'under_repair',
            'notes' => 'Technician Imran started screen replacement',
            'changed_by' => $technician->id,
        ]);

        // 11. Expenses
        $expCatRent = ExpenseCategory::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'name' => 'Store Rent & Maintenance',
        ]);

        $expCatUtil = ExpenseCategory::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'name' => 'Electricity & High-Speed Internet',
        ]);

        Expense::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branch1->id,
            'category_id' => $expCatUtil->id,
            'amount' => 4500.00,
            'expense_date' => now()->subDays(2)->toDateString(),
            'vendor_name' => 'Adani Electricity Mumbai Ltd',
            'payment_method' => 'bank_transfer',
            'reference_no' => 'BILL-ADANI-4491',
            'notes' => 'Commercial power bill for showroom and repair desk',
            'user_id' => $manager->id,
            'status' => 'approved',
        ]);

        // 12. Cash Register for Branch 1
        CashRegister::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branch1->id,
            'user_id' => $sales->id,
            'opened_at' => now()->startOfDay()->addHours(9),
            'opening_balance' => 5000.00,
            'cash_sales' => 0.00,
            'cash_expenses' => 0.00,
            'system_expected_cash' => 5000.00,
            'discrepancy' => 0.00,
            'status' => 'open',
            'notes' => 'Morning shift register opened with float ₹5,000',
        ]);

        $this->call(MoreProductsAndRepairsSeeder::class);
    }
}
