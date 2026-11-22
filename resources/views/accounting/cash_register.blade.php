@extends('layouts.app')

@section('title', 'Daily Cash Closing - thrumble.mobile-shop')

@section('content')
<div class="space-y-4" x-data="{ openModal: false, closeModal: false }">
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">Daily Cash Closing & Drawer Management</h1>
            <p class="text-xs text-slate-500">Track shift opening float, cash sales, cash expenses, and drawer discrepancies</p>
        </div>
        <div>
            @if($currentRegister)
                <button @click="closeModal = true" class="px-3 py-1.5 rounded bg-rose-600 hover:bg-rose-500 text-white text-xs font-semibold shadow-sm flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <span>Close Shift & Balance Cash Drawer</span>
                </button>
            @else
                <button @click="openModal = true" class="px-3 py-1.5 rounded bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-sm flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                    <span>Open New Cash Register Shift</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Active Register Status Card -->
    @if($currentRegister)
        <div class="bg-white rounded border border-emerald-200 p-4 shadow-sm text-xs space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h3 class="font-bold text-slate-900 text-sm">Active Cash Register Session</h3>
                </div>
                <span class="px-2 py-0.5 rounded font-mono font-bold text-emerald-800 bg-emerald-50 text-xs border border-emerald-200">
                    Opened at {{ $currentRegister->opened_at->format('h:i A, d M Y') }}
                </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="p-2.5 rounded bg-slate-50 border border-slate-200">
                    <div class="text-[10px] text-slate-500 font-medium">Opening Cash Float</div>
                    <div class="text-base font-bold text-slate-900 mt-0.5">₹{{ number_format($currentRegister->opening_balance, 2) }}</div>
                </div>
                <div class="p-2.5 rounded bg-slate-50 border border-slate-200">
                    <div class="text-[10px] text-slate-500 font-medium">Cash Sales (Shift)</div>
                    <div class="text-base font-bold text-emerald-600 mt-0.5">+₹{{ number_format($currentRegister->cash_sales, 2) }}</div>
                </div>
                <div class="p-2.5 rounded bg-slate-50 border border-slate-200">
                    <div class="text-[10px] text-slate-500 font-medium">Cash Expenses Paid</div>
                    <div class="text-base font-bold text-rose-600 mt-0.5">-₹{{ number_format($currentRegister->cash_expenses, 2) }}</div>
                </div>
                <div class="p-2.5 rounded bg-indigo-50 border border-indigo-200">
                    <div class="text-[10px] text-indigo-700 font-medium">Expected Drawer Cash</div>
                    <div class="text-base font-bold text-indigo-700 mt-0.5">
                        ₹{{ number_format($currentRegister->opening_balance + $currentRegister->cash_sales - $currentRegister->cash_expenses, 2) }}
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-amber-50 border border-amber-200 p-4 rounded text-xs text-amber-900 flex items-center justify-between">
            <div>
                <strong>No cash register is currently open</strong> for this branch shift. Please open a register before collecting cash sales.
            </div>
            <button @click="openModal = true" class="px-3 py-1.5 rounded bg-amber-700 text-white font-bold text-xs hover:bg-amber-800">
                Open Shift Register
            </button>
        </div>
    @endif

    <!-- Shift History Table -->
    <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden text-xs">
        <div class="p-2.5 bg-slate-50 border-b border-slate-200 font-bold text-slate-800 uppercase tracking-wider text-[11px]">
            Cash Register History & Reconciliation Log
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left compact-table">
                <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th>Opened At</th>
                        <th>Closed At</th>
                        <th>Cashier / User</th>
                        <th>Opening Float</th>
                        <th>Actual Count</th>
                        <th>Expected</th>
                        <th>Discrepancy (Overage / Shortage)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($historyRegisters as $reg)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td>{{ $reg->opened_at->format('d M, Y H:i') }}</td>
                            <td>{{ $reg->closed_at ? $reg->closed_at->format('d M, Y H:i') : 'In Progress' }}</td>
                            <td>{{ $reg->user?->name }}</td>
                            <td>₹{{ number_format($reg->opening_balance, 2) }}</td>
                            <td class="font-bold text-slate-900">{{ $reg->closing_balance !== null ? '₹' . number_format($reg->closing_balance, 2) : '-' }}</td>
                            <td>₹{{ number_format($reg->system_expected_cash, 2) }}</td>
                            <td>
                                @if($reg->closed_at)
                                    <span class="font-bold {{ $reg->discrepancy < 0 ? 'text-rose-600' : ($reg->discrepancy > 0 ? 'text-emerald-600' : 'text-slate-500') }}">
                                        {{ $reg->discrepancy == 0 ? 'Exact Match (₹0)' : ($reg->discrepancy > 0 ? '+₹' . number_format($reg->discrepancy, 2) : '-₹' . number_format(abs($reg->discrepancy), 2)) }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase
                                    {{ $reg->status === 'open' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $reg->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-6 text-slate-400">No previous shifts recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-2 border-t border-slate-200">
            {{ $historyRegisters->links() }}
        </div>
    </div>

    <!-- Modal: Open Register -->
    <div x-show="openModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-4 border border-slate-200 text-xs space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm">Open Shift Cash Register</h3>
                <button @click="openModal = false" class="text-slate-400 hover:text-slate-600 text-base">&times;</button>
            </div>

            <form action="{{ route('accounting.cash-register.open') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Opening Cash Float Amount (₹) *</label>
                    <input type="number" step="0.01" name="opening_balance" value="5000.00" required class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                    <p class="text-[10px] text-slate-400 mt-1">Starting physical cash inside the register drawer</p>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Shift Notes</label>
                    <input type="text" name="notes" placeholder="e.g. Morning counter shift" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>

                <div class="pt-2 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" @click="openModal = false" class="px-3 py-1.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700">Cancel</button>
                    <button type="submit" class="px-4 py-1.5 rounded bg-emerald-600 hover:bg-emerald-500 text-white font-bold">Open Register</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Close Register -->
    @if($currentRegister)
        <div x-show="closeModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-4 border border-slate-200 text-xs space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900 text-sm">Close Register & Count Cash Drawer</h3>
                    <button @click="closeModal = false" class="text-slate-400 hover:text-slate-600 text-base">&times;</button>
                </div>

                <form action="{{ route('accounting.cash-register.close', $currentRegister->id) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Counted Physical Cash in Drawer (₹) *</label>
                        <input type="number" step="0.01" name="closing_balance" required placeholder="0.00" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                        <p class="text-[10px] text-slate-400 mt-1">System expects ₹{{ number_format($currentRegister->opening_balance + $currentRegister->cash_sales - $currentRegister->cash_expenses, 2) }}</p>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Closing Remarks</label>
                        <textarea name="notes" rows="2" placeholder="Mention reason for any discrepancy if observed" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs"></textarea>
                    </div>

                    <div class="pt-2 flex justify-end gap-2 border-t border-slate-100">
                        <button type="button" @click="closeModal = false" class="px-3 py-1.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-rose-600 hover:bg-rose-500 text-white font-bold">Submit Closing Count</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
