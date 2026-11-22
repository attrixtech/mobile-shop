@extends('layouts.app')

@section('title', "Edit Product - {$product->name}")

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">Edit Product: {{ $product->name }}</h1>
            <p class="text-xs text-slate-500">Update rates, specifications, and stock alert levels</p>
        </div>
        <a href="{{ route('products.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">&larr; Back to Catalog</a>
    </div>

    <form action="{{ route('products.update', $product->id) }}" method="POST" class="bg-white rounded border border-slate-200 p-4 shadow-sm text-xs space-y-4">
        @csrf
        @method('PUT')

        <div>
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px] mb-2 pb-1 border-b border-slate-100">1. Product Identity</h3>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="sm:col-span-2">
                    <label class="block font-semibold text-slate-700 mb-1">Product / Model Name *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Product Type *</label>
                    <select name="type" required class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                        <option value="serialized" {{ $product->type === 'serialized' ? 'selected' : '' }}>Serialized (IMEI)</option>
                        <option value="standard" {{ $product->type === 'standard' ? 'selected' : '' }}>Standard (Accessories)</option>
                        <option value="service" {{ $product->type === 'service' ? 'selected' : '' }}>Service / Labor</option>
                        <option value="bundle" {{ $product->type === 'bundle' ? 'selected' : '' }}>Combo Bundle</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Brand</label>
                    <select name="brand_id" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                        <option value="">-- Choose Brand --</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}" {{ $product->brand_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 mt-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Category</label>
                    <select name="category_id" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                        <option value="">-- Choose Category --</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ $product->category_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Model Code</label>
                    <input type="text" name="code" value="{{ old('code', $product->code) }}" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Barcode</label>
                    <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
            </div>
        </div>

        <div>
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px] mb-2 pb-1 border-b border-slate-100">2. Pricing, GST & Stock Controls</h3>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Purchase Cost (₹) *</label>
                    <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" required class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Selling Price (₹) *</label>
                    <input type="number" step="0.01" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" required class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">MRP (₹)</label>
                    <input type="number" step="0.01" name="mrp" value="{{ old('mrp', $product->mrp) }}" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Wholesale (₹)</label>
                    <input type="number" step="0.01" name="wholesale_price" value="{{ old('wholesale_price', $product->wholesale_price) }}" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 mt-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">HSN Code</label>
                    <input type="text" name="hsn_code" value="{{ old('hsn_code', $product->hsn_code) }}" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">GST Tax Rate (%)</label>
                    <select name="gst_rate" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                        <option value="18.00" {{ $product->gst_rate == 18 ? 'selected' : '' }}>18.00%</option>
                        <option value="12.00" {{ $product->gst_rate == 12 ? 'selected' : '' }}>12.00%</option>
                        <option value="28.00" {{ $product->gst_rate == 28 ? 'selected' : '' }}>28.00%</option>
                        <option value="5.00" {{ $product->gst_rate == 5 ? 'selected' : '' }}>5.00%</option>
                        <option value="0.00" {{ $product->gst_rate == 0 ? 'selected' : '' }}>0.00%</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Min. Stock Alert</label>
                    <input type="number" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Warranty (Months)</label>
                    <input type="number" name="warranty_months" value="{{ old('warranty_months', $product->warranty_months) }}" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
            </div>
        </div>

        <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
            <button type="button" onclick="if(confirm('Delete product?')) document.getElementById('deleteForm').submit()" class="text-rose-600 hover:text-rose-800 text-xs font-semibold">Delete Product</button>
            <div class="flex items-center gap-2">
                <a href="{{ route('products.index') }}" class="px-3 py-1.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700">Cancel</a>
                <button type="submit" class="px-4 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-sm">
                    Save Changes
                </button>
            </div>
        </div>
    </form>

    <form id="deleteForm" action="{{ route('products.destroy', $product->id) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection
