<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - {{ $company->name ?? 'Webotapp Mobiles' }}</title>
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

    @php
        $cleanPhone = preg_replace('/[^0-9]/', '', $company->phone ?? '9820154321');
        $fullPhone = str_starts_with($cleanPhone, '91') ? $cleanPhone : '91'.$cleanPhone;
        $approxMrp = $product->selling_price * 1.15;
        $savings = $approxMrp - $product->selling_price;
    @endphp

    <!-- Public Header -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between">
            <a href="{{ route('shop.index') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-sky-500 text-white flex items-center justify-center font-black text-xl shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-transform">
                    W
                </div>
                <div>
                    <h1 class="font-display font-extrabold text-slate-900 text-base leading-tight">{{ $company->name ?? 'Webotapp Mobiles' }}</h1>
                    <p class="text-[11px] text-indigo-600 font-semibold flex items-center gap-1">
                        &larr; Back to Shop Catalog
                    </p>
                </div>
            </a>

            <div class="flex items-center gap-3">
                <a href="https://wa.me/{{ $fullPhone }}?text=Hello,%20I%20am%20inquiring%20about%20{{ urlencode($product->name) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold shadow-md shadow-emerald-600/20 transition">
                    <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.18-2.588-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.299.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.353.101.174.45 0.743.966 1.203.664.591 1.224.774 1.397.86.173.087.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/></svg>
                    <span>Inquire via WhatsApp</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-8 flex-1 w-full space-y-6">
        <!-- Breadcrumb Bar -->
        <nav class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('shop.index') }}" class="hover:text-indigo-600 transition">Store Catalog</a>
            <span>&rsaquo;</span>
            <span class="text-slate-700 font-semibold">{{ $product->category->name ?? 'Devices' }}</span>
            <span>&rsaquo;</span>
            <span class="text-indigo-600 font-bold">{{ $product->brand->name ?? 'Brand' }}</span>
        </nav>

        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 p-6 sm:p-10">
                <!-- Left Visual Showcase -->
                <div class="md:col-span-5 flex flex-col items-center justify-center p-8 bg-slate-50 rounded-2xl border border-slate-100">
                    <div class="w-32 h-32 rounded-2xl bg-white shadow-md border border-slate-100 flex items-center justify-center text-indigo-600 mb-4">
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="3" stroke-width="1.75"/><line x1="12" y1="18" x2="12.01" y2="18" stroke-width="2.5" stroke-linecap="round"/><line x1="10" y1="5" x2="14" y2="5" stroke-width="1.75" stroke-linecap="round"/></svg>
                    </div>
                    <span class="text-xs font-bold text-slate-700">{{ $product->brand->name ?? 'Brand' }} Official Unit</span>
                    <span class="text-[11px] text-emerald-600 font-medium mt-1">100% Genuine Box Pack</span>
                </div>

                <!-- Right Details -->
                <div class="md:col-span-7 space-y-5">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200">
                                {{ $product->brand->name ?? 'Mobile' }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Ready for Store Pickup / Delivery
                            </span>
                        </div>
                        <h1 class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900 leading-tight">
                            {{ $product->name }}
                        </h1>
                        <p class="text-xs text-slate-500 mt-1">
                            SKU: <span class="font-mono text-slate-700 font-semibold">{{ $product->sku }}</span> &bull; HSN: {{ $product->hsn_code ?? '85171300' }} &bull; GST {{ $product->gst_rate }}% Tax Invoice
                        </p>
                    </div>

                    <!-- Price Block -->
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 flex items-baseline justify-between">
                        <div>
                            <span class="text-[11px] text-slate-400 block uppercase font-bold tracking-wider">Store Price</span>
                            <div class="text-3xl font-black font-display text-slate-900">
                                ₹{{ number_format($product->selling_price, 2) }}
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-slate-400 line-through">MRP ₹{{ number_format($approxMrp, 0) }}</span>
                            <div class="text-xs font-bold text-emerald-600 bg-emerald-100/70 px-2 py-0.5 rounded-md">Save ₹{{ number_format($savings, 0) }}</div>
                        </div>
                    </div>

                    <!-- Variants If Any -->
                    @if($product->variants->count() > 0)
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-800 uppercase tracking-wider block">Available Options &amp; Storage</label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                @foreach($product->variants as $variant)
                                    <div class="p-3 rounded-xl border border-slate-200 bg-white hover:border-indigo-500 transition-colors cursor-pointer">
                                        <div class="text-xs font-bold text-slate-900">{{ $variant->name }}</div>
                                        <div class="text-[11px] text-slate-500 font-mono mt-0.5">₹{{ number_format($variant->selling_price, 2) }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Product Description -->
                    <div class="space-y-2 pt-2 border-t border-slate-100">
                        <label class="text-xs font-bold text-slate-800 uppercase tracking-wider block">Product Overview</label>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            {{ $product->description ?: 'Factory sealed retail pack with official brand warranty. Eligible for instant trade-in bonus and zero-interest EMI schemes at our Andheri and Bandra branches.' }}
                        </p>
                    </div>

                    <!-- In-store CTA -->
                    <div class="pt-4 flex flex-col sm:flex-row items-center gap-3">
                        <a href="https://wa.me/{{ $fullPhone }}?text=Hello,%20I%20am%20inquiring%20about%20{{ urlencode($product->name) }}" target="_blank" rel="noopener" class="w-full sm:w-auto flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-lg shadow-emerald-600/30 transition">
                            <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.18-2.588-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.299.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.353.101.174.45 0.743.966 1.203.664.591 1.224.774 1.397.86.173.087.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/></svg>
                            <span>Book In-Store or Inquire on WhatsApp</span>
                        </a>
                        <a href="{{ route('shop.index') }}" class="w-full sm:w-auto px-5 py-3.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-sm text-center transition">
                            Back to Store
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-950 text-slate-400 py-8 text-center text-xs mt-12 border-t border-slate-900">
        <p>&copy; {{ date('Y') }} {{ $company->name ?? 'Webotapp' }}. All rights reserved.</p>
        <p class="text-[11px] text-slate-500 mt-1">Store Locations: Andheri West Mega Store &amp; Bandra Retail Hub &bull; Mumbai</p>
    </footer>

</body>
</html>