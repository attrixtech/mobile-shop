@extends('layouts.app')

@section('title', 'Product Catalog - Webotapp Mobile Shop ERP')

@section('content')
<div class="space-y-3">
    <!-- Top Header -->
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">Product Catalog & Device Models</h1>
            <p class="text-xs text-slate-500">Manage smartphones, accessories, service packages, and stock thresholds</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('products.create') }}" class="px-3 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-sm flex items-center gap-1.5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Add New Product</span>
            </a>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-2.5 rounded border border-slate-200 shadow-sm text-xs">
        <form action="{{ route('products.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-5 gap-2">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, barcode, SKU..." class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <select name="category_id" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="brand_id" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                    <option value="">All Brands</option>
                    @foreach($brands as $b)
                        <option value="{{ $b->id }}" {{ request('brand_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="type" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                    <option value="">All Types</option>
                    <option value="serialized" {{ request('type') === 'serialized' ? 'selected' : '' }}>Serialized (Smartphones)</option>
                    <option value="standard" {{ request('type') === 'standard' ? 'selected' : '' }}>Standard (Accessories)</option>
                    <option value="service" {{ request('type') === 'service' ? 'selected' : '' }}>Service / Labor</option>
                    <option value="bundle" {{ request('type') === 'bundle' ? 'selected' : '' }}>Combo Bundle</option>
                </select>
            </div>
            <div class="flex items-center gap-1.5">
                <button type="submit" class="px-3 py-1.5 rounded bg-indigo-600 text-white font-medium hover:bg-indigo-500 text-xs">Filter</button>
                <a href="{{ route('products.index') }}" class="px-2.5 py-1.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200 text-xs">Reset</a>
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden text-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left compact-table">
                <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th>Product & Model</th>
                        <th>Category / Brand</th>
                        <th>Type</th>
                        <th>HSN</th>
                        <th>Purchase Cost</th>
                        <th>Selling Price</th>
                        <th>MRP</th>
                        <th>Branch Stock</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $p)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td>
                                <div class="font-bold text-slate-900">{{ $p->name }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">
                                    SKU: {{ $p->sku ?: '-' }} | Barcode: {{ $p->barcode ?: '-' }}
                                </div>
                                @if($p->variants->count() > 0)
                                    <div class="text-[10px] text-indigo-600 font-medium mt-0.5">
                                        {{ $p->variants->count() }} variant(s): {{ $p->variants->pluck('name')->join(', ') }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="font-medium text-slate-800">{{ $p->category?->name ?: 'Uncategorized' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $p->brand?->name ?: 'No Brand' }}</div>
                            </td>
                            <td>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase
                                    {{ $p->type === 'serialized' ? 'bg-indigo-100 text-indigo-700' : '' }}
                                    {{ $p->type === 'standard' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                    {{ $p->type === 'service' ? 'bg-rose-100 text-rose-700' : '' }}
                                    {{ $p->type === 'bundle' ? 'bg-amber-100 text-amber-700' : '' }}">
                                    {{ $p->type }}
                                </span>
                            </td>
                            <td class="font-mono text-slate-500">{{ $p->hsn_code }}</td>
                            <td>₹{{ number_format($p->purchase_price, 2) }}</td>
                            <td class="font-bold text-slate-900">₹{{ number_format($p->selling_price, 2) }}</td>
                            <td class="text-slate-500">₹{{ number_format($p->mrp, 2) }}</td>
                            <td>
                                @php $stock = $p->totalStockForBranch($branchId); @endphp
                                <span class="px-2 py-0.5 rounded font-bold text-xs
                                    {{ $stock <= $p->min_stock ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ $stock }} {{ $p->unit }}
                                </span>
                            </td>
                            <td class="text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('products.edit', $p->id) }}" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 text-slate-400">No products found matching filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-2 border-t border-slate-200">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
