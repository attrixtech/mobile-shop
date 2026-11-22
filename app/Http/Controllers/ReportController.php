<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use App\Models\Expense;
use App\Models\ImeiRecord;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $tenantId = session('active_tenant_id', auth()->user()->tenant_id);
        $branchId = $request->input('branch_id', session('active_branch_id'));

        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $salesQuery = Sale::where('tenant_id', $tenantId)
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId));

        $totalSalesCount = (clone $salesQuery)->count();
        $totalRevenue = (clone $salesQuery)->sum('grand_total');
        $totalTax = (clone $salesQuery)->sum('tax_amount');
        $totalDiscount = (clone $salesQuery)->sum('discount_amount');

        $salesList = (clone $salesQuery)->with(['customer', 'branch'])->latest()->paginate(20);

        // Product-wise sales breakdown
        $productSales = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.tenant_id', $tenantId)
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->when($branchId, fn($q) => $q->where('sales.branch_id', $branchId))
            ->select('products.name', 'products.type', DB::raw('SUM(sale_items.quantity) as total_qty'), DB::raw('SUM(sale_items.total_amount) as total_amount'))
            ->groupBy('products.id', 'products.name', 'products.type')
            ->orderByDesc('total_amount')
            ->get();

        $branches = Branch::where('is_active', true)->get();

        return view('reports.sales', compact(
            'salesList',
            'totalSalesCount',
            'totalRevenue',
            'totalTax',
            'totalDiscount',
            'productSales',
            'startDate',
            'endDate',
            'branches',
            'branchId'
        ));
    }

    public function inventory(Request $request)
    {
        $tenantId = session('active_tenant_id', auth()->user()->tenant_id);
        $branchId = $request->input('branch_id', session('active_branch_id'));

        $balances = StockBalance::with(['product.category', 'product.brand', 'variant', 'branch'])
            ->where('tenant_id', $tenantId)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->paginate(25);

        $totalUnits = StockBalance::where('tenant_id', $tenantId)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('quantity');

        $valuation = StockBalance::with('product')
            ->where('tenant_id', $tenantId)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->get()
            ->sum(fn($b) => ($b->product ? $b->product->purchase_price : 0) * $b->quantity);

        $branches = Branch::where('is_active', true)->get();

        return view('reports.inventory', compact('balances', 'totalUnits', 'valuation', 'branches', 'branchId'));
    }

    public function profitability(Request $request)
    {
        $tenantId = session('active_tenant_id', auth()->user()->tenant_id);
        $branchId = $request->input('branch_id', session('active_branch_id'));

        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $revenue = Sale::where('tenant_id', $tenantId)
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('grand_total');

        $cogs = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.tenant_id', $tenantId)
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->when($branchId, fn($q) => $q->where('sales.branch_id', $branchId))
            ->sum(DB::raw('sale_items.cost_price * sale_items.quantity'));

        $expenses = Expense::where('tenant_id', $tenantId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('amount');

        $grossProfit = max(0, $revenue - $cogs);
        $netProfit = $grossProfit - $expenses;
        $grossMarginPercent = $revenue > 0 ? round(($grossProfit / $revenue) * 100, 2) : 0;
        $netMarginPercent = $revenue > 0 ? round(($netProfit / $revenue) * 100, 2) : 0;

        $branches = Branch::where('is_active', true)->get();

        return view('reports.profitability', compact(
            'revenue',
            'cogs',
            'expenses',
            'grossProfit',
            'netProfit',
            'grossMarginPercent',
            'netMarginPercent',
            'startDate',
            'endDate',
            'branches',
            'branchId'
        ));
    }

    public function gst(Request $request)
    {
        $tenantId = session('active_tenant_id', auth()->user()->tenant_id);
        $branchId = $request->input('branch_id', session('active_branch_id'));

        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $sales = Sale::with('customer')
            ->where('tenant_id', $tenantId)
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->get();

        $taxableTotal = $sales->sum('subtotal') - $sales->sum('discount_amount');
        $cgstTotal = $sales->sum('cgst');
        $sgstTotal = $sales->sum('sgst');
        $igstTotal = $sales->sum('igst');
        $totalTax = $cgstTotal + $sgstTotal + $igstTotal;
        $invoiceTotal = $sales->sum('grand_total');

        // HSN Summary
        $hsnSummary = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.tenant_id', $tenantId)
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->when($branchId, fn($q) => $q->where('sales.branch_id', $branchId))
            ->select(
                'products.hsn_code',
                DB::raw('SUM(sale_items.quantity) as total_qty'),
                DB::raw('SUM(sale_items.total_amount - sale_items.tax_amount) as taxable_value'),
                DB::raw('SUM(sale_items.cgst) as total_cgst'),
                DB::raw('SUM(sale_items.sgst) as total_sgst'),
                DB::raw('SUM(sale_items.igst) as total_igst'),
                DB::raw('SUM(sale_items.total_amount) as total_value')
            )
            ->groupBy('products.hsn_code')
            ->get();

        $company = Company::where('tenant_id', $tenantId)->first();
        $branches = Branch::where('is_active', true)->get();

        return view('reports.gst', compact(
            'sales',
            'taxableTotal',
            'cgstTotal',
            'sgstTotal',
            'igstTotal',
            'totalTax',
            'invoiceTotal',
            'hsnSummary',
            'company',
            'startDate',
            'endDate',
            'branches',
            'branchId'
        ));
    }
}
