<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function company()
    {
        $tenantId = session('active_tenant_id', auth()->user()->tenant_id);
        $company = Company::where('tenant_id', $tenantId)->first();
        return view('settings.company', compact('company'));
    }

    public function updateCompany(Request $request)
    {
        $tenantId = session('active_tenant_id', auth()->user()->tenant_id);
        $company = Company::where('tenant_id', $tenantId)->first();

        $validated = $request->validate([
            'name' => 'required|string',
            'legal_name' => 'nullable|string',
            'gstin' => 'nullable|string|max:15',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'state_code' => 'nullable|string|max:2',
            'pincode' => 'nullable|string|max:10',
            'invoice_prefix' => 'required|string|max:10',
        ]);

        $company->update($validated);
        return back()->with('success', 'Company details updated successfully.');
    }

    public function branches()
    {
        $branches = Branch::withCount(['users', 'stockBalances', 'sales'])->get();
        return view('settings.branches', compact('branches'));
    }

    public function storeBranch(Request $request)
    {
        $tenantId = session('active_tenant_id', auth()->user()->tenant_id);
        $company = Company::where('tenant_id', $tenantId)->first();

        $validated = $request->validate([
            'name' => 'required|string',
            'code' => 'required|string|max:20',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'state_code' => 'nullable|string|max:2',
            'pincode' => 'nullable|string|max:10',
        ]);

        $validated['tenant_id'] = $tenantId;
        $validated['company_id'] = $company->id;
        $validated['is_main'] = false;
        $validated['is_active'] = true;

        Branch::create($validated);
        return back()->with('success', 'New branch added successfully.');
    }

    public function users()
    {
        $users = User::with('branch')->get();
        $branches = Branch::where('is_active', true)->get();

        return view('settings.users', compact('users', 'branches'));
    }

    public function storeUser(Request $request)
    {
        $tenantId = session('active_tenant_id', auth()->user()->tenant_id);

        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string',
            'role' => 'required|in:store_manager,cashier,sales_executive,inventory_manager,accountant,repair_technician',
            'branch_id' => 'nullable|exists:branches,id',
            'password' => 'required|string|min:6',
        ]);

        $validated['tenant_id'] = $tenantId;
        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = true;

        User::create($validated);
        return back()->with('success', 'Team member added successfully.');
    }
}
