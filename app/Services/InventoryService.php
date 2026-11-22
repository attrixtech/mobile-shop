<?php

namespace App\Services;

use App\Models\ImeiMovement;
use App\Models\ImeiRecord;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use Exception;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Increment or decrement stock balance safely and log movement.
     */
    public function adjustStock(
        int $tenantId,
        int $branchId,
        int $productId,
        ?int $variantId,
        int $quantity, // positive for in, negative for out
        string $movementType,
        ?string $refType = null,
        ?int $refId = null,
        ?string $notes = null,
        ?int $userId = null
    ): StockBalance {
        // If variantId is null on deduction, auto-resolve from variant balance that has stock
        if ($variantId === null && $quantity < 0) {
            $existingBalanceWithStock = StockBalance::withoutGlobalScope('tenant')
                ->where('tenant_id', $tenantId)
                ->where('branch_id', $branchId)
                ->where('product_id', $productId)
                ->where('quantity', '>=', abs($quantity))
                ->orderByDesc('quantity')
                ->first();

            if ($existingBalanceWithStock) {
                $variantId = $existingBalanceWithStock->variant_id;
                $balance = $existingBalanceWithStock;
            }
        }

        if (!isset($balance)) {
            $balance = StockBalance::withoutGlobalScope('tenant')->firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'branch_id' => $branchId,
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                ],
                [
                    'quantity' => 0,
                    'reserved_qty' => 0,
                ]
            );
        }

        $newQty = $balance->quantity + $quantity;
        if ($newQty < 0) {
            throw new Exception("Stock cannot be negative for product ID {$productId}. Current stock: {$balance->quantity}, requested deduction: " . abs($quantity));
        }

        $balance->quantity = $newQty;
        $balance->save();

        StockMovement::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenantId,
            'branch_id' => $branchId,
            'product_id' => $productId,
            'variant_id' => $variantId,
            'type' => $movementType,
            'quantity' => $quantity,
            'reference_type' => $refType,
            'reference_id' => $refId,
            'notes' => $notes,
            'user_id' => $userId ?? auth()->id(),
        ]);

        return $balance;
    }

    /**
     * Register a new or received IMEI.
     */
    public function registerImei(
        int $tenantId,
        int $branchId,
        int $productId,
        ?int $variantId,
        string $imei1,
        ?string $imei2 = null,
        ?string $serialNumber = null,
        ?int $supplierId = null,
        ?int $purchaseId = null,
        float $cost = 0,
        int $warrantyMonths = 12,
        ?int $userId = null
    ): ImeiRecord {
        $existing = ImeiRecord::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('imei1', trim($imei1))
            ->first();

        if ($existing) {
            throw new Exception("IMEI '{$imei1}' is already registered in the system (Status: {$existing->status}). Duplicate IMEIs are prohibited.");
        }

        $record = ImeiRecord::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenantId,
            'branch_id' => $branchId,
            'product_id' => $productId,
            'variant_id' => $variantId,
            'imei1' => trim($imei1),
            'imei2' => $imei2 ? trim($imei2) : null,
            'serial_number' => $serialNumber ? trim($serialNumber) : null,
            'supplier_id' => $supplierId,
            'purchase_id' => $purchaseId,
            'purchase_cost' => $cost,
            'status' => 'in_stock',
            'warranty_months' => $warrantyMonths,
            'warranty_start' => now()->toDateString(),
            'warranty_end' => now()->addMonths($warrantyMonths)->toDateString(),
        ]);

        ImeiMovement::withoutGlobalScope('tenant')->create([
            'tenant_id' => $tenantId,
            'imei_record_id' => $record->id,
            'from_branch_id' => null,
            'to_branch_id' => $branchId,
            'action' => 'purchase_received',
            'reference_type' => $purchaseId ? 'Purchase' : 'InitialStock',
            'reference_id' => $purchaseId,
            'user_id' => $userId ?? auth()->id(),
            'notes' => "Registered via purchase/inventory entry",
        ]);

        return $record;
    }

    /**
     * Transfer stock and optional IMEIs between branches.
     */
    public function executeTransfer(StockTransfer $transfer, ?int $userId = null): void
    {
        DB::transaction(function () use ($transfer, $userId) {
            foreach ($transfer->items as $item) {
                $variantId = $item->variant_id;
                if (!$variantId) {
                    $sourceBal = StockBalance::withoutGlobalScope('tenant')
                        ->where('tenant_id', $transfer->tenant_id)
                        ->where('branch_id', $transfer->from_branch_id)
                        ->where('product_id', $item->product_id)
                        ->where('quantity', '>=', $item->quantity)
                        ->first();
                    if ($sourceBal && $sourceBal->variant_id) {
                        $variantId = $sourceBal->variant_id;
                        $item->variant_id = $variantId;
                        $item->save();
                    }
                }

                // Deduct from source branch
                $this->adjustStock(
                    $transfer->tenant_id,
                    $transfer->from_branch_id,
                    $item->product_id,
                    $variantId,
                    -$item->quantity,
                    'transfer_out',
                    'StockTransfer',
                    $transfer->id,
                    "Transferred to Branch #{$transfer->to_branch_id}",
                    $userId
                );

                // Add to destination branch
                $this->adjustStock(
                    $transfer->tenant_id,
                    $transfer->to_branch_id,
                    $item->product_id,
                    $variantId,
                    $item->quantity,
                    'transfer_in',
                    'StockTransfer',
                    $transfer->id,
                    "Transferred from Branch #{$transfer->from_branch_id}",
                    $userId
                );

                // Transfer IMEI records if provided
                if (!empty($item->imeis) && is_array($item->imeis)) {
                    foreach ($item->imeis as $imeiStr) {
                        $imei = ImeiRecord::withoutGlobalScope('tenant')
                            ->where('tenant_id', $transfer->tenant_id)
                            ->where('branch_id', $transfer->from_branch_id)
                            ->where('imei1', trim($imeiStr))
                            ->first();

                        if ($imei) {
                            $imei->branch_id = $transfer->to_branch_id;
                            $imei->save();

                            ImeiMovement::withoutGlobalScope('tenant')->create([
                                'tenant_id' => $transfer->tenant_id,
                                'imei_record_id' => $imei->id,
                                'from_branch_id' => $transfer->from_branch_id,
                                'to_branch_id' => $transfer->to_branch_id,
                                'action' => 'transfer',
                                'reference_type' => 'StockTransfer',
                                'reference_id' => $transfer->id,
                                'user_id' => $userId ?? auth()->id(),
                                'notes' => "Inter-branch stock transfer #{$transfer->transfer_number}",
                            ]);
                        }
                    }
                }
            }

            $transfer->status = 'received';
            $transfer->received_at = now();
            $transfer->save();
        });
    }
}
