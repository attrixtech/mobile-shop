<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Product;
use App\Models\RepairJob;
use App\Models\RepairStatusHistory;
use App\Models\Tenant;
use App\Models\User;
use App\Services\InventoryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RepairJobController extends Controller
{
    public function __construct(protected InventoryService $inventoryService) {}

    public function index(Request $request)
    {
        $branchId = session('active_branch_id');
        $query = RepairJob::with(['customer', 'technician', 'branch'])->latest();

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('job_number', 'like', "%{$s}%")
                  ->orWhere('imei', 'like', "%{$s}%")
                  ->orWhere('brand', 'like', "%{$s}%")
                  ->orWhere('model', 'like', "%{$s}%")
                  ->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%"));
            });
        }

        $jobs = $query->paginate(15);
        $technicians = User::where('role', 'repair_technician')->where('is_active', true)->get();

        return view('repairs.index', compact('jobs', 'technicians'));
    }

    public function create()
    {
        $customers = Customer::latest()->get();
        $technicians = User::whereIn('role', ['repair_technician', 'store_manager'])->where('is_active', true)->get();
        $branches = Branch::where('is_active', true)->get();
        $branchId = session('active_branch_id', auth()->user()?->branch_id) ?: $branches->first()?->id ?? 1;

        return view('repairs.create', compact('customers', 'technicians', 'branches', 'branchId'));
    }

    public function storeTechnician(Request $request)
    {
        $tenantId = session('active_tenant_id', auth()->user()?->tenant_id) ?? Tenant::first()?->id ?? 1;

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'branch_id' => 'nullable|exists:branches,id',
            'password' => 'nullable|string|min:6',
        ]);

        $email = !empty($validated['email']) 
            ? $validated['email'] 
            : 'tech.' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $validated['name'])) . rand(100, 999) . '@webotapp.com';

        if (User::where('email', $email)->exists()) {
            $email = 'tech.' . uniqid() . '@webotapp.com';
        }

        $branchId = $validated['branch_id'] ?? session('active_branch_id', auth()->user()?->branch_id) ?: Branch::first()?->id ?? 1;

        $technician = User::create([
            'tenant_id' => $tenantId,
            'name' => $validated['name'],
            'email' => $email,
            'phone' => $validated['phone'] ?? null,
            'role' => 'repair_technician',
            'branch_id' => $branchId,
            'password' => Hash::make($validated['password'] ?? 'password123'),
            'is_active' => true,
        ]);

        if ($request->expectsJson() || $request->isJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Service engineer registered successfully.',
                'technician' => [
                    'id' => $technician->id,
                    'name' => $technician->name,
                    'phone' => $technician->phone,
                    'email' => $technician->email,
                    'role' => 'repair_technician',
                ]
            ]);
        }

        return back()->with('success', "Service engineer {$technician->name} added successfully.");
    }

    public function store(Request $request)
    {
        $tenantId = session('active_tenant_id', auth()->user()->tenant_id);
        $branchId = session('active_branch_id', auth()->user()->branch_id);

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'brand' => 'required|string',
            'model' => 'required|string',
            'imei' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'device_condition' => 'nullable|string',
            'reported_problem' => 'required|string',
            'accessories_received' => 'nullable|string',
            'warranty_status' => 'required|in:out_of_warranty,under_store_warranty,under_brand_warranty',
            'technician_id' => 'nullable|exists:users,id',
            'estimated_cost' => 'nullable|numeric|min:0',
            'advance_paid' => 'nullable|numeric|min:0',
            'estimated_delivery_at' => 'nullable|date',
        ]);

        $jobNumber = 'REP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        $job = RepairJob::create([
            'tenant_id' => $tenantId,
            'branch_id' => $branchId,
            'customer_id' => $validated['customer_id'],
            'job_number' => $jobNumber,
            'brand' => $validated['brand'],
            'model' => $validated['model'],
            'imei' => $validated['imei'] ?? null,
            'serial_number' => $validated['serial_number'] ?? null,
            'device_condition' => $validated['device_condition'] ?? null,
            'reported_problem' => $validated['reported_problem'],
            'accessories_received' => $validated['accessories_received'] ?? null,
            'warranty_status' => $validated['warranty_status'],
            'technician_id' => $validated['technician_id'] ?? null,
            'estimated_cost' => $validated['estimated_cost'] ?? 0,
            'final_cost' => $validated['estimated_cost'] ?? 0,
            'advance_paid' => $validated['advance_paid'] ?? 0,
            'status' => 'received',
            'estimated_delivery_at' => $validated['estimated_delivery_at'] ?? null,
            'created_by' => auth()->id(),
        ]);

        $job->histories()->create([
            'status' => 'received',
            'notes' => 'Job ticket generated at front desk reception',
            'changed_by' => auth()->id(),
        ]);

        return redirect()->route('repairs.show', $job->id)->with('success', "Repair Job Card #{$jobNumber} created successfully.");
    }

    public function show(int $id)
    {
        $job = RepairJob::with(['customer', 'technician', 'branch', 'items.product', 'histories.user'])->findOrFail($id);
        $technicians = User::whereIn('role', ['repair_technician', 'store_manager'])->where('is_active', true)->get();
        $spareParts = Product::whereIn('type', ['standard', 'service'])->where('is_active', true)->get();

        return view('repairs.show', compact('job', 'technicians', 'spareParts'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $job = RepairJob::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:received,under_inspection,estimate_pending,approval_pending,approved,parts_required,under_repair,testing,ready,delivered,cancelled',
            'notes' => 'nullable|string',
            'technician_id' => 'nullable|exists:users,id',
            'final_cost' => 'nullable|numeric|min:0',
        ]);

        $oldStatus = $job->status;
        $job->status = $validated['status'];

        if (!empty($validated['technician_id'])) {
            $job->technician_id = $validated['technician_id'];
        }

        if (isset($validated['final_cost'])) {
            $job->final_cost = $validated['final_cost'];
        }

        if ($validated['status'] === 'delivered') {
            $job->delivered_at = now();
        }

        $job->save();

        $job->histories()->create([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? "Status updated from {$oldStatus} to {$validated['status']}",
            'changed_by' => auth()->id(),
        ]);

        return back()->with('success', 'Repair status updated successfully.');
    }

    public function addItem(Request $request, int $id)
    {
        $job = RepairJob::findOrFail($id);

        $validated = $request->validate([
            'type' => 'required|in:part,labor',
            'description' => 'required|string',
            'product_id' => 'nullable|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $lineTotal = round($validated['quantity'] * $validated['unit_price'], 2);

        $job->items()->create([
            'type' => $validated['type'],
            'description' => $validated['description'],
            'product_id' => $validated['product_id'] ?? null,
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
            'total_amount' => $lineTotal,
        ]);

        // If part consumed, deduct from inventory
        if ($validated['type'] === 'part' && !empty($validated['product_id'])) {
            $this->inventoryService->adjustStock(
                $job->tenant_id,
                $job->branch_id,
                $validated['product_id'],
                null,
                -$validated['quantity'],
                'repair',
                'RepairJob',
                $job->id,
                "Spare part consumed for Repair #{$job->job_number}",
                auth()->id()
            );
        }

        // Recalculate job final cost
        $job->final_cost = $job->items()->sum('total_amount');
        $job->save();

        return back()->with('success', 'Repair item added successfully.');
    }

    public function removeItem(int $id, int $itemId)
    {
        $job = RepairJob::findOrFail($id);
        $item = $job->items()->findOrFail($itemId);

        // If part was consumed from inventory, restore it
        if ($item->type === 'part' && !empty($item->product_id)) {
            $this->inventoryService->adjustStock(
                $job->tenant_id,
                $job->branch_id,
                $item->product_id,
                null,
                $item->quantity,
                'return_in',
                'RepairJob',
                $job->id,
                "Restored spare part '{$item->description}' removed from Repair #{$job->job_number}",
                auth()->id()
            );
        }

        $desc = $item->description;
        $item->delete();

        // Recalculate job final cost
        $job->final_cost = $job->items()->sum('total_amount');
        $job->save();

        return back()->with('success', "Item '{$desc}' removed from job sheet.");
    }

    public function print(int $id)
    {
        $job = RepairJob::with(['customer', 'technician', 'branch', 'items'])->findOrFail($id);
        return view('repairs.print', compact('job'));
    }
}
