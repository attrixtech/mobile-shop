@extends('layouts.app')

@section('title', 'Add Customer')

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Add New Customer</h1>
            <p class="text-xs text-slate-500">Register retail, wholesale, or corporate customer with GST details</p>
        </div>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary text-xs">
            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Customers
        </a>
    </div>

    <form method="POST" action="{{ route('customers.store') }}" class="card p-5 space-y-4 bg-white">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="form-label text-xs font-semibold text-slate-700">Customer Name *</label>
                <input type="text" name="name" class="form-input text-xs" required placeholder="Full Name or Business Name" value="{{ old('name') }}">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label text-xs font-semibold text-slate-700">Phone Number *</label>
                <input type="text" name="phone" class="form-input text-xs" required placeholder="10-digit mobile" value="{{ old('phone') }}">
                @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label text-xs font-semibold text-slate-700">Email Address</label>
                <input type="email" name="email" class="form-input text-xs" placeholder="customer@example.com" value="{{ old('email') }}">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2 border-t border-slate-100">
            <div>
                <label class="form-label text-xs font-semibold text-slate-700">Customer Type *</label>
                <select name="customer_type" class="form-input text-xs" required>
                    <option value="retail" {{ old('customer_type') == 'retail' ? 'selected' : '' }}>Retail (B2C)</option>
                    <option value="wholesale" {{ old('customer_type') == 'wholesale' ? 'selected' : '' }}>Wholesale (B2B)</option>
                    <option value="corporate" {{ old('customer_type') == 'corporate' ? 'selected' : '' }}>Corporate</option>
                </select>
            </div>
            <div>
                <label class="form-label text-xs font-semibold text-slate-700">GSTIN (Optional for B2B)</label>
                <input type="text" name="gstin" class="form-input text-xs uppercase" placeholder="27AAAAA0000A1Z5" value="{{ old('gstin') }}" maxlength="15">
                @error('gstin')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label text-xs font-semibold text-slate-700">Credit Limit (₹)</label>
                <input type="number" step="0.01" name="credit_limit" class="form-input text-xs" placeholder="0.00" value="{{ old('credit_limit', 0) }}">
            </div>
        </div>

        <div class="pt-2 border-t border-slate-100 space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Billing Address</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="form-label text-xs font-semibold text-slate-700">Street Address</label>
                    <input type="text" name="address" class="form-input text-xs" placeholder="Shop / House No, Street name" value="{{ old('address') }}">
                </div>
                <div>
                    <label class="form-label text-xs font-semibold text-slate-700">City</label>
                    <input type="text" name="city" class="form-input text-xs" placeholder="e.g. Mumbai" value="{{ old('city') }}">
                </div>
                <div>
                    <label class="form-label text-xs font-semibold text-slate-700">State</label>
                    <input type="text" name="state" class="form-input text-xs" placeholder="Maharashtra" value="{{ old('state', 'Maharashtra') }}">
                </div>
                <div>
                    <label class="form-label text-xs font-semibold text-slate-700">State Code (GST)</label>
                    <input type="text" name="state_code" class="form-input text-xs" placeholder="27" value="{{ old('state_code', '27') }}" maxlength="2">
                </div>
                <div>
                    <label class="form-label text-xs font-semibold text-slate-700">Pincode</label>
                    <input type="text" name="pincode" class="form-input text-xs" placeholder="400053" value="{{ old('pincode') }}">
                </div>
            </div>
        </div>

        <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
            <a href="{{ route('customers.index') }}" class="btn btn-secondary text-xs">Cancel</a>
            <button type="submit" class="btn btn-primary text-xs px-5">Save Customer</button>
        </div>
    </form>
</div>
@endsection
