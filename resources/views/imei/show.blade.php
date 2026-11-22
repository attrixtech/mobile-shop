@extends('layouts.app')

@section('title', "IMEI Audit Trail - {$imei->imei1}")

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-base font-bold text-slate-900 tracking-tight font-mono">IMEI: {{ $imei->imei1 }}</h1>
                <span class="px-2 py-0.5 rounded text-[11px] font-semibold uppercase
                    {{ $imei->status === 'in_stock' ? 'bg-emerald-100 text-emerald-700' : '' }}
                    {{ $imei->status === 'sold' ? 'bg-blue-100 text-blue-700' : '' }}
                    {{ $imei->status === 'reserved' ? 'bg-amber-100 text-amber-700' : '' }}
                    {{ $imei->status === 'under_repair' ? 'bg-rose-100 text-rose-700' : '' }}
                    {{ $imei->status === 'damaged' ? 'bg-red-100 text-red-700' : '' }}
                    {{ $imei->status === 'transferred' ? 'bg-purple-100 text-purple-700' : '' }}">
                    {{ str_replace('_', ' ', $imei->status) }}
                </span>
            </div>
            <p class="text-xs text-slate-500">Immutable lifecycle audit trail and ownership records</p>
        </div>
        <a href="{{ route('imei.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">&larr; Back to IMEI Registry</a>
    </div>

    <!-- Device Overview Card -->
    <div class="bg-white rounded border border-slate-200 p-4 shadow-sm text-xs grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px] mb-1">Hardware Information</h3>
            <div class="font-bold text-slate-900 text-sm">{{ $imei->product?->name }}</div>
            @if($imei->variant)
                <div class="text-indigo-600 font-medium">{{ $imei->variant->name }}</div>
            @endif
            <div class="mt-2 space-y-0.5 text-slate-500 font-mono">
                <div>Primary IMEI: <span class="text-slate-800 font-bold">{{ $imei->imei1 }}</span></div>
                @if($imei->imei2)
                    <div>Secondary IMEI: <span class="text-slate-800">{{ $imei->imei2 }}</span></div>
                @endif
                @if($imei->serial_number)
                    <div>Serial Number: <span class="text-slate-800">{{ $imei->serial_number }}</span></div>
                @endif
            </div>
        </div>

        <div>
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px] mb-1">Origin & Purchasing</h3>
            <div class="space-y-1 text-slate-600">
                <div>Supplier: <strong class="text-slate-900">{{ $imei->supplier?->name ?: 'Direct Inventory Entry' }}</strong></div>
                @if($imei->purchase)
                    <div>Purchase Order: <a href="{{ route('purchases.show', $imei->purchase_id) }}" class="text-indigo-600 font-bold hover:underline">{{ $imei->purchase->purchase_number }}</a></div>
                    <div>Purchase Date: {{ $imei->purchase->invoice_date->format('d M, Y') }}</div>
                @endif
                <div>Purchase Cost: <strong>₹{{ number_format($imei->purchase_cost, 2) }}</strong></div>
                <div>Current Location: <strong>{{ $imei->branch?->name }}</strong></div>
            </div>
        </div>

        <div>
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px] mb-1">Customer & Warranty</h3>
            <div class="space-y-1 text-slate-600">
                @if($imei->customer)
                    <div>Owner: <strong class="text-slate-900">{{ $imei->customer->name }}</strong></div>
                    <div>Phone: {{ $imei->customer->phone }}</div>
                    @if($imei->sale)
                        <div>Sold Via: <a href="{{ route('sales.show', $imei->sale_id) }}" class="text-indigo-600 font-bold hover:underline">{{ $imei->sale->invoice_number }}</a></div>
                        <div>Sold Date: {{ $imei->sold_at ? $imei->sold_at->format('d M, Y H:i') : '-' }}</div>
                    @endif
                @else
                    <div class="text-slate-400 italic">Unassigned (In Store Inventory)</div>
                @endif

                <div class="pt-1 border-t border-slate-100">
                    <div>Warranty End: <strong>{{ $imei->warranty_end ? $imei->warranty_end->format('d M, Y') : 'N/A' }}</strong></div>
                    <div>Status: <span class="font-bold {{ $imei->isUnderWarranty() ? 'text-emerald-600' : 'text-slate-400' }}">{{ $imei->isUnderWarranty() ? 'Active Manufacturer Warranty' : 'Warranty Expired' }}</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Complete Lifecycle Timeline -->
    <div class="bg-white rounded border border-slate-200 p-4 shadow-sm text-xs space-y-3">
        <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px] pb-1 border-b border-slate-100 flex items-center justify-between">
            <span>Chronological Lifecycle Audit Log</span>
            <span class="text-[10px] text-slate-400 font-normal">Tamper-evident history</span>
        </h3>

        <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
            @forelse($imei->movements as $m)
                <div class="relative">
                    <!-- Circle indicator -->
                    <div class="absolute -left-6 top-0.5 w-4 h-4 rounded-full border-2 border-white bg-indigo-600 shadow-sm flex items-center justify-center"></div>

                    <div class="bg-slate-50 p-2.5 rounded border border-slate-200 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-indigo-700 uppercase tracking-wider text-[10px]">
                                {{ str_replace('_', ' ', $m->action) }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-medium">
                                {{ $m->created_at->format('d M Y, h:i A') }}
                            </span>
                        </div>

                        <div class="text-slate-700 font-medium text-xs">
                            {{ $m->notes }}
                        </div>

                        <div class="flex items-center gap-3 text-[10px] text-slate-500 pt-1 border-t border-slate-100">
                            @if($m->from_branch_id)
                                <span>From: {{ $m->fromBranch?->name }}</span>
                            @endif
                            @if($m->to_branch_id)
                                <span>To: {{ $m->toBranch?->name }}</span>
                            @endif
                            @if($m->reference_type)
                                <span>Ref: {{ $m->reference_type }} #{{ $m->reference_id }}</span>
                            @endif
                            @if($m->user)
                                <span>Actioned by: <strong>{{ $m->user->name }}</strong></span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-slate-400 text-xs">No historical movements registered yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
