<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Customer;
use App\Models\ExchangeTransaction;
use App\Models\ImeiRecord;
use App\Models\Product;
use App\Services\SaleService;
use Exception;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function __construct(protected SaleService $saleService) {}

    public function index(Request $request)
    {
        $branchId = session('active_branch_id', auth()->user()?->branch_id) ?: Branch::first()?->id ?? 1;
        $tenantId = session('active_tenant_id', auth()->user()?->tenant_id) ?? Branch::find($branchId)?->tenant_id ?? 1;

        $categories = Category::where('is_active', true)->get();
        $customers = Customer::latest()->take(20)->get();

        // Pending approved exchanges for this customer / branch
        $approvedExchanges = ExchangeTransaction::where('branch_id', $branchId)
            ->where('status', 'approved')
            ->whereNull('sale_id')
            ->get();

        $currentBranch = Branch::find($branchId);

        return view('pos.index', compact('categories', 'customers', 'approvedExchanges', 'currentBranch'));
    }

    /**
     * API endpoint for fast product & barcode & IMEI search.
     */
    public function searchProducts(Request $request)
    {
        $branchId = session('active_branch_id', auth()->user()?->branch_id) ?: Branch::first()?->id ?? 1;
        $tenantId = session('active_tenant_id', auth()->user()?->tenant_id) ?? Branch::find($branchId)?->tenant_id ?? 1;
        $query = trim($request->input('q', ''));
        $categoryId = $request->input('category_id');

        // Check if query exactly matches an in-stock IMEI
        if (!empty($query)) {
            $directImei = ImeiRecord::where('branch_id', $branchId)
                ->where('status', 'in_stock')
                ->where(function ($q) use ($query) {
                    $q->where('imei1', $query)
                      ->orWhere('imei2', $query)
                      ->orWhere('serial_number', $query);
                })
                ->with(['product', 'variant'])
                ->first();

            if ($directImei && $directImei->product) {
                return response()->json([
                    'matched_imei' => true,
                    'imei' => $directImei->imei1,
                    'product' => [
                        'id' => $directImei->product->id,
                        'name' => $directImei->product->name,
                        'variant_id' => $directImei->variant_id,
                        'variant_name' => $directImei->variant?->name,
                        'price' => (float) ($directImei->variant?->selling_price ?: $directImei->product->selling_price),
                        'gst_rate' => (float) $directImei->product->gst_rate,
                        'is_serialized' => true,
                        'available_imeis' => [$directImei->imei1],
                    ],
                ]);
            }
        }

        $products = Product::with(['variants', 'brand', 'category'])
            ->where('is_active', true)
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('barcode', $query)
                  ->orWhere('sku', 'like', "%{$query}%")
                  ->orWhere('code', 'like', "%{$query}%");
            })
            ->take(30)
            ->get();

        $result = $products->map(function ($p) use ($branchId) {
            $availableImeis = [];
            if ($p->isSerialized()) {
                $availableImeis = ImeiRecord::where('branch_id', $branchId)
                    ->where('product_id', $p->id)
                    ->where('status', 'in_stock')
                    ->pluck('imei1')
                    ->toArray();
            }

            return [
                'id' => $p->id,
                'name' => $p->name,
                'code' => $p->code,
                'barcode' => $p->barcode,
                'type' => $p->type,
                'is_serialized' => $p->isSerialized(),
                'selling_price' => (float) $p->selling_price,
                'mrp' => (float) $p->mrp,
                'gst_rate' => (float) $p->gst_rate,
                'stock' => $p->totalStockForBranch($branchId),
                'available_imeis' => $availableImeis,
                'variants' => $p->variants->map(fn($v) => [
                    'id' => $v->id,
                    'name' => $v->name,
                    'selling_price' => (float) $v->selling_price,
                ]),
            ];
        });

        return response()->json($result);
    }

    /**
     * Submit POS sale checkout.
     */
    public function checkout(Request $request)
    {
        $branchId = session('active_branch_id', auth()->user()?->branch_id) ?: Branch::first()?->id ?? 1;
        $tenantId = session('active_tenant_id', auth()->user()?->tenant_id) ?? Branch::find($branchId)?->tenant_id ?? 1;

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.variant_id' => 'nullable|integer',
            'items.*.imeis' => 'nullable|array',
            'customer_id' => 'nullable|integer',
            'customer_name' => 'nullable|string',
            'customer_phone' => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0',
            'exchange_id' => 'nullable|integer',
            'payments' => 'nullable|array',
            'payment_method' => 'nullable|string',
            'payment_reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['tenant_id'] = $tenantId;
        $validated['branch_id'] = $branchId;
        $validated['user_id'] = auth()->id();

        try {
            $sale = $this->saleService->processSale($validated);

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sale completed successfully!',
                    'sale_id' => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                    'receipt_url' => route('sales.thermal', $sale->id),
                    'invoice_url' => route('sales.invoice', $sale->id),
                ]);
            }

            return redirect()->route('sales.thermal', $sale->id)->with('success', 'Sale created successfully!');
        } catch (Exception $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
