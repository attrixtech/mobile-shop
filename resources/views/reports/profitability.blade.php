@extends('layouts.app')

@section('title', 'Profitability & Margin Analysis')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Profitability & Margin Analysis</h1>
            <p class="text-xs text-slate-500">Gross profit, operating expenses, cost of goods sold (COGS), and net earnings</p>
        </div>
        <form method="GET" action="{{ route('reports.profitability') }}" class="flex flex-wrap items-center gap-2">
            <select name="branch_id" class="form-input text-xs py-1.5 w-auto">
                <option value="">All Branches</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
            <input type="date" name="start_date" value="{{ $startDate }}" class="form-input text-xs py-1.5 w-auto">
            <span class="text-xs text-slate-400">to</span>
            <input type="date" name="end_date" value="{{ $endDate }}" class="form-input text-xs py-1.5 w-auto">
            <button type="submit" class="btn btn-primary text-xs py-1.5">Filter</button>
        </form>
    </div>

    <!-- KPI Summary Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="card p-3 bg-white">
            <span class="text-xs text-slate-500 font-medium">Total Sales Revenue</span>
            <div class="text-lg font-bold text-slate-800 mt-1">₹{{ number_format($revenue, 2) }}</div>
            <span class="text-[10px] text-slate-400">Net billing revenue</span>
        </div>
        <div class="card p-3 bg-white">
            <span class="text-xs text-slate-500 font-medium">Cost of Goods Sold (COGS)</span>
            <div class="text-lg font-bold text-amber-600 mt-1">₹{{ number_format($cogs, 2) }}</div>
            <span class="text-[10px] text-amber-700">Cost of sold hardware</span>
        </div>
        <div class="card p-3 bg-white">
            <span class="text-xs text-slate-500 font-medium">Gross Profit</span>
            <div class="text-lg font-bold text-emerald-600 mt-1">₹{{ number_format($grossProfit, 2) }}</div>
            <span class="text-[10px] font-semibold text-emerald-700">{{ $grossMarginPercent }}% Gross Margin</span>
        </div>
        <div class="card p-3 bg-white">
            <span class="text-xs text-slate-500 font-medium">Net Profit (EBITDA)</span>
            <div class="text-lg font-bold {{ $netProfit >= 0 ? 'text-indigo-600' : 'text-red-600' }} mt-1">
                ₹{{ number_format($netProfit, 2) }}
            </div>
            <span class="text-[10px] font-semibold {{ $netProfit >= 0 ? 'text-indigo-700' : 'text-red-700' }}">
                {{ $netMarginPercent }}% Net Margin
            </span>
        </div>
    </div>

    <!-- Detailed Financial Breakdown Table -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- P&L Statement Card -->
        <div class="card bg-white p-4">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider border-b border-slate-100 pb-2 mb-3">Profit & Loss Summary</h3>
            <div class="space-y-3 text-xs">
                <div class="flex justify-between py-1.5 border-b border-slate-50 font-medium">
                    <span class="text-slate-700">(+) Gross Sales Revenue</span>
                    <span class="text-slate-900 font-semibold">₹{{ number_format($revenue, 2) }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-50 font-medium">
                    <span class="text-slate-700">(-) Direct Hardware Purchase Cost (COGS)</span>
                    <span class="text-amber-600 font-semibold">₹{{ number_format($cogs, 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-200 font-bold bg-slate-50 px-2 rounded">
                    <span class="text-emerald-700">(=) Gross Profit</span>
                    <span class="text-emerald-700">₹{{ number_format($grossProfit, 2) }} ({{ $grossMarginPercent }}%)</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-50 font-medium">
                    <span class="text-slate-700">(-) Operating & Store Expenses</span>
                    <span class="text-red-500 font-semibold">₹{{ number_format($expenses, 2) }}</span>
                </div>
                <div class="flex justify-between py-2.5 font-bold text-sm bg-indigo-50 text-indigo-900 px-2 rounded">
                    <span>(=) Estimated Net Profit</span>
                    <span>₹{{ number_format($netProfit, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Strategy Insights Card -->
        <div class="card bg-white p-4 space-y-3">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider border-b border-slate-100 pb-2">Business Margin Health</h3>
            <div class="space-y-3 text-xs text-slate-600">
                <div class="p-3 bg-emerald-50 rounded-lg border border-emerald-100">
                    <div class="font-semibold text-emerald-800">Healthy Gross Margins</div>
                    <p class="text-[11px] text-emerald-700 mt-1">
                        High margins on accessories and repair service labor buffer typical thin retail hardware smartphone margins (usually 4% - 8%).
                    </p>
                </div>
                <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                    <div class="font-semibold text-blue-800">Trade-In Exchange Uplift</div>
                    <p class="text-[11px] text-blue-700 mt-1">
                        Refurbishing and reselling traded-in secondary mobile devices yields 18% - 30% gross margins compared to brand-new box sales.
                    </p>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                    <div class="font-semibold text-slate-800">Operational Overhead Ratio</div>
                    <p class="text-[11px] text-slate-600 mt-1">
                        Store operating expenses account for {{ $revenue > 0 ? round(($expenses / $revenue) * 100, 1) : 0 }}% of total gross retail turnover.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
