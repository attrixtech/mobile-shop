@extends('layouts.app')

@section('title', 'Suppliers - thrumble.mobile-shop')

@section('content')
<div class="space-y-3">
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">Suppliers & Mobile Phone Distributors</h1>
            <p class="text-xs text-slate-500">Manage distributors, GSTIN credentials, purchase balances, and dues</p>
        </div>
        <a href="{{ route('suppliers.create') }}" class="px-3 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-sm flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Add New Supplier</span>
        </a>
    </div>

    <!-- Suppliers Table -->
    <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden text-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left compact-table">
                <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th>Supplier / Business Name</th>
                        <th>Contact Person</th>
                        <th>Phone</th>
                        <th>GSTIN</th>
                        <th>Location</th>
                        <th>Current Payable</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($suppliers as $sup)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td>
                                <a href="{{ route('suppliers.show', $sup->id) }}" class="font-bold text-indigo-600 hover:underline">
                                    {{ $sup->name }}
                                </a>
                                @if($sup->business_name)
                                    <div class="text-[10px] text-slate-500">{{ $sup->business_name }}</div>
                                @endif
                            </td>
                            <td>{{ $sup->contact_person ?: '-' }}</td>
                            <td>{{ $sup->phone }}</td>
                            <td class="font-mono text-slate-600">{{ $sup->gstin ?: 'Unregistered' }}</td>
                            <td>{{ $sup->city }}, {{ $sup->state }}</td>
                            <td>
                                <span class="font-bold {{ $sup->current_balance > 0 ? 'text-rose-600' : 'text-slate-700' }}">
                                    ₹{{ number_format($sup->current_balance, 2) }}
                                </span>
                            </td>
                            <td class="text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('suppliers.show', $sup->id) }}" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs">View Ledger</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-slate-400">No suppliers registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-2 border-t border-slate-200">
            {{ $suppliers->links() }}
        </div>
    </div>
</div>
@endsection
