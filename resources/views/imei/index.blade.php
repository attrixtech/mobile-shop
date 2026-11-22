@extends('layouts.app')

@section('title', 'IMEI Registry & Serial Tracking - thrumble.mobile-shop')

@section('content')
<div class="space-y-3" x-data="{ addModal: false, statusModal: false, selectedImei: null }">
    <!-- Header -->
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">IMEI Registry & Device Serialization</h1>
            <p class="text-xs text-slate-500">End-to-end smartphone lifecycle traceability, warranty dates, and audit trail</p>
        </div>
        <button @click="addModal = true" class="px-3 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-sm flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Register New IMEI</span>
        </button>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-2.5 rounded border border-slate-200 shadow-sm text-xs">
        <form action="{{ route('imei.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-2">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search IMEI 1, IMEI 2, Serial No, Product or Customer..." class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <select name="status" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                    <option value="">All IMEI Statuses</option>
                    <option value="in_stock" {{ request('status') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="sold" {{ request('status') === 'sold' ? 'selected' : '' }}>Sold</option>
                    <option value="reserved" {{ request('status') === 'reserved' ? 'selected' : '' }}>Reserved</option>
                    <option value="under_repair" {{ request('status') === 'under_repair' ? 'selected' : '' }}>Under Repair</option>
                    <option value="damaged" {{ request('status') === 'damaged' ? 'selected' : '' }}>Damaged</option>
                    <option value="transferred" {{ request('status') === 'transferred' ? 'selected' : '' }}>Transferred</option>
                    <option value="written_off" {{ request('status') === 'written_off' ? 'selected' : '' }}>Written Off</option>
                </select>
            </div>
            <div class="flex items-center gap-1.5">
                <button type="submit" class="px-3 py-1.5 rounded bg-indigo-600 text-white font-medium hover:bg-indigo-500 text-xs">Filter</button>
                <a href="{{ route('imei.index') }}" class="px-2.5 py-1.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200 text-xs">Reset</a>
            </div>
        </form>
    </div>

    <!-- IMEI Table -->
    <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden text-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left compact-table">
                <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th>Primary IMEI (1)</th>
                        <th>Product & Model</th>
                        <th>Branch</th>
                        <th>Purchase Cost</th>
                        <th>Status</th>
                        <th>Customer / Sold Via</th>
                        <th>Warranty Expiry</th>
                        <th class="text-right">Audit Trail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($imeis as $im)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="font-mono font-bold text-slate-900">
                                <a href="{{ route('imei.show', $im->id) }}" class="text-indigo-600 hover:underline">
                                    {{ $im->imei1 }}
                                </a>
                                @if($im->imei2)
                                    <div class="text-[10px] text-slate-400 font-mono">IMEI 2: {{ $im->imei2 }}</div>
                                @endif
                                @if($im->serial_number)
                                    <div class="text-[10px] text-slate-400 font-mono">S/N: {{ $im->serial_number }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="font-bold text-slate-900">{{ $im->product?->name }}</div>
                                @if($im->variant)
                                    <div class="text-[10px] text-slate-500">{{ $im->variant->name }}</div>
                                @endif
                            </td>
                            <td><span class="text-slate-600 font-medium">{{ $im->branch?->name }}</span></td>
                            <td>₹{{ number_format($im->purchase_cost, 2) }}</td>
                            <td>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase
                                    {{ $im->status === 'in_stock' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                    {{ $im->status === 'sold' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $im->status === 'reserved' ? 'bg-amber-100 text-amber-700' : '' }}
                                    {{ $im->status === 'under_repair' ? 'bg-rose-100 text-rose-700' : '' }}
                                    {{ $im->status === 'damaged' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $im->status === 'transferred' ? 'bg-purple-100 text-purple-700' : '' }}">
                                    {{ str_replace('_', ' ', $im->status) }}
                                </span>
                            </td>
                            <td>
                                @if($im->customer)
                                    <div class="font-medium text-slate-900">{{ $im->customer->name }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $im->customer->phone }}</div>
                                    @if($im->sale)
                                        <a href="{{ route('sales.show', $im->sale_id) }}" class="text-[10px] text-indigo-600 hover:underline">
                                            Inv: {{ $im->sale->invoice_number }}
                                        </a>
                                    @endif
                                @else
                                    <span class="text-slate-400 italic">Unassigned (In Store)</span>
                                @endif
                            </td>
                            <td>
                                @if($im->warranty_end)
                                    <div class="font-medium {{ $im->isUnderWarranty() ? 'text-emerald-600' : 'text-slate-400' }}">
                                        {{ $im->warranty_end->format('d M, Y') }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        {{ $im->isUnderWarranty() ? 'Under Warranty' : 'Expired' }}
                                    </div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap space-x-1">
                                <a href="{{ route('imei.show', $im->id) }}" class="px-2 py-0.5 rounded bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-medium">
                                    History
                                </a>
                                <button
                                    type="button"
                                    @click="selectedImei = {{ $im }}; statusModal = true"
                                    class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs"
                                >
                                    Status
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-400">No IMEI records registered matching query.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-2 border-t border-slate-200">
            {{ $imeis->links() }}
        </div>
    </div>

    <!-- Modal: Register New IMEI -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-4 border border-slate-200 text-xs space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm">Register Smartphone IMEI into Stock</h3>
                <button @click="addModal = false" class="text-slate-400 hover:text-slate-600 text-base">&times;</button>
            </div>

            <form action="{{ route('imei.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Select Smartphone Model *</label>
                    <select name="product_id" required class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                        <option value="">-- Choose Serialized Product --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} (Cost: ₹{{ number_format($p->purchase_price, 2) }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">IMEI 1 (Primary 15-digits) *</label>
                        <input type="text" name="imei1" required placeholder="e.g. 356789101234567" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 font-mono text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">IMEI 2 (Optional Dual-SIM)</label>
                        <input type="text" name="imei2" placeholder="e.g. 356789101234568" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 font-mono text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Serial Number</label>
                        <input type="text" name="serial_number" placeholder="e.g. F2LZ99810Q" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 font-mono text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Warranty (Months)</label>
                        <input type="number" name="warranty_months" value="12" min="0" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                    </div>
                </div>

                <div class="pt-2 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" @click="addModal = false" class="px-3 py-1.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700">Cancel</button>
                    <button type="submit" class="px-4 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white font-bold">Register & Add to Stock</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Update IMEI Status -->
    <div x-show="statusModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-4 border border-slate-200 text-xs space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm">Update IMEI Status</h3>
                <button @click="statusModal = false" class="text-slate-400 hover:text-slate-600 text-base">&times;</button>
            </div>

            <form :action="'{{ url('imei') }}/' + (selectedImei ? selectedImei.id : '') + '/status'" method="POST" class="space-y-3">
                @csrf
                <div>
                    <div class="text-slate-500 mb-1">Device IMEI: <span class="font-mono font-bold text-indigo-700" x-text="selectedImei ? selectedImei.imei1 : ''"></span></div>
                    <div class="text-slate-500 mb-2">Current Status: <span class="font-bold uppercase" x-text="selectedImei ? selectedImei.status : ''"></span></div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">New Status *</label>
                    <select name="status" required class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                        <option value="in_stock">In Stock (Available for Sale)</option>
                        <option value="reserved">Reserved (Customer Hold)</option>
                        <option value="under_repair">Under Repair (Sent to Service Desk)</option>
                        <option value="damaged">Damaged (Defective Device)</option>
                        <option value="written_off">Written Off (Total Loss)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Audit Notes / Reason *</label>
                    <textarea name="notes" rows="2" required placeholder="Explain why status is changing..." class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs"></textarea>
                </div>

                <div class="pt-2 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" @click="statusModal = false" class="px-3 py-1.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700">Cancel</button>
                    <button type="submit" class="px-4 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white font-bold">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
