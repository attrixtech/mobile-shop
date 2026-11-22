@extends('layouts.app')

@section('title', 'Stock Adjustments - Webotapp Mobile Shop ERP')

@section('content')
<div class="space-y-3" x-data="{ adjModal: false }">
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">Stock Adjustments & Damage Audits</h1>
            <p class="text-xs text-slate-500">Record inventory shrinkage, physical stock mismatches, and transit damages</p>
        </div>
        <button @click="adjModal = true" class="px-3 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-sm flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Record New Adjustment</span>
        </button>
    </div>

    <!-- Adjustments Table -->
    <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden text-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left compact-table">
                <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th>Adjustment No</th>
                        <th>Date</th>
                        <th>Branch</th>
                        <th>Type</th>
                        <th>Items Adjusted</th>
                        <th>Reason / Notes</th>
                        <th>Actioned By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($adjustments as $a)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="font-bold text-indigo-600 font-mono">{{ $a->adjustment_number }}</td>
                            <td>{{ $a->created_at->format('d M, Y') }}</td>
                            <td>{{ $a->branch?->name }}</td>
                            <td>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase
                                    {{ $a->type === 'increase' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $a->type }}
                                </span>
                            </td>
                            <td>
                                @foreach($a->items as $ai)
                                    <div><strong>{{ $ai->product?->name }}</strong> ({{ $ai->quantity }} units)</div>
                                @endforeach
                            </td>
                            <td class="text-slate-700">{{ $a->reason }}</td>
                            <td>{{ $a->user?->name ?: 'Admin' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-slate-400">No stock adjustments recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-2 border-t border-slate-200">
            {{ $adjustments->links() }}
        </div>
    </div>

    <!-- Modal: New Adjustment -->
    <div x-show="adjModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-4 border border-slate-200 text-xs space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm">Record Stock Adjustment</h3>
                <button @click="adjModal = false" class="text-slate-400 hover:text-slate-600 text-base">&times;</button>
            </div>

            <form action="{{ route('inventory.adjustments.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Select Branch *</label>
                    <select name="branch_id" required class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Select Product *</label>
                    <select name="product_id" required class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                        <option value="">-- Choose Product --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Adjustment Type *</label>
                        <select name="type" required class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                            <option value="damage">Damage (Deduct from Stock)</option>
                            <option value="loss">Loss / Theft (Deduct)</option>
                            <option value="decrease">Physical Shortage (Deduct)</option>
                            <option value="increase">Found Stock (Add to Stock)</option>
                            <option value="write_off">Total Write-Off (Deduct)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Quantity *</label>
                        <input type="number" name="quantity" value="1" min="1" required class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Reason / Notes *</label>
                    <textarea name="reason" rows="2" required placeholder="Explain reason for stock adjustment..." class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs"></textarea>
                </div>

                <div class="pt-2 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" @click="adjModal = false" class="px-3 py-1.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700">Cancel</button>
                    <button type="submit" class="px-4 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white font-bold">Apply Adjustment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
