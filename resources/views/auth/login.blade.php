<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Webotapp Mobile Shop ERP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', system-ui, sans-serif; }</style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md" x-data="{
        fill(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password123';
        }
    }">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-600 font-bold text-white text-2xl shadow-lg mb-2">
                W
            </div>
            <h1 class="text-xl font-bold tracking-tight text-white">Webotapp Mobile Shop ERP</h1>
            <p class="text-xs text-slate-400 mt-1">Multi-tenant Cloud Management Suite for Mobile Retailers</p>
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-lg p-5 shadow-xl text-xs">
            @if ($errors->any())
                <div class="mb-4 bg-rose-950 border border-rose-800 text-rose-200 p-2.5 rounded text-xs">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-slate-300 font-medium mb-1">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email', 'owner@webotapp.com') }}" required class="w-full px-3 py-1.5 rounded bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 text-xs">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-slate-300 font-medium">Password</label>
                    </div>
                    <input type="password" id="password" name="password" value="password123" required class="w-full px-3 py-1.5 rounded bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 text-xs">
                </div>

                <div class="flex items-center justify-between text-slate-400">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="remember" checked class="rounded bg-slate-900 border-slate-700 text-indigo-600 text-xs">
                        <span class="ml-1.5">Remember session</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-2 px-3 rounded bg-indigo-600 hover:bg-indigo-500 text-white font-medium shadow transition text-xs">
                    Sign In to Workspace
                </button>
            </form>

            <!-- Quick Demo Accounts Switcher -->
            <div class="mt-5 pt-4 border-t border-slate-700/80">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2 text-center">Quick Demo Accounts (1-Click Fill)</p>
                <div class="grid grid-cols-2 gap-1.5">
                    <button type="button" @click="fill('owner@webotapp.com')" class="px-2 py-1.5 rounded bg-slate-700/60 hover:bg-slate-700 text-slate-200 text-left transition border border-slate-600/50">
                        <div class="font-medium text-indigo-300">Business Owner</div>
                        <div class="text-[10px] text-slate-400 truncate">owner@webotapp.com</div>
                    </button>
                    <button type="button" @click="fill('manager@webotapp.com')" class="px-2 py-1.5 rounded bg-slate-700/60 hover:bg-slate-700 text-slate-200 text-left transition border border-slate-600/50">
                        <div class="font-medium text-emerald-300">Store Manager</div>
                        <div class="text-[10px] text-slate-400 truncate">manager@webotapp.com</div>
                    </button>
                    <button type="button" @click="fill('sales@webotapp.com')" class="px-2 py-1.5 rounded bg-slate-700/60 hover:bg-slate-700 text-slate-200 text-left transition border border-slate-600/50">
                        <div class="font-medium text-amber-300">Cashier / Sales</div>
                        <div class="text-[10px] text-slate-400 truncate">sales@webotapp.com</div>
                    </button>
                    <button type="button" @click="fill('tech@webotapp.com')" class="px-2 py-1.5 rounded bg-slate-700/60 hover:bg-slate-700 text-slate-200 text-left transition border border-slate-600/50">
                        <div class="font-medium text-rose-300">Lead Technician</div>
                        <div class="text-[10px] text-slate-400 truncate">tech@webotapp.com</div>
                    </button>
                    <button type="button" @click="fill('superadmin@webotapp.com')" class="col-span-2 px-2 py-1.5 rounded bg-slate-700/40 hover:bg-slate-700 text-slate-200 text-center transition border border-slate-600/40">
                        <span class="font-medium text-sky-300">Platform Super Admin</span> <span class="text-[10px] text-slate-400">(superadmin@webotapp.com)</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('shop.index') }}" class="text-xs text-indigo-400 hover:text-indigo-300 underline">
                &larr; View Public Storefront & Online Catalog
            </a>
        </div>
    </div>

</body>
</html>
