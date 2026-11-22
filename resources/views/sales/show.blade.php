@extends('layouts.app')

@section('title', "Invoice {$sale->invoice_number} - thrumble.mobile-shop")

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <!-- Header with Action Buttons -->
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-lg font-bold text-slate-900 tracking-tight">Invoice #{{ $sale->invoice_number }}</h1>
                <span class="px-2 py-0.5 rounded text-[11px] font-semibold uppercase
                    {{ $sale->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                    {{ $sale->status }}
                </span>
            </div>
            <p class="text-xs text-slate-500">Date: {{ $sale->sale_date->format('d F Y') }} | Branch: {{ $sale->branch?->name }} | Billed By: {{ $sale->creator?->name }}</p>
        </div>
        <div class="flex items-center gap-1.5 text-xs">
            <a href="{{ route('sales.thermal', $sale->id) }}" target="_blank" class="px-3 py-1.5 rounded bg-slate-800 hover:bg-slate-700 text-white font-medium flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Thermal (80mm)</span>
            </a>
            <a href="{{ route('sales.invoice', $sale->id) }}" target="_blank" class="px-3 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white font-medium flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                <span>A4 Tax Invoice</span>
            </a>
            @if($sale->status === 'completed')
                <a href="{{ route('sales.return', $sale->id) }}" class="px-3 py-1.5 rounded bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 font-medium">
                    Process Return
                </a>
            @endif
        </div>
    </div>

    <!-- Bill Parties Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
        <div class="bg-white p-3 rounded border border-slate-200 shadow-sm space-y-1">
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Billed By (Seller)</h3>
            <div class="font-semibold text-slate-900">{{ $company->name ?? 'thrumble.mobile-shop' }}</div>
            <div class="text-slate-500">GSTIN: <span class="font-mono text-slate-800 font-medium">{{ $company->gstin ?? 'N/A' }}</span></div>
            <div class="text-slate-500">{{ $sale->branch?->address }}</div>
            <div class="text-slate-500">{{ $sale->branch?->city }}, {{ $sale->branch?->state }} - {{ $sale->branch?->pincode }}</div>
            <div class="text-slate-500">Phone: {{ $sale->branch?->phone }}</div>
        </div>

        <div class="bg-white p-3 rounded border border-slate-200 shadow-sm space-y-1">
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Billed To (Customer)</h3>
            <div class="font-semibold text-slate-900">{{ $sale->customer?->name ?: 'Walk-in Customer' }}</div>
            <div class="text-slate-500">Mobile: {{ $sale->customer?->phone ?: 'Not provided' }}</div>
            @if($sale->customer?->gstin)
                <div class="text-slate-500">Customer GSTIN: <span class="font-mono text-slate-800 font-medium">{{ $sale->customer->gstin }}</span></div>
            @endif
            <div class="text-slate-500">{{ $sale->customer?->address ?: 'Over-the-counter POS sale' }}</div>
            <div class="text-slate-500">{{ $sale->customer?->city }} {{ $sale->customer?->state }}</div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden text-xs">
        <div class="p-2.5 bg-slate-50 border-b border-slate-200 font-bold text-slate-800 uppercase tracking-wider text-[11px]">
            Billed Products & Serialized IMEIs
        </div>
        <table class="w-full text-left compact-table">
            <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                <tr>
                    <th>#</th>
                    <th>Product & Serial / IMEI Details</th>
                    <th>HSN</th>
                    <th>Qty</th>
                    <th>Unit Rate</th>
                    <th>Taxable</th>
                    <th>GST %</th>
                    <th class="text-right">Total (₹)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($sale->items as $idx => $item)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>
                            <div class="font-bold text-slate-900">{{ $item->product?->name }}</div>
                            @if($item->variant)
                                <div class="text-[11px] text-slate-500 font-medium">{{ $item->variant->name }}</div>
                            @endif
                            @if(!empty($item->imeis) && is_array($item->imeis))
                                <div class="mt-1 flex flex-wrap gap-1">
                                    @foreach($item->imeis as $im)
                                        <a href="{{ route('imei.index', ['search' => $im]) }}" class="inline-flex items-center gap-1 font-mono text-[10px] px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                            <span>IMEI: {{ $im }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="font-mono text-slate-500">{{ $item->product?->hsn_code ?? '85171300' }}</td>
                        <td class="font-medium">{{ $item->quantity }}</td>
                        <td>₹{{ number_format($item->unit_price, 2) }}</td>
                        <td>₹{{ number_format(($item->quantity * $item->unit_price) - $item->discount_amount, 2) }}</td>
                        <td>{{ $item->tax_rate }}%</td>
                        <td class="text-right font-bold text-slate-900">₹{{ number_format($item->total_amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals Calculation Footer -->
        <div class="p-3 bg-slate-50 border-t border-slate-200 flex justify-end">
            <div class="w-72 space-y-1.5 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Taxable Value:</span>
                    <span>₹{{ number_format($sale->subtotal - $sale->discount_amount, 2) }}</span>
                </div>
                @if($sale->cgst > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>CGST:</span>
                        <span>₹{{ number_format($sale->cgst, 2) }}</span>
                    </div>
                @endif
                @if($sale->sgst > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>SGST:</span>
                        <span>₹{{ number_format($sale->sgst, 2) }}</span>
                    </div>
                @endif
                @if($sale->igst > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>IGST:</span>
                        <span>₹{{ number_format($sale->igst, 2) }}</span>
                    </div>
                @endif
                @if($sale->exchange_allowance > 0)
                    <div class="flex justify-between text-emerald-600 font-semibold">
                        <span>Trade-In Exchange Voucher:</span>
                        <span>-₹{{ number_format($sale->exchange_allowance, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between font-bold text-sm text-slate-900 pt-1.5 border-t border-slate-300">
                    <span>Grand Total:</span>
                    <span class="text-indigo-600 text-base">₹{{ number_format($sale->grand_total, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Amount Paid:</span>
                    <span>₹{{ number_format($sale->paid_amount, 2) }}</span>
                </div>
                @if($sale->due_amount > 0)
                    <div class="flex justify-between text-rose-600 font-bold">
                        <span>Balance Due:</span>
                        <span>₹{{ number_format($sale->due_amount, 2) }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Payments Record -->
    <div class="bg-white p-3 rounded border border-slate-200 shadow-sm text-xs space-y-2">
        <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Payment Transactions</h3>
        <div class="space-y-1">
            @forelse($sale->payments as $pay)
                <div class="flex items-center justify-between p-2 rounded bg-slate-50 border border-slate-100">
                    <div>
                        <span class="font-bold text-slate-900 uppercase">{{ $pay->payment_method }}</span>
                        @if($pay->reference_no)
                            <span class="text-slate-500 text-[11px] ml-1.5">(Ref: {{ $pay->reference_no }})</span>
                        @endif
                    </div>
                    <div class="font-bold text-slate-900">₹{{ number_format($pay->amount, 2) }}</div>
                </div>
            @empty
                <div class="text-slate-400">No payment transaction records found.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
