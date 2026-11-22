@extends('layouts.app')

@section('title', 'Platform SaaS Overview')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">SaaS Multi-Tenant Super Admin</h1>
            <p class="text-xs text-slate-500">Platform overview, active tenants, subscription tiers, and monthly recurring revenue</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('superadmin.tenants') }}" class="btn btn-secondary text-xs">Manage Tenants</a>
            <a href="{{ route('superadmin.plans') }}" class="btn btn-primary text-xs">Subscription Plans</a>
        </div>
    </div>

    <!-- KPI Summary Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="card p-3 bg-white">
            <span class="text-xs text-slate-500 font-medium">Total Tenants</span>
            <div class="text-lg font-bold text-slate-800 mt-1">{{ number_format($totalTenants) }} shops</div>
            <span class="text-[10px] text-slate-400">Total registered retailers</span>
        </div>
        <div class="card p-3 bg-white">
            <span class="text-xs text-slate-500 font-medium">Active Subscriptions</span>
            <div class="text-lg font-bold text-emerald-600 mt-1">{{ number_format($activeTenants) }} live</div>
            <span class="text-[10px] text-emerald-700 font-semibold">{{ $trialTenants }} currently in free trial</span>
        </div>
        <div class="card p-3 bg-white">
            <span class="text-xs text-slate-500 font-medium">Platform Users</span>
            <div class="text-lg font-bold text-indigo-600 mt-1">{{ number_format($totalUsers) }} accounts</div>
            <span class="text-[10px] text-indigo-700">Cashiers, managers, owners</span>
        </div>
        <div class="card p-3 bg-white">
            <span class="text-xs text-slate-500 font-medium">Platform Billings</span>
            <div class="text-lg font-bold text-purple-600 mt-1">₹{{ number_format($totalPlatformRevenue, 2) }}</div>
            <span class="text-[10px] text-purple-700">SaaS subscription revenue</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Recent Tenants -->
        <div class="lg:col-span-2 card bg-white overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Recently Registered Tenants</h3>
                <a href="{{ route('superadmin.tenants') }}" class="text-xs text-indigo-600 hover:underline">View All &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="compact-table w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 border-b border-slate-200">
                            <th class="text-left py-2 px-3">Tenant / Shop</th>
                            <th class="text-left py-2 px-3">Subdomain</th>
                            <th class="text-left py-2 px-3">Plan</th>
                            <th class="text-center py-2 px-3">Status</th>
                            <th class="text-right py-2 px-3">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentTenants as $t)
                        <tr class="hover:bg-slate-50">
                            <td class="py-2 px-3 font-semibold text-slate-800">
                                {{ $t->name }}
                            </td>
                            <td class="py-2 px-3 font-mono text-slate-500">{{ $t->subdomain }}.webotapp.com</td>
                            <td class="py-2 px-3 font-medium text-indigo-600">{{ $t->plan->name ?? 'Free Tier' }}</td>
                            <td class="py-2 px-3 text-center">
                                @if($t->status === 'active')
                                    <span class="badge badge-success text-[10px]">Active</span>
                                @elseif($t->status === 'trial')
                                    <span class="badge badge-warning text-[10px]">Trial</span>
                                @else
                                    <span class="badge badge-danger text-[10px]">{{ ucfirst($t->status) }}</span>
                                @endif
                            </td>
                            <td class="py-2 px-3 text-right text-slate-500">{{ $t->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-slate-400">No tenants registered yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Plan Breakdown -->
        <div class="card bg-white p-4 space-y-3">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider border-b border-slate-100 pb-2">Plan Distribution</h3>
            <div class="space-y-2">
                @foreach($plans as $plan)
                <div class="flex items-center justify-between p-2.5 rounded-lg border border-slate-100 bg-slate-50">
                    <div>
                        <div class="font-bold text-xs text-slate-800">{{ $plan->name }}</div>
                        <div class="text-[11px] text-slate-500">₹{{ number_format($plan->price) }} / {{ $plan->billing_interval }}</div>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-extrabold text-indigo-600">{{ $plan->tenants_count }}</span>
                        <span class="text-[10px] text-slate-400 block">tenants</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
