@extends('layouts.app')

@section('title', 'Mobile Exchange & Used Phones - thrumble.mobile-shop')

@section('content')
<div class="space-y-4" x-data="{ tab: 'used_inventory', statusModal: false, selectedDevice: null }">
    <!-- Header -->
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">Smartphone Exchange & Pre-Owned Inventory</h1>
            <p class="text-xs text-slate-500">Device inspection, buyback valuations, condition grading, and resale inventory</p>
        </div>
        <a href="{{ route('exchange.create') }}" class="px-3 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-sm flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Inspect & Evaluate Old Phone</span>
        </a>
    </div>

    <!-- Tab Selector -->
    <div class="flex items-center gap-2 border-b border-slate-200 text-xs">
        <button
            @click="tab = 'used_inventory'"
            :class="tab === 'used_inventory' ? 'border-b-2 border-indigo-600 font-bold text-indigo-600' : 'text-slate-500 hover:text-slate-700'"
            class="pb-2 px-1 transition"
        >
            Pre-Owned Phone Inventory (Resale Stock)
        </button>
        <button
            @click="tab = 'evaluations'"
            :class="tab === 'evaluations' ? 'border-b-2 border-indigo-600 font-bold text-indigo-600' : 'text-slate-500 hover:text-slate-700'"
            class="pb-2 px-1 transition"
        >
            Customer Exchange Evaluations & Vouchers
        </button>
    </div>

    <!-- Tab 1: Used Phone Inventory -->
    <div x-show="tab === 'used_inventory'" class="space-y-3">
        <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden text-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left compact-table">
                    <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                        <tr>
                            <th>Pre-Owned Model & IMEI</th>
                            <th>Condition Grading</th>
                            <th>Trade-In Cost</th>
                            <th>Refurbish / Repair Cost</th>
                            <th>Expected Resale</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($usedDevices as $ud)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td>
                                    <div class="font-bold text-slate-900">{{ $ud->brand }} {{ $ud->model }}</div>
                                    <div class="text-[10px] text-slate-500 font-mono">IMEI: {{ $ud->imei1 }}</div>
                                </td>
                                <td>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase
                                        {{ str_contains($ud->grading, 'A_') ? 'bg-emerald-100 text-emerald-700' : '' }}
                                        {{ str_contains($ud->grading, 'B_') ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ str_contains($ud->grading, 'C_') ? 'bg-amber-100 text-amber-700' : '' }}
                                        {{ str_contains($ud->grading, 'defective') ? 'bg-rose-100 text-rose-700' : '' }}">
                                        {{ str_replace('_', ' ', $ud->grading) }}
                                    </span>
                                </td>
                                <td>₹{{ number_format($ud->purchase_cost, 2) }}</td>
                                <td>₹{{ number_format($ud->repair_cost, 2) }}</td>
                                <td class="font-bold text-emerald-600">₹{{ number_format($ud->expected_selling_price, 2) }}</td>
                                <td>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase
                                        {{ $ud->status === 'in_stock' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                        {{ str_replace('_', ' ', $ud->status) }}
                                    </span>
                                </td>
                                <td class="text-right space-x-1 whitespace-nowrap">
                                    <button
                                        type="button"
                                        @click="selectedDevice = {{ $ud }}; statusModal = true"
                                        class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium"
                                    >
                                        Update Resale
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-slate-400">No used devices currently in stock.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-2 border-t border-slate-200">
                {{ $usedDevices->links() }}
            </div>
        </div>
    </div>

    <!-- Tab 2: Exchange Evaluations -->
    <div x-show="tab === 'evaluations'" class="space-y-3">
        <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden text-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left compact-table">
                    <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                        <tr>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Exchanged Device & IMEI</th>
                            <th>Screen / Battery / Body</th>
                            <th>Approved Value</th>
                            <th>Status</th>
                            <th>Evaluator</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($exchanges as $ex)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td>{{ $ex->created_at->format('d M, Y') }}</td>
                                <td>
                                    <div class="font-bold text-slate-900">{{ $ex->customer?->name }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $ex->customer?->phone }}</div>
                                </td>
                                <td>
                                    <div class="font-bold text-slate-900">{{ $ex->old_brand }} {{ $ex->old_model }}</div>
                                    <div class="text-[10px] text-slate-500 font-mono">IMEI: {{ $ex->imei1 }}</div>
                                </td>
                                <td>
                                    <div class="text-[10px] text-slate-600">Screen: {{ $ex->condition_screen }}</div>
                                    <div class="text-[10px] text-slate-600">Battery: {{ $ex->condition_battery }}</div>
                                </td>
                                <td class="font-bold text-emerald-600">₹{{ number_format($ex->approved_value, 2) }}</td>
                                <td>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase
                                        {{ $ex->status === 'approved' ? 'bg-indigo-100 text-indigo-700' : '' }}
                                        {{ $ex->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $ex->status }}
                                    </span>
                                </td>
                                <td>{{ $ex->evaluator?->name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-slate-400">No exchange evaluations recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-2 border-t border-slate-200">
                {{ $exchanges->links() }}
            </div>
        </div>
    </div>

    <!-- Modal: Update Used Device Status & Resale Price -->
    <div x-show="statusModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-4 border border-slate-200 text-xs space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm">Update Used Phone Resale Status</h3>
                <button @click="statusModal = false" class="text-slate-400 hover:text-slate-600 text-base">&times;</button>
            </div>

            <form :action="'{{ url('exchange/used-device') }}/' + (selectedDevice ? selectedDevice.id : '')" method="POST" class="space-y-3">
                @csrf
                <div>
                    <div class="text-slate-500 mb-0.5">Model: <strong class="text-slate-900" x-text="selectedDevice ? selectedDevice.brand + ' ' + selectedDevice.model : ''"></strong></div>
                    <div class="text-slate-500 mb-2">IMEI: <span class="font-mono" x-text="selectedDevice ? selectedDevice.imei1 : ''"></span></div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Status *</label>
                    <select name="status" required class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                        <option value="in_stock">In Stock (Ready for Resale)</option>
                        <option value="under_inspection">Under Inspection / Diagnosis</option>
                        <option value="refurbished">Refurbished / Polished</option>
                        <option value="sold">Sold</option>
                        <option value="scrapped">Scrapped / Salvaged for Spares</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Refurbish Cost (₹)</label>
                        <input type="number" step="0.01" name="repair_cost" placeholder="0.00" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Sold Price (₹)</label>
                        <input type="number" step="0.01" name="sold_price" placeholder="0.00" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Notes</label>
                    <input type="text" name="notes" placeholder="e.g. Battery swapped, ready in showcase" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>

                <div class="pt-2 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" @click="statusModal = false" class="px-3 py-1.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700">Cancel</button>
                    <button type="submit" class="px-4 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white font-bold">Save Updates</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
