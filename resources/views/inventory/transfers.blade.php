@extends('layouts.app')

@section('title', 'Inter-Branch Stock Transfers - thrumble.mobile-shop')

@section('content')
@php
    $defaultFrom = session('active_branch_id', $branches->first()?->id);
    $defaultTo = $branches->firstWhere('id', '!=', $defaultFrom)?->id ?? $branches->last()?->id;

    // Build JSON data of in-stock items by branch for Alpine
    $stockPayload = $stockBalances->map(function($b) {
        return [
            'id' => $b->id,
            'branch_id' => $b->branch_id,
            'product_id' => $b->product_id,
            'variant_id' => $b->variant_id,
            'product_name' => $b->product?->name ?? 'Product #' . $b->product_id,
            'variant_name' => $b->variant?->name ?? '',
            'type' => $b->product?->type ?? 'standard',
            'is_serialized' => $b->product ? $b->product->isSerialized() : false,
            'brand' => $b->product?->brand?->name ?? '',
            'quantity' => $b->quantity,
        ];
    })->values();

    $imeiPayload = $imeiRecords->map(function($im) {
        return [
            'id' => $im->id,
            'branch_id' => $im->branch_id,
            'product_id' => $im->product_id,
            'variant_id' => $im->variant_id,
            'imei1' => $im->imei1,
            'serial' => $im->serial_number,
        ];
    })->values();
@endphp

