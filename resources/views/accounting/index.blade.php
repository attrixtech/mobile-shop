@extends('layouts.app')

@section('title', 'Chart of Accounts - Webotapp Mobile Shop ERP')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">Chart of Accounts & General Ledger</h1>
            <p class="text-xs text-slate-500">Structured double-entry financial accounting ledger and live account balances</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('accounting.journal') }}" class="px-3 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-sm flex items-center gap-1.5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                <span>Journal Entries Audit</span>
            </a>
            <a href="{{ route('accounting.cash-register') }}" class="px-3 py-1.5 rounded bg-slate-800 hover:bg-slate-700 text-white text-xs font-medium flex items-center gap-1.5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Cash Register</span>
            </a>
        </div>
    </div>

    <!-- Chart of Accounts Table -->
    <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden text-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left compact-table">
                <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th>Code</th>
                        <th>Account Name</th>
                        <th>Primary Type</th>
                        <th>Subtype Classification</th>
                        <th class="text-right">Current Ledger Balance (₹)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($allAccounts as $acc)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="font-mono font-bold text-indigo-700">{{ $acc->code }}</td>
                            <td class="font-semibold text-slate-900">{{ $acc->name }}</td>
                            <td>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase
                                    {{ $acc->type === 'asset' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $acc->type === 'liability' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $acc->type === 'equity' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $acc->type === 'revenue' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $acc->type === 'expense' ? 'bg-rose-100 text-rose-800' : '' }}">
                                    {{ $acc->type }}
                                </span>
                            </td>
                            <td class="text-slate-500 capitalize">{{ str_replace('_', ' ', $acc->subtype ?: '-') }}</td>
                            <td class="text-right font-mono font-bold text-slate-900 text-sm">
                                ₹{{ number_format($acc->balance, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-slate-400">No accounts registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
