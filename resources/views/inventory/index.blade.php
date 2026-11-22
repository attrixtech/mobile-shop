@extends('layouts.app')

@section('title', 'Stock Overview - thrumble.mobile-shop')

@section('content')
<div class="space-y-3">
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">Current Inventory & Branch Stock Balances</h1>
            <p class="text-xs text-slate-500">Live stock counts, asset valuations, and low-inventory alerts</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('inventory.transfers') }}" class="px-3 py-1.5 rounded bg-slate-800 hover:bg-slate-700 text-white text-xs font-medium flex items-center gap-1.5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                <span>Branch Transfers</span>
            </a>
            <a href="{{ route('inventory.movements') }}" class="px-3 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-sm flex items-center gap-1.5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                <span>Audit Movements</span>
            </a>
        </div>
    </div>

    <!-- Search Form -->
    <div class="bg-white p-2.5 rounded border border-slate-200 shadow-sm text-xs">
        <form action="{{ route('inventory.index') }}" method="GET" class="flex items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search stock by product name, barcode, SKU..." class="flex-1 px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
            <button type="submit" class="px-3 py-1.5 rounded bg-indigo-600 text-white font-medium hover:bg-indigo-500 text-xs">Filter</button>
            <a href="{{ route('inventory.index') }}" class="px-2.5 py-1.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200 text-xs">Reset</a>
        </form>
    </div>

    <!-- Balances Table -->
    <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden text-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left compact-table">
                <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th>Product / Model</th>
                        <th>Variant</th>
                        <th>Branch</th>
                        <th>Type</th>
                        <th>Available Qty</th>
                        <th>Cost Value (₹)</th>
                        <th>Selling Value (₹)</th>
                        <th class="text-right">IMEI List</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($balances as $b)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="font-bold text-slate-900">
                                {{ $b->product?->name }}
                                <div class="text-[10px] text-slate-400 font-mono">SKU: {{ $b->product?->sku ?: '-' }}</div>
                            </td>
                            <td>{{ $b->variant?->name ?: 'Standard' }}</td>
                            <td><span class="text-slate-600 font-medium">{{ $b->branch?->name }}</span></td>
                            <td>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase
                                    {{ $b->product?->type === 'serialized' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $b->product?->type }}
                                </span>
                            </td>
                            <td>
                                <span class="px-2 py-0.5 rounded font-bold text-xs
                                    {{ $b->quantity <= ($b->product?->min_stock ?? 2) ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ $b->quantity }} {{ $b->product?->unit ?: 'pcs' }}
                                </span>
                            </td>
                            <td>₹{{ number_format(($b->product?->purchase_price ?? 0) * $b->quantity, 2) }}</td>
                            <td class="font-bold text-slate-900">₹{{ number_format(($b->product?->selling_price ?? 0) * $b->quantity, 2) }}</td>
                            <td class="text-right">
                                @if($b->product?->isSerialized())
                                    <a href="{{ route('imei.index', ['search' => $b->product?->name]) }}" class="px-2 py-0.5 rounded bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-medium">
                                        View IMEIs &rarr;
                                    </a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-400">No stock balances found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-2 border-t border-slate-200">
            {{ $balances->links() }}
        </div>
    </div>
</div>
@endsection
