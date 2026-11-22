@extends('layouts.app')

@section('title', 'Journal Entries - thrumble.mobile-shop')

@section('content')
<div class="space-y-3">
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">Double-Entry Journal Entries Audit</h1>
            <p class="text-xs text-slate-500">Every sales, purchase, and expense transaction posted with strictly balanced debits & credits</p>
        </div>
        <a href="{{ route('accounting.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">&larr; Back to Chart of Accounts</a>
    </div>

    <!-- Journal Entries List -->
    <div class="space-y-3">
        @forelse($entries as $je)
            <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden text-xs">
                <div class="p-2.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="font-mono font-bold text-indigo-600">{{ $je->entry_number }}</span>
                        <span class="text-slate-400">|</span>
                        <span class="text-slate-700">{{ $je->entry_date->format('d M, Y') }}</span>
                        <span class="text-slate-400">|</span>
                        <span class="text-slate-900 font-medium">{{ $je->narration }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-200 text-slate-700">
                            {{ $je->reference_type }} #{{ $je->reference_id }}
                        </span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase {{ $je->isBalanced() ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ $je->isBalanced() ? 'Balanced' : 'Unbalanced' }}
                        </span>
                    </div>
                </div>

                <table class="w-full text-left compact-table">
                    <thead class="bg-slate-100/60 text-slate-600 border-b border-slate-200 text-[10px]">
                        <tr>
                            <th style="width: 15%;">Account Code</th>
                            <th style="width: 35%;">Account Name</th>
                            <th style="width: 25%;">Line Description</th>
                            <th style="width: 12%;" class="text-right">Debit (₹)</th>
                            <th style="width: 13%;" class="text-right">Credit (₹)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($je->lines as $line)
                            <tr>
                                <td class="font-mono font-bold text-slate-700">{{ $line->account?->code }}</td>
                                <td class="font-semibold text-slate-900">{{ $line->account?->name }}</td>
                                <td class="text-slate-500">{{ $line->description ?: '-' }}</td>
                                <td class="text-right font-mono font-semibold {{ $line->debit > 0 ? 'text-slate-900' : 'text-slate-400' }}">
                                    {{ $line->debit > 0 ? number_format($line->debit, 2) : '-' }}
                                </td>
                                <td class="text-right font-mono font-semibold {{ $line->credit > 0 ? 'text-slate-900' : 'text-slate-400' }}">
                                    {{ $line->credit > 0 ? number_format($line->credit, 2) : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 font-bold border-t border-slate-200">
                        <tr>
                            <td colspan="3" class="text-right uppercase text-[10px] text-slate-500">Balanced Totals:</td>
                            <td class="text-right font-mono text-indigo-700">₹{{ number_format($je->totalDebit(), 2) }}</td>
                            <td class="text-right font-mono text-indigo-700">₹{{ number_format($je->totalCredit(), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @empty
            <div class="bg-white rounded border border-slate-200 p-8 text-center text-slate-400 text-xs">
                No journal entries posted yet. Complete sales, purchases, or expenses to auto-generate entries.
            </div>
        @endforelse

        <div class="p-2">
            {{ $entries->links() }}
        </div>
    </div>
</div>
@endsection
