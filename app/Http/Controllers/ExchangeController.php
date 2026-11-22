<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\ExchangeTransaction;
use App\Models\UsedDevice;
use Illuminate\Http\Request;

class ExchangeController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('active_branch_id');

        $exchanges = ExchangeTransaction::with(['customer', 'branch', 'evaluator', 'sale'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->latest()
            ->paginate(15);

        $usedDevices = UsedDevice::with(['exchange', 'branch'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->latest()
            ->paginate(15);

        return view('exchange.index', compact('exchanges', 'usedDevices'));
    }

    public function create()
    {
        $customers = Customer::latest()->get();
        $branches = Branch::where('is_active', true)->get();
        $branchId = session('active_branch_id');

        return view('exchange.create', compact('customers', 'branches', 'branchId'));
    }

    public function store(Request $request)
    {
        $tenantId = session('active_tenant_id', auth()->user()->tenant_id);
        $branchId = $request->input('branch_id') ?: session('active_branch_id', auth()->user()->branch_id);

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'old_brand' => 'required|string',
            'old_model' => 'required|string',
            'imei1' => 'required|string|min:14|max:20',
            'imei2' => 'nullable|string|min:14|max:20',
            'serial_number' => 'nullable|string',
            'condition_screen' => 'required|string',
            'condition_battery' => 'required|string',
            'condition_body' => 'required|string',
            'functional_status' => 'required|string',
            'accessories_received' => 'nullable|string',
            'estimated_value' => 'required|numeric|min:0',
            'approved_value' => 'required|numeric|min:0',
            'grading' => 'required|in:A_flawless,B_good,C_fair,defective',
            'expected_selling_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $exchange = ExchangeTransaction::create([
            'tenant_id' => $tenantId,
            'branch_id' => $branchId,
            'customer_id' => $validated['customer_id'],
            'old_brand' => $validated['old_brand'],
            'old_model' => $validated['old_model'],
            'imei1' => trim($validated['imei1']),
            'imei2' => !empty($validated['imei2']) ? trim($validated['imei2']) : null,
            'serial_number' => $validated['serial_number'] ?? null,
            'condition_screen' => $validated['condition_screen'],
            'condition_battery' => $validated['condition_battery'],
            'condition_body' => $validated['condition_body'],
            'functional_status' => $validated['functional_status'],
            'accessories_received' => $validated['accessories_received'] ?? null,
            'estimated_value' => $validated['estimated_value'],
            'approved_value' => $validated['approved_value'],
            'notes' => $validated['notes'] ?? null,
            'evaluated_by' => auth()->id(),
            'status' => 'approved',
        ]);

        // Create used device inventory entry
        UsedDevice::create([
            'tenant_id' => $tenantId,
            'branch_id' => $branchId,
            'exchange_id' => $exchange->id,
            'brand' => $validated['old_brand'],
            'model' => $validated['old_model'],
            'imei1' => trim($validated['imei1']),
            'imei2' => !empty($validated['imei2']) ? trim($validated['imei2']) : null,
            'grading' => $validated['grading'],
            'purchase_cost' => $validated['approved_value'],
            'expected_selling_price' => $validated['expected_selling_price'],
            'status' => 'in_stock',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('exchange.index')->with('success', 'Mobile exchange evaluation approved and added to Used Phone inventory. Available for trade-in deduction at POS!');
    }

    public function updateUsedDevice(Request $request, int $id)
    {
        $device = UsedDevice::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:in_stock,under_inspection,refurbished,sold,scrapped',
            'sold_price' => 'nullable|numeric|min:0',
            'repair_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $device->update($validated);
        return back()->with('success', 'Used device status updated.');
    }
}
