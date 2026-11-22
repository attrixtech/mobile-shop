@extends('layouts.app')

@section('title', 'Expenses - Webotapp Mobile Shop ERP')

@section('content')
<div class="space-y-3" x-data="{ expModal: false }">
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">Store Operating Expenses</h1>
            <p class="text-xs text-slate-500">Record shop rent, electricity, technician wages, maintenance, and logistics</p>
        </div>
        <button @click="expModal = true" class="px-3 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-sm flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Record New Expense</span>
        </button>
    </div>

    <!-- Expenses Table -->
    <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden text-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left compact-table">
                <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Vendor / Payee</th>
                        <th>Branch</th>
                        <th>Amount (₹)</th>
                        <th>Payment Mode</th>
                        <th>Reference / Bill #</th>
                        <th>Logged By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($expenses as $e)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td>{{ $e->expense_date->format('d M, Y') }}</td>
                            <td>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-800">
                                    {{ $e->category?->name }}
                                </span>
                            </td>
                            <td class="font-bold text-slate-900">{{ $e->vendor_name ?: '-' }}</td>
                            <td>{{ $e->branch?->name }}</td>
                            <td class="font-bold text-rose-600 text-xs">₹{{ number_format($e->amount, 2) }}</td>
                            <td>
                                <span class="uppercase font-semibold text-[10px]">{{ $e->payment_method }}</span>
                            </td>
                            <td class="font-mono text-slate-500">{{ $e->reference_no ?: '-' }}</td>
                            <td>{{ $e->user?->name ?: 'Staff' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-400">No expenses recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-2 border-t border-slate-200">
            {{ $expenses->links() }}
        </div>
    </div>

    <!-- Modal: Record Expense -->
    <div x-show="expModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-4 border border-slate-200 text-xs space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm">Record Store Operating Expense</h3>
                <button @click="expModal = false" class="text-slate-400 hover:text-slate-600 text-base">&times;</button>
            </div>

            <form action="{{ route('expenses.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Expense Category *</label>
                    <select name="category_id" required class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                        <option value="">-- Choose Category --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Amount (₹) *</label>
                        <input type="number" step="0.01" name="amount" required placeholder="0.00" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Expense Date *</label>
                        <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Vendor / Payee</label>
                        <input type="text" name="vendor_name" placeholder="e.g. Electric Company, Landlord" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Payment Method *</label>
                        <select name="payment_method" required class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                            <option value="cash">Cash in Hand</option>
                            <option value="upi">UPI</option>
                            <option value="bank_transfer">Bank Transfer / NEFT</option>
                            <option value="card">Card</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Bill Reference / Transaction ID</label>
                    <input type="text" name="reference_no" placeholder="e.g. BILL-99410" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Notes</label>
                    <input type="text" name="notes" placeholder="Optional notes" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>

                <div class="pt-2 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" @click="expModal = false" class="px-3 py-1.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700">Cancel</button>
                    <button type="submit" class="px-4 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white font-bold">Record Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
