@extends('layouts.app')

@section('title', 'Business Dashboard - thrumble.mobile-shop')

@section('content')
<div class="space-y-4">

    <!-- Top Status Bar & Branch Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-lg font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <span>Business Operations Overview</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 font-medium">
                    {{ $currentBranch ? $currentBranch->name : 'All Branches' }}
                </span>
            </h1>
            <p class="text-xs text-slate-500">Live operational KPIs, IMEI tracking, and financial performance</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pos.index') }}" class="px-3 py-1.5 rounded bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-sm flex items-center gap-1.5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                <span>New POS Bill</span>
            </a>
            <a href="{{ route('purchases.create') }}" class="px-3 py-1.5 rounded bg-slate-800 hover:bg-slate-700 text-white text-xs font-medium flex items-center gap-1.5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                <span>Receive Stock (GRN)</span>
            </a>
        </div>
    </div>

    <!-- Financial KPI Summary Cards (Compact 4-column Grid) -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2.5">
        <div class="bg-white rounded border border-slate-200 p-2.5 shadow-sm">
            <div class="text-[11px] font-medium text-slate-500">Today's Sales</div>
            <div class="text-base font-bold text-slate-900 mt-0.5">₹{{ number_format($todaySales, 2) }}</div>
            <div class="text-[10px] text-emerald-600 font-medium mt-0.5 flex items-center">
                <span>Gross Revenue</span>
            </div>
        </div>

        <div class="bg-white rounded border border-slate-200 p-2.5 shadow-sm">
            <div class="text-[11px] font-medium text-slate-500">Today's Purchases</div>
            <div class="text-base font-bold text-slate-900 mt-0.5">₹{{ number_format($todayPurchases, 2) }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">GRN Received</div>
        </div>

        <div class="bg-white rounded border border-slate-200 p-2.5 shadow-sm">
            <div class="text-[11px] font-medium text-slate-500">Gross Profit</div>
            <div class="text-base font-bold text-emerald-600 mt-0.5">₹{{ number_format($grossProfit, 2) }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Total Margin</div>
        </div>

        <div class="bg-white rounded border border-slate-200 p-2.5 shadow-sm">
            <div class="text-[11px] font-medium text-slate-500">Customer Dues</div>
            <div class="text-base font-bold text-amber-600 mt-0.5">₹{{ number_format($customerReceivables, 2) }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Receivables</div>
        </div>

        <div class="bg-white rounded border border-slate-200 p-2.5 shadow-sm">
            <div class="text-[11px] font-medium text-slate-500">Supplier Dues</div>
            <div class="text-base font-bold text-rose-600 mt-0.5">₹{{ number_format($supplierPayables, 2) }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Payables</div>
        </div>

        <div class="bg-white rounded border border-slate-200 p-2.5 shadow-sm">
            <div class="text-[11px] font-medium text-slate-500">Inventory Valuation</div>
            <div class="text-base font-bold text-indigo-600 mt-0.5">₹{{ number_format($inventoryValuation, 2) }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Asset Cost</div>
        </div>
    </div>

    <!-- Operational Split: Inventory & IMEI Status Counters -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2.5">
        <!-- Stock Units Card -->
        <div class="bg-white rounded border border-slate-200 p-3 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-xs text-slate-500 font-medium">Smartphones In Stock</div>
                <div class="text-xl font-bold text-slate-900 mt-0.5">{{ $smartphonesInStock }} <span class="text-xs font-normal text-slate-500">devices</span></div>
                <div class="text-[11px] text-indigo-600 mt-0.5">Unique tracked IMEIs</div>
            </div>
            <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            </div>
        </div>

        <!-- Accessories Card -->
        <div class="bg-white rounded border border-slate-200 p-3 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-xs text-slate-500 font-medium">Accessories In Stock</div>
                <div class="text-xl font-bold text-slate-900 mt-0.5">{{ $accessoriesInStock }} <span class="text-xs font-normal text-slate-500">units</span></div>
                <div class="text-[11px] text-emerald-600 mt-0.5">Chargers, Earbuds & Cases</div>
            </div>
            <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
        </div>

        <!-- Pending Repairs Card -->
        <div class="bg-white rounded border border-slate-200 p-3 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-xs text-slate-500 font-medium">Active Repair Jobs</div>
                <div class="text-xl font-bold text-slate-900 mt-0.5">{{ $pendingRepairsCount }} <span class="text-xs font-normal text-slate-500">jobs</span></div>
                <div class="text-[11px] text-rose-600 mt-0.5">In service center queue</div>
            </div>
            <div class="w-10 h-10 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </div>
        </div>

        <!-- Expiring Warranty Card -->
        <div class="bg-white rounded border border-slate-200 p-3 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-xs text-slate-500 font-medium">Expiring Warranties</div>
                <div class="text-xl font-bold text-slate-900 mt-0.5">{{ $expiringWarrantiesCount }} <span class="text-xs font-normal text-slate-500">devices</span></div>
                <div class="text-[11px] text-amber-600 mt-0.5">Expiring in next 30 days</div>
            </div>
            <div class="w-10 h-10 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- IMEI Lifecycle Traceability Status Bar -->
    <div class="bg-slate-900 text-white rounded p-3 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <div class="text-xs font-semibold text-slate-200 uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                <span>IMEI Lifecycle Registry Overview</span>
            </div>
            <a href="{{ route('imei.index') }}" class="text-[11px] text-indigo-400 hover:text-indigo-300 font-medium">View Full Registry &rarr;</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2 text-center text-xs">
            <div class="bg-slate-800 rounded p-1.5 border border-slate-700">
                <div class="text-slate-400 text-[10px]">Total IMEIs</div>
                <div class="font-bold text-white text-sm mt-0.5">{{ $imeiStats['total'] }}</div>
            </div>
            <div class="bg-slate-800 rounded p-1.5 border border-emerald-900/50">
                <div class="text-emerald-400 text-[10px]">In Stock</div>
                <div class="font-bold text-emerald-400 text-sm mt-0.5">{{ $imeiStats['in_stock'] }}</div>
            </div>
            <div class="bg-slate-800 rounded p-1.5 border border-blue-900/50">
                <div class="text-blue-400 text-[10px]">Sold</div>
                <div class="font-bold text-blue-400 text-sm mt-0.5">{{ $imeiStats['sold'] }}</div>
            </div>
            <div class="bg-slate-800 rounded p-1.5 border border-amber-900/50">
                <div class="text-amber-400 text-[10px]">Reserved</div>
                <div class="font-bold text-amber-400 text-sm mt-0.5">{{ $imeiStats['reserved'] }}</div>
            </div>
            <div class="bg-slate-800 rounded p-1.5 border border-rose-900/50">
                <div class="text-rose-400 text-[10px]">Under Repair</div>
                <div class="font-bold text-rose-400 text-sm mt-0.5">{{ $imeiStats['under_repair'] }}</div>
            </div>
            <div class="bg-slate-800 rounded p-1.5 border border-slate-700">
                <div class="text-slate-400 text-[10px]">Damaged / Loss</div>
                <div class="font-bold text-slate-300 text-sm mt-0.5">{{ $imeiStats['damaged'] }}</div>
            </div>
        </div>
    </div>

    <!-- Interactive Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
        <!-- Sales Trend Line Chart (2 Columns) -->
        <div class="lg:col-span-2 bg-white rounded border border-slate-200 p-3 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">7-Day Sales Performance (₹)</h3>
                <span class="text-[10px] text-slate-400">Database actuals</span>
            </div>
            <div class="h-56">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>

        <!-- Brand Distribution Doughnut Chart (1 Column) -->
        <div class="bg-white rounded border border-slate-200 p-3 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Sales by Brand</h3>
                <span class="text-[10px] text-slate-400">Share</span>
            </div>
            <div class="h-56 flex items-center justify-center">
                <canvas id="brandDistributionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Lower Operational Grid: Recent Sales & Stock Alerts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
        <!-- Recent Sales Table (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-3 py-2 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Recent Transactions</h3>
                <a href="{{ route('sales.index') }}" class="text-[11px] text-indigo-600 hover:text-indigo-800 font-medium">View All &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left compact-table">
                    <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                        <tr>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Payment</th>
                            <th>Total</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentSales as $s)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="font-semibold text-indigo-600">
                                    <a href="{{ route('sales.show', $s->id) }}">{{ $s->invoice_number }}</a>
                                    <div class="text-[10px] text-slate-400">{{ $s->sale_date->format('d M, Y') }}</div>
                                </td>
                                <td>
                                    <div class="font-medium text-slate-900">{{ $s->customer?->name ?: 'Walk-in Customer' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $s->customer?->phone ?: '-' }}</div>
                                </td>
                                <td>
                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 text-[10px]">
                                        {{ $s->items->count() }} item(s)
                                    </span>
                                </td>
                                <td>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-medium uppercase
                                        {{ $s->payment_method === 'upi' ? 'bg-purple-100 text-purple-700' : '' }}
                                        {{ $s->payment_method === 'cash' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                        {{ $s->payment_method === 'card' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $s->payment_method === 'split' ? 'bg-amber-100 text-amber-700' : '' }}">
                                        {{ $s->payment_method }}
                                    </span>
                                </td>
                                <td class="font-bold text-slate-900">
                                    ₹{{ number_format($s->grand_total, 2) }}
                                </td>
                                <td class="text-right space-x-1">
                                    <a href="{{ route('sales.thermal', $s->id) }}" target="_blank" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px]" title="Thermal Receipt">Receipt</a>
                                    <a href="{{ route('sales.invoice', $s->id) }}" target="_blank" class="px-1.5 py-0.5 rounded bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[10px]" title="Tax Invoice A4">A4</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-6 text-slate-400">No transactions recorded today yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Low Stock Alerts (1 col) -->
        <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="px-3 py-2 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                        <span>Low Stock Alerts</span>
                    </h3>
                    <a href="{{ route('inventory.index') }}" class="text-[11px] text-indigo-600 hover:text-indigo-800 font-medium">View All &rarr;</a>
                </div>
                <div class="p-2 divide-y divide-slate-100">
                    @forelse($lowStockProducts as $lp)
                        <div class="py-2 flex items-center justify-between text-xs">
                            <div>
                                <div class="font-medium text-slate-900">{{ $lp->name }}</div>
                                <div class="text-[10px] text-slate-400">{{ $lp->brand?->name }} | Min Stock: {{ $lp->min_stock }}</div>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-0.5 rounded font-bold text-[11px] {{ $lp->totalStockForBranch($currentBranch?->id) == 0 ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $lp->totalStockForBranch($currentBranch?->id) }} left
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-xs">
                            All products are adequately stocked.
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="p-2.5 bg-slate-50 border-t border-slate-100 text-center">
                <a href="{{ route('purchases.create') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">
                    + Create Reorder Purchase Order
                </a>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Daily Sales Trend Chart
        const salesCtx = document.getElementById('salesTrendChart')?.getContext('2d');
        if (salesCtx) {
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: @json($dailySales['labels'] ?? []),
                    datasets: [{
                        label: 'Daily Sales (₹)',
                        data: @json($dailySales['data'] ?? []),
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.08)',
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: '#4f46e5',
                        pointRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: { size: 10 },
                                callback: val => '₹' + Number(val).toLocaleString('en-IN')
                            }
                        },
                        x: { ticks: { font: { size: 10 } } }
                    }
                }
            });
        }

        // Brand Distribution Chart
        const brandCtx = document.getElementById('brandDistributionChart')?.getContext('2d');
        if (brandCtx) {
            const brandData = @json($brandSales ?? []);
            new Chart(brandCtx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(brandData),
                    datasets: [{
                        data: Object.values(brandData),
                        backgroundColor: ['#4f46e5', '#06b6d4', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6'],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
