@extends('layouts.app')

@section('title', "Supplier Profile - {$supplier->name}")

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">{{ $supplier->name }}</h1>
            <p class="text-xs text-slate-500">GSTIN: {{ $supplier->gstin ?: 'Unregistered' }} | Phone: {{ $supplier->phone }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('purchases.create') }}" class="px-3 py-1.5 rounded bg-indigo-600 text-white font-medium text-xs">+ New GRN Purchase</a>
            <a href="{{ route('suppliers.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">&larr; Back</a>
        </div>
    </div>

    <!-- Stats & Profile Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
        <div class="bg-white p-3 rounded border border-slate-200 shadow-sm space-y-1">
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Outstanding Balance</h3>
            <div class="text-lg font-bold text-rose-600 mt-1">₹{{ number_format($supplier->current_balance, 2) }}</div>
            <div class="text-slate-400 text-[10px]">Current Accounts Payable</div>
        </div>

        <div class="bg-white p-3 rounded border border-slate-200 shadow-sm space-y-1 md:col-span-2">
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Distributor Contact & Address</h3>
            <div class="text-slate-700"><strong>Contact:</strong> {{ $supplier->contact_person }} ({{ $supplier->phone }})</div>
            <div class="text-slate-700"><strong>Email:</strong> {{ $supplier->email ?: '-' }}</div>
            <div class="text-slate-700"><strong>Address:</strong> {{ $supplier->address ?: '-' }}, {{ $supplier->city }}, {{ $supplier->state }}</div>
        </div>
    </div>

    <!-- Purchase Orders History -->
    <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden text-xs">
        <div class="p-2.5 bg-slate-50 border-b border-slate-200 font-bold text-slate-800 uppercase tracking-wider text-[11px]">
            Recent Invoices from this Supplier
        </div>
        <table class="w-full text-left compact-table">
            <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                <tr>
                    <th>Purchase No</th>
                    <th>Date</th>
                    <th>Supplier Invoice #</th>
                    <th>Amount</th>
                    <th>Paid</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($supplier->purchases as $p)
                    <tr>
                        <td class="font-bold text-indigo-600">
                            <a href="{{ route('purchases.show', $p->id) }}">{{ $p->purchase_number }}</a>
                        </td>
                        <td>{{ $p->invoice_date->format('d M, Y') }}</td>
                        <td class="font-mono text-slate-600">{{ $p->supplier_invoice_no ?: '-' }}</td>
                        <td class="font-bold text-slate-900">₹{{ number_format($p->grand_total, 2) }}</td>
                        <td class="text-emerald-600">₹{{ number_format($p->paid_amount, 2) }}</td>
                        <td>
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase
                                {{ $p->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                {{ $p->payment_status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-slate-400">No purchases found for this supplier.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
