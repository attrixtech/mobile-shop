<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\CashRegister;
use App\Models\JournalEntry;
use App\Services\AccountingService;
use Illuminate\Http\Request;

class AccountingController extends Controller
{
    public function __construct(protected AccountingService $accountingService) {}

    public function index()
    {
        $tenantId = session('active_tenant_id', auth()->user()->tenant_id);
        $accounts = $this->accountingService->getOrCreateAccounts($tenantId);
        $allAccounts = Account::orderBy('code')->get();

        return view('accounting.index', compact('allAccounts'));
    }

    public function journalEntries()
    {
        $entries = JournalEntry::with(['lines.account', 'creator', 'branch'])->latest()->paginate(20);
        return view('accounting.journal', compact('entries'));
    }

    public function cashRegister(Request $request)
    {
        $branchId = session('active_branch_id', auth()->user()->branch_id);

        $currentRegister = CashRegister::where('branch_id', $branchId)
            ->where('status', 'open')
            ->latest()
            ->first();

        $historyRegisters = CashRegister::with(['user', 'branch'])
            ->where('branch_id', $branchId)
            ->latest()
            ->paginate(15);

        return view('accounting.cash_register', compact('currentRegister', 'historyRegisters'));
    }

    public function openRegister(Request $request)
    {
        $tenantId = session('active_tenant_id', auth()->user()->tenant_id);
        $branchId = session('active_branch_id', auth()->user()->branch_id);

        $validated = $request->validate([
            'opening_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        CashRegister::create([
            'tenant_id' => $tenantId,
            'branch_id' => $branchId,
            'user_id' => auth()->id(),
            'opened_at' => now(),
            'opening_balance' => $validated['opening_balance'],
            'system_expected_cash' => $validated['opening_balance'],
            'status' => 'open',
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Cash register opened successfully with starting float.');
    }

    public function closeRegister(Request $request, int $id)
    {
        $register = CashRegister::findOrFail($id);

        $validated = $request->validate([
            'closing_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $actualCash = (float) $validated['closing_balance'];
        $expectedCash = $register->opening_balance + $register->cash_sales - $register->cash_expenses;
        $discrepancy = $actualCash - $expectedCash;

        $register->update([
            'closed_at' => now(),
            'closing_balance' => $actualCash,
            'system_expected_cash' => $expectedCash,
            'discrepancy' => $discrepancy,
            'status' => 'closed',
            'notes' => $validated['notes'] ?? $register->notes,
        ]);

        return back()->with('success', 'Cash register closed successfully for this shift.');
    }
}
