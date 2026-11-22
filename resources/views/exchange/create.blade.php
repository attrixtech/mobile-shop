@extends('layouts.app')

@section('title', 'Evaluate Device - Webotapp Mobile Shop ERP')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">Evaluate Customer Smartphone for Trade-In</h1>
            <p class="text-xs text-slate-500">Perform physical and functional inspection, determine trade-in valuation, and generate voucher</p>
        </div>
        <a href="{{ route('exchange.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">&larr; Back to Exchange</a>
    </div>

    <form action="{{ route('exchange.store') }}" method="POST" class="bg-white rounded border border-slate-200 p-4 shadow-sm text-xs space-y-4">
        @csrf

        <!-- Customer & Branch -->
        <div>
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px] mb-2 pb-1 border-b border-slate-100">1. Customer Identification</h3>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Customer *</label>
                    <select name="customer_id" required class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                        <option value="">-- Choose Customer --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Evaluating Branch *</label>
                    <select name="branch_id" required class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Old Device Details -->
        <div>
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px] mb-2 pb-1 border-b border-slate-100">2. Old Smartphone Details</h3>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Brand Name *</label>
                    <input type="text" name="old_brand" required placeholder="e.g. Apple, Samsung, OnePlus" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Model & Storage *</label>
                    <input type="text" name="old_model" required placeholder="e.g. iPhone 12 64GB Blue" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-2 mt-2">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Primary IMEI *</label>
                    <input type="text" name="imei1" required placeholder="15-digit IMEI" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 font-mono text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Secondary IMEI</label>
                    <input type="text" name="imei2" placeholder="Optional IMEI 2" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 font-mono text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Serial Number</label>
                    <input type="text" name="serial_number" placeholder="Optional S/N" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 font-mono text-xs">
                </div>
            </div>
        </div>

        <!-- Physical & Functional Inspection -->
        <div>
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px] mb-2 pb-1 border-b border-slate-100">3. Diagnostic Condition Check</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Screen Condition</label>
                    <select name="condition_screen" class="w-full px-2 py-1 rounded bg-slate-50 border border-slate-300 text-xs">
                        <option value="Flawless">Flawless (No Scratches)</option>
                        <option value="Minor Scratches" selected>Minor Scratches</option>
                        <option value="Deep Scratches">Deep Scratches</option>
                        <option value="Cracked Glass">Cracked Glass</option>
                        <option value="Display Bleeding">Display Bleeding / Lines</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Battery Health</label>
                    <select name="condition_battery" class="w-full px-2 py-1 rounded bg-slate-50 border border-slate-300 text-xs">
                        <option value="Healthy (>85%)" selected>Healthy (>85%)</option>
                        <option value="Moderate (75-85%)">Moderate (75-85%)</option>
                        <option value="Service Required (<75%)">Service Required (<75%)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Body / Frame</label>
                    <select name="condition_body" class="w-full px-2 py-1 rounded bg-slate-50 border border-slate-300 text-xs">
                        <option value="Like New">Like New</option>
                        <option value="Normal Wear" selected>Normal Wear & Tear</option>
                        <option value="Dented / Bent">Dented / Heavy Scratches</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Functional Status</label>
                    <select name="functional_status" class="w-full px-2 py-1 rounded bg-slate-50 border border-slate-300 text-xs">
                        <option value="Fully Functional" selected>Fully Functional</option>
                        <option value="Camera Defect">Camera Defect</option>
                        <option value="Speaker/Mic Defect">Speaker / Mic Defect</option>
                        <option value="Network/WiFi Defect">Network / Wi-Fi Defect</option>
                        <option value="Dead / No Power">Dead / No Power</option>
                    </select>
                </div>
            </div>

            <div class="mt-2">
                <label class="block font-semibold text-slate-700 mb-1">Accessories Received with Device</label>
                <input type="text" name="accessories_received" placeholder="e.g. Original Box, Charging Cable, Original Purchase Invoice" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
            </div>
        </div>

        <!-- Valuation & Grading -->
        <div>
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px] mb-2 pb-1 border-b border-slate-100">4. Valuation & Resale Inventory Setup</h3>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Estimated Value (₹) *</label>
                    <input type="number" step="0.01" name="estimated_value" required placeholder="0.00" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Approved Trade-In Value (₹) *</label>
                    <input type="number" step="0.01" name="approved_value" required placeholder="0.00" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Inventory Grading *</label>
                    <select name="grading" required class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                        <option value="A_flawless">A - Flawless (Pristine)</option>
                        <option value="B_good" selected>B - Good (Minor scratches)</option>
                        <option value="C_fair">C - Fair (Noticeable wear)</option>
                        <option value="defective">Defective (Requires repair)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Expected Resale Price (₹) *</label>
                    <input type="number" step="0.01" name="expected_selling_price" required placeholder="0.00" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
            </div>

            <div class="mt-2">
                <label class="block font-semibold text-slate-700 mb-1">Inspection Notes / Evaluator Remarks</label>
                <textarea name="notes" rows="2" placeholder="Tested cameras, fingerprint scanner, and battery life. Device approved for POS trade-in voucher." class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs"></textarea>
            </div>
        </div>

        <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
            <a href="{{ route('exchange.index') }}" class="px-3 py-1.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700">Cancel</a>
            <button type="submit" class="px-4 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-sm">
                Approve Trade-In & Generate Voucher
            </button>
        </div>
    </form>
</div>
@endsection
