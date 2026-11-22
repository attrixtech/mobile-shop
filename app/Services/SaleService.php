<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Customer;
use App\Models\ExchangeTransaction;
use App\Models\ImeiMovement;
use App\Models\ImeiRecord;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\StockBalance;
use Exception;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected AccountingService $accountingService
    ) {}

    /**
     * Process and complete a POS sale.
     *
     * @param array $data Structure:
     *  - tenant_id
     *  - branch_id
     *  - customer_id (optional)
     *  - customer_data (if new walk-in)
     *  - items: array of [product_id, variant_id, quantity, unit_price, discount, imeis: []]
     *  - payments: array of [payment_method, amount, reference_no]
     *  - exchange_id (optional)
     *  - discount_amount (optional)
     *  - notes (optional)
     *  - user_id (optional)
     */
    public function processSale(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $tenantId = $data['tenant_id'];
            $branchId = $data['branch_id'];
            $userId = $data['user_id'] ?? auth()->id();

            $branch = Branch::withoutGlobalScope('tenant')->findOrFail($branchId);
            $company = Company::withoutGlobalScope('tenant')->where('tenant_id', $tenantId)->first();
            $sellerStateCode = $company ? $company->state_code : '27';

            // 1. Resolve or create customer
            $customerId = $data['customer_id'] ?? null;
            $customerStateCode = $sellerStateCode;

            if ($customerId) {
                $customer = Customer::withoutGlobalScope('tenant')->find($customerId);
                if ($customer && $customer->state_code) {
                    $customerStateCode = $customer->state_code;
                }
            } elseif (!empty($data['customer_phone'])) {
                $customer = Customer::withoutGlobalScope('tenant')->firstOrCreate(
                    [
                        'tenant_id' => $tenantId,
                        'phone' => trim($data['customer_phone']),
                    ],
                    [
                        'name' => $data['customer_name'] ?? 'Walk-in Customer',
                        'email' => $data['customer_email'] ?? null,
                        'state' => $data['customer_state'] ?? 'Maharashtra',
                        'state_code' => $data['customer_state_code'] ?? $sellerStateCode,
                    ]
                );
                $customerId = $customer->id;
                $customerStateCode = $customer->state_code ?? $sellerStateCode;
            }

            // Generate unique invoice number
            $invoicePrefix = $company->invoice_prefix ?? 'INV';
            $invoiceNumber = $invoicePrefix . '-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            // 2. Pre-verify items & IMEIs
            $processedItems = [];
            $totalSubtotal = 0;
            $totalDiscount = (float) ($data['discount_amount'] ?? 0);
            $totalTax = 0;
            $totalCgst = 0;
            $totalSgst = 0;
            $totalIgst = 0;

            foreach ($data['items'] as $itemData) {
                $product = Product::withoutGlobalScope('tenant')->findOrFail($itemData['product_id']);
                $quantity = (int) $itemData['quantity'];
                $unitPrice = (float) $itemData['unit_price'];
                $itemDiscount = (float) ($itemData['discount'] ?? 0);
                $lineSubtotal = round(($unitPrice * $quantity) - $itemDiscount, 2);

                $imeis = $itemData['imeis'] ?? [];
                $variantId = !empty($itemData['variant_id']) ? (int) $itemData['variant_id'] : null;

                // If serialized product, ensure IMEIs are specified and available
                if ($product->isSerialized()) {
                    if (count($imeis) !== $quantity) {
                        throw new Exception("Product '{$product->name}' requires {$quantity} IMEI(s), but " . count($imeis) . " provided.");
                    }

                    foreach ($imeis as $imeiStr) {
                        $imeiRecord = ImeiRecord::withoutGlobalScope('tenant')
                            ->where('tenant_id', $tenantId)
                            ->where('branch_id', $branchId)
                            ->where('imei1', trim($imeiStr))
                            ->first();

                        if (!$imeiRecord) {
                            throw new Exception("IMEI '{$imeiStr}' was not found in branch stock.");
                        }

                        if ($imeiRecord->status !== 'in_stock') {
                            throw new Exception("IMEI '{$imeiStr}' cannot be sold because its status is '{$imeiRecord->status}'. Only 'in_stock' devices can be sold.");
                        }

                        if (!$variantId && $imeiRecord->variant_id) {
                            $variantId = $imeiRecord->variant_id;
                        }
                    }
                }

                if (!$variantId) {
                    $stockWithVariant = StockBalance::withoutGlobalScope('tenant')
                        ->where('tenant_id', $tenantId)
                        ->where('branch_id', $branchId)
                        ->where('product_id', $product->id)
                        ->where('quantity', '>=', $quantity)
                        ->first();
                    if ($stockWithVariant && $stockWithVariant->variant_id) {
                        $variantId = $stockWithVariant->variant_id;
                    }
                }

                // Calculate GST for this item
                $gst = GstService::calculate(
                    $lineSubtotal,
                    (float) $product->gst_rate,
                    $sellerStateCode,
                    $customerStateCode
                );

                $itemTax = $gst['total_tax'];
                $lineTotal = $lineSubtotal + $itemTax;

                $totalSubtotal += $lineSubtotal;
                $totalTax += $itemTax;
                $totalCgst += $gst['cgst'];
                $totalSgst += $gst['sgst'];
                $totalIgst += $gst['igst'];

                $processedItems[] = [
                    'product' => $product,
                    'variant_id' => $variantId,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'cost_price' => $product->purchase_price,
                    'discount_amount' => $itemDiscount,
                    'tax_rate' => $gst['rate'],
                    'tax_amount' => $itemTax,
                    'cgst' => $gst['cgst'],
                    'sgst' => $gst['sgst'],
                    'igst' => $gst['igst'],
                    'total_amount' => $lineTotal,
                    'imeis' => $imeis,
                    'warranty_months' => $product->warranty_months ?? 12,
                ];
            }

            // 3. Handle Exchange Allowance
            $exchangeAllowance = 0;
            $exchangeId = $data['exchange_id'] ?? null;
            if ($exchangeId) {
                $exchange = ExchangeTransaction::withoutGlobalScope('tenant')->findOrFail($exchangeId);
                $exchangeAllowance = (float) $exchange->approved_value;
            }

            $grandTotal = max(0, round(($totalSubtotal + $totalTax) - $totalDiscount - $exchangeAllowance, 2));

            // Calculate payments
            $paidAmount = 0;
            $payments = $data['payments'] ?? [];
            if (empty($payments) && isset($data['payment_method'])) {
                $payments = [[
                    'payment_method' => $data['payment_method'],
                    'amount' => $grandTotal,
                    'reference_no' => $data['payment_reference'] ?? null,
                ]];
            }

            foreach ($payments as $p) {
                $paidAmount += (float) ($p['amount'] ?? 0);
            }
            $paidAmount = round($paidAmount, 2);
            $dueAmount = max(0, round($grandTotal - $paidAmount, 2));

            $paymentStatus = 'paid';
            if ($dueAmount > 0 && $paidAmount > 0) {
                $paymentStatus = 'partial';
            } elseif ($dueAmount > 0 && $paidAmount == 0) {
                $paymentStatus = 'due';
            }

            $primaryMethod = count($payments) > 1 ? 'split' : ($payments[0]['payment_method'] ?? 'cash');

            // 4. Create Sale Record
            $sale = Sale::withoutGlobalScope('tenant')->create([
                'tenant_id' => $tenantId,
                'branch_id' => $branchId,
                'customer_id' => $customerId,
                'invoice_number' => $invoiceNumber,
                'sale_date' => now()->toDateString(),
                'sale_type' => $data['sale_type'] ?? 'retail',
                'status' => 'completed',
                'subtotal' => $totalSubtotal,
                'discount_amount' => $totalDiscount,
                'tax_amount' => $totalTax,
                'cgst' => $totalCgst,
                'sgst' => $totalSgst,
                'igst' => $totalIgst,
                'exchange_allowance' => $exchangeAllowance,
                'grand_total' => $grandTotal,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_status' => $paymentStatus,
                'payment_method' => $primaryMethod,
                'exchange_id' => $exchangeId,
                'notes' => $data['notes'] ?? null,
                'created_by' => $userId,
            ]);

            // 5. Create Sale Items, Deduct Stock & Update IMEIs
            foreach ($processedItems as $item) {
                $saleItem = SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product']->id,
                    'variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'cost_price' => $item['cost_price'],
                    'discount_amount' => $item['discount_amount'],
                    'tax_rate' => $item['tax_rate'],
                    'tax_amount' => $item['tax_amount'],
                    'cgst' => $item['cgst'],
                    'sgst' => $item['sgst'],
                    'igst' => $item['igst'],
                    'total_amount' => $item['total_amount'],
                    'imeis' => $item['imeis'],
                    'warranty_months' => $item['warranty_months'],
                ]);

                // Deduct stock balance
                $this->inventoryService->adjustStock(
                    $tenantId,
                    $branchId,
                    $item['product']->id,
                    $item['variant_id'],
                    -$item['quantity'],
                    'sale',
                    'Sale',
                    $sale->id,
                    "Sale Invoice #{$invoiceNumber}",
                    $userId
                );

                // Update IMEIs to 'sold'
                foreach ($item['imeis'] as $imeiStr) {
                    $imei = ImeiRecord::withoutGlobalScope('tenant')
                        ->where('tenant_id', $tenantId)
                        ->where('imei1', trim($imeiStr))
                        ->first();

                    if ($imei) {
                        $imei->status = 'sold';
                        $imei->sale_id = $sale->id;
                        $imei->customer_id = $customerId;
                        $imei->sold_at = now();
                        $imei->warranty_start = now()->toDateString();
                        $imei->warranty_end = now()->addMonths($item['warranty_months'])->toDateString();
                        $imei->save();

                        ImeiMovement::withoutGlobalScope('tenant')->create([
                            'tenant_id' => $tenantId,
                            'imei_record_id' => $imei->id,
                            'from_branch_id' => $branchId,
                            'to_branch_id' => null,
                            'action' => 'sold',
                            'reference_type' => 'Sale',
                            'reference_id' => $sale->id,
                            'user_id' => $userId,
                            'notes' => "Sold to customer via Invoice #{$invoiceNumber}",
                        ]);
                    }
                }
            }

            // 6. Record Payment Entries
            foreach ($payments as $payment) {
                if (($payment['amount'] ?? 0) > 0) {
                    SalePayment::withoutGlobalScope('tenant')->create([
                        'tenant_id' => $tenantId,
                        'sale_id' => $sale->id,
                        'payment_date' => now()->toDateString(),
                        'amount' => (float) $payment['amount'],
                        'payment_method' => $payment['payment_method'],
                        'reference_no' => $payment['reference_no'] ?? null,
                        'notes' => $payment['notes'] ?? null,
                        'user_id' => $userId,
                    ]);
                }
            }

            // 7. Update Customer Loyalty & Balance
            if ($customerId) {
                $cust = Customer::withoutGlobalScope('tenant')->find($customerId);
                if ($cust) {
                    $earnedPoints = (int) floor($grandTotal / 100);
                    if ($earnedPoints > 0) {
                        $cust->loyalty_points += $earnedPoints;
                        $cust->loyaltyTransactions()->create([
                            'tenant_id' => $tenantId,
                            'points' => $earnedPoints,
                            'type' => 'earned',
                            'reference_type' => 'Sale',
                            'reference_id' => $sale->id,
                            'notes' => "Points earned on Sale #{$invoiceNumber}",
                        ]);
                    }
                    if ($dueAmount > 0) {
                        $cust->current_balance += $dueAmount;
                    }
                    $cust->save();
                }
            }

            // 8. If exchange attached, mark exchange transaction completed
            if ($exchangeId && isset($exchange)) {
                $exchange->status = 'completed';
                $exchange->sale_id = $sale->id;
                $exchange->save();
            }

            // 9. Post double-entry bookkeeping journal entries
            $this->accountingService->recordSale($sale);

            return $sale;
        });
    }
}
