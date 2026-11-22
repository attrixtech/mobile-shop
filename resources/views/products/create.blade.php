@extends('layouts.app')

@section('title', 'Add Product - Webotapp Mobile Shop ERP')

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">Register New Product or Smartphone Model</h1>
            <p class="text-xs text-slate-500">Configure smartphone hardware specifications, pricing, HSN, and GST rates</p>
        </div>
        <a href="{{ route('products.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">&larr; Back to Catalog</a>
    </div>

    <form action="{{ route('products.store') }}" method="POST" class="bg-white rounded border border-slate-200 p-4 shadow-sm text-xs space-y-4" x-data="{
        type: 'serialized',
        variants: []
    }">
        @csrf

        <!-- Product Classification -->
        <div>
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px] mb-2 pb-1 border-b border-slate-100">1. Product Identity</h3>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="sm:col-span-2">
                    <label class="block font-semibold text-slate-700 mb-1">Product / Model Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Apple iPhone 15 Pro, Samsung Galaxy S24" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Product Type *</label>
                    <select name="type" x-model="type" required class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                        <option value="serialized">Serialized Device (Track by IMEI)</option>
                        <option value="standard">Standard Non-serialized (Accessories)</option>
                        <option value="service">Repair Service / Labor Charge</option>
                        <option value="bundle">Product Combo Bundle</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Brand</label>
                    <select name="brand_id" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                        <option value="">-- Choose Brand --</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 mt-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Category</label>
                    <select name="category_id" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                        <option value="">-- Choose Category --</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Model Code</label>
                    <input type="text" name="code" placeholder="e.g. A2848, SM-S928B" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">SKU</label>
                    <input type="text" name="sku" placeholder="e.g. IPH-15P-BASE" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Barcode / EAN</label>
                    <input type="text" name="barcode" placeholder="Scan or enter barcode" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
            </div>
        </div>

        <!-- Smartphone Hardware Specifications (Visible when serialized) -->
        <div x-show="type === 'serialized'">
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px] mb-2 pb-1 border-b border-slate-100 flex items-center justify-between">
                <span>2. Smartphone Hardware Specifications</span>
                <span class="text-[10px] text-indigo-600 font-normal">Captured for mobile customer specs</span>
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div>
                    <label class="block text-slate-600 font-medium mb-1">Processor</label>
                    <input type="text" name="processor" placeholder="e.g. Snapdragon 8 Gen 3" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block text-slate-600 font-medium mb-1">Display Size</label>
                    <input type="text" name="display_size" placeholder="e.g. 6.7 inch AMOLED 120Hz" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block text-slate-600 font-medium mb-1">Battery</label>
                    <input type="text" name="battery" placeholder="e.g. 5000 mAh 45W Fast" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block text-slate-600 font-medium mb-1">Operating System</label>
                    <input type="text" name="os" placeholder="e.g. Android 14 / iOS 17" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
            </div>
        </div>

        <!-- Pricing, Indian GST & Stock Thresholds -->
        <div>
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px] mb-2 pb-1 border-b border-slate-100">3. Pricing, GST & Inventory Controls</h3>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Purchase Cost (₹) *</label>
                    <input type="number" step="0.01" name="purchase_price" required placeholder="0.00" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Retail Selling Price (₹) *</label>
                    <input type="number" step="0.01" name="selling_price" required placeholder="0.00" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">MRP (₹)</label>
                    <input type="number" step="0.01" name="mrp" placeholder="0.00" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Wholesale Price (₹)</label>
                    <input type="number" step="0.01" name="wholesale_price" placeholder="0.00" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 mt-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">HSN Code</label>
                    <input type="text" name="hsn_code" value="85171300" placeholder="85171300 for mobiles" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">GST Tax Rate (%) *</label>
                    <select name="gst_rate" required class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                        <option value="18.00">18.00% (Standard Mobile GST)</option>
                        <option value="12.00">12.00%</option>
                        <option value="28.00">28.00%</option>
                        <option value="5.00">5.00%</option>
                        <option value="0.00">0.00% (Exempt)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Min. Stock Alert Level</label>
                    <input type="number" name="min_stock" value="2" min="0" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Warranty (Months)</label>
                    <input type="number" name="warranty_months" value="12" min="0" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
            </div>
        </div>

        <!-- Optional RAM/Storage/Color Variants -->
        <div x-show="type === 'serialized'">
            <div class="flex items-center justify-between pb-1 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">4. Storage & Color Variants (Optional)</h3>
                <button type="button" @click="variants.push({ name: '', ram: '', storage: '', color: '', purchase_price: '', selling_price: '' })" class="text-[11px] text-indigo-600 font-semibold hover:text-indigo-800">
                    + Add Variant Row
                </button>
            </div>

            <div class="space-y-2 mt-2">
                <template x-for="(v, index) in variants" :key="index">
                    <div class="grid grid-cols-6 gap-2 p-2 rounded bg-slate-50 border border-slate-200 items-end">
                        <div class="col-span-2">
                            <label class="block text-[10px] text-slate-500 mb-0.5">Variant Name</label>
                            <input type="text" :name="'variants['+index+'][name]'" x-model="v.name" placeholder="e.g. 8GB/128GB Black" class="w-full px-2 py-1 rounded bg-white border border-slate-300 text-xs">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-0.5">RAM</label>
                            <input type="text" :name="'variants['+index+'][ram]'" x-model="v.ram" placeholder="8GB" class="w-full px-2 py-1 rounded bg-white border border-slate-300 text-xs">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-0.5">Storage</label>
                            <input type="text" :name="'variants['+index+'][storage]'" x-model="v.storage" placeholder="128GB" class="w-full px-2 py-1 rounded bg-white border border-slate-300 text-xs">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-0.5">Color</label>
                            <input type="text" :name="'variants['+index+'][color]'" x-model="v.color" placeholder="Black" class="w-full px-2 py-1 rounded bg-white border border-slate-300 text-xs">
                        </div>
                        <div class="text-right">
                            <button type="button" @click="variants.splice(index, 1)" class="px-2 py-1 text-rose-600 hover:text-rose-800 text-xs font-semibold">Remove</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
            <a href="{{ route('products.index') }}" class="px-3 py-1.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700">Cancel</a>
            <button type="submit" class="px-4 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-sm">
                Save & Register Product
            </button>
        </div>
    </form>
</div>
@endsection
