<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ImeiMovement;
use App\Models\ImeiRecord;
use App\Models\Product;
use App\Services\InventoryService;
use Exception;
use Illuminate\Http\Request;

class ImeiController extends Controller
{
    public function __construct(protected InventoryService $inventoryService) {}

    public function index(Request $request)
    {
        $branchId = session('active_branch_id');
        $query = ImeiRecord::with(['product', 'variant', 'branch', 'supplier', 'sale', 'customer'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('imei1', 'like', "%{$s}%")
                  ->orWhere('imei2', 'like', "%{$s}%")
                  ->orWhere('serial_number', 'like', "%{$s}%")
                  ->orWhereHas('product', fn($pq) => $pq->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%"));
            });
        }

        $imeis = $query->paginate(20);
        $branches = Branch::where('is_active', true)->get();
        $products = Product::where('type', 'serialized')->where('is_active', true)->get();

        return view('imei.index', compact('imeis', 'branches', 'products'));
    }

    public function show(int $id)
    {
        $imei = ImeiRecord::with(['product', 'variant', 'branch', 'supplier', 'purchase', 'sale', 'customer', 'movements.fromBranch', 'movements.toBranch', 'movements.user'])->findOrFail($id);
        return view('imei.show', compact('imei'));
    }

    public function store(Request $request)
    {
        $tenantId = session('active_tenant_id', auth()->user()->tenant_id);
        $branchId = session('active_branch_id', auth()->user()->branch_id);

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'imei1' => 'required|string|min:14|max:20',
            'imei2' => 'nullable|string|min:14|max:20',
            'serial_number' => 'nullable|string|max:50',
            'purchase_cost' => 'nullable|numeric|min:0',
            'warranty_months' => 'nullable|integer|min:0',
        ]);

        try {
            $this->inventoryService->registerImei(
                $tenantId,
                $branchId,
                $validated['product_id'],
                $validated['variant_id'] ?? null,
                $validated['imei1'],
                $validated['imei2'] ?? null,
                $validated['serial_number'] ?? null,
                null,
                null,
                (float) ($validated['purchase_cost'] ?? 0),
                (int) ($validated['warranty_months'] ?? 12),
                auth()->id()
            );

            // Increment inventory stock balance for serialized item
            $this->inventoryService->adjustStock(
                $tenantId,
                $branchId,
                $validated['product_id'],
                $validated['variant_id'] ?? null,
                1,
                'adjustment',
                'ManualImeiRegistration',
                null,
                "Manually registered IMEI #{$validated['imei1']}",
                auth()->id()
            );

            return back()->with('success', "IMEI {$validated['imei1']} registered successfully into stock.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function updateStatus(Request $request, int $id)
    {
        $imei = ImeiRecord::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:in_stock,reserved,under_repair,damaged,written_off',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $imei->status;
        $newStatus = $validated['status'];

        $imei->status = $newStatus;
        $imei->save();

        ImeiMovement::create([
            'tenant_id' => $imei->tenant_id,
            'imei_record_id' => $imei->id,
            'from_branch_id' => $imei->branch_id,
            'to_branch_id' => $imei->branch_id,
            'action' => 'status_changed',
            'reference_type' => 'ManualUpdate',
            'reference_id' => null,
            'user_id' => auth()->id(),
            'notes' => "Status changed from '{$oldStatus}' to '{$newStatus}'. " . ($validated['notes'] ?? ''),
        ]);

        return back()->with('success', "IMEI status updated to '{$newStatus}'.");
    }
}
