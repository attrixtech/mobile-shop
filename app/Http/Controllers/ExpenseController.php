<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('active_branch_id');
        $query = Expense::with(['category', 'branch', 'user'])->latest();

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $expenses = $query->paginate(15);
        $categories = ExpenseCategory::where('is_active', true)->get();
        $branches = Branch::where('is_active', true)->get();

        return view('expenses.index', compact('expenses', 'categories', 'branches'));
    }

    public function store(Request $request)
    {
        $tenantId = session('active_tenant_id', auth()->user()->tenant_id);
        $branchId = $request->input('branch_id') ?: session('active_branch_id', auth()->user()->branch_id);

        $validated = $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:1',
            'expense_date' => 'required|date',
            'vendor_name' => 'nullable|string',
            'payment_method' => 'required|string',
            'reference_no' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        Expense::create([
            'tenant_id' => $tenantId,
            'branch_id' => $branchId,
            'category_id' => $validated['category_id'],
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'vendor_name' => $validated['vendor_name'] ?? null,
            'payment_method' => $validated['payment_method'],
            'reference_no' => $validated['reference_no'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'user_id' => auth()->id(),
            'status' => 'approved',
        ]);

        return back()->with('success', 'Expense recorded successfully.');
    }
}
