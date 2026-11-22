<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $company->name ?? 'Webotapp Mobiles' }} - Premier Smartphones, Accessories & Repairs</title>
    <meta name="description" content="Shop latest smartphones, accessories, and instant device exchanges with certified repair services at {{ $company->name ?? 'Webotapp Mobiles' }} Mumbai.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        .font-display { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col selection:bg-indigo-500 selection:text-white">

    <!-- Top Announcement Strip -->
    <div class="bg-gradient-to-r from-slate-950 via-indigo-950 to-slate-900 text-white text-xs py-2 px-4 border-b border-indigo-900/40">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 text-center sm:text-left">
            <div class="flex items-center gap-2 text-slate-300">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-400 text-slate-950 tracking-wide uppercase">Festive Bonus</span>
                <span>Get up to <strong>₹15,000 Exchange Bonus</strong> on old devices &bull; 0% Interest EMI Available</span>
            </div>
            <div class="flex items-center gap-4 text-slate-400 text-[11px]">
                <span class="hidden md:inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                    Stores Open: 10:00 AM &ndash; 9:30 PM (All 7 Days)
                </span>
                <span class="inline-flex items-center gap-1 text-slate-300">
                    <svg class="w-3.5 h-3.5 text-indigo-400" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 4V3z"/></svg>
                    {{ $company->phone ?? '+91 98201 54321' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Navigation Header -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-40 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between gap-4">
            <!-- Brand Logo -->
            <a href="{{ route('shop.index') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-700 to-sky-500 text-white flex items-center justify-center font-black text-xl shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-transform">
                    W
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="font-display font-black text-slate-900 text-base sm:text-lg tracking-tight">{{ $company->name ?? 'Webotapp Mobiles' }}</span>
                        <span class="hidden sm:inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <svg class="w-3 h-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Verified Retailer
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 font-medium hidden sm:block">Mumbai's Trusted Multi-Brand Smart Store &bull; Andheri &amp; Bandra</p>
                </div>
            </a>

            <!-- Quick Nav Links -->
            <nav class="hidden lg:flex items-center gap-6 text-sm font-semibold text-slate-600">
                <a href="#catalog" class="hover:text-indigo-600 transition">Smartphones</a>
                <a href="#brands" class="hover:text-indigo-600 transition">Brands</a>
                <a href="#exchange" class="hover:text-indigo-600 transition flex items-center gap-1">
                    Exchange &amp; Buyback
                    <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-amber-100 text-amber-800">Bonus</span>
                </a>
                <a href="#repairs" class="hover:text-indigo-600 transition">30-Min Repairs</a>
                <a href="#branches" class="hover:text-indigo-600 transition">Stores</a>
            </nav>

            <!-- Actions: WhatsApp Inquiry & Login -->
            <div class="flex items-center gap-2 sm:gap-3">
                @if($company && $company->phone)
                    @php
                        $cleanPhone = preg_replace('/[^0-9]/', '', $company->phone);
                    @endphp
                    <a href="https://wa.me/{{ str_starts_with($cleanPhone, '91') ? $cleanPhone : '91'.$cleanPhone }}?text=Hello%20{{ urlencode($company->name ?? 'Webotapp') }},%20I%20am%20interested%20in%20purchasing%20a%20phone%20or%20booking%20a%20repair." target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs sm:text-sm font-bold shadow-md shadow-emerald-600/20 transition-all hover:shadow-emerald-600/30">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-300 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-100"></span>
                        </span>
                        <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.18-2.588-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.299.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.353.101.174.45 0.743.966 1.203.664.591 1.224.774 1.397.86.173.087.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/></svg>
                        <span>WhatsApp Us</span>
                    </a>
                @endif
                <a href="{{ route('login') }}" class="px-3.5 py-2 border border-slate-300 hover:border-slate-400 bg-white hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl transition shadow-xs">
                    Staff Portal
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-b from-slate-900 via-indigo-950 to-slate-900 text-white overflow-hidden py-16 sm:py-24">
        <!-- Background Glow FX -->
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[700px] h-[500px] bg-indigo-600/25 blur-[120px] rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-20 right-10 w-[400px] h-[300px] bg-sky-500/15 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Hero Left: Text & Badges -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-400/30 text-indigo-300 text-xs font-semibold backdrop-blur-sm">
                        <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                        Authorised Multi-Brand Retailer &bull; Mumbai Stores
                    </div>

                    <h1 class="font-display font-extrabold text-3xl sm:text-5xl lg:text-6xl tracking-tight leading-[1.15] text-white">
                        The Best Flagships. <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-300 via-sky-300 to-emerald-300">
                            Guaranteed Genuine.
                        </span>
                    </h1>

                    <p class="text-slate-300 text-sm sm:text-base lg:text-lg max-w-2xl leading-relaxed font-normal mx-auto lg:mx-0">
                        Explore the newest iPhones, Samsung Galaxy, OnePlus &amp; premium audio gear. Instant trade-in valuations, 0% finance, and same-day certified 30-minute repair service with official GST invoice.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-wrap items-center justify-center lg:justify-start gap-3 pt-2">
                        <a href="#catalog" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-lg shadow-indigo-600/30 transition-all hover:scale-105">
                            Browse In-Stock Devices &darr;
                        </a>
                        <a href="#exchange" class="px-5 py-3 rounded-xl bg-white/10 hover:bg-white/15 border border-white/20 text-white font-semibold text-sm backdrop-blur-sm transition">
                            Instant Old Phone Valuation
                        </a>
                        <a href="#repairs" class="px-5 py-3 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30 border border-emerald-500/40 text-emerald-300 font-semibold text-sm backdrop-blur-sm transition">
                            Book Screen / Battery Repair
                        </a>
                    </div>

                    <!-- Trust Stats Bar -->
                    <div class="pt-8 border-t border-slate-800/80 grid grid-cols-2 sm:grid-cols-4 gap-4 text-center sm:text-left">
                        <div class="space-y-1">
                            <div class="text-xl sm:text-2xl font-black font-display text-white">100%</div>
                            <div class="text-xs text-slate-400">Genuine Sealed Stock</div>
                        </div>
                        <div class="space-y-1">
                            <div class="text-xl sm:text-2xl font-black font-display text-emerald-400">30 Min</div>
                            <div class="text-xs text-slate-400">Fast Repair Turnaround</div>
                        </div>
                        <div class="space-y-1">
                            <div class="text-xl sm:text-2xl font-black font-display text-amber-400">₹15K</div>
                            <div class="text-xs text-slate-400">Max Trade-In Bonus</div>
                        </div>
                        <div class="space-y-1">
                            <div class="text-xl sm:text-2xl font-black font-display text-sky-400">2 Stores</div>
                            <div class="text-xs text-slate-400">Andheri &amp; Bandra Hubs</div>
                        </div>
                    </div>
                </div>

                <!-- Hero Right: Highlight Card / Promo Showcase -->
                <div class="lg:col-span-5">
                    <div class="relative mx-auto max-w-md bg-gradient-to-b from-slate-800/90 to-slate-900/95 rounded-3xl p-6 sm:p-7 border border-slate-700/80 shadow-2xl backdrop-blur-xl">
                        <!-- Floating pill -->
                        <div class="flex items-center justify-between pb-4 border-b border-slate-700/60">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-400 bg-indigo-950/80 border border-indigo-800/60 px-2 py-0.5 rounded-md">Featured Flagship</span>
                                <h3 class="text-base font-bold text-white mt-1">iPhone 16 Pro Max 256GB</h3>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                In Stock
                            </span>
                        </div>

                        <!-- Card Body Visual -->
                        <div class="py-6 flex items-center justify-center">
                            <div class="w-36 h-36 rounded-2xl bg-gradient-to-tr from-indigo-500/20 to-sky-400/20 border border-indigo-400/30 flex items-center justify-center p-4 relative group">
                                <svg class="w-24 h-24 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <rect x="5" y="2" width="14" height="20" rx="3" stroke-width="1.75"/>
                                    <line x1="12" y1="18" x2="12.01" y2="18" stroke-width="2.5" stroke-linecap="round"/>
                                    <line x1="10" y1="5" x2="14" y2="5" stroke-width="1.75" stroke-linecap="round"/>
                                </svg>
                                <span class="absolute -bottom-2 bg-indigo-600 text-[10px] font-bold text-white px-2 py-0.5 rounded-full shadow">
                                    Desert Titanium
                                </span>
                            </div>
                        </div>

                        <!-- Specs list -->
                        <div class="grid grid-cols-2 gap-2 text-xs py-2">
                            <div class="bg-slate-800/60 rounded-lg p-2 border border-slate-700/50">
                                <span class="text-slate-400 block text-[10px]">Chipset</span>
                                <span class="font-bold text-white">Apple A18 Pro</span>
                            </div>
                            <div class="bg-slate-800/60 rounded-lg p-2 border border-slate-700/50">
                                <span class="text-slate-400 block text-[10px]">Camera</span>
                                <span class="font-bold text-white">48MP Fusion 5x Tele</span>
                            </div>
                            <div class="bg-slate-800/60 rounded-lg p-2 border border-slate-700/50">
                                <span class="text-slate-400 block text-[10px]">Display</span>
                                <span class="font-bold text-white">6.9" ProMotion OLED</span>
                            </div>
                            <div class="bg-slate-800/60 rounded-lg p-2 border border-slate-700/50">
                                <span class="text-slate-400 block text-[10px]">Warranty</span>
                                <span class="font-bold text-emerald-400">1 Year Apple Care</span>
                            </div>
                        </div>

                        <!-- Price & CTA -->
                        <div class="mt-4 pt-4 border-t border-slate-700/60 flex items-center justify-between">
                            <div>
                                <span class="text-[11px] text-slate-400 block">Store Offer Price</span>
                                <div class="text-2xl font-extrabold text-white">₹1,34,900</div>
                            </div>
                            @if($company && $company->phone)
                                <a href="https://wa.me/{{ str_starts_with($cleanPhone, '91') ? $cleanPhone : '91'.$cleanPhone }}?text=Hi,%20is%20the%20iPhone%2016%20Pro%20Max%20available%20in-store?" target="_blank" rel="noopener" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition shadow-md shadow-emerald-600/30">
                                    Inquire Deal
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust Badges Bar -->
    <section class="bg-white border-b border-slate-200 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-sm font-bold text-slate-900">100% Original Products</h4>
                        <p class="text-[11px] text-slate-500">Official brand warranty &amp; seal</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-sm font-bold text-slate-900">30-Min Fast Repairs</h4>
                        <p class="text-[11px] text-slate-500">Certified technicians &amp; genuine parts</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-sm font-bold text-slate-900">Instant Trade-in Valuation</h4>
                        <p class="text-[11px] text-slate-500">Best exchange quote in 5 minutes</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-sm font-bold text-slate-900">GST Input Credit Invoices</h4>
                        <p class="text-[11px] text-slate-500">Save up to 18% for business buyers</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Brands Carousel / Filter Strip -->
    <section id="brands" class="py-8 bg-slate-100/70 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Official Brand Partners</h3>
                    <p class="text-sm font-bold text-slate-800">Explore by manufacturer or filter active catalog</p>
                </div>
                @if(request('brand') || request('category') || request('search'))
                    <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 px-3 py-1.5 rounded-lg transition w-fit">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Clear Active Filters
                    </a>
                @endif
            </div>

            <!-- Brand Badges -->
            <div class="flex items-center gap-2.5 overflow-x-auto pb-2 scrollbar-none">
                <a href="{{ route('shop.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold transition shrink-0 {{ !request('brand') ? 'bg-slate-900 text-white shadow-sm' : 'bg-white hover:bg-slate-200 text-slate-700 border border-slate-200' }}">
                    All Brands
                </a>
                @foreach($brands as $brand)
                    <a href="{{ route('shop.index', array_merge(request()->query(), ['brand' => $brand->slug])) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition shrink-0 flex items-center gap-2 {{ request('brand') == $brand->slug ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 border border-slate-200' }}">
                        <span>{{ $brand->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Main Catalog Section -->
    <section id="catalog" class="max-w-7xl mx-auto px-4 sm:px-6 py-10 flex-1 w-full space-y-8">
        <!-- Interactive Search & Category Filter Toolbar -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm flex flex-col lg:flex-row items-center justify-between gap-4">
            <!-- Search Input -->
            <form method="GET" action="{{ route('shop.index') }}" class="w-full lg:w-96">
                @if(request('brand'))
                    <input type="hidden" name="brand" value="{{ request('brand') }}">
                @endif
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search iPhone, Galaxy, earbuds, fast chargers..." class="w-full text-xs sm:text-sm pl-10 pr-24 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 bg-slate-50 hover:bg-white transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <button type="submit" class="absolute right-1.5 top-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition">
                        Search
                    </button>
                </div>
            </form>

            <!-- Category Filter Pills -->
            <div class="flex items-center gap-1.5 overflow-x-auto w-full lg:w-auto pb-1 lg:pb-0 scrollbar-none">
                <a href="{{ route('shop.index', array_filter(['brand' => request('brand'), 'search' => request('search')])) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition {{ !request('category') ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                    All Categories
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('shop.index', array_filter(['category' => $cat->slug, 'brand' => request('brand'), 'search' => request('search')])) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5 {{ request('category') == $cat->slug ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        <span>{{ $cat->name }}</span>
                        @if(isset($cat->products_count))
                            <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ request('category') == $cat->slug ? 'bg-indigo-700 text-indigo-100' : 'bg-slate-200 text-slate-600' }}">
                                {{ $cat->products_count }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Catalog Header Status -->
        <div class="flex items-center justify-between text-xs text-slate-500 px-1">
            <div>
                Showing <strong class="text-slate-800">{{ $products->total() }}</strong> verified devices &amp; accessories
                @if(request('brand'))
                    for brand <span class="font-bold text-indigo-600 uppercase">{{ request('brand') }}</span>
                @endif
                @if(request('category'))
                    in <span class="font-bold text-indigo-600">{{ request('category') }}</span>
                @endif
            </div>
            <div class="hidden sm:block text-slate-400">
                All prices inclusive of GST &bull; Instant delivery or in-store pickup
            </div>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            @forelse($products as $prod)
                @php
                    $approxMrp = $prod->selling_price * 1.15;
                    $discount = $approxMrp - $prod->selling_price;
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200 hover:border-indigo-400/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group">
                    <div class="p-5 space-y-4">
                        <!-- Top tags -->
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black uppercase tracking-wider text-indigo-600 bg-indigo-50 border border-indigo-100 px-2.5 py-1 rounded-md">
                                {{ $prod->brand->name ?? 'Device' }}
                            </span>
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                In Stock
                            </span>
                        </div>

                        <!-- Product Visual Illustration / Icon Box -->
                        <div class="py-4 flex items-center justify-center bg-slate-50/70 rounded-xl group-hover:bg-indigo-50/40 transition-colors">
                            <div class="w-20 h-20 rounded-xl bg-white shadow-sm border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-indigo-600 group-hover:scale-105 transition-all">
                                @if(stripos($prod->name, 'iphone') !== false || stripos($prod->name, 'galaxy') !== false || stripos($prod->name, 'pro') !== false || stripos($prod->name, 'phone') !== false || $prod->type === 'single')
                                    <!-- Phone Icon -->
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="3" stroke-width="1.75"/><line x1="12" y1="18" x2="12.01" y2="18" stroke-width="2.5" stroke-linecap="round"/><line x1="10" y1="5" x2="14" y2="5" stroke-width="1.75" stroke-linecap="round"/></svg>
                                @elseif(stripos($prod->name, 'buds') !== false || stripos($prod->name, 'airpods') !== false || stripos($prod->name, 'audio') !== false)
                                    <!-- Audio Icon -->
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z"/></svg>
                                @elseif(stripos($prod->name, 'charger') !== false || stripos($prod->name, 'adapter') !== false || stripos($prod->name, 'cable') !== false)
                                    <!-- Charger Icon -->
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                @else
                                    <!-- Device Icon -->
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                @endif
                            </div>
                        </div>

                        <!-- Product Title -->
                        <div>
                            <a href="{{ route('shop.show', $prod->id) }}" class="block">
                                <h3 class="font-display font-bold text-slate-900 text-sm sm:text-base leading-snug group-hover:text-indigo-600 transition line-clamp-2">
                                    {{ $prod->name }}
                                </h3>
                            </a>
                            <p class="text-[11px] text-slate-400 mt-1">Category: {{ $prod->category->name ?? 'Electronics' }} &bull; GST {{ $prod->gst_rate }}%</p>
                        </div>

                        <!-- Variants Indicator -->
                        @if($prod->variants->count() > 0)
                            <div class="bg-slate-50 border border-slate-100 rounded-lg p-2 text-[11px] text-slate-600">
                                <span class="font-bold text-slate-800">{{ $prod->variants->count() }} Variants:</span>
                                {{ $prod->variants->pluck('name')->take(2)->join(', ') }}{{ $prod->variants->count() > 2 ? '...' : '' }}
                            </div>
                        @else
                            <div class="text-[11px] text-slate-400 font-mono">
                                SKU: {{ $prod->sku }}
                            </div>
                        @endif
                    </div>

                    <!-- Card Bottom / Pricing & CTAs -->
                    <div class="p-4 bg-slate-50/80 border-t border-slate-100 space-y-3">
                        <div class="flex items-baseline justify-between">
                            <div>
                                <div class="text-[10px] text-slate-400 uppercase tracking-wide">Special Offer</div>
                                <div class="text-lg font-black font-display text-slate-900">
                                    ₹{{ number_format($prod->selling_price, 2) }}
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-slate-400 line-through">₹{{ number_format($approxMrp, 0) }}</span>
                                <span class="block text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.2 rounded">Save ₹{{ number_format($discount, 0) }}</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="grid grid-cols-2 gap-2 pt-1">
                            @if($company && $company->phone)
                                @php
                                    $inquiryMsg = "Hello " . ($company->name ?? 'Webotapp') . ", I would like to inquire about " . $prod->name . " (Price: Rs " . number_format($prod->selling_price, 2) . ") in-store stock.";
                                @endphp
                                <a href="https://wa.me/{{ str_starts_with($cleanPhone, '91') ? $cleanPhone : '91'.$cleanPhone }}?text={{ urlencode($inquiryMsg) }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-bold transition shadow-xs">
                                    <svg class="w-3.5 h-3.5 fill-current shrink-0" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.18-2.588-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.299.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.353.101.174.45 0.743.966 1.203.664.591 1.224.774 1.397.86.173.087.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/></svg>
                                    <span>Inquire</span>
                                </a>
                            @endif
                            <a href="{{ route('shop.show', $prod->id) }}" class="inline-flex items-center justify-center px-3 py-2 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold transition shadow-xs text-center">
                                View Specs
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-3xl border border-slate-200 p-12 text-center text-slate-500 shadow-sm space-y-4">
                    <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-base font-bold text-slate-800">No products found</h3>
                        <p class="text-xs text-slate-400">Try searching for another keyword, brand, or reset the active filters.</p>
                    </div>
                    <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 text-white font-semibold text-xs hover:bg-indigo-700 transition">
                        Reset All Filters
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $products->links() }}
            </div>
        @endif
    </section>

    <!-- Trade-In / Exchange Promotional Feature Section -->
    <section id="exchange" class="py-16 bg-gradient-to-br from-indigo-900 via-slate-900 to-indigo-950 text-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-7 space-y-5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-400 text-slate-950 tracking-wide uppercase">
                        Instant Trade-In &amp; Buyback
                    </span>
                    <h2 class="font-display font-black text-2xl sm:text-4xl tracking-tight leading-snug">
                        Upgrade Your Old Smartphone. <br>
                        <span class="text-amber-300">Get Up to ₹15,000 Exchange Bonus.</span>
                    </h2>
                    <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                        Don't let your old device sit in a drawer. Bring any working smartphone, iPhone, or iPad to our stores. Our automated diagnostic checklist evaluates your phone in 5 minutes and offers instant store credit or bank transfer.
                    </p>

                    <!-- 3 Step Process -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4">
                        <div class="bg-white/5 border border-white/10 rounded-2xl p-4">
                            <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-black text-sm mb-2">1</div>
                            <h4 class="text-xs font-bold text-white">Walk-in with Old Phone</h4>
                            <p class="text-[11px] text-slate-400 mt-1">Bring your old device with or without the original box and charger.</p>
                        </div>
                        <div class="bg-white/5 border border-white/10 rounded-2xl p-4">
                            <div class="w-8 h-8 rounded-lg bg-amber-500 text-slate-950 flex items-center justify-center font-black text-sm mb-2">2</div>
                            <h4 class="text-xs font-bold text-white">5-Min Fast Diagnosis</h4>
                            <p class="text-[11px] text-slate-400 mt-1">Screen, battery health, IMEI check, and camera grading in front of you.</p>
                        </div>
                        <div class="bg-white/5 border border-white/10 rounded-2xl p-4">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500 text-slate-950 flex items-center justify-center font-black text-sm mb-2">3</div>
                            <h4 class="text-xs font-bold text-white">Instant Credit / Cash</h4>
                            <p class="text-[11px] text-slate-400 mt-1">Deduct from your new phone bill or receive direct instant payout.</p>
                        </div>
                    </div>
                </div>

                <!-- Right Action Box -->
                <div class="lg:col-span-5">
                    <div class="bg-white text-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-5 border border-slate-100">
                        <h3 class="font-display font-extrabold text-xl text-slate-900">Get an Instant Valuation Estimate</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Message our evaluation specialists directly on WhatsApp with your phone model, storage capacity, and condition for an immediate price estimate.
                        </p>

                        <div class="space-y-3 bg-slate-50 p-4 rounded-xl border border-slate-200/80 text-xs text-slate-700">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>All brands accepted (Apple, Samsung, OnePlus, Vivo, etc.)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>Bonus value applied automatically on new phone purchase</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>Safe data wipe certificate provided upon trade-in</span>
                            </div>
                        </div>

                        @if($company && $company->phone)
                            <a href="https://wa.me/{{ str_starts_with($cleanPhone, '91') ? $cleanPhone : '91'.$cleanPhone }}?text=Hello%20Webotapp,%20I%20want%20to%20get%20an%20exchange%20quote%20for%20my%20old%20phone." target="_blank" rel="noopener" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-lg shadow-emerald-600/30 transition">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.18-2.588-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.299.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.353.101.174.45 0.743.966 1.203.664.591 1.224.774 1.397.86.173.087.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/></svg>
                                <span>Get Exchange Quote on WhatsApp</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Certified Repair Center Section -->
    <section id="repairs" class="py-16 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="text-center max-w-3xl mx-auto space-y-3 mb-12">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 tracking-wide uppercase border border-indigo-200">
                    Certified Repair &amp; Service Lab
                </span>
                <h2 class="font-display font-extrabold text-2xl sm:text-4xl text-slate-900 tracking-tight">
                    Broken Screen? Draining Battery? <br>
                    <span class="text-indigo-600">Fixed in 30 Minutes In-Store.</span>
                </h2>
                <p class="text-slate-500 text-xs sm:text-sm">
                    Walk in to either of our Mumbai service centers. Genuine grade-A OEM screens, factory batteries, and micro-soldering repairs with a 90-day comprehensive service warranty.
                </p>
            </div>

            <!-- Repair Service Matrix Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Screen Replacement -->
                <div class="bg-slate-50 hover:bg-white rounded-2xl p-6 border border-slate-200 hover:border-indigo-400 hover:shadow-lg transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center mb-4 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-1">Display &amp; Glass Repair</h3>
                    <p class="text-xs text-slate-500 leading-relaxed mb-4">Original OLED &amp; high-refresh LCD panels for iPhone, Samsung, and OnePlus. 30-min turnaround.</p>
                    <span class="text-xs font-bold text-indigo-600">From ₹1,499 &bull; 90-Day Warranty</span>
                </div>

                <!-- Battery Replacement -->
                <div class="bg-slate-50 hover:bg-white rounded-2xl p-6 border border-slate-200 hover:border-indigo-400 hover:shadow-lg transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-4 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-1">Battery Health Restoration</h3>
                    <p class="text-xs text-slate-500 leading-relaxed mb-4">Brand new high-density OEM batteries to restore 100% capacity and peak performance.</p>
                    <span class="text-xs font-bold text-emerald-600">From ₹999 &bull; Instant Calibration</span>
                </div>

                <!-- Charging & Port Fix -->
                <div class="bg-slate-50 hover:bg-white rounded-2xl p-6 border border-slate-200 hover:border-indigo-400 hover:shadow-lg transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center mb-4 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-1">Charging Port &amp; Mic</h3>
                    <p class="text-xs text-slate-500 leading-relaxed mb-4">Fast repair for loose Type-C/Lightning connectors, moisture damage, and ear speaker issues.</p>
                    <span class="text-xs font-bold text-amber-600">From ₹699 &bull; Same Day Ready</span>
                </div>

                <!-- Motherboard & Logic Board -->
                <div class="bg-slate-50 hover:bg-white rounded-2xl p-6 border border-slate-200 hover:border-indigo-400 hover:shadow-lg transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center mb-4 group-hover:bg-sky-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-1">Water Damage &amp; IC Repair</h3>
                    <p class="text-xs text-slate-500 leading-relaxed mb-4">Level-4 microscopic board repair, power IC, network baseband, and memory recovery.</p>
                    <span class="text-xs font-bold text-sky-600">Free Initial Diagnostic</span>
                </div>
            </div>

            <!-- Repair Call to Action Strip -->
            <div class="mt-8 bg-indigo-50 border border-indigo-200 rounded-2xl p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold">
                        📋
                    </div>
                    <div>
                        <div class="font-bold text-xs sm:text-sm text-indigo-950">Already have a device in repair with us?</div>
                        <p class="text-[11px] text-indigo-700">Check repair job status, parts restock, or final invoice estimate anytime.</p>
                    </div>
                </div>
                @if($company && $company->phone)
                    <a href="https://wa.me/{{ str_starts_with($cleanPhone, '91') ? $cleanPhone : '91'.$cleanPhone }}?text=Hello,%20I%20want%20to%20check%20my%20repair%20ticket%20status." target="_blank" rel="noopener" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/20 transition shrink-0">
                        Check Ticket Status on WhatsApp
                    </a>
                @endif
            </div>
        </div>
    </section>

    <!-- Physical Store Locations & Branch Details -->
    <section id="branches" class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="text-center max-w-2xl mx-auto space-y-2 mb-10">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Visit Our Experience Centers</span>
                <h2 class="font-display font-black text-2xl sm:text-3xl text-slate-900">
                    Two Convenient Mumbai Storefronts
                </h2>
                <p class="text-xs text-slate-500">
                    Touch, feel, and test devices before buying. Complimentary screen guards and data transfer with every new smartphone.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($branches as $branch)
                    <div class="bg-white rounded-3xl border border-slate-200/90 p-7 shadow-xs hover:shadow-md transition-shadow space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide bg-indigo-50 text-indigo-700 border border-indigo-200">
                                Branch Code: {{ $branch->code }}
                            </span>
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Open Today until 9:30 PM
                            </span>
                        </div>

                        <h3 class="font-display font-extrabold text-lg text-slate-900">{{ $branch->name }}</h3>

                        <div class="space-y-2 text-xs text-slate-600">
                            <div class="flex items-start gap-2.5">
                                <svg class="w-4 h-4 text-slate-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>{{ $branch->address }}, {{ $branch->city }}</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span class="font-semibold text-slate-800">{{ $branch->phone ?: ($company->phone ?? '+91 98201 54321') }}</span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[11px] text-slate-400">Services: Sales, Exchange &amp; Repairs</span>
                            @php
                                $bPhone = preg_replace('/[^0-9]/', '', $branch->phone ?: ($company->phone ?? '9820154321'));
                            @endphp
                            <a href="https://wa.me/{{ str_starts_with($bPhone, '91') ? $bPhone : '91'.$bPhone }}?text=Hello%20{{ urlencode($branch->name) }},%20I%20am%20visiting%20your%20store." target="_blank" rel="noopener" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1">
                                WhatsApp Store &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 bg-white rounded-3xl p-6 text-center text-slate-500">
                        {{ $company->address ?? 'Mumbai, Maharashtra' }}
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Comprehensive Footer -->
    <footer class="bg-slate-950 text-slate-400 text-xs border-t border-slate-900 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Col 1: Store Intro -->
                <div class="space-y-3 md:col-span-1">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-black text-base">
                            W
                        </div>
                        <span class="font-display font-bold text-white text-base">{{ $company->name ?? 'Webotapp Mobiles' }}</span>
                    </div>
                    <p class="text-[11px] text-slate-400 leading-relaxed">
                        {{ $company->legal_name ?? 'Webotapp Retail India Pvt Ltd' }}. Premium multi-brand smartphone retail, verified accessories, certified repairs, and smartphone buyback.
                    </p>
                    <div class="text-[10px] text-slate-500 font-mono">
                        GSTIN: {{ $company->gstin ?? '27AABCU9603R1ZM' }}
                    </div>
                </div>

                <!-- Col 2: Quick Links -->
                <div class="space-y-2">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider">Quick Catalog</h4>
                    <ul class="space-y-1.5 text-[11px]">
                        <li><a href="{{ route('shop.index') }}" class="hover:text-white transition">All Products</a></li>
                        <li><a href="{{ route('shop.index', ['category' => 'smartphones']) }}" class="hover:text-white transition">Smartphones</a></li>
                        <li><a href="{{ route('shop.index', ['brand' => 'apple']) }}" class="hover:text-white transition">Apple iPhones</a></li>
                        <li><a href="{{ route('shop.index', ['brand' => 'samsung']) }}" class="hover:text-white transition">Samsung Galaxy</a></li>
                        <li><a href="#exchange" class="hover:text-white transition">Old Phone Exchange</a></li>
                    </ul>
                </div>

                <!-- Col 3: Services & Warranty -->
                <div class="space-y-2">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider">Store Services</h4>
                    <ul class="space-y-1.5 text-[11px]">
                        <li><a href="#repairs" class="hover:text-white transition">30-Min Screen Replacement</a></li>
                        <li><a href="#repairs" class="hover:text-white transition">OEM Battery Health Service</a></li>
                        <li><a href="#exchange" class="hover:text-white transition">Cash Buyback &amp; Valuation</a></li>
                        <li><a href="#branches" class="hover:text-white transition">In-Store Demo &amp; Setup</a></li>
                        <li><a href="{{ route('warranty.index') }}" target="_blank" class="hover:text-white transition">IMEI Warranty Verification</a></li>
                    </ul>
                </div>

                <!-- Col 4: Payments & Security -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider">Accepted Payments</h4>
                    <p class="text-[11px] text-slate-400">
                        UPI (GPay, PhonePe, Paytm), Visa, Mastercard, RuPay, Bajaj Finserv No-Cost EMI &amp; Net Banking.
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium border border-slate-700 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            <span>Staff / Manager Sign In</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t border-slate-900 flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-slate-400">
                <p>&copy; {{ date('Y') }} {{ $company->name ?? 'Webotapp' }}. All rights reserved.</p>
                <p class="text-slate-400">Engineered with Webotapp Mobile Shop Cloud ERP</p>
            </div>
        </div>
    </footer>

</body>
</html>