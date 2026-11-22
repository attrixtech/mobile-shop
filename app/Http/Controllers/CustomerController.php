<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('gstin', 'like', "%{$s}%");
            });
        }

        $customers = $query->paginate(15);
        return view('customers.index', compact('customers'));
    }

    public function search(Request $request)
    {
        $tenantId = session('active_tenant_id', auth()->user()->tenant_id);
        $query = Customer::query();

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($b) use ($q) {
                $b->where('name', 'like', "%{$q}%")
                  ->orWhere('phone', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('gstin', 'like', "%{$q}%");
            });
        }

        $customers = $query->take(20)->get(['id', 'name', 'phone', 'email', 'city', 'customer_type']);
        return response()->json($customers);
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'gstin' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'state_code' => 'nullable|string|max:2',
            'pincode' => 'nullable|string|max:10',
            'customer_type' => 'nullable|in:retail,wholesale,corporate',
            'credit_limit' => 'nullable|numeric|min:0',
        ]);

        if (empty($validated['customer_type'])) {
            $validated['customer_type'] = 'retail';
        }

        $customer = Customer::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'customer' => $customer,
                'message' => 'Customer profile created successfully.',
            ]);
        }

        return redirect()->route('customers.index')->with('success', 'Customer profile created successfully.');
    }

    public function show(Customer $customer)
    {
        $customer->load([
            'sales.items.product',
            'repairJobs',
            'loyaltyTransactions',
            'imeiDevices.product',
        ]);

        return view('customers.show', compact('customer'));
    }
}
