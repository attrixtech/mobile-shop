@extends('layouts.app')

@section('title', "Repair Job {$job->job_number} - Webotapp Mobile Shop ERP")

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-base font-bold text-slate-900 tracking-tight font-mono">Job Card: {{ $job->job_number }}</h1>
                <span class="px-2 py-0.5 rounded text-[11px] font-semibold uppercase
                    {{ in_array($job->status, ['ready', 'delivered']) ? 'bg-emerald-100 text-emerald-700' : '' }}
                    {{ in_array($job->status, ['under_repair', 'testing']) ? 'bg-indigo-100 text-indigo-700' : '' }}
                    {{ in_array($job->status, ['received', 'under_inspection']) ? 'bg-amber-100 text-amber-700' : '' }}
                    {{ in_array($job->status, ['parts_required', 'cancelled']) ? 'bg-rose-100 text-rose-700' : '' }}">
                    {{ str_replace('_', ' ', $job->status) }}
                </span>
            </div>
            <p class="text-xs text-slate-500">Device: {{ $job->brand }} {{ $job->model }} | Customer: {{ $job->customer?->name }} ({{ $job->customer?->phone }})</p>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <a href="{{ route('repairs.print', $job->id) }}" target="_blank" class="px-3 py-1.5 rounded bg-slate-800 hover:bg-slate-700 text-white font-medium flex items-center gap-1.5 shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Print Job Ticket</span>
            </a>
            <a href="{{ route('repairs.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">&larr; Back</a>
        </div>
    </div>

    <!-- Job Info Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
        <div class="bg-white p-3 rounded border border-slate-200 shadow-sm space-y-1">
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Device Details</h3>
            <div class="font-bold text-slate-900 text-sm">{{ $job->brand }} {{ $job->model }}</div>
            <div class="text-slate-600 font-mono">IMEI: {{ $job->imei ?: 'Not provided' }}</div>
            <div class="text-slate-600">Physical: {{ $job->device_condition ?: 'Normal wear' }}</div>
            <div class="text-slate-600">Accessories: {{ $job->accessories_received ?: 'None' }}</div>
        </div>

        <div class="bg-white p-3 rounded border border-slate-200 shadow-sm space-y-1">
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Complaint & Diagnosis</h3>
            <div class="font-medium text-slate-900">{{ $job->reported_problem }}</div>
            <div class="text-slate-500 pt-1 border-t border-slate-100">
                Assigned Tech: <strong class="text-indigo-700">{{ $job->technician?->name ?: 'Unassigned' }}</strong>
            </div>
            <div class="text-slate-500">
                Est. Completion: {{ $job->estimated_delivery_at ? $job->estimated_delivery_at->format('d M, Y') : '-' }}
            </div>
        </div>

        <div class="bg-white p-3 rounded border border-slate-200 shadow-sm space-y-1">
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Billing & Advance</h3>
            <div>Estimated Cost: <strong class="text-slate-900">₹{{ number_format($job->estimated_cost, 2) }}</strong></div>
            <div>Final Bill: <strong class="text-indigo-600 text-sm">₹{{ number_format($job->final_cost, 2) }}</strong></div>
            <div>Advance Received: <strong class="text-emerald-600">₹{{ number_format($job->advance_paid, 2) }}</strong></div>
            @php $pendingBalance = max(0, $job->final_cost - $job->advance_paid); @endphp
            <div>Balance Due: <strong class="text-rose-600 font-bold">₹{{ number_format($pendingBalance, 2) }}</strong></div>
        </div>
    </div>

    <!-- Update Status & Pipeline Form -->
    <div class="bg-white p-3.5 rounded border border-slate-200 shadow-sm text-xs space-y-3">
        <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px] pb-1 border-b border-slate-100">
            Progress Pipeline Status & Technician Assignment
        </h3>

        <form action="{{ route('repairs.status', $job->id) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-4 gap-2.5 items-end">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Update Status *</label>
                <select name="status" class="w-full px-2 py-1 rounded bg-slate-50 border border-slate-300 text-xs">
                    <option value="received" {{ $job->status === 'received' ? 'selected' : '' }}>Received</option>
                    <option value="under_inspection" {{ $job->status === 'under_inspection' ? 'selected' : '' }}>Under Inspection</option>
                    <option value="estimate_pending" {{ $job->status === 'estimate_pending' ? 'selected' : '' }}>Estimate Pending</option>
                    <option value="approval_pending" {{ $job->status === 'approval_pending' ? 'selected' : '' }}>Customer Approval Pending</option>
                    <option value="approved" {{ $job->status === 'approved' ? 'selected' : '' }}>Approved by Customer</option>
                    <option value="parts_required" {{ $job->status === 'parts_required' ? 'selected' : '' }}>Parts Required / Waiting for Spare</option>
                    <option value="under_repair" {{ $job->status === 'under_repair' ? 'selected' : '' }}>Under Repair</option>
                    <option value="testing" {{ $job->status === 'testing' ? 'selected' : '' }}>Testing Quality Check</option>
                    <option value="ready" {{ $job->status === 'ready' ? 'selected' : '' }}>Ready for Customer Pickup</option>
                    <option value="delivered" {{ $job->status === 'delivered' ? 'selected' : '' }}>Delivered to Customer</option>
                    <option value="cancelled" {{ $job->status === 'cancelled' ? 'selected' : '' }}>Cancelled / Returned Unrepaired</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Technician</label>
                <select name="technician_id" class="w-full px-2 py-1 rounded bg-slate-50 border border-slate-300 text-xs">
                    @foreach($technicians as $t)
                        <option value="{{ $t->id }}" {{ $job->technician_id == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Status Notes</label>
                <input type="text" name="notes" placeholder="e.g. Display fitted, running touch screen test" class="w-full px-2 py-1 rounded bg-slate-50 border border-slate-300 text-xs">
            </div>

            <div>
                <button type="submit" class="w-full py-1.5 px-3 rounded bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-sm">
                    Update Pipeline
                </button>
            </div>
        </form>
    </div>

    <!-- Spare Parts & Labor Charges Table -->
    <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden text-xs">
        <div class="p-2.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Spare Parts & Labor Charges</h3>
            <span class="text-slate-500 font-medium">Final Cost: ₹{{ number_format($job->final_cost, 2) }}</span>
        </div>

        <table class="w-full text-left compact-table">
            <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                <tr>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th class="text-right">Total (₹)</th>
                    <th class="text-right w-20">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($job->items as $item)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td>
                            <span class="px-1.5 py-0.5 rounded text-[10px] uppercase font-bold
                                {{ $item->type === 'part' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ $item->type }}
                            </span>
                        </td>
                        <td class="font-bold text-slate-900">{{ $item->description }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>₹{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right font-bold text-slate-900">₹{{ number_format($item->total_amount, 2) }}</td>
                        <td class="text-right">
                            <form action="{{ route('repairs.remove-item', ['id' => $job->id, 'itemId' => $item->id]) }}" method="POST" onsubmit="return confirm('Remove \'{{ addslashes($item->description) }}\' from this repair job sheet?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2 py-0.5 rounded bg-rose-50 hover:bg-rose-100 text-rose-600 font-semibold text-[11px] inline-flex items-center gap-1 transition" title="Remove item">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    <span>Remove</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-slate-400">No parts or labor charges logged yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Add Spare / Labor Row Form -->
        <div class="p-3 bg-slate-50 border-t border-slate-200">
            <h4 class="font-bold text-slate-700 text-xs mb-2">+ Add Spare Part or Labor Charge</h4>
            <form action="{{ route('repairs.add-item', $job->id) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-5 gap-2 items-end">
                @csrf
                <div>
                    <label class="block text-[10px] text-slate-500 mb-0.5">Type</label>
                    <select name="type" class="w-full px-2 py-1 rounded bg-white border border-slate-300 text-xs">
                        <option value="labor">Service / Labor Charge</option>
                        <option value="part">Spare Part (from inventory)</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[10px] text-slate-500 mb-0.5">Description *</label>
                    <input type="text" name="description" required placeholder="e.g. Display Assembly, IC reballing" class="w-full px-2 py-1 rounded bg-white border border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block text-[10px] text-slate-500 mb-0.5">Charge (₹) *</label>
                    <input type="number" step="0.01" name="unit_price" required placeholder="0.00" class="w-full px-2 py-1 rounded bg-white border border-slate-300 text-xs">
                    <input type="hidden" name="quantity" value="1">
                </div>
                <div>
                    <button type="submit" class="w-full py-1 px-3 rounded bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs">
                        Add to Job Card
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Timeline Audit History -->
    <div class="bg-white p-3 rounded border border-slate-200 shadow-sm text-xs space-y-2">
        <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px] pb-1 border-b border-slate-100">Status Change Audit Log</h3>
        <div class="space-y-2">
            @foreach($job->histories as $hist)
                <div class="flex items-start justify-between p-2 rounded bg-slate-50 border border-slate-100">
                    <div>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-200 text-slate-800">
                            {{ str_replace('_', ' ', $hist->status) }}
                        </span>
                        <div class="text-slate-700 mt-1">{{ $hist->notes }}</div>
                    </div>
                    <div class="text-[10px] text-slate-400 text-right">
                        <div>{{ $hist->created_at->format('d M, Y H:i') }}</div>
                        <div>By: {{ $hist->user?->name ?: 'Staff' }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