<div class="space-y-4" x-data="branchTransferApp()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200">
        <div>
            <h1 class="text-lg font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                <span>Inter-Branch Stock Transfers</span>
            </h1>
            <p class="text-xs text-slate-500">Dispatch phones, serialized IMEIs, accessories, and spare parts seamlessly between store locations.</p>
        </div>
        <button @click="openModal()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white text-xs font-semibold shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>+ New Stock Transfer</span>
        </button>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-white rounded-lg border border-slate-200 p-3 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Total Transfers</span>
                <span class="text-lg font-bold text-slate-900">{{ $transfers->total() }}</span>
            </div>
            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                ⇄
            </div>
        </div>
        <div class="bg-white rounded-lg border border-slate-200 p-3 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Completed & Received</span>
                <span class="text-lg font-bold text-emerald-600">{{ $transfers->where('status', 'received')->count() }}</span>
            </div>
            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                ✓
            </div>
        </div>
        <div class="bg-white rounded-lg border border-slate-200 p-3 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Active Branches</span>
                <span class="text-lg font-bold text-slate-700">{{ $branches->count() }} Locations</span>
            </div>
            <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center font-bold">
                🏬
            </div>
        </div>
    </div>

    <!-- Transfers Table -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden text-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left compact-table">
                <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th class="py-2.5 px-3">Transfer #</th>
                        <th class="py-2.5 px-3">Route (From &rarr; To)</th>
                        <th class="py-2.5 px-3">Products &amp; Quantities</th>
                        <th class="py-2.5 px-3">IMEIs Transferred</th>
                        <th class="py-2.5 px-3">Requested By</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-3">Date Completed</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transfers as $t)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-3">
                                <span class="font-mono font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">
                                    {{ $t->transfer_number }}
                                </span>
                                @if($t->notes)
                                    <div class="text-[10px] text-slate-400 mt-1 max-w-[150px] truncate" title="{{ $t->notes }}">{{ $t->notes }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-1.5 font-medium">
                                    <span class="text-slate-600">{{ $t->fromBranch?->name ?? 'Branch #' . $t->from_branch_id }}</span>
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    <span class="text-slate-900 font-bold">{{ $t->toBranch?->name ?? 'Branch #' . $t->to_branch_id }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-3 space-y-1">
                                @foreach($t->items as $ti)
                                    <div>
                                        <span class="font-bold text-slate-800">{{ $ti->product?->name }}</span>
                                        @if($ti->variant)
                                            <span class="text-slate-500 font-normal">({{ $ti->variant->name }})</span>
                                        @endif
                                        <span class="ml-1 inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-slate-100 text-slate-700">
                                            &times;{{ $ti->quantity }}
                                        </span>
                                    </div>
                                @endforeach
                            </td>
                            <td class="py-3 px-3">
                                @php
                                    $allImeis = collect($t->items)->flatMap(fn($i) => $i->imeis ?? [])->filter();
                                @endphp
                                @if($allImeis->count() > 0)
                                    <div class="space-y-1">
                                        <div class="flex flex-wrap gap-1 max-w-xs">
                                            @foreach($allImeis->take(3) as $im)
                                                <span class="font-mono text-[10px] bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded border border-slate-200">
                                                    {{ $im }}
                                                </span>
                                            @endforeach
                                            @if($allImeis->count() > 3)
                                                <span class="text-[10px] text-slate-400 font-medium">
                                                    +{{ $allImeis->count() - 3 }} more
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Non-serialized stock</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-slate-600">
                                {{ $t->requestedBy?->name ?? 'Staff' }}
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide
                                    {{ $t->status === 'received' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $t->status }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-slate-500 text-[11px]">
                                {{ $t->received_at ? $t->received_at->format('d M, Y H:i') : ($t->created_at ? $t->created_at->format('d M, Y H:i') : '-') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-400">
                                <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                <div class="font-semibold text-slate-600">No inter-branch transfers recorded yet.</div>
                                <p class="text-xs text-slate-400 mt-1">Click the button above to dispatch stock between your Mumbai stores.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transfers->hasPages())
            <div class="p-3 border-t border-slate-200 bg-slate-50">
                {{ $transfers->links() }}
            </div>
        @endif
    </div>

    <!-- Modal: Interactive New Stock Transfer -->
    <div x-show="transferModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="transferModal = false" class="bg-white rounded-xl shadow-2xl max-w-xl w-full p-5 border border-slate-200 text-xs space-y-4">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Create Inter-Branch Stock Transfer</h3>
                    <p class="text-[11px] text-slate-500">Pick source branch, destination, and in-stock units to transfer</p>
                </div>
                <button @click="transferModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
            </div>

            <form action="{{ route('inventory.transfers.store') }}" method="POST" class="space-y-4" @submit="isSubmitting = true">
                @csrf

                <!-- Branch Selection Grid -->
                <div class="grid grid-cols-2 gap-3 p-3 bg-slate-50 rounded-lg border border-slate-200">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Source (From Branch) *</label>
                        <select name="from_branch_id" x-model="fromBranchId" @change="onFromBranchChange()" required class="w-full px-2.5 py-1.5 rounded bg-white border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-1 focus:ring-indigo-500">
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Destination (To Branch) *</label>
                        <select name="to_branch_id" x-model="toBranchId" required class="w-full px-2.5 py-1.5 rounded bg-white border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-1 focus:ring-indigo-500">
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Product Selection -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Select In-Stock Item to Transfer *</label>
                    <template x-if="availableStockItems.length === 0">
                        <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-amber-800 text-xs">
                            No active stock balances found at the selected source branch. Please select another source branch or restock inventory.
                        </div>
                    </template>

                    <template x-if="availableStockItems.length > 0">
                        <select x-model="selectedStockKey" @change="onStockItemSelect()" required class="w-full px-2.5 py-2 rounded bg-slate-50 border border-slate-300 text-xs font-medium text-slate-800 focus:ring-1 focus:ring-indigo-500">
                            <option value="">-- Choose Item (Showing Available Stock) --</option>
                            <template x-for="item in availableStockItems" :key="item.stock_key">
                                <option :value="item.stock_key" x-text="`${item.product_name} ${item.variant_name ? '(' + item.variant_name + ')' : ''} &bull; ${item.quantity} in stock [${item.is_serialized ? 'Serialized' : 'Standard'}]`"></option>
                            </template>
                        </select>
                    </template>

                    <!-- Hidden Inputs for Form Submission -->
                    <input type="hidden" name="items[0][product_id]" :value="selectedItem ? selectedItem.product_id : ''">
                    <input type="hidden" name="items[0][variant_id]" :value="selectedItem && selectedItem.variant_id ? selectedItem.variant_id : ''">
                </div>

                <!-- Serialized Item IMEIs Selection -->
                <template x-if="selectedItem && selectedItem.is_serialized">
                    <div class="space-y-2 p-3 bg-indigo-50/60 rounded-lg border border-indigo-200">
                        <div class="flex items-center justify-between">
                            <label class="font-bold text-indigo-950 block">
                                Select Device IMEIs to Transfer *
                                <span class="text-[10px] font-normal text-indigo-700">(Click to select/deselect)</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="selectAllImeis()" class="text-[10px] font-bold text-indigo-600 hover:underline">Select All</button>
                                <span class="text-slate-300">&bull;</span>
                                <button type="button" @click="clearImeis()" class="text-[10px] font-bold text-slate-500 hover:underline">Clear</button>
                            </div>
                        </div>

                        <template x-if="availableImeis.length === 0">
                            <p class="text-xs text-amber-700 italic">No in-stock IMEIs found for this model at the source branch.</p>
                        </template>

                        <div class="grid grid-cols-2 gap-2 max-h-36 overflow-y-auto pr-1">
                            <template x-for="im in availableImeis" :key="im.id">
                                <label class="flex items-center gap-2 p-2 rounded bg-white border cursor-pointer transition text-xs"
                                    :class="selectedImeis.includes(im.imei1) ? 'border-indigo-600 bg-indigo-50/50 shadow-2xs' : 'border-slate-200 hover:border-slate-300'">
                                    <input type="checkbox" :value="im.imei1" @change="toggleImei(im.imei1)" :checked="selectedImeis.includes(im.imei1)" class="rounded text-indigo-600 focus:ring-indigo-500">
                                    <span class="font-mono text-[11px] text-slate-800" x-text="im.imei1"></span>
                                </label>
                            </template>
                        </div>

                        <!-- Hidden input for IMEIs -->
                        <template x-for="(imei, idx) in selectedImeis" :key="idx">
                            <input type="hidden" name="items[0][imeis][]" :value="imei">
                        </template>

                        <div class="text-[11px] text-indigo-800 font-semibold flex items-center justify-between pt-1">
                            <span>Selected: <strong x-text="selectedImeis.length"></strong> IMEIs</span>
                            <span>Available at branch: <strong x-text="availableImeis.length"></strong></span>
                        </div>
                    </div>
                </template>

                <!-- Quantity Input -->
                <div class="grid grid-cols-2 gap-3 items-center">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Transfer Quantity *</label>
                        <input type="number" name="items[0][quantity]" x-model="quantity" :min="1" :max="selectedItem ? selectedItem.quantity : 999" :readonly="selectedItem && selectedItem.is_serialized" required
                            class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs font-bold text-slate-900 focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div class="pt-5 text-[11px] text-slate-500">
                        <template x-if="selectedItem && selectedItem.is_serialized">
                            <span>Quantity locks automatically to the number of IMEIs selected above.</span>
                        </template>
                        <template x-if="selectedItem && !selectedItem.is_serialized">
                            <span>Max available: <strong class="text-slate-800" x-text="selectedItem.quantity"></strong> units.</span>
                        </template>
                    </div>
                </div>

                <!-- Notes / Reason -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Transfer Notes / Reason (Optional)</label>
                    <input type="text" name="notes" placeholder="e.g. Stock balancing for weekend retail demand" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>

                <!-- Footer CTAs -->
                <div class="pt-3 flex items-center justify-between border-t border-slate-100">
                    <button type="button" @click="transferModal = false" class="px-3.5 py-1.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium">
                        Cancel
                    </button>
                    <button type="submit" :disabled="!isFormValid || isSubmitting"
                        class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 disabled:opacity-50 text-white font-bold flex items-center gap-1.5 shadow-sm transition">
                        <span x-show="!isSubmitting">Dispatch &amp; Complete Transfer</span>
                        <span x-show="isSubmitting">Processing Transfer...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function branchTransferApp() {
    return {
        transferModal: false,
        isSubmitting: false,
        branches: @json($branches),
        fromBranchId: '{{ $defaultFrom }}',
        toBranchId: '{{ $defaultTo }}',
        stockData: @json($stockPayload),
        imeiData: @json($imeiPayload),
        selectedStockKey: '',
        selectedItem: null,
        availableImeis: [],
        selectedImeis: [],
        quantity: 1,

        openModal() {
            this.transferModal = true;
            this.onFromBranchChange();
        },

        get availableStockItems() {
            return this.stockData
                .filter(item => String(item.branch_id) === String(this.fromBranchId) && item.quantity > 0)
                .map(item => {
                    return {
                        ...item,
                        stock_key: `${item.product_id}_${item.variant_id || '0'}`
                    };
                });
        },

        onFromBranchChange() {
            // Ensure toBranch is not identical to fromBranch
            if (String(this.fromBranchId) === String(this.toBranchId)) {
                const other = this.branches.find(b => String(b.id) !== String(this.fromBranchId));
                if (other) {
                    this.toBranchId = String(other.id);
                }
            }
            this.selectedStockKey = '';
            this.selectedItem = null;
            this.availableImeis = [];
            this.selectedImeis = [];
            this.quantity = 1;
        },

        onStockItemSelect() {
            if (!this.selectedStockKey) {
                this.selectedItem = null;
                this.availableImeis = [];
                this.selectedImeis = [];
                this.quantity = 1;
                return;
            }

            const item = this.availableStockItems.find(i => i.stock_key === this.selectedStockKey);
            this.selectedItem = item || null;
            this.selectedImeis = [];

            if (this.selectedItem && this.selectedItem.is_serialized) {
                this.availableImeis = this.imeiData.filter(im => {
                    const matchBranch = String(im.branch_id) === String(this.fromBranchId);
                    const matchProduct = Number(im.product_id) === Number(this.selectedItem.product_id);
                    const matchVariant = !this.selectedItem.variant_id || Number(im.variant_id) === Number(this.selectedItem.variant_id);
                    return matchBranch && matchProduct && matchVariant;
                });
                this.quantity = 0;
            } else {
                this.availableImeis = [];
                this.quantity = 1;
            }
        },

        toggleImei(imei) {
            const idx = this.selectedImeis.indexOf(imei);
            if (idx > -1) {
                this.selectedImeis.splice(idx, 1);
            } else {
                this.selectedImeis.push(imei);
            }
            this.quantity = this.selectedImeis.length;
        },

        selectAllImeis() {
            this.selectedImeis = this.availableImeis.map(im => im.imei1);
            this.quantity = this.selectedImeis.length;
        },

        clearImeis() {
            this.selectedImeis = [];
            this.quantity = 0;
        },

        get isFormValid() {
            if (!this.fromBranchId || !this.toBranchId || String(this.fromBranchId) === String(this.toBranchId)) {
                return false;
            }
            if (!this.selectedItem) {
                return false;
            }
            if (this.selectedItem.is_serialized) {
                return this.selectedImeis.length > 0 && this.quantity > 0;
            }
            return this.quantity >= 1 && this.quantity <= this.selectedItem.quantity;
        }
    };
}
</script>
@endpush
@endsection
