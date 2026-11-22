@extends('layouts.app')

@section('title', 'New Purchase & GRN - thrumble.mobile-shop')

@section('content')
<div class="max-w-5xl mx-auto space-y-4" x-data="purchaseForm()">
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">Create Purchase Order / Goods Receipt Note (GRN)</h1>
            <p class="text-xs text-slate-500">Record distributor invoice, update stock balances, and register smartphone IMEIs</p>
        </div>
        <a href="{{ route('purchases.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">&larr; Back to Purchases</a>
    </div>

    <form action="{{ route('purchases.store') }}" method="POST" class="bg-white rounded border border-slate-200 p-4 shadow-sm text-xs space-y-4">
        @csrf

        <!-- Supplier & Meta Info -->
        <div>
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px] mb-2 pb-1 border-b border-slate-100">1. Supplier & Invoice Details</h3>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Select Supplier *</label>
                    <select name="supplier_id" required class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                        <option value="">-- Choose Supplier --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->name }} ({{ $sup->business_name ?: 'Distributor' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Receiving Branch *</label>
                    <select name="branch_id" required class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Supplier Invoice No</label>
                    <input type="text" name="supplier_invoice_no" placeholder="e.g. ING-2026-9041" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Invoice Date *</label>
                    <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" required class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
            </div>
        </div>

        <!-- Purchase Line Items -->
        <div>
            <div class="flex items-center justify-between pb-1 border-b border-slate-100 mb-2">
                <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">2. Purchased Products & Serialized IMEIs</h3>
                <button type="button" @click="addItem()" class="px-2.5 py-1 rounded bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-semibold text-xs">
                    + Add Product Row
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, idx) in items" :key="idx">
                    <div class="p-3 rounded bg-slate-50 border border-slate-200 space-y-2">
                        <div class="grid grid-cols-1 sm:grid-cols-5 gap-2 items-end">
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] text-slate-500 font-medium mb-0.5">Product *</label>
                                <select :name="'items['+idx+'][product_id]'" x-model="item.product_id" @change="onProductSelect(idx)" required class="w-full px-2 py-1 rounded bg-white border border-slate-300 text-xs">
                                    <option value="">-- Choose Product --</option>
                                    @foreach($products as $prod)
                                        <option value="{{ $prod->id }}" data-cost="{{ $prod->purchase_price }}" data-serialized="{{ $prod->isSerialized() ? '1' : '0' }}">
                                            {{ $prod->name }} [{{ $prod->isSerialized() ? 'Serialized' : 'Standard' }}]
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 font-medium mb-0.5">Quantity *</label>
                                <input type="number" :name="'items['+idx+'][quantity]'" x-model.number="item.quantity" min="1" required class="w-full px-2 py-1 rounded bg-white border border-slate-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-500 font-medium mb-0.5">Unit Cost (₹) *</label>
                                <input type="number" step="0.01" :name="'items['+idx+'][unit_cost]'" x-model.number="item.unit_cost" min="0" required class="w-full px-2 py-1 rounded bg-white border border-slate-300 text-xs">
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="block text-[10px] text-slate-500 font-medium mb-0.5">Line Total</label>
                                    <div class="font-bold text-slate-900 text-xs mt-1">₹<span x-text="(item.quantity * item.unit_cost).toFixed(2)"></span></div>
                                </div>
                                <button type="button" @click="removeItem(idx)" class="text-rose-600 hover:text-rose-800 text-xs font-semibold">Remove</button>
                            </div>
                        </div>

                        <!-- IMEI Textarea if serialized -->
                        <div x-show="item.is_serialized">
                            <label class="block text-[10px] text-indigo-700 font-medium mb-0.5 flex items-center justify-between">
                                <span>Scan / Paste IMEIs (1 per line or comma-separated) *</span>
                                <span class="text-[9px] text-slate-500" x-text="'Required count: ' + item.quantity"></span>
                            </label>
                            <textarea
                                :name="'items['+idx+'][imeis]'"
                                x-model="item.imeis"
                                rows="2"
                                placeholder="Scan barcodes or paste IMEIs here (e.g. 356789101234567)"
                                class="w-full px-2.5 py-1.5 rounded bg-white border border-indigo-200 font-mono text-[11px] text-slate-800 placeholder-slate-400"
                            ></textarea>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Payments & Total Summary -->
        <div class="pt-2 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-2">
                <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Payment to Supplier</h3>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-slate-600 font-medium mb-1">Paid Amount (₹)</label>
                        <input type="number" step="0.01" name="paid_amount" placeholder="0.00" class="w-full px-2 py-1 rounded bg-slate-50 border border-slate-300 text-xs">
                    </div>
                    <div>
                        <label class="block text-slate-600 font-medium mb-1">Payment Method</label>
                        <select name="payment_method" class="w-full px-2 py-1 rounded bg-slate-50 border border-slate-300 text-xs">
                            <option value="bank_transfer">Bank Transfer / NEFT / RTGS</option>
                            <option value="cheque">Cheque</option>
                            <option value="upi">UPI</option>
                            <option value="cash">Cash</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-slate-600 font-medium mb-1">Payment Reference / UTR No</label>
                    <input type="text" name="payment_reference" placeholder="e.g. UTR / NEFT Ref Number" class="w-full px-2 py-1 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
            </div>

            <div class="p-3 bg-slate-50 rounded border border-slate-200 flex flex-col justify-between">
                <div class="space-y-1.5">
                    <div class="flex justify-between text-slate-600">
                        <span>Items Subtotal:</span>
                        <span>₹<span x-text="calculateSubtotal().toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Est. GST (18%):</span>
                        <span>₹<span x-text="(calculateSubtotal() * 0.18).toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between font-bold text-slate-900 text-sm pt-1 border-t border-slate-300">
                        <span>Est. Grand Total:</span>
                        <span class="text-indigo-600 text-base">₹<span x-text="(calculateSubtotal() * 1.18).toFixed(2)"></span></span>
                    </div>
                </div>
                <div class="pt-3 flex justify-end gap-2">
                    <a href="{{ route('purchases.index') }}" class="px-3 py-1.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700">Cancel</a>
                    <button type="submit" class="px-4 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-sm">
                        Confirm GRN & Receive Stock
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function purchaseForm() {
    return {
        items: [
            { product_id: '', quantity: 1, unit_cost: 0, is_serialized: false, imeis: '' }
        ],

        addItem() {
            this.items.push({ product_id: '', quantity: 1, unit_cost: 0, is_serialized: false, imeis: '' });
        },

        removeItem(idx) {
            if (this.items.length > 1) {
                this.items.splice(idx, 1);
            }
        },

        onProductSelect(idx) {
            const select = event.target;
            const opt = select.options[select.selectedIndex];
            if (opt) {
                this.items[idx].unit_cost = opt.dataset.cost ? parseFloat(opt.dataset.cost) : 0;
                this.items[idx].is_serialized = opt.dataset.serialized === '1';
            }
        },

        calculateSubtotal() {
            return this.items.reduce((sum, i) => sum + ((i.quantity || 0) * (i.unit_cost || 0)), 0);
        }
    };
}
</script>
@endpush
@endsection
