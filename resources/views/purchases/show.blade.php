@extends('layouts.app')

@section('title', "Purchase {$purchase->purchase_number} - Webotapp Mobile Shop ERP")

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-base font-bold text-slate-900 tracking-tight">Purchase / GRN #{{ $purchase->purchase_number }}</h1>
                <span class="px-2 py-0.5 rounded text-[11px] font-semibold uppercase
                    {{ $purchase->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : '' }}
                    {{ $purchase->payment_status === 'partial' ? 'bg-amber-100 text-amber-700' : '' }}
                    {{ $purchase->payment_status === 'unpaid' ? 'bg-rose-100 text-rose-700' : '' }}">
                    {{ $purchase->payment_status }}
                </span>
            </div>
            <p class="text-xs text-slate-500">Supplier: {{ $purchase->supplier?->name }} | Date: {{ $purchase->invoice_date->format('d M, Y') }} | Receiving Branch: {{ $purchase->branch?->name }}</p>
        </div>
        <a href="{{ route('purchases.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">&larr; Back to Purchases</a>
    </div>

    <!-- Overview Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
        <div class="bg-white p-3 rounded border border-slate-200 shadow-sm space-y-1">
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Supplier Information</h3>
            <div class="font-bold text-slate-900">{{ $purchase->supplier?->name }}</div>
            <div class="text-slate-500">{{ $purchase->supplier?->business_name }}</div>
            <div class="text-slate-500">GSTIN: <span class="font-mono text-slate-800">{{ $purchase->supplier?->gstin ?: 'N/A' }}</span></div>
            <div class="text-slate-500">Phone: {{ $purchase->supplier?->phone }}</div>
            <div class="text-slate-500">{{ $purchase->supplier?->address }}</div>
        </div>

        <div class="bg-white p-3 rounded border border-slate-200 shadow-sm space-y-1">
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Invoice & Payment Meta</h3>
            <div>Supplier Inv #: <span class="font-mono font-bold text-slate-800">{{ $purchase->supplier_invoice_no ?: '-' }}</span></div>
            <div>Grand Total: <span class="font-bold text-slate-900">₹{{ number_format($purchase->grand_total, 2) }}</span></div>
            <div>Paid Amount: <span class="font-bold text-emerald-600">₹{{ number_format($purchase->paid_amount, 2) }}</span></div>
            @php $due = max(0, $purchase->grand_total - $purchase->paid_amount); @endphp
            <div>Outstanding Due: <span class="font-bold text-rose-600">₹{{ number_format($due, 2) }}</span></div>
        </div>
    </div>

    <!-- Items & Captured IMEIs -->
    <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden text-xs">
        <div class="p-2.5 bg-slate-50 border-b border-slate-200 font-bold text-slate-800 uppercase tracking-wider text-[11px]">
            Received Products & Registered IMEIs
        </div>
        <table class="w-full text-left compact-table">
            <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                <tr>
                    <th>#</th>
                    <th>Product & Registered Serial / IMEI Details</th>
                    <th>Qty</th>
                    <th>Unit Cost</th>
                    <th>Tax Rate</th>
                    <th>Tax (₹)</th>
                    <th class="text-right">Line Total (₹)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($purchase->items as $idx => $item)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>
                            <div class="font-bold text-slate-900">{{ $item->product?->name }}</div>
                            @if($item->variant)
                                <div class="text-slate-500 text-[11px]">{{ $item->variant->name }}</div>
                            @endif
                            @if(!empty($item->imeis) && is_array($item->imeis))
                                <div class="mt-1 flex flex-wrap gap-1">
                                    @foreach($item->imeis as $im)
                                        <a href="{{ route('imei.index', ['search' => $im]) }}" class="font-mono text-[10px] px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            IMEI: {{ $im }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="font-bold text-slate-800">{{ $item->quantity }}</td>
                        <td>₹{{ number_format($item->unit_cost, 2) }}</td>
                        <td>{{ $item->tax_rate }}%</td>
                        <td>₹{{ number_format($item->tax_amount, 2) }}</td>
                        <td class="text-right font-bold text-slate-900">₹{{ number_format($item->total_cost, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Record Additional Payment Form if Due -->
    @if($purchase->paid_amount < $purchase->grand_total)
        <div class="bg-white p-3 rounded border border-slate-200 shadow-sm text-xs space-y-3">
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Record Payment to Supplier</h3>
            <form action="{{ route('purchases.payment', $purchase->id) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-4 gap-2 items-end">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-0.5">Amount (₹)</label>
                    <input type="number" step="0.01" name="amount" value="{{ $purchase->grand_total - $purchase->paid_amount }}" required class="w-full px-2 py-1 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-0.5">Payment Method</label>
                    <select name="payment_method" class="w-full px-2 py-1 rounded bg-slate-50 border border-slate-300 text-xs">
                        <option value="bank_transfer">Bank Transfer / NEFT / RTGS</option>
                        <option value="cheque">Cheque</option>
                        <option value="upi">UPI</option>
                        <option value="cash">Cash</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-0.5">Payment Date</label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="w-full px-2 py-1 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
                <div>
                    <button type="submit" class="w-full py-1.5 px-3 rounded bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs">
                        Record Supplier Payment
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
@endsection
