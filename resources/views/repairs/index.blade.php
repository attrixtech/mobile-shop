@extends('layouts.app')

@section('title', 'Repair Center - Webotapp Mobile Shop ERP')

@section('content')
<div class="space-y-3">
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">Mobile Service & Repair Job Cards</h1>
            <p class="text-xs text-slate-500">Track device repair lifecycle from intake diagnosis to customer delivery</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('settings.users') }}" class="px-3 py-1.5 rounded bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold border border-slate-300 shadow-2xs flex items-center gap-1.5 transition">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span>Manage Service Engineers</span>
            </a>
            <a href="{{ route('repairs.create') }}" class="px-3 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-sm flex items-center gap-1.5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>New Repair Job Card</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-2.5 rounded border border-slate-200 shadow-sm text-xs">
        <form action="{{ route('repairs.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-2">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Job #, IMEI, Device, Customer..." class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <select name="status" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                    <option value="">All Repair Statuses</option>
                    <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>Received</option>
                    <option value="under_inspection" {{ request('status') === 'under_inspection' ? 'selected' : '' }}>Under Inspection</option>
                    <option value="parts_required" {{ request('status') === 'parts_required' ? 'selected' : '' }}>Parts Required</option>
                    <option value="under_repair" {{ request('status') === 'under_repair' ? 'selected' : '' }}>Under Repair</option>
                    <option value="testing" {{ request('status') === 'testing' ? 'selected' : '' }}>Testing</option>
                    <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>Ready for Delivery</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="flex items-center gap-1.5">
                <button type="submit" class="px-3 py-1.5 rounded bg-indigo-600 text-white font-medium hover:bg-indigo-500 text-xs">Filter</button>
                <a href="{{ route('repairs.index') }}" class="px-2.5 py-1.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200 text-xs">Reset</a>
            </div>
        </form>
    </div>

    <!-- Job Cards Table -->
    <div class="bg-white rounded border border-slate-200 shadow-sm overflow-hidden text-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left compact-table">
                <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th>Job Card #</th>
                        <th>Customer</th>
                        <th>Device & Serial</th>
                        <th>Reported Problem</th>
                        <th>Technician</th>
                        <th>Estimated / Final (₹)</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($jobs as $j)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="font-bold text-indigo-600 font-mono">
                                <a href="{{ route('repairs.show', $j->id) }}">{{ $j->job_number }}</a>
                                <div class="text-[10px] text-slate-400 font-sans">{{ $j->received_at->format('d M, Y') }}</div>
                            </td>
                            <td>
                                <div class="font-bold text-slate-900">{{ $j->customer?->name }}</div>
                                <div class="text-[10px] text-slate-400">{{ $j->customer?->phone }}</div>
                            </td>
                            <td>
                                <div class="font-bold text-slate-900">{{ $j->brand }} {{ $j->model }}</div>
                                @if($j->imei)
                                    <div class="text-[10px] text-slate-500 font-mono">IMEI: {{ $j->imei }}</div>
                                @endif
                            </td>
                            <td class="max-w-xs truncate text-slate-700" title="{{ $j->reported_problem }}">
                                {{ $j->reported_problem }}
                            </td>
                            <td>{{ $j->technician?->name ?: 'Unassigned' }}</td>
                            <td>
                                <div class="font-bold text-slate-900">₹{{ number_format($j->final_cost ?: $j->estimated_cost, 2) }}</div>
                                @if($j->advance_paid > 0)
                                    <div class="text-[10px] text-emerald-600">Advance: ₹{{ number_format($j->advance_paid, 2) }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase
                                    {{ in_array($j->status, ['ready', 'delivered']) ? 'bg-emerald-100 text-emerald-700' : '' }}
                                    {{ in_array($j->status, ['under_repair', 'testing']) ? 'bg-indigo-100 text-indigo-700' : '' }}
                                    {{ in_array($j->status, ['received', 'under_inspection']) ? 'bg-amber-100 text-amber-700' : '' }}
                                    {{ in_array($j->status, ['parts_required', 'cancelled']) ? 'bg-rose-100 text-rose-700' : '' }}">
                                    {{ str_replace('_', ' ', $j->status) }}
                                </span>
                            </td>
                            <td class="text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('repairs.show', $j->id) }}" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs">View Ticket</a>
                                <a href="{{ route('repairs.print', $j->id) }}" target="_blank" class="px-2 py-0.5 rounded bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs">Print Card</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-400">No repair jobs matching query found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-2 border-t border-slate-200">
            {{ $jobs->links() }}
        </div>
    </div>
</div>
@endsection
