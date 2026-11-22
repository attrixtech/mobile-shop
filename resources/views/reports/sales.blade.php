@extends('layouts.app')

@section('title', 'Sales Performance Report')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Sales Performance Report</h1>
            <p class="text-xs text-slate-500">Comprehensive sales breakdown, turnover, GST and product aggregates</p>
        </div>
        <!-- Date & Branch Filter Form -->
        <form method="GET" action="{{ route('reports.sales') }}" class="flex flex-wrap items-center gap-2">
            <select name="branch_id" class="form-input text-xs py-1.5 w-auto">
                <option value="">All Branches</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
            <input type="date" name="start_date" value="{{ $startDate }}" class="form-input text-xs py-1.5 w-auto">
            <span class="text-xs text-slate-400">to</span>
            <input type="date" name="end_date" value="{{ $endDate }}" class="form-input text-xs py-1.5 w-auto">
            <button type="submit" class="btn btn-primary text-xs py-1.5">Filter</button>
        </form>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="card p-3 bg-white">
            <span class="text-xs text-slate-500 font-medium">Invoices Issued</span>
            <div class="text-lg font-bold text-slate-800 mt-1">{{ number_format($totalSalesCount) }}</div>
            <span class="text-[10px] text-slate-400">Between selected dates</span>
        </div>
        <div class="card p-3 bg-white">
            <span class="text-xs text-slate-500 font-medium">Total Revenue (Gross)</span>
            <div class="text-lg font-bold text-emerald-600 mt-1">₹{{ number_format($totalRevenue, 2) }}</div>
            <span class="text-[10px] text-emerald-700">Total billings inclusive of taxes</span>
        </div>
        <div class="card p-3 bg-white">
            <span class="text-xs text-slate-500 font-medium">Total GST Collected</span>
            <div class="text-lg font-bold text-indigo-600 mt-1">₹{{ number_format($totalTax, 2) }}</div>
            <span class="text-[10px] text-indigo-700">CGST + SGST + IGST</span>
        </div>
        <div class="card p-3 bg-white">
            <span class="text-xs text-slate-500 font-medium">Total Discounts</span>
            <div class="text-lg font-bold text-amber-600 mt-1">₹{{ number_format($totalDiscount, 2) }}</div>
            <span class="text-[10px] text-amber-700">Vouchers & special discounts</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Product Sales Aggregate -->
        <div class="card bg-white overflow-hidden lg:col-span-1">
            <div class="px-4 py-3 border-b border-slate-100">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Top Selling Items</h3>
            </div>
            <div class="overflow-y-auto max-h-[420px]">
                <table class="compact-table w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500">
                            <th class="text-left py-2 px-3">Item</th>
                            <th class="text-right py-2 px-2">Qty</th>
                            <th class="text-right py-2 px-3">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($productSales as $ps)
                        <tr class="hover:bg-slate-50">
                            <td class="py-2 px-3 font-medium text-slate-800">
                                <div>{{ $ps->name }}</div>
                                <span class="text-[10px] text-slate-400 capitalize">{{ $ps->type }}</span>
                            </td>
                            <td class="py-2 px-2 text-right font-semibold">{{ $ps->total_qty }}</td>
                            <td class="py-2 px-3 text-right font-medium text-emerald-600">₹{{ number_format($ps->total_amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-slate-400">No items sold in this period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Invoices List -->
        <div class="card bg-white overflow-hidden lg:col-span-2">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Sales Invoices</h3>
                <span class="text-xs text-slate-400">Showing {{ $salesList->count() }} of {{ $salesList->total() }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="compact-table w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 border-b border-slate-200">
                            <th class="text-left py-2 px-3">Invoice #</th>
                            <th class="text-left py-2 px-3">Date</th>
                            <th class="text-left py-2 px-3">Customer</th>
                            <th class="text-left py-2 px-3">Branch</th>
                            <th class="text-right py-2 px-3">Total</th>
                            <th class="text-center py-2 px-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($salesList as $sale)
                        <tr class="hover:bg-slate-50">
                            <td class="py-2 px-3 font-semibold text-indigo-600">
                                <a href="{{ route('sales.show', $sale->id) }}">{{ $sale->invoice_number }}</a>
                            </td>
                            <td class="py-2 px-3 text-slate-600">{{ $sale->sale_date->format('d M Y') }}</td>
                            <td class="py-2 px-3 font-medium text-slate-800">{{ $sale->customer->name ?? 'Walk-in' }}</td>
                            <td class="py-2 px-3 text-slate-500">{{ $sale->branch->name ?? '—' }}</td>
                            <td class="py-2 px-3 text-right font-semibold text-slate-800">₹{{ number_format($sale->grand_total, 2) }}</td>
                            <td class="py-2 px-3 text-center">
                                <span class="badge badge-success text-[10px]">{{ ucfirst($sale->status) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400">No sales invoices recorded for this range.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($salesList->hasPages())
                <div class="p-3 border-t border-slate-100">
                    {{ $salesList->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
