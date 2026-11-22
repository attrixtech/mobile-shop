@extends('layouts.app')

@section('title', 'Add Supplier - Webotapp Mobile Shop ERP')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">Register New Supplier / Distributor</h1>
            <p class="text-xs text-slate-500">Record distributor company profile and GST identification number</p>
        </div>
        <a href="{{ route('suppliers.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">&larr; Back to Suppliers</a>
    </div>

    <form action="{{ route('suppliers.store') }}" method="POST" class="bg-white rounded border border-slate-200 p-4 shadow-sm text-xs space-y-3">
        @csrf
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Company / Supplier Name *</label>
                <input type="text" name="name" required placeholder="e.g. Ingram Micro India Pvt Ltd" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Trading / Brand Name</label>
                <input type="text" name="business_name" placeholder="e.g. Ingram Micro Distribution" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Contact Person</label>
                <input type="text" name="contact_person" placeholder="Manager / Sales Rep" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Phone Number *</label>
                <input type="text" name="phone" required placeholder="Mobile / Office Phone" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block font-semibold text-slate-700 mb-1">GSTIN</label>
                <input type="text" name="gstin" placeholder="15-digit GSTIN" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 font-mono text-xs">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Email</label>
                <input type="email" name="email" placeholder="orders@supplier.com" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
            </div>
        </div>

        <div>
            <label class="block font-semibold text-slate-700 mb-1">Address</label>
            <textarea name="address" rows="2" placeholder="Warehouse / Office address" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs"></textarea>
        </div>

        <div class="grid grid-cols-3 gap-2">
            <div>
                <label class="block font-semibold text-slate-700 mb-1">City</label>
                <input type="text" name="city" value="Mumbai" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">State</label>
                <input type="text" name="state" value="Maharashtra" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">State Code</label>
                <input type="text" name="state_code" value="27" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
            </div>
        </div>

        <div>
            <label class="block font-semibold text-slate-700 mb-1">Opening Payable Balance (₹)</label>
            <input type="number" step="0.01" name="opening_balance" value="0.00" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
        </div>

        <div class="pt-2 flex justify-end gap-2 border-t border-slate-100">
            <a href="{{ route('suppliers.index') }}" class="px-3 py-1.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700">Cancel</a>
            <button type="submit" class="px-4 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white font-bold">Register Supplier</button>
        </div>
    </form>
</div>
@endsection
