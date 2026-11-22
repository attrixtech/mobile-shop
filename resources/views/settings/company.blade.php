@extends('layouts.app')

@section('title', 'Company Profile & Indian GST Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Company & Tax Settings</h1>
            <p class="text-xs text-slate-500">Configure Indian business legal details, GSTIN, invoice numbering and address</p>
        </div>
    </div>

    <form method="POST" action="{{ route('settings.company.update') }}" class="card p-5 space-y-4 bg-white">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="form-label text-xs font-semibold text-slate-700">Display Business / Store Name *</label>
                <input type="text" name="name" class="form-input text-xs" required value="{{ old('name', $company->name ?? '') }}">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label text-xs font-semibold text-slate-700">Legal Registered Name</label>
                <input type="text" name="legal_name" class="form-input text-xs" value="{{ old('legal_name', $company->legal_name ?? '') }}" placeholder="e.g. thrumble.mobile-shop Retail LLP">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2 border-t border-slate-100">
            <div>
                <label class="form-label text-xs font-semibold text-slate-700">Indian GSTIN</label>
                <input type="text" name="gstin" class="form-input text-xs uppercase" maxlength="15" value="{{ old('gstin', $company->gstin ?? '') }}" placeholder="27ABCDE1234F1Z5">
            </div>
            <div>
                <label class="form-label text-xs font-semibold text-slate-700">Support Phone</label>
                <input type="text" name="phone" class="form-input text-xs" value="{{ old('phone', $company->phone ?? '') }}">
            </div>
            <div>
                <label class="form-label text-xs font-semibold text-slate-700">Official Email</label>
                <input type="email" name="email" class="form-input text-xs" value="{{ old('email', $company->email ?? '') }}">
            </div>
        </div>

        <div class="pt-2 border-t border-slate-100 space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Invoice Configuration & Location</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="form-label text-xs font-semibold text-slate-700">Invoice Prefix *</label>
                    <input type="text" name="invoice_prefix" class="form-input text-xs font-mono uppercase" required value="{{ old('invoice_prefix', $company->invoice_prefix ?? 'INV') }}">
                    <p class="text-[10px] text-slate-400 mt-1">Appears as INV-202611-0001</p>
                </div>
                <div>
                    <label class="form-label text-xs font-semibold text-slate-700">State *</label>
                    <input type="text" name="state" class="form-input text-xs" value="{{ old('state', $company->state ?? 'Maharashtra') }}">
                </div>
                <div>
                    <label class="form-label text-xs font-semibold text-slate-700">GST State Code *</label>
                    <input type="text" name="state_code" class="form-input text-xs font-mono" maxlength="2" value="{{ old('state_code', $company->state_code ?? '27') }}">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="form-label text-xs font-semibold text-slate-700">Registered Office Address</label>
                    <input type="text" name="address" class="form-input text-xs" value="{{ old('address', $company->address ?? '') }}">
                </div>
                <div>
                    <label class="form-label text-xs font-semibold text-slate-700">Pincode</label>
                    <input type="text" name="pincode" class="form-input text-xs" value="{{ old('pincode', $company->pincode ?? '') }}">
                </div>
            </div>
        </div>

        <div class="pt-3 border-t border-slate-100 flex justify-end">
            <button type="submit" class="btn btn-primary text-xs px-5">Save Company Settings</button>
        </div>
    </form>
</div>
@endsection
