@extends('layouts.app')

@section('title', 'Indian GSTR-1 Tax Summary')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-800">GST Compliance & GSTR-1 Return</h1>
            <p class="text-xs text-slate-500">Tax summary, B2B & B2C invoice register, and HSN-wise tax distribution</p>
        </div>
        <form method="GET" action="{{ route('reports.gst') }}" class="flex flex-wrap items-center gap-2">
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

    <!-- Tax Totals Grid -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div class="card p-3 bg-white">
            <span class="text-xs text-slate-500 font-medium">Taxable Value</span>
            <div class="text-lg font-bold text-slate-800 mt-1">₹{{ number_format($taxableTotal, 2) }}</div>
            <span class="text-[10px] text-slate-400">Base turnover</span>
        </div>
        <div class="card p-3 bg-white">
            <span class="text-xs text-slate-500 font-medium">CGST (Central)</span>
            <div class="text-lg font-bold text-indigo-600 mt-1">₹{{ number_format($cgstTotal, 2) }}</div>
            <span class="text-[10px] text-indigo-700">Intra-state share</span>
        </div>
        <div class="card p-3 bg-white">
            <span class="text-xs text-slate-500 font-medium">SGST (State)</span>
            <div class="text-lg font-bold text-indigo-600 mt-1">₹{{ number_format($sgstTotal, 2) }}</div>
            <span class="text-[10px] text-indigo-700">Intra-state share</span>
        </div>
        <div class="card p-3 bg-white">
            <span class="text-xs text-slate-500 font-medium">IGST (Integrated)</span>
            <div class="text-lg font-bold text-indigo-600 mt-1">₹{{ number_format($igstTotal, 2) }}</div>
            <span class="text-[10px] text-indigo-700">Inter-state transactions</span>
        </div>
        <div class="card p-3 bg-white">
            <span class="text-xs text-slate-500 font-medium">Total Tax Liability</span>
            <div class="text-lg font-bold text-emerald-600 mt-1">₹{{ number_format($totalTax, 2) }}</div>
            <span class="text-[10px] text-emerald-700">Payable to GSTN</span>
        </div>
    </div>

    <!-- HSN Summary Table -->
    <div class="card bg-white overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">HSN / SAC Wise Summary</h3>
            <span class="text-xs text-slate-400">GSTR-1 Table 12 format</span>
        </div>
        <div class="overflow-x-auto">
            <table class="compact-table w-full text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-200">
                        <th class="text-left py-2 px-3">HSN / SAC Code</th>
                        <th class="text-right py-2 px-3">Total Qty</th>
                        <th class="text-right py-2 px-3">Taxable Value (₹)</th>
                        <th class="text-right py-2 px-3">CGST (₹)</th>
                        <th class="text-right py-2 px-3">SGST (₹)</th>
                        <th class="text-right py-2 px-3">IGST (₹)</th>
                        <th class="text-right py-2 px-3">Total Value (₹)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($hsnSummary as $row)
                    <tr class="hover:bg-slate-50">
                        <td class="py-2 px-3 font-mono font-semibold text-slate-800">{{ $row->hsn_code ?? 'None' }}</td>
                        <td class="py-2 px-3 text-right font-medium">{{ $row->total_qty }}</td>
                        <td class="py-2 px-3 text-right text-slate-700 font-medium">₹{{ number_format($row->taxable_value, 2) }}</td>
                        <td class="py-2 px-3 text-right text-indigo-600">₹{{ number_format($row->total_cgst, 2) }}</td>
                        <td class="py-2 px-3 text-right text-indigo-600">₹{{ number_format($row->total_sgst, 2) }}</td>
                        <td class="py-2 px-3 text-right text-indigo-600">₹{{ number_format($row->total_igst, 2) }}</td>
                        <td class="py-2 px-3 text-right font-bold text-emerald-600">₹{{ number_format($row->total_value, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-6 text-center text-slate-400">No HSN data found for this period.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Invoice Register Table -->
    <div class="card bg-white overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">GSTR-1 Invoice Register</h3>
            <span class="text-xs text-slate-400">{{ $sales->count() }} invoices in period</span>
        </div>
        <div class="overflow-x-auto">
            <table class="compact-table w-full text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-200">
                        <th class="text-left py-2 px-3">Invoice #</th>
                        <th class="text-left py-2 px-3">Date</th>
                        <th class="text-left py-2 px-3">Customer</th>
                        <th class="text-left py-2 px-3">Customer GSTIN</th>
                        <th class="text-left py-2 px-3">POS State</th>
                        <th class="text-right py-2 px-3">Taxable</th>
                        <th class="text-right py-2 px-3">CGST</th>
                        <th class="text-right py-2 px-3">SGST</th>
                        <th class="text-right py-2 px-3">IGST</th>
                        <th class="text-right py-2 px-3">Invoice Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sales as $s)
                    <tr class="hover:bg-slate-50">
                        <td class="py-2 px-3 font-semibold text-indigo-600">
                            <a href="{{ route('sales.show', $s->id) }}">{{ $s->invoice_number }}</a>
                        </td>
                        <td class="py-2 px-3 text-slate-600">{{ $s->sale_date->format('d/m/Y') }}</td>
                        <td class="py-2 px-3 font-medium text-slate-800">{{ $s->customer->name ?? 'Retail Walk-in' }}</td>
                        <td class="py-2 px-3 font-mono text-slate-600">{{ $s->customer->gstin ?? '—' }}</td>
                        <td class="py-2 px-3 text-slate-500">{{ $s->customer->state ?? 'Maharashtra' }}</td>
                        <td class="py-2 px-3 text-right">₹{{ number_format($s->subtotal - $s->discount_amount, 2) }}</td>
                        <td class="py-2 px-3 text-right text-slate-600">₹{{ number_format($s->cgst, 2) }}</td>
                        <td class="py-2 px-3 text-right text-slate-600">₹{{ number_format($s->sgst, 2) }}</td>
                        <td class="py-2 px-3 text-right text-slate-600">₹{{ number_format($s->igst, 2) }}</td>
                        <td class="py-2 px-3 text-right font-semibold text-slate-900">₹{{ number_format($s->grand_total, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="py-6 text-center text-slate-400">No invoices generated for selected criteria.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
