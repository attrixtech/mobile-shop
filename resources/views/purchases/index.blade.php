@extends('layouts.app')

@section('title', 'Purchases & Goods Receipts - Webotapp Mobile Shop ERP')

@section('content')
<div class="space-y-3">
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">Supplier Purchases & Goods Receipt Notes (GRN)</h1>
            <p class="text-xs text-slate-500">Receive inventory stock from distributors with serialized IMEI capture</p>
        </div>
        <a href="{{ route('purchases.create') }}" class="px-3 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-sm flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>New Purchase / GRN</span>
        </a>
    </div>

    <!-- Purchases Table -->
    <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden text-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left compact-table">
                <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th>Purchase No</th>
                        <th>Supplier</th>
                        <th>Supplier Inv #</th>
                        <th>Date</th>
                        <th>Branch</th>
                        <th>Total Cost</th>
                        <th>Paid Amount</th>
                        <th>Payment Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($purchases as $p)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="font-bold text-indigo-600">
                                <a href="{{ route('purchases.show', $p->id) }}">{{ $p->purchase_number }}</a>
                            </td>
                            <td class="font-medium text-slate-900">{{ $p->supplier?->name }}</td>
                            <td><span class="font-mono text-slate-600">{{ $p->supplier_invoice_no ?: '-' }}</span></td>
                            <td>{{ $p->invoice_date->format('d M, Y') }}</td>
                            <td>{{ $p->branch?->name }}</td>
                            <td class="font-bold text-slate-900">₹{{ number_format($p->grand_total, 2) }}</td>
                            <td class="text-emerald-600 font-medium">₹{{ number_format($p->paid_amount, 2) }}</td>
                            <td>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase
                                    {{ $p->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                    {{ $p->payment_status === 'partial' ? 'bg-amber-100 text-amber-700' : '' }}
                                    {{ $p->payment_status === 'unpaid' ? 'bg-rose-100 text-rose-700' : '' }}">
                                    {{ $p->payment_status }}
                                </span>
                            </td>
                            <td class="text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('purchases.show', $p->id) }}" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs">View GRN</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 text-slate-400">No purchase invoices recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-2 border-t border-slate-200">
            {{ $purchases->links() }}
        </div>
    </div>
</div>
@endsection
