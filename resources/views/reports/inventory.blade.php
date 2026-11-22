@extends('layouts.app')

@section('title', 'Inventory Stock Valuation Report')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Inventory Stock Valuation</h1>
            <p class="text-xs text-slate-500">Live warehouse stock balance, unit quantities, and purchase cost valuation</p>
        </div>
        <form method="GET" action="{{ route('reports.inventory') }}" class="flex items-center gap-2">
            <select name="branch_id" class="form-input text-xs py-1.5 w-auto" onchange="this.form.submit()">
                <option value="">All Branches Stock</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
        <div class="card p-3 bg-white">
            <span class="text-xs text-slate-500 font-medium">Total Units in Stock</span>
            <div class="text-lg font-bold text-indigo-600 mt-1">{{ number_format($totalUnits) }} units</div>
            <span class="text-[10px] text-slate-400">Across all catalog items</span>
        </div>
        <div class="card p-3 bg-white">
            <span class="text-xs text-slate-500 font-medium">Total Inventory Valuation</span>
            <div class="text-lg font-bold text-emerald-600 mt-1">₹{{ number_format($valuation, 2) }}</div>
            <span class="text-[10px] text-emerald-700">Calculated at purchase cost</span>
        </div>
        <div class="card p-3 bg-white">
            <span class="text-xs text-slate-500 font-medium">Active Branches Audited</span>
            <div class="text-lg font-bold text-slate-800 mt-1">{{ $branches->count() }} locations</div>
            <span class="text-[10px] text-slate-400">Real-time synced multi-branch</span>
        </div>
    </div>

    <!-- Stock Balances Table -->
    <div class="card bg-white overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Product Stock Breakdown</h3>
            <span class="text-xs text-slate-400">Showing {{ $balances->count() }} of {{ $balances->total() }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="compact-table w-full text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-200">
                        <th class="text-left py-2 px-3">Product Name</th>
                        <th class="text-left py-2 px-3">Brand / Category</th>
                        <th class="text-left py-2 px-3">Variant</th>
                        <th class="text-left py-2 px-3">Branch</th>
                        <th class="text-right py-2 px-3">Unit Cost (₹)</th>
                        <th class="text-right py-2 px-3">Qty in Stock</th>
                        <th class="text-right py-2 px-3">Asset Value (₹)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($balances as $item)
                    @php
                        $cost = $item->product ? $item->product->purchase_price : 0;
                        $lineValuation = $cost * $item->quantity;
                    @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="py-2 px-3 font-medium text-slate-800">
                            {{ $item->product->name ?? 'Unknown Item' }}
                            @if($item->product && $item->product->type === 'serialized')
                                <span class="badge badge-indigo text-[9px] ml-1">IMEI</span>
                            @endif
                        </td>
                        <td class="py-2 px-3 text-slate-600">
                            {{ $item->product->brand->name ?? '—' }} &bull; {{ $item->product->category->name ?? '—' }}
                        </td>
                        <td class="py-2 px-3 text-slate-500 font-mono">
                            {{ $item->variant ? $item->variant->name : 'Standard' }}
                        </td>
                        <td class="py-2 px-3 text-slate-600">{{ $item->branch->name ?? '—' }}</td>
                        <td class="py-2 px-3 text-right text-slate-600">₹{{ number_format($cost, 2) }}</td>
                        <td class="py-2 px-3 text-right font-bold {{ $item->quantity <= 3 ? 'text-amber-600' : 'text-slate-800' }}">
                            {{ $item->quantity }}
                        </td>
                        <td class="py-2 px-3 text-right font-semibold text-emerald-600">
                            ₹{{ number_format($lineValuation, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-6 text-center text-slate-400">No stock records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($balances->hasPages())
            <div class="p-3 border-t border-slate-100">
                {{ $balances->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
