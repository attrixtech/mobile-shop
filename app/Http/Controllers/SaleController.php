<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Company;
use App\Models\ImeiMovement;
use App\Models\ImeiRecord;
use App\Models\Sale;
use App\Models\SalesReturn;
use App\Services\InventoryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function __construct(protected InventoryService $inventoryService) {}

    public function index(Request $request)
    {
        $branchId = session('active_branch_id');
        $query = Sale::with(['customer', 'branch', 'creator'])->latest();

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('invoice_number', 'like', "%{$s}%")
                  ->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('sale_date', $request->date);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $sales = $query->paginate(15);
        $branches = Branch::where('is_active', true)->get();

        return view('sales.index', compact('sales', 'branches'));
    }

    public function show(int $id)
    {
        $sale = Sale::with(['customer', 'branch', 'items.product', 'items.variant', 'payments', 'exchange', 'creator'])->findOrFail($id);
        $company = Company::where('tenant_id', $sale->tenant_id)->first();

        return view('sales.show', compact('sale', 'company'));
    }

    public function thermal(int $id)
    {
        $sale = Sale::with(['customer', 'branch', 'items.product', 'items.variant', 'payments', 'creator'])->findOrFail($id);
        $company = Company::where('tenant_id', $sale->tenant_id)->first();

        return view('sales.thermal', compact('sale', 'company'));
    }

    public function invoice(int $id)
    {
        $sale = Sale::with(['customer', 'branch', 'items.product', 'items.variant', 'payments', 'exchange', 'creator'])->findOrFail($id);
        $company = Company::where('tenant_id', $sale->tenant_id)->first();

        return view('sales.invoice', compact('sale', 'company'));
    }

    public function createReturn(int $id)
    {
        $sale = Sale::with(['customer', 'branch', 'items.product', 'items.variant'])->findOrFail($id);
        return view('sales.return', compact('sale'));
    }

    public function processReturn(Request $request, int $id)
    {
        $sale = Sale::with('items')->findOrFail($id);

        $validated = $request->validate([
            'reason' => 'required|string',
            'return_items' => 'required|array|min:1',
            'return_items.*.product_id' => 'required|integer',
            'return_items.*.quantity' => 'required|integer|min:1',
            'return_items.*.unit_price' => 'required|numeric',
            'return_items.*.refund_amount' => 'required|numeric',
            'return_items.*.imeis' => 'nullable|array',
            'return_items.*.restore_to_inventory' => 'nullable|boolean',
        ]);

        try {
            DB::transaction(function () use ($sale, $validated) {
                $returnNumber = 'RET-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
                $totalRefund = 0;

                foreach ($validated['return_items'] as $item) {
                    $totalRefund += (float) $item['refund_amount'];
                }

                $salesReturn = SalesReturn::create([
                    'tenant_id' => $sale->tenant_id,
                    'branch_id' => $sale->branch_id,
                    'sale_id' => $sale->id,
                    'return_number' => $returnNumber,
                    'return_date' => now()->toDateString(),
                    'total_refund_amount' => $totalRefund,
                    'reason' => $validated['reason'],
                    'inspected_by' => auth()->id(),
                    'status' => 'approved_stock_restored',
                ]);

                foreach ($validated['return_items'] as $itemData) {
                    $salesReturn->items()->create([
                        'product_id' => $itemData['product_id'],
                        'variant_id' => $itemData['variant_id'] ?? null,
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $itemData['unit_price'],
                        'refund_amount' => $itemData['refund_amount'],
                        'imeis' => $itemData['imeis'] ?? [],
                        'condition_status' => $itemData['condition_status'] ?? 'good',
                    ]);

                    // If restore to inventory is selected
                    if (!empty($itemData['restore_to_inventory'])) {
                        $this->inventoryService->adjustStock(
                            $sale->tenant_id,
                            $sale->branch_id,
                            $itemData['product_id'],
                            $itemData['variant_id'] ?? null,
                            (int) $itemData['quantity'],
                            'return_in',
                            'SalesReturn',
                            $salesReturn->id,
                            "Sales Return #{$returnNumber}",
                            auth()->id()
                        );

                        // If serialized item, restore IMEI status to in_stock
                        if (!empty($itemData['imeis'])) {
                            foreach ($itemData['imeis'] as $imeiStr) {
                                $imei = ImeiRecord::where('tenant_id', $sale->tenant_id)
                                    ->where('imei1', trim($imeiStr))
                                    ->first();

                                if ($imei) {
                                    $imei->status = 'in_stock';
                                    $imei->save();

                                    ImeiMovement::create([
                                        'tenant_id' => $sale->tenant_id,
                                        'imei_record_id' => $imei->id,
                                        'from_branch_id' => null,
                                        'to_branch_id' => $sale->branch_id,
                                        'action' => 'returned',
                                        'reference_type' => 'SalesReturn',
                                        'reference_id' => $salesReturn->id,
                                        'user_id' => auth()->id(),
                                        'notes' => "Returned by customer via #{$returnNumber}",
                                    ]);
                                }
                            }
                        }
                    }
                }

                $sale->status = 'returned';
                $sale->save();
            });

            return redirect()->route('sales.show', $sale->id)->with('success', 'Sales return recorded successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
