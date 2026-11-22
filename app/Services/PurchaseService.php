<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Company;
use App\Models\ImeiRecord;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchasePayment;
use App\Models\Supplier;
use Exception;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected AccountingService $accountingService
    ) {}

    public function createPurchase(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {
            $tenantId = $data['tenant_id'];
            $branchId = $data['branch_id'];
            $supplierId = $data['supplier_id'];
            $userId = $data['user_id'] ?? auth()->id();

            $branch = Branch::withoutGlobalScope('tenant')->findOrFail($branchId);
            $supplier = Supplier::withoutGlobalScope('tenant')->findOrFail($supplierId);
            $company = Company::withoutGlobalScope('tenant')->where('tenant_id', $tenantId)->first();
            $buyerStateCode = $company ? $company->state_code : '27';
            $supplierStateCode = $supplier->state_code ?? $buyerStateCode;

            $purchaseNumber = 'PO-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $totalSubtotal = 0;
            $totalTax = 0;
            $totalCgst = 0;
            $totalSgst = 0;
            $totalIgst = 0;
            $processedItems = [];

            foreach ($data['items'] as $itemData) {
                $product = Product::withoutGlobalScope('tenant')->findOrFail($itemData['product_id']);
                $quantity = (int) $itemData['quantity'];
                $unitCost = (float) $itemData['unit_cost'];
                $lineSubtotal = round($unitCost * $quantity, 2);

                $imeis = $itemData['imeis'] ?? [];
                if ($product->isSerialized() && !empty($imeis)) {
                    if (count($imeis) !== $quantity) {
                        throw new Exception("Product '{$product->name}' has {$quantity} units received, but " . count($imeis) . " IMEIs provided.");
                    }
                }

                $gst = GstService::calculate(
                    $lineSubtotal,
                    (float) ($itemData['tax_rate'] ?? $product->gst_rate),
                    $supplierStateCode,
                    $buyerStateCode
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
                    'variant_id' => $itemData['variant_id'] ?? null,
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'tax_rate' => $gst['rate'],
                    'tax_amount' => $itemTax,
                    'cgst' => $gst['cgst'],
                    'sgst' => $gst['sgst'],
                    'igst' => $gst['igst'],
                    'total_cost' => $lineTotal,
                    'imeis' => $imeis,
                ];
            }

            $discount = (float) ($data['discount'] ?? 0);
            $freight = (float) ($data['freight_charges'] ?? 0);
            $grandTotal = max(0, round(($totalSubtotal + $totalTax + $freight) - $discount, 2));

            $paidAmount = (float) ($data['paid_amount'] ?? 0);
            $paymentStatus = 'unpaid';
            if ($paidAmount >= $grandTotal) {
                $paymentStatus = 'paid';
            } elseif ($paidAmount > 0) {
                $paymentStatus = 'partial';
            }

            $purchase = Purchase::withoutGlobalScope('tenant')->create([
                'tenant_id' => $tenantId,
                'branch_id' => $branchId,
                'supplier_id' => $supplierId,
                'purchase_number' => $purchaseNumber,
                'supplier_invoice_no' => $data['supplier_invoice_no'] ?? null,
                'invoice_date' => $data['invoice_date'] ?? now()->toDateString(),
                'status' => 'received',
                'subtotal' => $totalSubtotal,
                'tax_amount' => $totalTax,
                'cgst' => $totalCgst,
                'sgst' => $totalSgst,
                'igst' => $totalIgst,
                'discount' => $discount,
                'freight_charges' => $freight,
                'grand_total' => $grandTotal,
                'paid_amount' => $paidAmount,
                'payment_status' => $paymentStatus,
                'notes' => $data['notes'] ?? null,
                'created_by' => $userId,
            ]);

            // Save items, register IMEIs & update stock
            foreach ($processedItems as $item) {
                $pItem = PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product']->id,
                    'variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                    'received_qty' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'tax_rate' => $item['tax_rate'],
                    'tax_amount' => $item['tax_amount'],
                    'cgst' => $item['cgst'],
                    'sgst' => $item['sgst'],
                    'igst' => $item['igst'],
                    'total_cost' => $item['total_cost'],
                    'imeis' => $item['imeis'],
                ]);

                // Update stock balance
                $this->inventoryService->adjustStock(
                    $tenantId,
                    $branchId,
                    $item['product']->id,
                    $item['variant_id'],
                    $item['quantity'],
                    'purchase',
                    'Purchase',
                    $purchase->id,
                    "Purchase Invoice #{$purchaseNumber}",
                    $userId
                );

                // Register received IMEIs
                if ($item['product']->isSerialized() && !empty($item['imeis'])) {
                    foreach ($item['imeis'] as $imeiStr) {
                        $this->inventoryService->registerImei(
                            $tenantId,
                            $branchId,
                            $item['product']->id,
                            $item['variant_id'],
                            trim($imeiStr),
                            null,
                            null,
                            $supplierId,
                            $purchase->id,
                            $item['unit_cost'],
                            $item['product']->warranty_months ?? 12,
                            $userId
                        );
                    }
                }
            }

            // Record initial payment if any
            if ($paidAmount > 0) {
                PurchasePayment::withoutGlobalScope('tenant')->create([
                    'tenant_id' => $tenantId,
                    'purchase_id' => $purchase->id,
                    'supplier_id' => $supplierId,
                    'payment_date' => now()->toDateString(),
                    'amount' => $paidAmount,
                    'payment_method' => $data['payment_method'] ?? 'bank_transfer',
                    'reference_no' => $data['payment_reference'] ?? null,
                    'notes' => "Initial payment on purchase",
                    'user_id' => $userId,
                ]);
            }

            // Update supplier balance
            $unpaidBalance = $grandTotal - $paidAmount;
            if ($unpaidBalance > 0) {
                $supplier->current_balance += $unpaidBalance;
                $supplier->save();
            }

            // Post double-entry accounting entry
            $accounts = $this->accountingService->getOrCreateAccounts($tenantId, $branchId);
            $lines = [];

            // Debit Inventory Asset
            $lines[] = [
                'account_id' => $accounts['1004']->id,
                'debit' => $totalSubtotal,
                'credit' => 0,
                'description' => "Inventory received for Purchase #{$purchaseNumber}",
            ];

            // Debit GST Input Tax (if any, balance to liabilities or tax accounts)
            if ($totalTax > 0) {
                $lines[] = [
                    'account_id' => $accounts['2002']->id,
                    'debit' => $totalTax,
                    'credit' => 0,
                    'description' => "Input GST on Purchase #{$purchaseNumber}",
                ];
            }

            // Credit Cash/Bank for paid amount
            if ($paidAmount > 0) {
                $paymentAcc = ($data['payment_method'] ?? '') === 'cash' ? $accounts['1001'] : $accounts['1002'];
                $lines[] = [
                    'account_id' => $paymentAcc->id,
                    'debit' => 0,
                    'credit' => $paidAmount,
                    'description' => "Payment made to supplier for Purchase #{$purchaseNumber}",
                ];
            }

            // Credit Accounts Payable for unpaid dues
            if ($unpaidBalance > 0) {
                $lines[] = [
                    'account_id' => $accounts['2001']->id,
                    'debit' => 0,
                    'credit' => $unpaidBalance,
                    'description' => "Supplier payable for Purchase #{$purchaseNumber}",
                ];
            }

            $this->accountingService->postJournalEntry(
                $tenantId,
                $branchId,
                'Purchase',
                $purchase->id,
                "Posting for Purchase #{$purchaseNumber}",
                $lines,
                $userId
            );

            return $purchase;
        });
    }
}
