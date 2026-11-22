@extends('layouts.app')

@section('title', 'Branch Management')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Branch Management</h1>
            <p class="text-xs text-slate-500">Configure multi-store retail branches, warehouse locations, and staff assignments</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Add Branch Form -->
        <div class="card p-4 bg-white space-y-3">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider border-b border-slate-100 pb-2">Add New Branch</h3>
            <form method="POST" action="{{ route('settings.branches.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="form-label text-xs font-semibold text-slate-700">Branch Name *</label>
                    <input type="text" name="name" class="form-input text-xs" required placeholder="e.g. Bandra West Store" value="{{ old('name') }}">
                </div>
                <div>
                    <label class="form-label text-xs font-semibold text-slate-700">Branch Code *</label>
                    <input type="text" name="code" class="form-input text-xs font-mono uppercase" required placeholder="e.g. BD02" value="{{ old('code') }}">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="form-label text-xs font-semibold text-slate-700">Phone</label>
                        <input type="text" name="phone" class="form-input text-xs" placeholder="Branch phone" value="{{ old('phone') }}">
                    </div>
                    <div>
                        <label class="form-label text-xs font-semibold text-slate-700">City</label>
                        <input type="text" name="city" class="form-input text-xs" placeholder="Mumbai" value="{{ old('city') }}">
                    </div>
                </div>
                <div>
                    <label class="form-label text-xs font-semibold text-slate-700">Address</label>
                    <input type="text" name="address" class="form-input text-xs" placeholder="Full address" value="{{ old('address') }}">
                </div>
                <button type="submit" class="btn btn-primary text-xs w-full mt-2">Create Branch</button>
            </form>
        </div>

        <!-- Branches List Table -->
        <div class="lg:col-span-2 card bg-white overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Active Store Locations</h3>
                <span class="text-xs text-slate-400">{{ $branches->count() }} locations</span>
            </div>
            <div class="overflow-x-auto">
                <table class="compact-table w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 border-b border-slate-200">
                            <th class="text-left py-2 px-3">Code / Name</th>
                            <th class="text-left py-2 px-3">Location</th>
                            <th class="text-center py-2 px-3">Staff</th>
                            <th class="text-center py-2 px-3">Stock SKUs</th>
                            <th class="text-center py-2 px-3">Sales</th>
                            <th class="text-center py-2 px-3">Type</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($branches as $branch)
                        <tr class="hover:bg-slate-50">
                            <td class="py-2.5 px-3">
                                <div class="font-bold text-slate-800">{{ $branch->name }}</div>
                                <span class="font-mono text-[10px] text-slate-400 font-semibold">{{ $branch->code }}</span>
                            </td>
                            <td class="py-2.5 px-3 text-slate-600">
                                <div>{{ $branch->city ?? '—' }}</div>
                                <span class="text-[10px] text-slate-400 truncate max-w-[150px] inline-block">{{ $branch->address }}</span>
                            </td>
                            <td class="py-2.5 px-3 text-center font-medium">{{ $branch->users_count }}</td>
                            <td class="py-2.5 px-3 text-center font-medium">{{ $branch->stock_balances_count }}</td>
                            <td class="py-2.5 px-3 text-center font-medium">{{ $branch->sales_count }}</td>
                            <td class="py-2.5 px-3 text-center">
                                @if($branch->is_main)
                                    <span class="badge badge-success text-[10px]">Head Office</span>
                                @else
                                    <span class="badge badge-info text-[10px]">Branch Store</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400">No branches configured.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
