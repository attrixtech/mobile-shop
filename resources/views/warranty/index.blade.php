@extends('layouts.app')

@section('title', 'IMEI Warranty Registry - thrumble.mobile-shop')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">IMEI Warranty Registry & Verification</h1>
            <p class="text-xs text-slate-500">Lookup manufacturer coverage, shop warranty terms, and active customer devices</p>
        </div>
    </div>

    <!-- Quick IMEI Warranty Lookup Search Box -->
    <div class="bg-slate-900 text-white rounded p-4 shadow-sm">
        <form action="{{ route('warranty.index') }}" method="GET" class="max-w-xl mx-auto space-y-2 text-center">
            <h3 class="text-xs font-semibold text-slate-200 uppercase tracking-wider">Instant IMEI Warranty & Ownership Lookup</h3>
            <div class="flex items-center gap-2">
                <input
                    type="text"
                    name="imei"
                    value="{{ $searchImei }}"
                    required
                    placeholder="Enter 15-digit IMEI or Serial number to check warranty..."
                    class="flex-1 px-3 py-2 rounded bg-slate-800 border border-slate-700 text-white placeholder-slate-400 font-mono text-xs focus:outline-none focus:border-indigo-500"
                >
                <button type="submit" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md">
                    Check Warranty
                </button>
            </div>
            @if($searchImei)
                <div class="text-[11px] text-slate-400">
                    <a href="{{ route('warranty.index') }}" class="underline hover:text-white">Clear search</a>
                </div>
            @endif
        </form>
    </div>

    <!-- Search Result Card if Searched -->
    @if($searchImei)
        @if($foundRecord)
            <div class="bg-white rounded border border-indigo-200 p-4 shadow-sm text-xs space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full {{ $foundRecord->isUnderWarranty() ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                        <h3 class="font-bold text-slate-900 text-sm">
                            {{ $foundRecord->isUnderWarranty() ? 'DEVICE IS UNDER VALID WARRANTY' : 'DEVICE WARRANTY HAS EXPIRED' }}
                        </h3>
                    </div>
                    <span class="px-2 py-0.5 rounded font-mono text-xs {{ $foundRecord->isUnderWarranty() ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                        {{ $foundRecord->warranty_end ? 'Expires: ' . $foundRecord->warranty_end->format('d M, Y') : 'No warranty date' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <div class="text-slate-400 text-[10px] uppercase font-bold">Device & Model</div>
                        <div class="font-bold text-slate-900 text-sm mt-0.5">{{ $foundRecord->product?->name }}</div>
                        <div class="font-mono text-slate-600">IMEI: {{ $foundRecord->imei1 }}</div>
                        @if($foundRecord->imei2)
                            <div class="font-mono text-slate-400">IMEI 2: {{ $foundRecord->imei2 }}</div>
                        @endif
                    </div>
                    <div>
                        <div class="text-slate-400 text-[10px] uppercase font-bold">Customer & Invoice</div>
                        <div class="font-bold text-slate-900 mt-0.5">{{ $foundRecord->customer?->name ?: 'Walk-in Customer' }}</div>
                        <div class="text-slate-600">Phone: {{ $foundRecord->customer?->phone ?: '-' }}</div>
                        @if($foundRecord->sale)
                            <div class="text-indigo-600">
                                <a href="{{ route('sales.show', $foundRecord->sale_id) }}" class="underline font-bold">
                                    Invoice #{{ $foundRecord->sale->invoice_number }}
                                </a>
                            </div>
                        @endif
                    </div>
                    <div>
                        <div class="text-slate-400 text-[10px] uppercase font-bold">Warranty Period</div>
                        <div class="text-slate-700 mt-0.5">Start: <strong>{{ $foundRecord->warranty_start ? $foundRecord->warranty_start->format('d M, Y') : '-' }}</strong></div>
                        <div class="text-slate-700">Expiry: <strong>{{ $foundRecord->warranty_end ? $foundRecord->warranty_end->format('d M, Y') : '-' }}</strong></div>
                        <div class="text-slate-700">Duration: <strong>{{ $foundRecord->warranty_months }} Months Standard Brand Warranty</strong></div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded text-xs text-center">
                <strong>No device found</strong> with IMEI/Serial '{{ $searchImei }}' in the system registry. Please verify the 15-digit number.
            </div>
        @endif
    @endif

    <!-- Active Warranties Registry Table -->
    <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden text-xs">
        <div class="p-2.5 bg-slate-50 border-b border-slate-200 font-bold text-slate-800 uppercase tracking-wider text-[11px]">
            Currently Active Customer Warranties
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left compact-table">
                <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th>Primary IMEI</th>
                        <th>Device Model</th>
                        <th>Customer Name</th>
                        <th>Mobile</th>
                        <th>Sold Date</th>
                        <th>Warranty Expiry Date</th>
                        <th>Days Remaining</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($activeWarranties as $aw)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="font-mono font-bold text-indigo-600">
                                <a href="{{ route('imei.show', $aw->id) }}">{{ $aw->imei1 }}</a>
                            </td>
                            <td class="font-bold text-slate-900">{{ $aw->product?->name }}</td>
                            <td>{{ $aw->customer?->name ?: 'Walk-in' }}</td>
                            <td>{{ $aw->customer?->phone ?: '-' }}</td>
                            <td>{{ $aw->sold_at ? $aw->sold_at->format('d M, Y') : '-' }}</td>
                            <td class="font-bold text-emerald-700">{{ $aw->warranty_end ? $aw->warranty_end->format('d M, Y') : '-' }}</td>
                            <td>
                                @if($aw->warranty_end)
                                    @php $days = (int) now()->diffInDays($aw->warranty_end, false); @endphp
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $days <= 30 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                        {{ $days }} days left
                                    </span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('imei.show', $aw->id) }}" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs">View History</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-6 text-slate-400">No active customer warranties recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-2 border-t border-slate-200">
            {{ $activeWarranties->links() }}
        </div>
    </div>
</div>
@endsection
