<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Exception;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function __construct(protected PurchaseService $purchaseService) {}

    public function index(Request $request)
    {
        $branchId = session('active_branch_id');
        $query = Purchase::with(['supplier', 'branch', 'creator'])->latest();

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $purchases = $query->paginate(15);
        $suppliers = Supplier::where('is_active', true)->get();

        return view('purchases.index', compact('purchases', 'suppliers'));
    }

    public function create()
    {
        $branchId = session('active_branch_id', auth()->user()->branch_id);
        $suppliers = Supplier::where('is_active', true)->get();
        $products = Product::with('variants')->where('is_active', true)->get();
        $branches = Branch::where('is_active', true)->get();

        return view('purchases.create', compact('suppliers', 'products', 'branches', 'branchId'));
    }

    public function store(Request $request)
    {
        $tenantId = session('active_tenant_id', auth()->user()->tenant_id);
        $branchId = $request->input('branch_id') ?: session('active_branch_id', auth()->user()->branch_id);

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'supplier_invoice_no' => 'nullable|string',
            'invoice_date' => 'required|date',
            'discount' => 'nullable|numeric|min:0',
            'freight_charges' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
            'payment_reference' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0',
            'items.*.imeis' => 'nullable|string', // newline or comma-separated IMEIs entered by user
        ]);

        // Parse IMEIs from text string to array
        foreach ($validated['items'] as $key => $item) {
            $imeis = [];
            if (!empty($item['imeis'])) {
                $rawList = preg_split('/[\r\n,]+/', $item['imeis']);
                foreach ($rawList as $im) {
                    $cleaned = trim($im);
                    if (!empty($cleaned)) {
                        $imeis[] = $cleaned;
                    }
                }
            }
            $validated['items'][$key]['imeis'] = $imeis;
        }

        $validated['tenant_id'] = $tenantId;
        $validated['branch_id'] = $branchId;
        $validated['user_id'] = auth()->id();

        try {
            $purchase = $this->purchaseService->createPurchase($validated);
            return redirect()->route('purchases.show', $purchase->id)->with('success', 'Purchase invoice & goods receipt created successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(int $id)
    {
        $purchase = Purchase::with(['supplier', 'branch', 'items.product', 'items.variant', 'payments', 'creator'])->findOrFail($id);
        return view('purchases.show', compact('purchase'));
    }

    public function recordPayment(Request $request, int $id)
    {
        $purchase = Purchase::with('supplier')->findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'payment_date' => 'required|date',
            'reference_no' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $amount = (float) $validated['amount'];

        PurchasePayment::create([
            'tenant_id' => $purchase->tenant_id,
            'purchase_id' => $purchase->id,
            'supplier_id' => $purchase->supplier_id,
            'payment_date' => $validated['payment_date'],
            'amount' => $amount,
            'payment_method' => $validated['payment_method'],
            'reference_no' => $validated['reference_no'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'user_id' => auth()->id(),
        ]);

        $purchase->paid_amount += $amount;
        if ($purchase->paid_amount >= $purchase->grand_total) {
            $purchase->payment_status = 'paid';
        } else {
            $purchase->payment_status = 'partial';
        }
        $purchase->save();

        if ($purchase->supplier) {
            $purchase->supplier->current_balance = max(0, $purchase->supplier->current_balance - $amount);
            $purchase->supplier->save();
        }

        return back()->with('success', 'Supplier payment recorded successfully.');
    }
}
