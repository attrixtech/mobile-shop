<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\ExchangeTransaction;
use App\Models\ImeiRecord;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\RepairJob;
use App\Models\StockBalance;
use App\Models\Tenant;
use App\Models\UsedDevice;
use App\Models\User;
use Illuminate\Database\Seeder;

class MoreProductsAndRepairsSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::withoutGlobalScope('tenant')->first();
        if (!$tenant) {
            return;
        }

        $branch1 = Branch::withoutGlobalScope('tenant')->where('tenant_id', $tenant->id)->first();
        $branch2 = Branch::withoutGlobalScope('tenant')->where('tenant_id', $tenant->id)->skip(1)->first() ?? $branch1;

        $catSmartphones = Category::withoutGlobalScope('tenant')->firstOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'smartphones'],
            ['name' => 'Smartphones & Feature Phones', 'is_active' => true]
        );
        $catAccessories = Category::withoutGlobalScope('tenant')->firstOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'accessories'],
            ['name' => 'Accessories', 'is_active' => true]
        );
        $catRepairs = Category::withoutGlobalScope('tenant')->firstOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'repairs'],
            ['name' => 'Repair Services & Spares', 'is_active' => true]
        );
        $catWearables = Category::withoutGlobalScope('tenant')->firstOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'wearables'],
            ['name' => 'Smart Wearables & Audio', 'is_active' => true]
        );

        // Brands
        $brandApple = Brand::withoutGlobalScope('tenant')->firstOrCreate(['tenant_id' => $tenant->id, 'slug' => 'apple'], ['name' => 'Apple']);
        $brandSamsung = Brand::withoutGlobalScope('tenant')->firstOrCreate(['tenant_id' => $tenant->id, 'slug' => 'samsung'], ['name' => 'Samsung']);
        $brandOnePlus = Brand::withoutGlobalScope('tenant')->firstOrCreate(['tenant_id' => $tenant->id, 'slug' => 'oneplus'], ['name' => 'OnePlus']);
        $brandGoogle = Brand::withoutGlobalScope('tenant')->firstOrCreate(['tenant_id' => $tenant->id, 'slug' => 'google'], ['name' => 'Google Pixel']);
        $brandVivo = Brand::withoutGlobalScope('tenant')->firstOrCreate(['tenant_id' => $tenant->id, 'slug' => 'vivo'], ['name' => 'Vivo']);
        $brandRealme = Brand::withoutGlobalScope('tenant')->firstOrCreate(['tenant_id' => $tenant->id, 'slug' => 'realme'], ['name' => 'Realme']);
        $brandNothing = Brand::withoutGlobalScope('tenant')->firstOrCreate(['tenant_id' => $tenant->id, 'slug' => 'nothing'], ['name' => 'Nothing']);
        $brandAnker = Brand::withoutGlobalScope('tenant')->firstOrCreate(['tenant_id' => $tenant->id, 'slug' => 'anker'], ['name' => 'Anker']);
        $brandSpigen = Brand::withoutGlobalScope('tenant')->firstOrCreate(['tenant_id' => $tenant->id, 'slug' => 'spigen'], ['name' => 'Spigen']);
        $brandNoise = Brand::withoutGlobalScope('tenant')->firstOrCreate(['tenant_id' => $tenant->id, 'slug' => 'noise'], ['name' => 'Noise']);

        // Tech user for assignments
        $tech = User::where('role', 'repair_technician')->first() ?? User::first();
        $admin = User::whereIn('role', ['store_manager', 'business_owner'])->first() ?? User::first();

        // 1. More Smartphones
        $newPhones = [
            [
                'brand_id' => $brandGoogle->id,
                'name' => 'Google Pixel 8 5G',
                'code' => 'PIX8-5G',
                'sku' => 'GGL-PIX8-BASE',
                'barcode' => '842776100123',
                'desc' => 'Google Tensor G3 chip, Best Take AI camera, 120Hz Actua display.',
                'proc' => 'Google Tensor G3',
                'battery' => '4575 mAh, 27W Fast Charge',
                'display' => '6.2 inch OLED 120Hz',
                'os' => 'Android 14',
                'price_p' => 61000,
                'price_s' => 75999,
                'mrp' => 79999,
                'variants' => [
                    ['name' => '8GB RAM / 128GB - Hazel', 'sku' => 'GGL-P8-128-HZL', 'ram' => '8GB', 'storage' => '128GB', 'color' => 'Hazel', 'p' => 61000, 's' => 75999],
                    ['name' => '8GB RAM / 256GB - Obsidian', 'sku' => 'GGL-P8-256-OBS', 'ram' => '8GB', 'storage' => '256GB', 'color' => 'Obsidian', 'p' => 68000, 's' => 82999],
                ],
                'imeis' => ['353891102938471', '353891102938472', '353891102938473'],
            ],
            [
                'brand_id' => $brandVivo->id,
                'name' => 'Vivo V30 Pro 5G',
                'code' => 'V2319',
                'sku' => 'VIV-V30P-BASE',
                'barcode' => '890760123450',
                'desc' => 'ZEISS Professional Portrait Camera, 50MP Sony IMX920, 80W FlashCharge.',
                'proc' => 'MediaTek Dimensity 8200 (4nm)',
                'battery' => '5000 mAh 80W',
                'display' => '6.78 inch 1.5K 120Hz 3D Curved AMOLED',
                'os' => 'Funtouch OS 14',
                'price_p' => 37000,
                'price_s' => 46999,
                'mrp' => 49999,
                'variants' => [
                    ['name' => '12GB RAM / 512GB - Andaman Blue', 'sku' => 'VIV-V30P-512-BLU', 'ram' => '12GB', 'storage' => '512GB', 'color' => 'Andaman Blue', 'p' => 37000, 's' => 46999],
                ],
                'imeis' => ['864720059182341', '864720059182342'],
            ],
            [
                'brand_id' => $brandNothing->id,
                'name' => 'Nothing Phone (2a) 5G',
                'code' => 'A142',
                'sku' => 'NOTH-2A-BASE',
                'barcode' => '890801234567',
                'desc' => 'Glyph Interface, Dimensity 7200 Pro, 50MP Dual Camera with OIS.',
                'proc' => 'MediaTek Dimensity 7200 Pro',
                'battery' => '5000 mAh 45W',
                'display' => '6.7 inch Flexible AMOLED 120Hz',
                'os' => 'Nothing OS 2.5',
                'price_p' => 21000,
                'price_s' => 25999,
                'mrp' => 27999,
                'variants' => [
                    ['name' => '8GB RAM / 128GB - White', 'sku' => 'NOTH-2A-128-WHT', 'ram' => '8GB', 'storage' => '128GB', 'color' => 'White', 'p' => 21000, 's' => 25999],
                    ['name' => '8GB RAM / 256GB - Black', 'sku' => 'NOTH-2A-256-BLK', 'ram' => '8GB', 'storage' => '256GB', 'color' => 'Black', 'p' => 23000, 's' => 27999],
                ],
                'imeis' => ['356192084758191', '356192084758192'],
            ],
            [
                'brand_id' => $brandRealme->id,
                'name' => 'Realme 12 Pro+ 5G',
                'code' => 'RMX3840',
                'sku' => 'RLM-12P-BASE',
                'barcode' => '890600123499',
                'desc' => '64MP Periscope Portrait Camera, Luxury Watch Design, Snapdragon 7s Gen 2.',
                'proc' => 'Snapdragon 7s Gen 2',
                'battery' => '5000 mAh 67W SuperVOOC',
                'display' => '6.7 inch 120Hz Curved Vision Display',
                'os' => 'Realme UI 5.0',
                'price_p' => 25000,
                'price_s' => 31999,
                'mrp' => 34999,
                'variants' => [
                    ['name' => '12GB RAM / 256GB - Submarine Blue', 'sku' => 'RLM-12P-256-BLU', 'ram' => '12GB', 'storage' => '256GB', 'color' => 'Submarine Blue', 'p' => 25000, 's' => 31999],
                ],
                'imeis' => ['869104058291021', '869104058291022'],
            ],
            [
                'brand_id' => $brandSamsung->id,
                'name' => 'Samsung Galaxy S23 FE 5G',
                'code' => 'SM-S711B',
                'sku' => 'SAM-S23FE-BASE',
                'barcode' => '880609501234',
                'desc' => 'Epic Nightography, Exynos 2200 flagship power, Dynamic AMOLED 2X.',
                'proc' => 'Samsung Exynos 2200',
                'battery' => '4500 mAh 25W',
                'display' => '6.4 inch Dynamic AMOLED 2X 120Hz',
                'os' => 'Android 14, One UI 6.1',
                'price_p' => 43000,
                'price_s' => 54999,
                'mrp' => 59999,
                'variants' => [
                    ['name' => '8GB RAM / 128GB - Mint', 'sku' => 'SAM-S23FE-128-MNT', 'ram' => '8GB', 'storage' => '128GB', 'color' => 'Mint', 'p' => 43000, 's' => 54999],
                ],
                'imeis' => ['357281094829101', '357281094829102'],
            ],
            [
                'brand_id' => $brandApple->id,
                'name' => 'Apple iPhone 14',
                'code' => 'IPH14',
                'sku' => 'APL-IPH14-BASE',
                'barcode' => '194253381234',
                'desc' => 'A15 Bionic chip with 5-core GPU, Crash Detection, Ceramic Shield front.',
                'proc' => 'Apple A15 Bionic',
                'battery' => '3279 mAh',
                'display' => '6.1 inch Super Retina XDR OLED',
                'os' => 'iOS 17',
                'price_p' => 56000,
                'price_s' => 69900,
                'mrp' => 79900,
                'variants' => [
                    ['name' => '128GB - Midnight', 'sku' => 'APL-14-128-MDN', 'ram' => '6GB', 'storage' => '128GB', 'color' => 'Midnight', 'p' => 56000, 's' => 69900],
                    ['name' => '128GB - Starlight', 'sku' => 'APL-14-128-STL', 'ram' => '6GB', 'storage' => '128GB', 'color' => 'Starlight', 'p' => 56000, 's' => 69900],
                ],
                'imeis' => ['354920194857201', '354920194857202', '354920194857203'],
            ],
            [
                'brand_id' => $brandOnePlus->id,
                'name' => 'OnePlus Nord CE4 5G',
                'code' => 'CPH2613',
                'sku' => 'OP-CE4-BASE',
                'barcode' => '890123999881',
                'desc' => 'Snapdragon 7 Gen 3, 100W SuperVOOC, 5500 mAh battery, Aqua Touch display.',
                'proc' => 'Snapdragon 7 Gen 3',
                'battery' => '5500 mAh 100W',
                'display' => '6.7 inch AMOLED 120Hz',
                'os' => 'OxygenOS 14',
                'price_p' => 20500,
                'price_s' => 24999,
                'mrp' => 26999,
                'variants' => [
                    ['name' => '8GB RAM / 128GB - Celadon Marble', 'sku' => 'OP-CE4-128-MRB', 'ram' => '8GB', 'storage' => '128GB', 'color' => 'Celadon Marble', 'p' => 20500, 's' => 24999],
                ],
                'imeis' => ['862910485720191', '862910485720192'],
            ],
        ];

        foreach ($newPhones as $pData) {
            $p = Product::withoutGlobalScope('tenant')->updateOrCreate(
                ['tenant_id' => $tenant->id, 'sku' => $pData['sku']],
                [
                    'category_id' => $catSmartphones->id,
                    'brand_id' => $pData['brand_id'],
                    'name' => $pData['name'],
                    'code' => $pData['code'],
                    'barcode' => $pData['barcode'],
                    'type' => 'serialized',
                    'description' => $pData['desc'],
                    'processor' => $pData['proc'],
                    'battery' => $pData['battery'],
                    'display_size' => $pData['display'],
                    'os' => $pData['os'],
                    'network' => '5G / 4G VoLTE',
                    'warranty_months' => 12,
                    'purchase_price' => $pData['price_p'],
                    'selling_price' => $pData['price_s'],
                    'mrp' => $pData['mrp'],
                    'wholesale_price' => $pData['price_p'] * 1.05,
                    'hsn_code' => '85171300',
                    'gst_rate' => 18.00,
                    'min_stock' => 2,
                    'reorder_qty' => 5,
                    'is_active' => true,
                    'is_featured' => true,
                ]
            );

            $firstVar = null;
            foreach ($pData['variants'] as $vData) {
                $var = ProductVariant::withoutGlobalScope('tenant')->updateOrCreate(
                    ['tenant_id' => $tenant->id, 'sku' => $vData['sku']],
                    [
                        'product_id' => $p->id,
                        'name' => $vData['name'],
                        'ram' => $vData['ram'],
                        'storage' => $vData['storage'],
                        'color' => $vData['color'],
                        'purchase_price' => $vData['p'],
                        'selling_price' => $vData['s'],
                        'mrp' => $vData['s'] * 1.06,
                    ]
                );
                if (!$firstVar) {
                    $firstVar = $var;
                }

                // Branch stock
                StockBalance::withoutGlobalScope('tenant')->updateOrCreate(
                    ['tenant_id' => $tenant->id, 'branch_id' => $branch1->id, 'product_id' => $p->id, 'variant_id' => $var->id],
                    ['quantity' => count($pData['imeis'])]
                );
            }

            // Seed IMEIs
            foreach ($pData['imeis'] as $imeiStr) {
                ImeiRecord::withoutGlobalScope('tenant')->firstOrCreate(
                    ['tenant_id' => $tenant->id, 'imei1' => $imeiStr],
                    [
                        'branch_id' => $branch1->id,
                        'product_id' => $p->id,
                        'variant_id' => $firstVar ? $firstVar->id : null,
                        'imei2' => substr($imeiStr, 0, 14) . '0',
                        'serial_number' => 'SN-' . substr($imeiStr, -6),
                        'purchase_cost' => $pData['price_p'],
                        'status' => 'in_stock',
                        'warranty_months' => 12,
                        'warranty_start' => now()->toDateString(),
                        'warranty_end' => now()->addYear()->toDateString(),
                    ]
                );
            }
        }

        // 2. More Accessories & Smart Wearables
        $accessories = [
            [
                'brand_id' => $brandAnker->id,
                'category_id' => $catAccessories->id,
                'name' => 'Anker 65W GaN Fast Charger (3-Port)',
                'code' => 'A2667',
                'sku' => 'ANK-GAN-65W',
                'barcode' => '848061001928',
                'type' => 'standard',
                'desc' => 'High-speed compact GaN charger with 2x USB-C and 1x USB-A ports for phones and laptops.',
                'price_p' => 2400,
                'price_s' => 3499,
                'mrp' => 3999,
                'hsn' => '85044030',
                'stock' => 12,
            ],
            [
                'brand_id' => $brandSamsung->id,
                'category_id' => $catAccessories->id,
                'name' => 'Samsung 25W USB-C Super Fast Power Adapter',
                'code' => 'EP-TA800',
                'sku' => 'SAM-25W-ADPT',
                'barcode' => '880609012345',
                'type' => 'standard',
                'desc' => 'Original Samsung 25W Type-C adapter with Power Delivery and PPS charging support.',
                'price_p' => 850,
                'price_s' => 1299,
                'mrp' => 1499,
                'hsn' => '85044030',
                'stock' => 25,
            ],
            [
                'brand_id' => $brandSpigen->id,
                'category_id' => $catAccessories->id,
                'name' => 'Spigen Ultra Hybrid Clear Case for iPhone 15 Pro',
                'code' => 'ACS06543',
                'sku' => 'SPG-IP15P-HYB',
                'barcode' => '880989674011',
                'type' => 'standard',
                'desc' => 'Air Cushion technology, anti-yellowing crystal clear back, raised bezels for camera protection.',
                'price_p' => 900,
                'price_s' => 1499,
                'mrp' => 1799,
                'hsn' => '39269099',
                'stock' => 18,
            ],
            [
                'brand_id' => $brandNoise->id,
                'category_id' => $catWearables->id,
                'name' => 'Noise ColorFit Pro 5 Smartwatch',
                'code' => 'WRB-NOISE-P5',
                'sku' => 'ACC-NOISE-P5',
                'barcode' => '890614589012',
                'type' => 'standard',
                'desc' => '1.85 inch AMOLED Display, Bluetooth calling, rapid SOS, TruSync & 100+ sports modes.',
                'price_p' => 2100,
                'price_s' => 2999,
                'mrp' => 7999,
                'hsn' => '85176290',
                'stock' => 10,
            ],
            [
                'brand_id' => $brandRealme->id,
                'category_id' => $catWearables->id,
                'name' => 'Realme Buds Air 5 Pro ANC Earbuds',
                'code' => 'RMA2120',
                'sku' => 'RLM-BUDS-A5P',
                'barcode' => '890600987654',
                'type' => 'standard',
                'desc' => '50dB Active Noise Cancellation, LDAC Hi-Res Audio, 40 hours total battery playback.',
                'price_p' => 3100,
                'price_s' => 4499,
                'mrp' => 5999,
                'hsn' => '85183000',
                'stock' => 8,
            ],
            [
                'brand_id' => $brandOnePlus->id,
                'category_id' => $catAccessories->id,
                'name' => 'OnePlus 100W SuperVOOC Dual Ports Adapter',
                'code' => 'CPH-100W',
                'sku' => 'OP-100W-PWR',
                'barcode' => '890123000112',
                'type' => 'standard',
                'desc' => 'Dual output USB-A & USB-C ports with 100W maximum flash charging power.',
                'price_p' => 2400,
                'price_s' => 3299,
                'mrp' => 3999,
                'hsn' => '85044030',
                'stock' => 14,
            ],
            // 3. More Spare Parts & Services
            [
                'brand_id' => $brandSamsung->id,
                'category_id' => $catRepairs->id,
                'name' => 'Samsung Galaxy S23/S24 Ultra OEM Battery Replacement Kit',
                'code' => 'SP-BAT-S24U',
                'sku' => 'SPARE-SAM-BAT5K',
                'barcode' => '880609990011',
                'type' => 'standard',
                'desc' => 'Original 5000 mAh Li-Ion battery pack with adhesive water-seal strips.',
                'price_p' => 1800,
                'price_s' => 3200,
                'mrp' => 3800,
                'hsn' => '85076000',
                'stock' => 6,
            ],
            [
                'brand_id' => $brandApple->id,
                'category_id' => $catRepairs->id,
                'name' => 'iPhone 13 / 14 Lightning Port & Mic Flex Cable',
                'code' => 'SP-PRT-IPH13',
                'sku' => 'SPARE-APL-CHG',
                'barcode' => '194252990022',
                'type' => 'standard',
                'desc' => 'Original charging dock flex assembly with dual microphones and cellular antenna traces.',
                'price_p' => 950,
                'price_s' => 1999,
                'mrp' => 2499,
                'hsn' => '85177090',
                'stock' => 9,
            ],
            [
                'brand_id' => $brandApple->id,
                'category_id' => $catRepairs->id,
                'name' => 'Mobile Motherboard Ultrasonic Cleaning & Micro-Soldering Service',
                'code' => 'SRV-MB-CLEAN',
                'sku' => 'SRV-LOGIC-BRD',
                'barcode' => 'SRV99002',
                'type' => 'service',
                'desc' => 'Chip-level diagnosis, short circuit removal, power IC reballing, and water-damage ultrasonic bath.',
                'price_p' => 800,
                'price_s' => 2500,
                'mrp' => 3500,
                'hsn' => '998713',
                'stock' => 0,
            ],
        ];

        foreach ($accessories as $accData) {
            $prod = Product::withoutGlobalScope('tenant')->updateOrCreate(
                ['tenant_id' => $tenant->id, 'sku' => $accData['sku']],
                [
                    'category_id' => $accData['category_id'],
                    'brand_id' => $accData['brand_id'],
                    'name' => $accData['name'],
                    'code' => $accData['code'],
                    'barcode' => $accData['barcode'],
                    'type' => $accData['type'],
                    'description' => $accData['desc'],
                    'purchase_price' => $accData['price_p'],
                    'selling_price' => $accData['price_s'],
                    'mrp' => $accData['mrp'],
                    'wholesale_price' => $accData['price_p'] * 1.10,
                    'hsn_code' => $accData['hsn'],
                    'gst_rate' => 18.00,
                    'min_stock' => 4,
                    'reorder_qty' => 10,
                    'is_active' => true,
                ]
            );

            if ($accData['type'] !== 'service') {
                StockBalance::withoutGlobalScope('tenant')->updateOrCreate(
                    ['tenant_id' => $tenant->id, 'branch_id' => $branch1->id, 'product_id' => $prod->id, 'variant_id' => null],
                    ['quantity' => $accData['stock']]
                );
            }
        }

        // 4. More Customers
        $customersData = [
            ['name' => 'Amit Verma', 'phone' => '9820033445', 'email' => 'amit.verma@gmail.com', 'city' => 'Mumbai', 'type' => 'retail'],
            ['name' => 'Neha Kulkarni', 'phone' => '9820044556', 'email' => 'neha.kulkarni@yahoo.com', 'city' => 'Mumbai', 'type' => 'retail'],
            ['name' => 'Pooja Mehta', 'phone' => '9820055667', 'email' => 'pooja.mehta@outlook.com', 'city' => 'Mumbai', 'type' => 'retail'],
            ['name' => 'Rajesh Khanna Electronics', 'phone' => '9820066778', 'email' => 'rk.electronics@gmail.com', 'city' => 'Thane', 'type' => 'wholesale', 'gstin' => '27AAAFR5544K1Z1'],
            ['name' => 'Deepak Joshi', 'phone' => '9820077889', 'email' => 'deepak.j@hotmail.com', 'city' => 'Navi Mumbai', 'type' => 'retail'],
        ];

        $seededCustomers = [];
        foreach ($customersData as $c) {
            $seededCustomers[] = Customer::withoutGlobalScope('tenant')->firstOrCreate(
                ['tenant_id' => $tenant->id, 'phone' => $c['phone']],
                [
                    'name' => $c['name'],
                    'email' => $c['email'],
                    'city' => $c['city'],
                    'state' => 'Maharashtra',
                    'state_code' => '27',
                    'customer_type' => $c['type'],
                    'gstin' => $c['gstin'] ?? null,
                ]
            );
        }

        // 5. Populate More Repair Jobs with Varied Statuses
        $repairs = [
            [
                'customer_id' => $seededCustomers[0]->id,
                'job_number' => 'REP-' . date('Ymd') . '-002',
                'brand' => 'Samsung',
                'model' => 'Galaxy S22 Ultra',
                'imei' => '359182049182910',
                'condition' => 'Back glass lifted by 2mm due to swollen battery, no screen cracks',
                'problem' => 'Battery drains within 2 hours of charge, device heats up significantly near camera module.',
                'accessories' => 'Stylus S-Pen retained inside slot',
                'warranty' => 'out_of_warranty',
                'est_cost' => 3800,
                'final_cost' => 3800,
                'adv' => 500,
                'status' => 'parts_required',
                'notes' => 'Awaiting OEM 5000mAh battery shipment arrival from central warehouse.',
            ],
            [
                'customer_id' => $seededCustomers[1]->id,
                'job_number' => 'REP-' . date('Ymd') . '-003',
                'brand' => 'OnePlus',
                'model' => 'OnePlus 11 5G',
                'imei' => '861920491829304',
                'condition' => 'Mint physical condition, tempered glass installed',
                'problem' => 'Bright green vertical line appeared on AMOLED panel immediately following OxygenOS 14 OTA update.',
                'accessories' => 'Original retail box with warranty card',
                'warranty' => 'under_brand_warranty',
                'est_cost' => 0,
                'final_cost' => 0,
                'adv' => 0,
                'status' => 'under_inspection',
                'notes' => 'Sent ticket log to OnePlus service gateway for free screen replacement voucher authorization.',
            ],
            [
                'customer_id' => $seededCustomers[2]->id,
                'job_number' => 'REP-' . date('Ymd') . '-004',
                'brand' => 'Vivo',
                'model' => 'Vivo V29 5G',
                'imei' => '864910293847581',
                'condition' => 'Slight lint and dust packed inside USB-C connector socket',
                'problem' => 'Cable keeps falling out, fast charging does not engage, moisture detected warning pops up.',
                'accessories' => 'Silicone protective sleeve',
                'warranty' => 'out_of_warranty',
                'est_cost' => 1200,
                'final_cost' => 1200,
                'adv' => 1200,
                'status' => 'ready',
                'notes' => 'Charging flex cleaned and pins re-tensioned. Tested with 80W FlashCharge successfully.',
            ],
            [
                'customer_id' => $seededCustomers[3]->id,
                'job_number' => 'REP-' . date('Ymd') . '-005',
                'brand' => 'Xiaomi Redmi',
                'model' => 'Redmi Note 12 Pro',
                'imei' => '868192049182736',
                'condition' => 'Cracked outer camera lens glass, sensor clean',
                'problem' => 'Rear main camera lens shattered upon impact. Photos showing lens glare and blur.',
                'accessories' => 'None',
                'warranty' => 'out_of_warranty',
                'est_cost' => 850,
                'final_cost' => 850,
                'adv' => 850,
                'status' => 'delivered',
                'notes' => 'Sapphire glass replaced, dust chamber purged with dry air. Customer collected with bill.',
            ],
            [
                'customer_id' => $seededCustomers[4]->id,
                'job_number' => 'REP-' . date('Ymd') . '-006',
                'brand' => 'Google Pixel',
                'model' => 'Pixel 7',
                'imei' => '354819204918273',
                'condition' => 'Minor scuffs along aluminum camera bar visor',
                'problem' => 'Ear-piece speaker sound extremely faint during phone calls, loudspeaker works fine.',
                'accessories' => 'Leather folio cover',
                'warranty' => 'out_of_warranty',
                'est_cost' => 1500,
                'final_cost' => 0,
                'adv' => 500,
                'status' => 'received',
                'notes' => 'Device taken in at reception, queued for technician diagnostic inspection.',
            ],
            [
                'customer_id' => $seededCustomers[0]->id,
                'job_number' => 'REP-' . date('Ymd') . '-007',
                'brand' => 'Apple',
                'model' => 'iPhone 14 Pro',
                'imei' => '358192048192837',
                'condition' => 'Rear back glass heavily spiderweb cracked, MagSafe ring exposed',
                'problem' => 'Dropped on concrete stairs. Needs full back housing laser glass separation and replacement.',
                'accessories' => 'MagSafe wallet detached at desk',
                'warranty' => 'out_of_warranty',
                'est_cost' => 5500,
                'final_cost' => 5500,
                'adv' => 2000,
                'status' => 'approved',
                'notes' => 'Customer approved ₹5,500 estimate via WhatsApp. Scheduled for laser back-glass machine work.',
            ],
        ];

        foreach ($repairs as $r) {
            $job = RepairJob::withoutGlobalScope('tenant')->updateOrCreate(
                ['tenant_id' => $tenant->id, 'job_number' => $r['job_number']],
                [
                    'branch_id' => $branch1->id,
                    'customer_id' => $r['customer_id'],
                    'brand' => $r['brand'],
                    'model' => $r['model'],
                    'imei' => $r['imei'],
                    'device_condition' => $r['condition'],
                    'reported_problem' => $r['problem'],
                    'accessories_received' => $r['accessories'],
                    'warranty_status' => $r['warranty'],
                    'technician_id' => $tech->id,
                    'estimated_cost' => $r['est_cost'],
                    'final_cost' => $r['final_cost'],
                    'advance_paid' => $r['adv'],
                    'status' => $r['status'],
                    'received_at' => now()->subDays(rand(1, 4)),
                    'estimated_delivery_at' => now()->addDays(rand(1, 3)),
                    'created_by' => $admin->id,
                ]
            );

            // Add history
            $job->histories()->create([
                'status' => $r['status'],
                'notes' => $r['notes'],
                'changed_by' => $tech->id,
            ]);
        }

        // 6. Ensure Job 2 (and other jobs) have items for testing removal
        $job2 = RepairJob::withoutGlobalScope('tenant')->where('job_number', 'REP-' . date('Ymd') . '-002')->first();
        if ($job2 && $job2->items()->count() === 0) {
            $batterySpare = Product::withoutGlobalScope('tenant')->where('sku', 'SPARE-SAM-BAT5K')->first();
            $job2->items()->create([
                'type' => 'part',
                'description' => 'Samsung S22 Ultra OEM 5000mAh Battery Pack',
                'product_id' => $batterySpare ? $batterySpare->id : null,
                'quantity' => 1,
                'unit_price' => 2800.00,
                'total_amount' => 2800.00,
            ]);
            $job2->items()->create([
                'type' => 'labor',
                'description' => 'Rear Glass Separation & Waterproof Seal Adhesive Installation',
                'product_id' => null,
                'quantity' => 1,
                'unit_price' => 1000.00,
                'total_amount' => 1000.00,
            ]);
            $job2->final_cost = 3800.00;
            $job2->save();
        }

        // Also add items to Job with ID 2 specifically (if different job number)
        $jobId2 = RepairJob::withoutGlobalScope('tenant')->find(2);
        if ($jobId2 && $jobId2->items()->count() <= 1) {
            $jobId2->items()->create([
                'type' => 'labor',
                'description' => 'Diagnostic Testing & Benchmark Stability Inspection',
                'product_id' => null,
                'quantity' => 1,
                'unit_price' => 450.00,
                'total_amount' => 450.00,
            ]);
            $jobId2->items()->create([
                'type' => 'part',
                'description' => 'Anti-Static Dust Shield & Internal Thermal Gasket',
                'product_id' => null,
                'quantity' => 1,
                'unit_price' => 350.00,
                'total_amount' => 350.00,
            ]);
            $jobId2->final_cost = $jobId2->items()->sum('total_amount');
            $jobId2->save();
        }

        // 7. Seed More Exchange Transactions & Used Devices
        $exchangeDevices = [
            [
                'customer_id' => $seededCustomers[0]->id,
                'brand' => 'Apple',
                'model' => 'iPhone 13 128GB Midnight',
                'imei1' => '359102938475819',
                'screen' => 'Minor hairline scratches, Oleophobic coating good',
                'battery' => 'Healthy (88% battery health)',
                'body' => 'Clean anodized aluminum bezels, no dents',
                'func' => 'Fully Functional, FaceID and TrueTone verified',
                'acc' => 'Original USB-C to Lightning braided cable',
                'est_val' => 29000,
                'app_val' => 28000,
                'grading' => 'A_flawless',
                'repair_cost' => 0,
                'resale_price' => 36500,
                'status' => 'in_stock',
                'notes' => 'Pristine condition traded-in iPhone 13, ready for display showcase',
            ],
            [
                'customer_id' => $seededCustomers[1]->id,
                'brand' => 'Samsung',
                'model' => 'Galaxy S21 FE 5G 128GB Olive',
                'imei1' => '861920485729104',
                'screen' => 'Flawless with tempered glass intact',
                'battery' => 'Good (85% capacity test)',
                'body' => 'Light pocket wear on polycarbonate back',
                'func' => 'Fully Functional, 120Hz display working',
                'acc' => 'Spigen rugged armor case',
                'est_val' => 14500,
                'app_val' => 14000,
                'grading' => 'B_good',
                'repair_cost' => 500,
                'resale_price' => 19999,
                'status' => 'in_stock',
                'notes' => 'Port cleaned and sanitization completed',
            ],
            [
                'customer_id' => $seededCustomers[2]->id,
                'brand' => 'OnePlus',
                'model' => 'OnePlus 10 Pro 5G 256GB Volcanic Black',
                'imei1' => '869201948572910',
                'screen' => 'Clean curved AMOLED panel, no burn-in',
                'battery' => 'Very Good (90%)',
                'body' => 'Matte frosted glass back in mint condition',
                'func' => 'Hasselblad camera and 80W SuperVOOC working',
                'acc' => 'Original 80W power adapter + red cable',
                'est_val' => 22000,
                'app_val' => 21000,
                'grading' => 'B_good',
                'repair_cost' => 0,
                'resale_price' => 27500,
                'status' => 'refurbished',
                'notes' => 'Refurbished quality-check certificate issued',
            ],
            [
                'customer_id' => $seededCustomers[3]->id,
                'brand' => 'Google Pixel',
                'model' => 'Pixel 6a 128GB Charcoal',
                'imei1' => '354920194858192',
                'screen' => 'Light surface scuffs, touch responsive',
                'battery' => '80% (Moderate use)',
                'body' => 'Slight corner nick near speaker grill',
                'func' => 'Titan M2 security and Tensor chip operational',
                'acc' => 'None',
                'est_val' => 10000,
                'app_val' => 9500,
                'grading' => 'C_fair',
                'repair_cost' => 800,
                'resale_price' => 14500,
                'status' => 'in_stock',
                'notes' => 'Budget phone trade-in, high resale demand',
            ],
            [
                'customer_id' => $seededCustomers[4]->id,
                'brand' => 'Vivo',
                'model' => 'Vivo X80 Pro 5G 256GB Cosmic Black',
                'imei1' => '864810294857291',
                'screen' => 'Pristine 2K E5 AMOLED, zero flaws',
                'battery' => 'Excellent (91%)',
                'body' => 'Ceramic finish camera island spotless',
                'func' => 'V1+ ISP imaging chip and gimbal OIS fully verified',
                'acc' => 'Original retail box with FlashCharge brick',
                'est_val' => 25000,
                'app_val' => 24000,
                'grading' => 'A_flawless',
                'repair_cost' => 0,
                'resale_price' => 31000,
                'status' => 'in_stock',
                'notes' => 'Top-tier camera smartphone pre-owned stock',
            ],
            [
                'customer_id' => $seededCustomers[0]->id,
                'brand' => 'Xiaomi',
                'model' => 'Xiaomi 11X 5G 128GB Celestial Silver',
                'imei1' => '868201948572910',
                'screen' => 'Minor scratch near top notch',
                'battery' => 'Good (83%)',
                'body' => 'Good condition with silver back',
                'func' => 'Snapdragon 870 benchmark test passed',
                'acc' => 'Transparent TPU bumper',
                'est_val' => 9000,
                'app_val' => 8500,
                'grading' => 'B_good',
                'repair_cost' => 300,
                'resale_price' => 12500,
                'status' => 'sold',
                'notes' => 'Sold to retail walk-in buyer on counter 2',
            ],
            [
                'customer_id' => $seededCustomers[1]->id,
                'brand' => 'Apple',
                'model' => 'iPhone 11 64GB Purple',
                'imei1' => '358920194857201',
                'screen' => 'Replaced with OEM grade TrueTone display',
                'battery' => '87% battery capacity',
                'body' => 'Small scuff on bottom aluminum rim',
                'func' => 'Face ID, audio, wireless charging all verified',
                'acc' => 'Silicone purple case',
                'est_val' => 12500,
                'app_val' => 11500,
                'grading' => 'C_fair',
                'repair_cost' => 1500,
                'resale_price' => 16999,
                'status' => 'refurbished',
                'notes' => 'Screen refurbished and sealed with waterproof gasket',
            ],
        ];

        foreach ($exchangeDevices as $ed) {
            $tx = ExchangeTransaction::withoutGlobalScope('tenant')->firstOrCreate(
                ['tenant_id' => $tenant->id, 'imei1' => $ed['imei1']],
                [
                    'branch_id' => $branch1->id,
                    'customer_id' => $ed['customer_id'],
                    'old_brand' => $ed['brand'],
                    'old_model' => $ed['model'],
                    'condition_screen' => $ed['screen'],
                    'condition_battery' => $ed['battery'],
                    'condition_body' => $ed['body'],
                    'functional_status' => $ed['func'],
                    'accessories_received' => $ed['acc'],
                    'estimated_value' => $ed['est_val'],
                    'approved_value' => $ed['app_val'],
                    'notes' => $ed['notes'],
                    'evaluated_by' => $admin->id,
                    'status' => 'approved',
                ]
            );

            UsedDevice::withoutGlobalScope('tenant')->updateOrCreate(
                ['tenant_id' => $tenant->id, 'imei1' => $ed['imei1']],
                [
                    'branch_id' => $branch1->id,
                    'exchange_id' => $tx->id,
                    'brand' => $ed['brand'],
                    'model' => $ed['model'],
                    'grading' => $ed['grading'],
                    'purchase_cost' => $ed['app_val'],
                    'repair_cost' => $ed['repair_cost'],
                    'expected_selling_price' => $ed['resale_price'],
                    'sold_price' => $ed['status'] === 'sold' ? $ed['resale_price'] : null,
                    'status' => $ed['status'],
                    'notes' => $ed['notes'],
                ]
            );
        }
    }
}
