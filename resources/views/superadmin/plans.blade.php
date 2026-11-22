@extends('layouts.app')

@section('title', 'Subscription Plans Configuration')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Subscription Plans</h1>
            <p class="text-xs text-slate-500">Configure multi-tenant SaaS tiers, feature limits, and pricing</p>
        </div>
        <a href="{{ route('superadmin.dashboard') }}" class="btn btn-secondary text-xs">Back to Overview</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Add Plan Form -->
        <div class="card p-4 bg-white space-y-3">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider border-b border-slate-100 pb-2">Create New Plan</h3>
            <form method="POST" action="{{ route('superadmin.plans.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="form-label text-xs font-semibold text-slate-700">Plan Name *</label>
                    <input type="text" name="name" class="form-input text-xs" required placeholder="e.g. Enterprise Tier" value="{{ old('name') }}">
                </div>
                <div>
                    <label class="form-label text-xs font-semibold text-slate-700">Slug *</label>
                    <input type="text" name="slug" class="form-input text-xs font-mono" required placeholder="enterprise-tier" value="{{ old('slug') }}">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="form-label text-xs font-semibold text-slate-700">Price (₹) *</label>
                        <input type="number" step="0.01" name="price" class="form-input text-xs" required placeholder="4999.00" value="{{ old('price') }}">
                    </div>
                    <div>
                        <label class="form-label text-xs font-semibold text-slate-700">Billing Interval *</label>
                        <select name="billing_interval" class="form-input text-xs" required>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="form-label text-xs font-semibold text-slate-700">Branch Limit *</label>
                        <input type="number" name="branch_limit" class="form-input text-xs" required value="5">
                    </div>
                    <div>
                        <label class="form-label text-xs font-semibold text-slate-700">User Limit *</label>
                        <input type="number" name="user_limit" class="form-input text-xs" required value="15">
                    </div>
                    <div>
                        <label class="form-label text-xs font-semibold text-slate-700">Product Limit *</label>
                        <input type="number" name="product_limit" class="form-input text-xs" required value="5000">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary text-xs w-full mt-2">Save Plan</button>
            </form>
        </div>

        <!-- Plans Cards Grid -->
        <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($plans as $plan)
            <div class="card p-4 bg-white flex flex-col justify-between border-t-4 {{ $plan->price > 0 ? 'border-t-indigo-600' : 'border-t-slate-400' }}">
                <div>
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-sm text-slate-800">{{ $plan->name }}</h4>
                        <span class="badge badge-info text-[10px]">{{ $plan->tenants_count }} Subscribers</span>
                    </div>
                    <div class="mt-2">
                        <span class="text-2xl font-extrabold text-slate-900">₹{{ number_format($plan->price) }}</span>
                        <span class="text-xs text-slate-400">/ {{ $plan->billing_interval }}</span>
                    </div>
                    <ul class="mt-3 space-y-1.5 text-xs text-slate-600 border-t border-slate-100 pt-3">
                        <li class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            Up to <strong>{{ $plan->branch_limit }}</strong> Store Branches
                        </li>
                        <li class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            Up to <strong>{{ $plan->user_limit }}</strong> Staff User Accounts
                        </li>
                        <li class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            Up to <strong>{{ number_format($plan->product_limit) }}</strong> Catalog SKUs
                        </li>
                    </ul>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
