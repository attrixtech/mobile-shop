@extends('layouts.app')

@section('title', "Sales Return for Invoice {$sale->invoice_number}")

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">Process Return for Invoice #{{ $sale->invoice_number }}</h1>
            <p class="text-xs text-slate-500">Inspect device condition, verify original IMEIs, and issue refund/credit</p>
        </div>
        <a href="{{ route('sales.show', $sale->id) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">&larr; Back to Invoice</a>
    </div>

    <form action="{{ route('sales.return.post', $sale->id) }}" method="POST" class="bg-white rounded border border-slate-200 p-4 shadow-sm text-xs space-y-4">
        @csrf

        <div>
            <label class="block font-semibold text-slate-700 mb-1">Reason for Return / Customer Complaint *</label>
            <textarea name="reason" rows="2" required placeholder="Describe reason for return, e.g., Customer changed mind, defect observed during initial 24 hours, etc." class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500"></textarea>
        </div>

        <div class="border-t border-slate-200 pt-3">
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px] mb-2">Select Items to Return</h3>
            <div class="space-y-3">
                @foreach($sale->items as $idx => $item)
                    <div class="p-3 rounded bg-slate-50 border border-slate-200 space-y-2">
                        <input type="hidden" name="return_items[{{ $idx }}][product_id]" value="{{ $item->product_id }}">
                        <input type="hidden" name="return_items[{{ $idx }}][variant_id]" value="{{ $item->variant_id }}">
                        <input type="hidden" name="return_items[{{ $idx }}][unit_price]" value="{{ $item->unit_price }}">

                        <div class="flex items-center justify-between">
                            <div>
                                <span class="font-bold text-slate-900">{{ $item->product?->name }}</span>
                                @if($item->variant)
                                    <span class="text-slate-500 text-[11px]">({{ $item->variant->name }})</span>
                                @endif
                            </div>
                            <div class="font-bold text-slate-900">
                                Sold Rate: ₹{{ number_format($item->unit_price, 2) }}
                            </div>
                        </div>

                        @if(!empty($item->imeis) && is_array($item->imeis))
                            <div class="text-[11px] text-indigo-700 bg-indigo-50 p-1.5 rounded border border-indigo-200">
                                <strong>Tracked Serial / IMEIs:</strong>
                                @foreach($item->imeis as $im)
                                    <label class="inline-flex items-center ml-2">
                                        <input type="checkbox" name="return_items[{{ $idx }}][imeis][]" value="{{ $im }}" checked class="rounded border-slate-300 text-indigo-600 text-xs">
                                        <span class="ml-1 font-mono font-medium">{{ $im }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-1 text-xs">
                            <div>
                                <label class="block text-slate-500 font-medium mb-0.5">Return Quantity</label>
                                <input type="number" name="return_items[{{ $idx }}][quantity]" value="{{ $item->quantity }}" min="1" max="{{ $item->quantity }}" required class="w-full px-2 py-1 rounded bg-white border border-slate-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-500 font-medium mb-0.5">Refund Amount (₹)</label>
                                <input type="number" step="0.01" name="return_items[{{ $idx }}][refund_amount]" value="{{ $item->total_amount }}" required class="w-full px-2 py-1 rounded bg-white border border-slate-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-500 font-medium mb-0.5">Physical Condition</label>
                                <select name="return_items[{{ $idx }}][condition_status]" class="w-full px-2 py-1 rounded bg-white border border-slate-300 text-xs">
                                    <option value="good">Good / Sealed Condition</option>
                                    <option value="damaged">Damaged / Scratched</option>
                                    <option value="defective">Defective / Non-working</option>
                                </select>
                            </div>
                        </div>

                        <div class="pt-1">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="return_items[{{ $idx }}][restore_to_inventory]" value="1" checked class="rounded border-slate-300 text-emerald-600 text-xs">
                                <span class="ml-1.5 text-slate-700 font-medium">Restore returned item into active sellable store inventory</span>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="pt-2 flex items-center justify-end gap-2">
            <a href="{{ route('sales.show', $sale->id) }}" class="px-3 py-1.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700">Cancel</a>
            <button type="submit" class="px-4 py-1.5 rounded bg-rose-600 hover:bg-rose-500 text-white font-bold shadow-sm">
                Confirm Return & Issue Refund
            </button>
        </div>
    </form>
</div>
@endsection
