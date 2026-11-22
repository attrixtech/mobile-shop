@extends('layouts.app')

@section('title', 'Customer Profile: ' . $customer->name)

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                {{ substr($customer->name, 0, 2) }}
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800">{{ $customer->name }}</h1>
                <p class="text-xs text-slate-500">Customer 360° Profile &bull; {{ ucfirst($customer->customer_type) }} &bull; Since {{ $customer->created_at->format('M Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('customers.index') }}" class="btn btn-secondary text-xs">Back</a>
            <a href="{{ route('pos.index', ['customer_id' => $customer->id]) }}" class="btn btn-primary text-xs">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                New POS Sale
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="card p-3 bg-white">
            <span class="text-xs text-slate-500 font-medium">Total Purchases</span>
            <div class="text-lg font-bold text-slate-800 mt-1">₹{{ number_format($customer->sales->sum('grand_total'), 2) }}</div>
            <span class="text-[10px] text-slate-400">{{ $customer->sales->count() }} completed invoices</span>
        </div>
        <div class="card p-3 bg-white">
            <span class="text-xs text-slate-500 font-medium">Loyalty Points</span>
            <div class="text-lg font-bold text-amber-600 mt-1">{{ number_format($customer->loyalty_points) }} pts</div>
            <span class="text-[10px] text-emerald-600">Active reward balance</span>
        </div>
        <div class="card p-3 bg-white">
            <span class="text-xs text-slate-500 font-medium">Repairs & Services</span>
            <div class="text-lg font-bold text-indigo-600 mt-1">{{ $customer->repairJobs->count() }} jobs</div>
            <span class="text-[10px] text-slate-400">Total job cards registered</span>
        </div>
        <div class="card p-3 bg-white">
            <span class="text-xs text-slate-500 font-medium">Registered Devices</span>
            <div class="text-lg font-bold text-emerald-600 mt-1">{{ $customer->imeiDevices->count() }} IMEIs</div>
            <span class="text-[10px] text-slate-400">Active serials linked</span>
        </div>
    </div>

    <!-- Detail Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Customer Info -->
        <div class="card p-4 bg-white space-y-3">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider border-b border-slate-100 pb-2">Customer Details</h3>
            <div class="text-xs space-y-2">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">Phone:</span>
                    <span class="font-medium text-slate-800">{{ $customer->phone }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">Email:</span>
                    <span class="font-medium text-slate-800">{{ $customer->email ?: '—' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">GSTIN:</span>
                    <span class="font-mono font-medium text-slate-800">{{ $customer->gstin ?: 'Unregistered' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">Credit Limit:</span>
                    <span class="font-medium text-slate-800">₹{{ number_format($customer->credit_limit, 2) }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-500">Address:</span>
                    <span class="text-right text-slate-700 max-w-[180px] truncate">{{ $customer->address ?: '—' }}, {{ $customer->city }} {{ $customer->pincode }}</span>
                </div>
            </div>
        </div>

        <!-- Sales History & Devices -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Invoices Table -->
            <div class="card bg-white overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Purchase History</h3>
                    <span class="text-xs text-slate-400">{{ $customer->sales->count() }} sales</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="compact-table w-full text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 border-b border-slate-200">
                                <th class="text-left py-2 px-3">Invoice #</th>
                                <th class="text-left py-2 px-3">Date</th>
                                <th class="text-right py-2 px-3">Total</th>
                                <th class="text-center py-2 px-3">Payment</th>
                                <th class="text-right py-2 px-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($customer->sales as $sale)
                            <tr class="hover:bg-slate-50">
                                <td class="py-2 px-3 font-semibold text-indigo-600">
                                    <a href="{{ route('sales.show', $sale->id) }}">{{ $sale->invoice_number }}</a>
                                </td>
                                <td class="py-2 px-3 text-slate-600">{{ $sale->sale_date->format('d M Y') }}</td>
                                <td class="py-2 px-3 text-right font-medium">₹{{ number_format($sale->grand_total, 2) }}</td>
                                <td class="py-2 px-3 text-center">
                                    <span class="badge badge-success text-[10px]">{{ ucfirst($sale->payment_status) }}</span>
                                </td>
                                <td class="py-2 px-3 text-right">
                                    <a href="{{ route('sales.invoice', $sale->id) }}" target="_blank" class="text-indigo-600 hover:underline">Invoice</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-slate-400">No purchases found for this customer.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Linked IMEI Devices -->
            <div class="card bg-white overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Registered IMEI Devices</h3>
                    <span class="text-xs text-slate-400">{{ $customer->imeiDevices->count() }} devices</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="compact-table w-full text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 border-b border-slate-200">
                                <th class="text-left py-2 px-3">Device / Model</th>
                                <th class="text-left py-2 px-3">Primary IMEI</th>
                                <th class="text-left py-2 px-3">Warranty Expiry</th>
                                <th class="text-center py-2 px-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($customer->imeiDevices as $device)
                            <tr class="hover:bg-slate-50">
                                <td class="py-2 px-3 font-medium text-slate-800">{{ $device->product->name ?? 'Device' }}</td>
                                <td class="py-2 px-3 font-mono text-slate-600">
                                    <a href="{{ route('imei.show', $device->id) }}" class="text-indigo-600 hover:underline">{{ $device->imei1 }}</a>
                                </td>
                                <td class="py-2 px-3 text-slate-500">
                                    {{ $device->warranty_expiry_date ? $device->warranty_expiry_date->format('d M Y') : 'N/A' }}
                                    @if($device->warranty_expiry_date && $device->warranty_expiry_date->isFuture())
                                        <span class="text-[10px] text-emerald-600 ml-1 font-semibold">(Active)</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3 text-center">
                                    <span class="badge badge-info text-[10px]">{{ ucfirst($device->status) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-3 text-center text-slate-400">No IMEI devices linked directly.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
