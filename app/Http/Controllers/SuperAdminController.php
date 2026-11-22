<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('status', 'active')->count();
        $trialTenants = Tenant::where('status', 'trial')->count();
        $totalUsers = User::count();
        $totalPlatformRevenue = SubscriptionPayment::sum('amount');

        $recentTenants = Tenant::with(['plan', 'company'])->latest()->take(10)->get();
        $plans = SubscriptionPlan::withCount('tenants')->get();

        return view('superadmin.dashboard', compact(
            'totalTenants',
            'activeTenants',
            'trialTenants',
            'totalUsers',
            'totalPlatformRevenue',
            'recentTenants',
            'plans'
        ));
    }

    public function tenants()
    {
        $tenants = Tenant::with(['plan', 'company', 'branches'])->latest()->paginate(15);
        $plans = SubscriptionPlan::where('is_active', true)->get();

        return view('superadmin.tenants', compact('tenants', 'plans'));
    }

    public function updateTenantStatus(Request $request, int $id)
    {
        $tenant = Tenant::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:active,trial,suspended,cancelled',
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $tenant->update($validated);
        return back()->with('success', "Tenant {$tenant->name} updated successfully.");
    }

    public function plans()
    {
        $plans = SubscriptionPlan::withCount('tenants')->get();
        return view('superadmin.plans', compact('plans'));
    }

    public function storePlan(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:subscription_plans,slug',
            'price' => 'required|numeric|min:0',
            'billing_interval' => 'required|in:monthly,yearly',
            'branch_limit' => 'required|integer|min:1',
            'user_limit' => 'required|integer|min:1',
            'product_limit' => 'required|integer|min:1',
        ]);

        SubscriptionPlan::create($validated);
        return back()->with('success', 'New subscription plan created successfully.');
    }
}
