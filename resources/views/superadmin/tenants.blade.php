@extends('layouts.app')

@section('title', 'Tenant Management')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Tenant Management</h1>
            <p class="text-xs text-slate-500">Monitor multi-tenant shops, switch plans, and activate or suspend tenant accounts</p>
        </div>
        <a href="{{ route('superadmin.dashboard') }}" class="btn btn-secondary text-xs">Back to Overview</a>
    </div>

    <!-- Tenants Table -->
    <div class="card bg-white overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">All Tenant Accounts</h3>
            <span class="text-xs text-slate-400">{{ $tenants->total() }} total</span>
        </div>
        <div class="overflow-x-auto">
            <table class="compact-table w-full text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-200">
                        <th class="text-left py-2 px-3">Tenant Name</th>
                        <th class="text-left py-2 px-3">Subdomain</th>
                        <th class="text-left py-2 px-3">Subscription Plan</th>
                        <th class="text-center py-2 px-3">Branches</th>
                        <th class="text-center py-2 px-3">Current Status</th>
                        <th class="text-right py-2 px-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tenants as $t)
                    <tr class="hover:bg-slate-50">
                        <td class="py-2.5 px-3">
                            <div class="font-bold text-slate-800">{{ $t->name }}</div>
                            <span class="text-[10px] text-slate-400">Created {{ $t->created_at->format('d M Y') }}</span>
                        </td>
                        <td class="py-2.5 px-3 font-mono text-indigo-600">{{ $t->subdomain }}.thrumble.com</td>
                        <td class="py-2.5 px-3">
                            <form method="POST" action="{{ route('superadmin.tenants.status', $t->id) }}" class="flex items-center gap-1">
                                @csrf
                                <select name="plan_id" class="form-input text-xs py-1 px-2 w-auto font-medium" onchange="this.form.submit()">
                                    @foreach($plans as $p)
                                        <option value="{{ $p->id }}" {{ $t->subscription_plan_id == $p->id ? 'selected' : '' }}>{{ $p->name }} (₹{{ number_format($p->price) }})</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="status" value="{{ $t->status }}">
                            </form>
                        </td>
                        <td class="py-2.5 px-3 text-center font-semibold text-slate-700">{{ $t->branches->count() }}</td>
                        <td class="py-2.5 px-3 text-center">
                            @if($t->status === 'active')
                                <span class="badge badge-success text-[10px]">Active</span>
                            @elseif($t->status === 'trial')
                                <span class="badge badge-warning text-[10px]">Trial</span>
                            @else
                                <span class="badge badge-danger text-[10px]">{{ ucfirst($t->status) }}</span>
                            @endif
                        </td>
                        <td class="py-2.5 px-3 text-right">
                            <form method="POST" action="{{ route('superadmin.tenants.status', $t->id) }}" class="inline-flex gap-1">
                                @csrf
                                <input type="hidden" name="plan_id" value="{{ $t->subscription_plan_id }}">
                                @if($t->status === 'active')
                                    <button type="submit" name="status" value="suspended" class="btn btn-danger text-[10px] py-1 px-2">Suspend</button>
                                @else
                                    <button type="submit" name="status" value="active" class="btn btn-primary text-[10px] py-1 px-2">Activate</button>
                                @endif
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-slate-400">No tenants registered yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tenants->hasPages())
            <div class="p-3 border-t border-slate-100">
                {{ $tenants->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
