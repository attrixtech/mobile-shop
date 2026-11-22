<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Webotapp Mobile Shop ERP')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        [x-cloak] { display: none !important; }
        /* Compact table styling */
        .compact-table th { padding: 0.5rem 0.75rem; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; }
        .compact-table td { padding: 0.5rem 0.75rem; font-size: 0.8125rem; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex flex-col" x-data="{ sidebarOpen: false }">

    <!-- Top Navigation Bar -->
    <header class="bg-slate-900 text-white border-b border-slate-800 sticky top-0 z-30">
        <div class="px-4 py-2 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-1.5 text-slate-400 hover:text-white rounded">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                    <div class="w-7 h-7 rounded bg-indigo-600 flex items-center justify-center font-bold text-white text-sm">W</div>
                    <span class="font-bold tracking-tight text-white text-sm sm:text-base">Webotapp <span class="text-indigo-400 text-xs px-1.5 py-0.5 rounded bg-indigo-950 font-normal border border-indigo-800">Mobile ERP</span></span>
                </a>
            </div>

            <div class="flex items-center space-x-2 sm:space-x-3">
                <!-- Branch Selector -->
                @if(auth()->check() && auth()->user()->tenant_id)
                    @php
                        $userBranches = \App\Models\Branch::where('is_active', true)->get();
                        $activeBranchId = session('active_branch_id', auth()->user()->branch_id);
                    @endphp
                    <form action="{{ route('branch.switch') }}" method="POST" class="inline-flex items-center">
                        @csrf
                        <div class="flex items-center space-x-1.5 bg-slate-800 border border-slate-700 rounded px-2 py-1 text-xs text-slate-300">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            <select name="branch_id" onchange="this.form.submit()" class="bg-transparent text-white border-0 focus:ring-0 text-xs py-0 pl-1 pr-6 cursor-pointer">
                                @foreach($userBranches as $b)
                                    <option value="{{ $b->id }}" {{ $activeBranchId == $b->id ? 'selected' : '' }} class="bg-slate-900 text-white">
                                        {{ $b->name }} ({{ $b->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                @endif

                <!-- Quick Actions -->
                <a href="{{ route('pos.index') }}" class="inline-flex items-center space-x-1 px-2.5 py-1 rounded bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-medium shadow-sm transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    <span>POS Billing</span>
                </a>

                <a href="{{ route('warranty.index') }}" class="hidden md:inline-flex items-center space-x-1 px-2 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs border border-slate-700 transition">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span>IMEI Lookup</span>
                </a>

                <a href="{{ route('shop.index') }}" target="_blank" class="hidden sm:inline-flex items-center space-x-1 px-2 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs border border-slate-700 transition" title="View Public Catalog">
                    <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    <span>Catalog</span>
                </a>

                <!-- User Dropdown -->
                @if(auth()->check())
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-1.5 text-xs text-slate-300 hover:text-white px-2 py-1 rounded bg-slate-800 border border-slate-700">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            <span class="font-medium max-w-[100px] truncate">{{ auth()->user()->name }}</span>
                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg py-1 border border-slate-200 z-50 text-xs text-slate-700">
                            <div class="px-3 py-1.5 border-b border-slate-100 font-semibold text-slate-900">
                                {{ auth()->user()->name }}
                                <div class="text-[10px] text-slate-500 font-normal capitalize">{{ str_replace('_', ' ', auth()->user()->role) }}</div>
                            </div>
                            @if(auth()->user()->role === 'super_admin')
                                <a href="{{ route('superadmin.dashboard') }}" class="block px-3 py-1.5 hover:bg-slate-50 text-indigo-600 font-medium">SaaS Platform Admin</a>
                            @endif
                            <a href="{{ route('settings.company') }}" class="block px-3 py-1.5 hover:bg-slate-50">Company Settings</a>
                            <a href="{{ route('accounting.cash-register') }}" class="block px-3 py-1.5 hover:bg-slate-50">Cash Register</a>
                            <form action="{{ route('logout') }}" method="POST" class="border-t border-slate-100">
                                @csrf
                                <button type="submit" class="w-full text-left px-3 py-1.5 text-red-600 hover:bg-red-50">Sign Out</button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </header>

    <div class="flex-1 flex overflow-hidden">
        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" class="fixed lg:static inset-y-0 left-0 z-20 w-56 bg-slate-900 text-slate-300 border-r border-slate-800 transition-transform duration-200 ease-in-out flex flex-col justify-between pt-12 lg:pt-0">
            <div class="overflow-y-auto flex-1 py-3 px-2 space-y-1 text-xs">
                
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5 px-2.5 py-1.5 rounded font-medium transition {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Dashboard</span>
                </a>

                <div class="pt-2 pb-1 px-2.5 text-[10px] font-semibold tracking-wider text-slate-500 uppercase">Sales & Billing</div>
                
                <a href="{{ route('pos.index') }}" class="flex items-center space-x-2.5 px-2.5 py-1.5 rounded font-medium transition {{ request()->routeIs('pos.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    <span>Point of Sale (POS)</span>
                </a>

                <a href="{{ route('sales.index') }}" class="flex items-center space-x-2.5 px-2.5 py-1.5 rounded font-medium transition {{ request()->routeIs('sales.index') || request()->routeIs('sales.show') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <span>Sales Invoices</span>
                </a>

                <div class="pt-2 pb-1 px-2.5 text-[10px] font-semibold tracking-wider text-slate-500 uppercase">Catalog & Stock</div>

                <a href="{{ route('products.index') }}" class="flex items-center space-x-2.5 px-2.5 py-1.5 rounded font-medium transition {{ request()->routeIs('products.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    <span>Products & Variants</span>
                </a>

                <a href="{{ route('imei.index') }}" class="flex items-center space-x-2.5 px-2.5 py-1.5 rounded font-medium transition {{ request()->routeIs('imei.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                    <span>IMEI Registry</span>
                </a>

                <a href="{{ route('inventory.index') }}" class="flex items-center space-x-2.5 px-2.5 py-1.5 rounded font-medium transition {{ request()->routeIs('inventory.index') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    <span>Stock Overview</span>
                </a>

                <a href="{{ route('inventory.transfers') }}" class="flex items-center space-x-2.5 px-2.5 py-1.5 rounded font-medium transition {{ request()->routeIs('inventory.transfers*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    <span>Branch Transfers</span>
                </a>

                <div class="pt-2 pb-1 px-2.5 text-[10px] font-semibold tracking-wider text-slate-500 uppercase">Services & Repairs</div>

                <a href="{{ route('repairs.index') }}" class="flex items-center justify-between px-2.5 py-1.5 rounded font-medium transition {{ request()->routeIs('repairs.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                    <div class="flex items-center space-x-2.5">
                        <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        <span>Repair Center</span>
                    </div>
                </a>

                <a href="{{ route('exchange.index') }}" class="flex items-center space-x-2.5 px-2.5 py-1.5 rounded font-medium transition {{ request()->routeIs('exchange.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    <span>Exchange & Trade-In</span>
                </a>

                <a href="{{ route('warranty.index') }}" class="flex items-center space-x-2.5 px-2.5 py-1.5 rounded font-medium transition {{ request()->routeIs('warranty.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span>Warranty Tracking</span>
                </a>

                <div class="pt-2 pb-1 px-2.5 text-[10px] font-semibold tracking-wider text-slate-500 uppercase">Purchases & CRM</div>

                <a href="{{ route('purchases.index') }}" class="flex items-center space-x-2.5 px-2.5 py-1.5 rounded font-medium transition {{ request()->routeIs('purchases.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span>Purchases & GRN</span>
                </a>

                <a href="{{ route('suppliers.index') }}" class="flex items-center space-x-2.5 px-2.5 py-1.5 rounded font-medium transition {{ request()->routeIs('suppliers.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span>Suppliers</span>
                </a>

                <a href="{{ route('customers.index') }}" class="flex items-center space-x-2.5 px-2.5 py-1.5 rounded font-medium transition {{ request()->routeIs('customers.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span>Customer CRM</span>
                </a>

                <div class="pt-2 pb-1 px-2.5 text-[10px] font-semibold tracking-wider text-slate-500 uppercase">Finance & Reports</div>

                <a href="{{ route('accounting.cash-register') }}" class="flex items-center space-x-2.5 px-2.5 py-1.5 rounded font-medium transition {{ request()->routeIs('accounting.cash-register*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Cash Register</span>
                </a>

                <a href="{{ route('expenses.index') }}" class="flex items-center space-x-2.5 px-2.5 py-1.5 rounded font-medium transition {{ request()->routeIs('expenses.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span>Store Expenses</span>
                </a>

                <a href="{{ route('accounting.index') }}" class="flex items-center space-x-2.5 px-2.5 py-1.5 rounded font-medium transition {{ request()->routeIs('accounting.index') || request()->routeIs('accounting.journal') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <span>General Ledger</span>
                </a>

                <a href="{{ route('reports.sales') }}" class="flex items-center space-x-2.5 px-2.5 py-1.5 rounded font-medium transition {{ request()->routeIs('reports.sales') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Sales Report</span>
                </a>

                <a href="{{ route('reports.profitability') }}" class="flex items-center space-x-2.5 px-2.5 py-1.5 rounded font-medium transition {{ request()->routeIs('reports.profitability') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    <span>Profitability Analysis</span>
                </a>

                <a href="{{ route('reports.gst') }}" class="flex items-center space-x-2.5 px-2.5 py-1.5 rounded font-medium transition {{ request()->routeIs('reports.gst') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                    <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                    <span>GST (GSTR-1) Summary</span>
                </a>

                <div class="pt-2 pb-1 px-2.5 text-[10px] font-semibold tracking-wider text-slate-500 uppercase">Configuration</div>

                <a href="{{ route('settings.company') }}" class="flex items-center space-x-2.5 px-2.5 py-1.5 rounded font-medium transition {{ request()->routeIs('settings.*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>Store Settings</span>
                </a>
            </div>

            <div class="p-3 bg-slate-950 border-t border-slate-800 text-[11px] text-slate-400">
                <div class="flex items-center justify-between">
                    <span>Webotapp v2.4</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-emerald-950 text-emerald-400 border border-emerald-800">Production</span>
                </div>
            </div>
        </aside>

        <!-- Main Workspace -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-5">
            <!-- Alert Messages -->
            @if($errors->any())
                <div class="mb-4 bg-rose-50 border-l-4 border-rose-500 p-3 rounded shadow-sm text-xs text-rose-800" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center justify-between">
                        <div class="font-bold flex items-center space-x-2">
                            <svg class="w-4 h-4 text-rose-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                            <span>Please check the errors below:</span>
                        </div>
                        <button @click="show = false" class="text-rose-600 hover:text-rose-800 font-bold">&times;</button>
                    </div>
                    <ul class="mt-1.5 list-disc list-inside space-y-0.5 text-[11px]">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 bg-emerald-50 border-l-4 border-emerald-500 p-3 rounded shadow-sm text-xs text-emerald-800 flex items-center justify-between" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-emerald-600 hover:text-emerald-800">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-rose-50 border-l-4 border-rose-500 p-3 rounded shadow-sm text-xs text-rose-800 flex items-center justify-between" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-rose-600 hover:text-rose-800">&times;</button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
