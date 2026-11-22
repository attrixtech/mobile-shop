<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ImeiRecord;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\RepairJob;
use App\Models\Sale;
use App\Models\StockBalance;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = session('active_tenant_id', auth()->user()->tenant_id);
        $branchId = session('active_branch_id', auth()->user()->branch_id);

        $today = now()->toDateString();

        // 1. Core Financial & Sales Metrics
        $salesQuery = Sale::query();
        $purchasesQuery = Purchase::query();
        $expensesQuery = Expense::query();
        $imeiQuery = ImeiRecord::query();

        if ($branchId) {
            $salesQuery->where('branch_id', $branchId);
            $purchasesQuery->where('branch_id', $branchId);
            $expensesQuery->where('branch_id', $branchId);
            $imeiQuery->where('branch_id', $branchId);
        }

        $todaySales = (clone $salesQuery)->whereDate('sale_date', $today)->sum('grand_total');
        $todayPurchases = (clone $purchasesQuery)->whereDate('invoice_date', $today)->sum('grand_total');
        $todayExpenses = (clone $expensesQuery)->whereDate('expense_date', $today)->sum('amount');
        
        $totalRevenue = (clone $salesQuery)->sum('grand_total');
        $totalCostOfGoodsSold = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.tenant_id', $tenantId)
            ->when($branchId, fn($q) => $q->where('sales.branch_id', $branchId))
            ->sum(DB::raw('sale_items.cost_price * sale_items.quantity'));

        $grossProfit = max(0, $totalRevenue - $totalCostOfGoodsSold);
        $totalExpensesAll = (clone $expensesQuery)->sum('amount');
        $netProfitEstimate = $grossProfit - $totalExpensesAll;

        $customerReceivables = Customer::sum('current_balance');
        $supplierPayables = Supplier::sum('current_balance');

        // 2. Inventory Metrics
        $stockBalances = StockBalance::with('product')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->get();

        $inventoryValuation = $stockBalances->sum(function ($sb) {
            return ($sb->product ? $sb->product->purchase_price : 0) * $sb->quantity;
        });

        $totalActiveProducts = Product::where('is_active', true)->count();
        $smartphonesInStock = (clone $imeiQuery)->where('status', 'in_stock')->count();
        
        $accessoriesInStock = DB::table('stock_balances')
            ->join('products', 'stock_balances.product_id', '=', 'products.id')
            ->where('products.tenant_id', $tenantId)
            ->where('products.type', 'standard')
            ->when($branchId, fn($q) => $q->where('stock_balances.branch_id', $branchId))
            ->sum('stock_balances.quantity');

        // 3. IMEI Status Breakdown
        $imeiStats = [
            'total' => (clone $imeiQuery)->count(),
            'in_stock' => (clone $imeiQuery)->where('status', 'in_stock')->count(),
            'sold' => (clone $imeiQuery)->where('status', 'sold')->count(),
            'reserved' => (clone $imeiQuery)->where('status', 'reserved')->count(),
            'under_repair' => (clone $imeiQuery)->where('status', 'under_repair')->count(),
            'damaged' => (clone $imeiQuery)->where('status', 'damaged')->count(),
        ];

        // 4. Alerts
        $lowStockProducts = Product::with(['category', 'brand'])
            ->where('is_active', true)
            ->where('type', '!=', 'service')
            ->get()
            ->filter(function ($p) use ($branchId) {
                return $p->totalStockForBranch($branchId) <= $p->min_stock;
            })
            ->take(6);

        $pendingRepairsCount = RepairJob::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereIn('status', ['received', 'under_inspection', 'parts_required', 'under_repair', 'testing'])
            ->count();

        $expiringWarrantiesCount = ImeiRecord::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('status', 'sold')
            ->whereBetween('warranty_end', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->count();

        // 5. Chart Data: Daily Sales for past 7 days
        $dailySales = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $label = now()->subDays($i)->format('d M');
            $val = (clone $salesQuery)->whereDate('sale_date', $date)->sum('grand_total');
            $dailySales['labels'][] = $label;
            $dailySales['data'][] = (float) $val;
        }

        // Brand-wise sales distribution
        $brandSales = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->where('sales.tenant_id', $tenantId)
            ->when($branchId, fn($q) => $q->where('sales.branch_id', $branchId))
            ->select(DB::raw('COALESCE(brands.name, "Generic") as brand_name'), DB::raw('SUM(sale_items.total_amount) as total'))
            ->groupBy('brands.name')
            ->pluck('total', 'brand_name')
            ->toArray();

        // Payment method distribution
        $paymentMethods = DB::table('sale_payments')
            ->where('tenant_id', $tenantId)
            ->select('payment_method', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->toArray();

        // Recent Sales
        $recentSales = (clone $salesQuery)->with(['customer', 'items.product'])->latest()->take(5)->get();

        // Available branches for switcher
        $branches = Branch::where('is_active', true)->get();
        $currentBranch = Branch::find($branchId);

        return view('dashboard.index', compact(
            'todaySales',
            'todayPurchases',
            'todayExpenses',
            'totalRevenue',
            'grossProfit',
            'netProfitEstimate',
            'customerReceivables',
            'supplierPayables',
            'inventoryValuation',
            'totalActiveProducts',
            'smartphonesInStock',
            'accessoriesInStock',
            'imeiStats',
            'lowStockProducts',
            'pendingRepairsCount',
            'expiringWarrantiesCount',
            'dailySales',
            'brandSales',
            'paymentMethods',
            'recentSales',
            'branches',
            'currentBranch'
        ));
    }
}
