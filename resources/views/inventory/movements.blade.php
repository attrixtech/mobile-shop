@extends('layouts.app')

@section('title', 'Stock Movements Ledger - thrumble.mobile-shop')

@section('content')
<div class="space-y-3">
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">Stock Movement Audit Ledger</h1>
            <p class="text-xs text-slate-500">Every inventory deduction and addition recorded with timestamp and reason</p>
        </div>
        <a href="{{ route('inventory.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">&larr; Back to Stock</a>
    </div>

    <!-- Movement Table -->
    <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden text-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left compact-table">
                <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th>Date & Time</th>
                        <th>Product</th>
                        <th>Branch</th>
                        <th>Movement Type</th>
                        <th>Qty Delta</th>
                        <th>Transaction Ref</th>
                        <th>Notes / Audit</th>
                        <th>User</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($movements as $m)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="font-mono text-slate-500">{{ $m->created_at->format('d M, Y H:i') }}</td>
                            <td class="font-bold text-slate-900">
                                {{ $m->product?->name }}
                                @if($m->variant)
                                    <span class="text-slate-500 font-normal">({{ $m->variant->name }})</span>
                                @endif
                            </td>
                            <td>{{ $m->branch?->name }}</td>
                            <td>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase
                                    {{ in_array($m->type, ['purchase', 'transfer_in', 'return_in', 'adjustment']) && $m->quantity > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ str_replace('_', ' ', $m->type) }}
                                </span>
                            </td>
                            <td>
                                <span class="font-bold {{ $m->quantity > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $m->quantity > 0 ? '+' . $m->quantity : $m->quantity }}
                                </span>
                            </td>
                            <td>
                                @if($m->reference_type)
                                    <span class="font-mono text-indigo-600">{{ $m->reference_type }} #{{ $m->reference_id }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="text-slate-600">{{ $m->notes }}</td>
                            <td>{{ $m->user?->name ?: 'System' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-400">No stock movements found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-2 border-t border-slate-200">
            {{ $movements->links() }}
        </div>
    </div>
</div>
@endsection
