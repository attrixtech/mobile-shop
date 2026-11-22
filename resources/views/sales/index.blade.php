@extends('layouts.app')

@section('title', 'Sales Invoices - Webotapp Mobile Shop ERP')

@section('content')
<div class="space-y-3">
    <!-- Header -->
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">Sales Invoices & Transactions</h1>
            <p class="text-xs text-slate-500">View, search, print thermal receipts and manage customer returns</p>
        </div>
        <a href="{{ route('pos.index') }}" class="px-3 py-1.5 rounded bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-sm flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Open POS Terminal</span>
        </a>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-2.5 rounded border border-slate-200 shadow-sm text-xs">
        <form action="{{ route('sales.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-2">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Invoice # or Customer..." class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <select name="payment_status" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                    <option value="">All Payment Statuses</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="due" {{ request('payment_status') === 'due' ? 'selected' : '' }}>Due / Unpaid</option>
                </select>
            </div>
            <div class="flex items-center gap-1.5">
                <button type="submit" class="px-3 py-1.5 rounded bg-indigo-600 text-white font-medium hover:bg-indigo-500 text-xs">Filter</button>
                <a href="{{ route('sales.index') }}" class="px-2.5 py-1.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200 text-xs">Reset</a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left compact-table">
                <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th>Invoice No</th>
                        <th>Date</th>
                        <th>Branch</th>
                        <th>Customer</th>
                        <th>Mode</th>
                        <th>Taxable</th>
                        <th>GST</th>
                        <th>Net Total</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sales as $s)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="font-bold text-indigo-600">
                                <a href="{{ route('sales.show', $s->id) }}">{{ $s->invoice_number }}</a>
                            </td>
                            <td>{{ $s->sale_date->format('d M, Y') }}</td>
                            <td><span class="text-slate-600 font-medium">{{ $s->branch?->name }}</span></td>
                            <td>
                                <div class="font-medium text-slate-900">{{ $s->customer?->name ?: 'Walk-in Customer' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $s->customer?->phone ?: '-' }}</div>
                            </td>
                            <td>
                                <span class="px-1.5 py-0.5 rounded text-[10px] uppercase font-semibold
                                    {{ $s->payment_method === 'upi' ? 'bg-purple-100 text-purple-700' : '' }}
                                    {{ $s->payment_method === 'cash' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                    {{ $s->payment_method === 'card' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $s->payment_method === 'split' ? 'bg-amber-100 text-amber-700' : '' }}">
                                    {{ $s->payment_method }}
                                </span>
                            </td>
                            <td>₹{{ number_format($s->subtotal - $s->discount_amount, 2) }}</td>
                            <td>₹{{ number_format($s->tax_amount, 2) }}</td>
                            <td class="font-bold text-slate-900">₹{{ number_format($s->grand_total, 2) }}</td>
                            <td>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase
                                    {{ $s->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $s->status }}
                                </span>
                            </td>
                            <td class="text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('sales.show', $s->id) }}" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs">View</a>
                                <a href="{{ route('sales.thermal', $s->id) }}" target="_blank" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs">Thermal</a>
                                <a href="{{ route('sales.invoice', $s->id) }}" target="_blank" class="px-2 py-0.5 rounded bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-medium">A4 Tax Invoice</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-8 text-slate-400">No invoices found matching criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-2 border-t border-slate-200">
            {{ $sales->links() }}
        </div>
    </div>
</div>
@endsection
