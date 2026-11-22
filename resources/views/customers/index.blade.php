@extends('layouts.app')

@section('title', 'Customer CRM - Webotapp Mobile Shop ERP')

@section('content')
<div class="space-y-3">
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">Customer Relationship Management (CRM)</h1>
            <p class="text-xs text-slate-500">Track device purchase history, warranty records, repair tickets, and loyalty rewards</p>
        </div>
        <a href="{{ route('customers.create') }}" class="px-3 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-sm flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            <span>Add New Customer</span>
        </a>
    </div>

    <!-- Search Form -->
    <div class="bg-white p-2.5 rounded border border-slate-200 shadow-sm text-xs">
        <form action="{{ route('customers.index') }}" method="GET" class="flex items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customer name, phone, email, or GSTIN..." class="flex-1 px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
            <button type="submit" class="px-3 py-1.5 rounded bg-indigo-600 text-white font-medium hover:bg-indigo-500 text-xs">Filter</button>
            <a href="{{ route('customers.index') }}" class="px-2.5 py-1.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200 text-xs">Reset</a>
        </form>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden text-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left compact-table">
                <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th>Customer Name</th>
                        <th>Mobile Number</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Loyalty Points</th>
                        <th>Outstanding Dues</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($customers as $c)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="font-bold text-slate-900">
                                <a href="{{ route('customers.show', $c->id) }}" class="text-indigo-600 hover:underline">
                                    {{ $c->name }}
                                </a>
                                @if($c->gstin)
                                    <div class="text-[10px] text-slate-400 font-mono">GSTIN: {{ $c->gstin }}</div>
                                @endif
                            </td>
                            <td class="font-mono text-slate-700">{{ $c->phone }}</td>
                            <td>{{ $c->email ?: '-' }}</td>
                            <td>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase
                                    {{ $c->customer_type === 'corporate' ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $c->customer_type }}
                                </span>
                            </td>
                            <td>
                                <span class="px-2 py-0.5 rounded font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 text-xs">
                                    {{ $c->loyalty_points }} pts
                                </span>
                            </td>
                            <td>
                                <span class="font-bold {{ $c->current_balance > 0 ? 'text-rose-600' : 'text-slate-700' }}">
                                    ₹{{ number_format($c->current_balance, 2) }}
                                </span>
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <a href="{{ route('customers.show', $c->id) }}" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs">View 360° Profile</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-slate-400">No customer profiles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-2 border-t border-slate-200">
            {{ $customers->links() }}
        </div>
    </div>
</div>
@endsection
