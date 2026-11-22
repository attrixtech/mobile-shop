@extends('layouts.app')

@section('title', 'Team & Staff Management')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Team & Staff Access</h1>
            <p class="text-xs text-slate-500">Manage cashier, store manager, technician, and inventory manager user accounts</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Add Staff Form -->
        <div class="card p-4 bg-white space-y-3">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider border-b border-slate-100 pb-2">Add Team Member</h3>
            <form method="POST" action="{{ route('settings.users.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="form-label text-xs font-semibold text-slate-700">Full Name *</label>
                    <input type="text" name="name" class="form-input text-xs" required placeholder="Staff member name" value="{{ old('name') }}">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label text-xs font-semibold text-slate-700">Email Address *</label>
                    <input type="email" name="email" class="form-input text-xs" required placeholder="user@thrumble.com" value="{{ old('email') }}">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label text-xs font-semibold text-slate-700">Mobile Phone</label>
                    <input type="text" name="phone" class="form-input text-xs" placeholder="Mobile number" value="{{ old('phone') }}">
                </div>
                <div>
                    <label class="form-label text-xs font-semibold text-slate-700">Role & Permissions *</label>
                    <select name="role" class="form-input text-xs" required>
                        <option value="store_manager">Store Manager</option>
                        <option value="cashier" selected>Cashier / POS Operator</option>
                        <option value="sales_executive">Sales Executive</option>
                        <option value="inventory_manager">Inventory Manager</option>
                        <option value="repair_technician">Repair Technician</option>
                        <option value="accountant">Accountant</option>
                    </select>
                </div>
                <div>
                    <label class="form-label text-xs font-semibold text-slate-700">Assigned Branch</label>
                    <select name="branch_id" class="form-input text-xs">
                        <option value="">All Branches / Float</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }} ({{ $branch->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label text-xs font-semibold text-slate-700">Initial Password *</label>
                    <input type="password" name="password" class="form-input text-xs" required placeholder="Min 6 characters">
                </div>
                <button type="submit" class="btn btn-primary text-xs w-full mt-2">Add Member</button>
            </form>
        </div>

        <!-- Team Members Table -->
        <div class="lg:col-span-2 card bg-white overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Active Staff</h3>
                <span class="text-xs text-slate-400">{{ $users->count() }} members</span>
            </div>
            <div class="overflow-x-auto">
                <table class="compact-table w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 border-b border-slate-200">
                            <th class="text-left py-2 px-3">Name & Email</th>
                            <th class="text-left py-2 px-3">Role</th>
                            <th class="text-left py-2 px-3">Assigned Branch</th>
                            <th class="text-center py-2 px-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                        <tr class="hover:bg-slate-50">
                            <td class="py-2.5 px-3">
                                <div class="font-bold text-slate-800">{{ $user->name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $user->email }}</div>
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="badge badge-info text-[10px]">{{ ucwords(str_replace('_', ' ', $user->role)) }}</span>
                            </td>
                            <td class="py-2.5 px-3 text-slate-600 font-medium">
                                {{ $user->branch->name ?? 'All Branches' }}
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                @if($user->is_active)
                                    <span class="badge badge-success text-[10px]">Active</span>
                                @else
                                    <span class="badge badge-danger text-[10px]">Disabled</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-slate-400">No staff members found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
