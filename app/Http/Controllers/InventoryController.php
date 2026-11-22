<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ImeiRecord;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Services\InventoryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function __construct(protected InventoryService $inventoryService) {}

    public function index(Request $request)
    {
        $branchId = session('active_branch_id');
        $query = StockBalance::with(['product.category', 'product.brand', 'variant', 'branch']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('product', function ($pq) use ($s) {
                $pq->where('name', 'like', "%{$s}%")
                   ->orWhere('code', 'like', "%{$s}%")
                   ->orWhere('sku', 'like', "%{$s}%")
                   ->orWhere('barcode', 'like', "%{$s}%");
            });
        }

        $balances = $query->paginate(20);
        $branches = Branch::where('is_active', true)->get();

        return view('inventory.index', compact('balances', 'branches', 'branchId'));
    }

    public function movements(Request $request)
    {
        $branchId = session('active_branch_id');
        $query = StockMovement::with(['product', 'variant', 'branch', 'user'])->latest();

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $movements = $query->paginate(25);
        return view('inventory.movements', compact('movements'));
    }

    public function transfers(Request $request)
    {
        $transfers = StockTransfer::with(['fromBranch', 'toBranch', 'items.product', 'items.variant', 'requestedBy', 'approvedBy'])
            ->latest()
            ->paginate(15);

        $branches = Branch::where('is_active', true)->get();

        // In-stock products by branch
        $stockBalances = StockBalance::where('quantity', '>', 0)
            ->with(['product:id,name,type,brand_id', 'product.brand:id,name', 'variant:id,name,sku'])
            ->get();

        // In-stock IMEIs by branch
        $imeiRecords = ImeiRecord::where('status', 'in_stock')
            ->get(['id', 'branch_id', 'product_id', 'variant_id', 'imei1', 'serial_number']);

        return view('inventory.transfers', compact('transfers', 'branches', 'stockBalances', 'imeiRecords'));
    }

    public function storeTransfer(Request $request)
    {
        $validated = $request->validate([
            'from_branch_id' => 'required|exists:branches,id',
            'to_branch_id' => 'required|exists:branches,id|different:from_branch_id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variant_id' => 'nullable',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.imeis' => 'nullable',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $fromBranch = Branch::findOrFail($validated['from_branch_id']);
                $toBranch = Branch::findOrFail($validated['to_branch_id']);
                $tenantId = session('active_tenant_id', auth()->user()?->tenant_id) ?? $fromBranch->tenant_id ?? 1;

                $transferNumber = 'TRF-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

                $transfer = StockTransfer::create([
                    'tenant_id' => $tenantId,
                    'transfer_number' => $transferNumber,
                    'from_branch_id' => $validated['from_branch_id'],
                    'to_branch_id' => $validated['to_branch_id'],
                    'status' => 'pending',
                    'notes' => $validated['notes'] ?? null,
                    'requested_by' => auth()->id(),
                ]);

                foreach ($validated['items'] as $item) {
                    $productId = (int) $item['product_id'];
                    $product = Product::findOrFail($productId);
                    $requestedQty = (int) $item['quantity'];

                    $imeis = [];
                    if (!empty($item['imeis'])) {
                        if (is_array($item['imeis'])) {
                            $imeis = array_values(array_filter(array_map('trim', $item['imeis'])));
                        } else {
                            $rawList = preg_split('/[\r\n,]+/', $item['imeis']);
                            foreach ($rawList as $im) {
                                $c = trim($im);
                                if (!empty($c)) {
                                    $imeis[] = $c;
                                }
                            }
                        }
                    }

                    // If IMEIs provided, lock requestedQty to IMEIs count
                    if (!empty($imeis)) {
                        $requestedQty = count($imeis);
                    }

                    // Resolve variant_id
                    $variantId = !empty($item['variant_id']) ? (int) $item['variant_id'] : null;

                    // If variant not specified, check if IMEI has variant
                    if (!$variantId && !empty($imeis)) {
                        $imeiObj = ImeiRecord::withoutGlobalScope('tenant')
                            ->where('tenant_id', $tenantId)
                            ->where('branch_id', $validated['from_branch_id'])
                            ->where('imei1', $imeis[0])
                            ->first();
                        if ($imeiObj && $imeiObj->variant_id) {
                            $variantId = $imeiObj->variant_id;
                        }
                    }

                    // If still no variant, check StockBalance at source branch with available stock
                    if (!$variantId) {
                        $balanceQuery = StockBalance::withoutGlobalScope('tenant')
                            ->where('tenant_id', $tenantId)
                            ->where('branch_id', $validated['from_branch_id'])
                            ->where('product_id', $productId);

                        $availableWithStock = (clone $balanceQuery)->where('quantity', '>=', $requestedQty)->first();
                        if ($availableWithStock && $availableWithStock->variant_id) {
                            $variantId = $availableWithStock->variant_id;
                        }
                    }

                    // Validate stock availability at from_branch
                    $sourceBalance = StockBalance::withoutGlobalScope('tenant')
                        ->where('tenant_id', $tenantId)
                        ->where('branch_id', $validated['from_branch_id'])
                        ->where('product_id', $productId)
                        ->when($variantId, fn($q) => $q->where('variant_id', $variantId), fn($q) => $q->whereNull('variant_id'))
                        ->first();

                    $currentQty = $sourceBalance ? $sourceBalance->quantity : 0;
                    if ($currentQty < $requestedQty) {
                        $vName = $variantId ? " (Variant ID {$variantId})" : "";
                        throw new Exception("Insufficient stock for {$product->name}{$vName} at {$fromBranch->name}. Available: {$currentQty}, Requested: {$requestedQty}.");
                    }

                    // If IMEIs provided, verify they belong to from_branch and are in_stock
                    if (!empty($imeis)) {
                        foreach ($imeis as $imStr) {
                            $imRec = ImeiRecord::withoutGlobalScope('tenant')
                                ->where('tenant_id', $tenantId)
                                ->where('branch_id', $validated['from_branch_id'])
                                ->where('imei1', $imStr)
                                ->first();

                            if (!$imRec) {
                                throw new Exception("IMEI {$imStr} was not found at {$fromBranch->name}.");
                            }
                            if ($imRec->status !== 'in_stock') {
                                throw new Exception("IMEI {$imStr} is not available for transfer (Status: {$imRec->status}).");
                            }
                        }
                    }

                    $transfer->items()->create([
                        'product_id' => $productId,
                        'variant_id' => $variantId,
                        'quantity' => $requestedQty,
                        'received_qty' => $requestedQty,
                        'imeis' => $imeis,
                    ]);
                }

                // Execute transfer directly
                $this->inventoryService->executeTransfer($transfer, auth()->id());
            });

            return redirect()->route('inventory.transfers')->with('success', 'Stock transfer completed and dispatched successfully between branches.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function adjustments()
    {
        $adjustments = StockAdjustment::with(['branch', 'user', 'items.product'])->latest()->paginate(15);
        $products = Product::where('is_active', true)->get();
        $branches = Branch::where('is_active', true)->get();

        return view('inventory.adjustments', compact('adjustments', 'products', 'branches'));
    }

    public function storeAdjustment(Request $request)
    {
        $branchId = $request->input('branch_id') ?: session('active_branch_id', auth()->user()?->branch_id) ?: Branch::first()?->id ?? 1;
        $branch = Branch::find($branchId);
        $tenantId = session('active_tenant_id', auth()->user()?->tenant_id) ?? $branch?->tenant_id ?? 1;

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:increase,decrease,damage,loss,write_off',
            'reason' => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($tenantId, $branchId, $validated) {
                $adjNum = 'ADJ-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
                $product = Product::findOrFail($validated['product_id']);

                $adj = StockAdjustment::create([
                    'tenant_id' => $tenantId,
                    'branch_id' => $branchId,
                    'adjustment_number' => $adjNum,
                    'type' => $validated['type'],
                    'reason' => $validated['reason'],
                    'status' => 'approved',
                    'user_id' => auth()->id(),
                ]);

                $adj->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $validated['quantity'],
                    'unit_cost' => $product->purchase_price,
                    'notes' => $validated['reason'],
                ]);

                $delta = $validated['type'] === 'increase' ? (int) $validated['quantity'] : -(int) $validated['quantity'];

                $this->inventoryService->adjustStock(
                    $tenantId,
                    $branchId,
                    $product->id,
                    null,
                    $delta,
                    'adjustment',
                    'StockAdjustment',
                    $adj->id,
                    "Stock adjustment {$adjNum}: {$validated['reason']}",
                    auth()->id()
                );
            });

            return back()->with('success', 'Stock adjustment recorded successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
