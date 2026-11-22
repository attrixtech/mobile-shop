@extends('layouts.app')

@section('title', 'Point of Sale (POS) - Webotapp Mobile Shop ERP')

@section('content')
<div x-data="posApp()" x-init="init()" class="h-[calc(100vh-5.5rem)] flex flex-col lg:flex-row gap-3">

    <!-- Left Column: Product Search, Category Pills & Grid (Takes 65% width) -->
    <div class="flex-1 flex flex-col bg-white rounded border border-slate-200 shadow-sm overflow-hidden">
        <!-- Top Search Bar -->
        <div class="p-2.5 bg-slate-900 border-b border-slate-800 flex items-center gap-2">
            <div class="relative flex-1">
                <input
                    type="text"
                    x-ref="searchInput"
                    x-model="searchQuery"
                    @input.debounce.250ms="searchProducts()"
                    @keydown.enter.prevent="handleBarcodeScan()"
                    placeholder="Scan Barcode / Serial / IMEI or search product name..."
                    class="w-full pl-8 pr-3 py-1.5 rounded bg-slate-800 border border-slate-700 text-white placeholder-slate-400 text-xs focus:outline-none focus:border-indigo-500"
                    autofocus
                >
                <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <button @click="searchProducts()" class="px-3 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-medium">Search</button>
        </div>

        <!-- Category Horizontal Pills -->
        <div class="px-2.5 py-1.5 bg-slate-50 border-b border-slate-200 flex items-center gap-1.5 overflow-x-auto text-xs whitespace-nowrap">
            <button
                @click="selectedCategory = null; searchProducts()"
                :class="selectedCategory === null ? 'bg-indigo-600 text-white' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100'"
                class="px-2.5 py-1 rounded text-xs font-medium transition"
            >
                All Products
            </button>
            @foreach($categories as $cat)
                <button
                    @click="selectedCategory = {{ $cat->id }}; searchProducts()"
                    :class="selectedCategory === {{ $cat->id }} ? 'bg-indigo-600 text-white' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100'"
                    class="px-2.5 py-1 rounded text-xs font-medium transition"
                >
                    {{ $cat->name }}
                </button>
            @endforeach
        </div>

        <!-- Product Grid (Compact, responsive cards) -->
        <div class="flex-1 overflow-y-auto p-2.5">
            <div x-show="loading" class="text-center py-12 text-slate-400 text-xs">
                Scanning catalog...
            </div>

            <div x-show="!loading && products.length === 0" class="text-center py-12 text-slate-400 text-xs">
                No products found matching query.
            </div>

            <div x-show="!loading && products.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                <template x-for="p in products" :key="p.id">
                    <div
                        @click="addToCart(p)"
                        class="bg-white border border-slate-200 rounded p-2 hover:border-indigo-400 hover:shadow-sm cursor-pointer transition flex flex-col justify-between text-left group"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-1">
                                <span
                                    class="text-[9px] font-semibold px-1 rounded uppercase tracking-wider"
                                    :class="p.is_serialized ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-600'"
                                    x-text="p.is_serialized ? 'Serialized' : p.type"
                                ></span>
                                <span
                                    class="text-[10px] font-bold"
                                    :class="p.stock > 0 ? 'text-emerald-600' : 'text-rose-500'"
                                    x-text="p.type === 'service' ? 'Service' : p.stock + ' in stock'"
                                ></span>
                            </div>
                            <h4 class="text-xs font-semibold text-slate-900 group-hover:text-indigo-600 line-clamp-2" x-text="p.name"></h4>
                            <div class="text-[10px] text-slate-400 mt-0.5" x-text="p.code || p.barcode || ''"></div>
                        </div>

                        <div class="mt-2 pt-1 border-t border-slate-100 flex items-center justify-between">
                            <div class="text-xs font-bold text-slate-900">
                                ₹<span x-text="Number(p.selling_price).toLocaleString('en-IN')"></span>
                            </div>
                            <span class="text-[10px] text-indigo-600 font-medium group-hover:translate-x-0.5 transition">+ Add</span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Right Column: Cart, Customer, Payments & Bill Preview (Takes 35% width) -->
    <div class="w-full lg:w-96 flex flex-col bg-white rounded border border-slate-200 shadow-sm overflow-hidden text-xs">
        
        <!-- Customer & Exchange Header -->
        <div class="p-2.5 bg-slate-50 border-b border-slate-200 space-y-2">
            <div class="flex items-center justify-between">
                <span class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Customer & Dues</span>
                <span class="text-[10px] text-slate-400">{{ $currentBranch ? $currentBranch->name : '' }}</span>
            </div>

            <div class="grid grid-cols-2 gap-1.5">
                <div>
                    <label class="block text-[10px] text-slate-500 font-medium mb-0.5">Customer Name</label>
                    <input type="text" x-model="customerName" placeholder="Walk-in Customer" class="w-full px-2 py-1 rounded bg-white border border-slate-300 text-xs focus:ring-0 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-[10px] text-slate-500 font-medium mb-0.5">Mobile Number</label>
                    <input type="text" x-model="customerPhone" placeholder="10-digit mobile" class="w-full px-2 py-1 rounded bg-white border border-slate-300 text-xs focus:ring-0 focus:border-indigo-500">
                </div>
            </div>

            <!-- Optional Mobile Exchange trade-in voucher -->
            @if($approvedExchanges->count() > 0)
                <div>
                    <label class="block text-[10px] text-indigo-700 font-medium mb-0.5 flex items-center justify-between">
                        <span>Trade-In / Buyback Voucher</span>
                        <span class="text-[9px] text-indigo-600">Approved</span>
                    </label>
                    <select x-model="selectedExchangeId" @change="updateExchangeAllowance()" class="w-full px-2 py-1 rounded bg-indigo-50/50 border border-indigo-200 text-xs text-indigo-900 focus:ring-0">
                        <option value="">No trade-in applied</option>
                        @foreach($approvedExchanges as $ex)
                            <option value="{{ $ex->id }}" data-value="{{ $ex->approved_value }}">
                                {{ $ex->old_brand }} {{ $ex->old_model }} (IMEI: {{ substr($ex->imei1, -6) }}) - ₹{{ number_format($ex->approved_value, 2) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <!-- Cart Items List (Scrollable) -->
        <div class="flex-1 overflow-y-auto p-2 divide-y divide-slate-100">
            <template x-if="cart.length === 0">
                <div class="text-center py-16 text-slate-400">
                    <svg class="w-8 h-8 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span>Cart is empty. Scan barcode or tap products to bill.</span>
                </div>
            </template>

            <template x-for="(item, idx) in cart" :key="idx">
                <div class="py-2 space-y-1">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 pr-2">
                            <div class="font-semibold text-slate-900 text-xs leading-snug" x-text="item.name"></div>
                            <div class="text-[10px] text-slate-400">
                                Rate: ₹<span x-text="Number(item.unit_price).toLocaleString('en-IN')"></span> | GST: <span x-text="item.gst_rate"></span>%
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-slate-900 text-xs">
                                ₹<span x-text="Number(item.quantity * item.unit_price).toLocaleString('en-IN')"></span>
                            </div>
                            <button @click="removeItem(idx)" class="text-[10px] text-rose-500 hover:text-rose-700">Remove</button>
                        </div>
                    </div>

                    <!-- Quantity Controls -->
                    <div class="flex items-center justify-between text-xs pt-1">
                        <div class="flex items-center border border-slate-200 rounded">
                            <button @click="changeQty(idx, -1)" class="px-2 py-0.5 bg-slate-50 hover:bg-slate-100 text-slate-600 font-bold">-</button>
                            <span class="px-2.5 py-0.5 font-medium" x-text="item.quantity"></span>
                            <button @click="changeQty(idx, 1)" class="px-2 py-0.5 bg-slate-50 hover:bg-slate-100 text-slate-600 font-bold">+</button>
                        </div>

                        <!-- Serialized device IMEI Selection -->
                        <div x-show="item.is_serialized" class="flex-1 ml-2">
                            <select
                                x-model="item.selectedImei"
                                class="w-full text-[11px] py-1 px-1.5 rounded bg-amber-50 border border-amber-200 text-amber-900 focus:ring-0"
                            >
                                <option value="">-- Choose IMEI --</option>
                                <template x-for="im in item.available_imeis" :key="im">
                                    <option :value="im" x-text="'IMEI: ' + im"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Bill Calculation Summary (Compact) -->
        <div class="p-2.5 bg-slate-50 border-t border-slate-200 space-y-1 text-xs">
            <div class="flex justify-between text-slate-600">
                <span>Subtotal (Excl. Tax):</span>
                <span>₹<span x-text="subtotal.toFixed(2)"></span></span>
            </div>
            <div class="flex justify-between text-slate-600">
                <span>GST (CGST + SGST 18%):</span>
                <span>₹<span x-text="totalTax.toFixed(2)"></span></span>
            </div>
            <div x-show="exchangeAllowance > 0" class="flex justify-between text-emerald-600 font-medium">
                <span>Trade-In Buyback Allowance:</span>
                <span>-₹<span x-text="exchangeAllowance.toFixed(2)"></span></span>
            </div>
            <div class="flex justify-between text-slate-600">
                <span>Bill Discount:</span>
                <input type="number" x-model.number="billDiscount" min="0" placeholder="0" class="w-16 px-1 py-0.5 text-right text-xs rounded bg-white border border-slate-200">
            </div>
            <div class="flex justify-between font-bold text-sm text-slate-900 pt-1 border-t border-slate-200">
                <span>Net Payable:</span>
                <span class="text-indigo-600 text-base">₹<span x-text="grandTotal.toFixed(2)"></span></span>
            </div>
        </div>

        <!-- Payment Mode & Checkout Button -->
        <div class="p-2.5 bg-slate-900 text-white space-y-2">
            <div class="grid grid-cols-4 gap-1 text-[11px]">
                <button
                    type="button"
                    @click="paymentMethod = 'cash'"
                    :class="paymentMethod === 'cash' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
                    class="py-1 rounded text-center transition"
                >
                    Cash
                </button>
                <button
                    type="button"
                    @click="paymentMethod = 'upi'"
                    :class="paymentMethod === 'upi' ? 'bg-purple-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
                    class="py-1 rounded text-center transition"
                >
                    UPI QR
                </button>
                <button
                    type="button"
                    @click="paymentMethod = 'card'"
                    :class="paymentMethod === 'card' ? 'bg-blue-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
                    class="py-1 rounded text-center transition"
                >
                    Card
                </button>
                <button
                    type="button"
                    @click="paymentMethod = 'split'"
                    :class="paymentMethod === 'split' ? 'bg-amber-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
                    class="py-1 rounded text-center transition"
                >
                    Split
                </button>
            </div>

            <!-- Transaction Reference input if card / upi -->
            <div x-show="paymentMethod === 'upi' || paymentMethod === 'card'">
                <input type="text" x-model="paymentReference" placeholder="Enter UPI Ref ID or Card Auth Code" class="w-full px-2 py-1 rounded bg-slate-800 border border-slate-700 text-xs text-white placeholder-slate-500">
            </div>

            <button
                @click="submitCheckout()"
                :disabled="cart.length === 0 || isSubmitting"
                class="w-full py-2 px-3 rounded bg-emerald-600 hover:bg-emerald-500 disabled:bg-slate-700 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-1.5"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span x-text="isSubmitting ? 'Processing Bill...' : 'Complete & Print Invoice (₹' + grandTotal.toFixed(2) + ')'"></span>
            </button>
        </div>

    </div>

</div>

@push('scripts')
<script>
function posApp() {
    return {
        products: [],
        cart: [],
        searchQuery: '',
        selectedCategory: null,
        loading: false,
        isSubmitting: false,
        customerName: '',
        customerPhone: '',
        selectedExchangeId: '',
        exchangeAllowance: 0,
        billDiscount: 0,
        paymentMethod: 'cash',
        paymentReference: '',

        init() {
            this.searchProducts();
        },

        async searchProducts() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                if (this.searchQuery) params.append('q', this.searchQuery);
                if (this.selectedCategory) params.append('category_id', this.selectedCategory);

                const res = await fetch(`{{ route('pos.search') }}?${params.toString()}`);
                const data = await res.json();

                if (data.matched_imei) {
                    // Exact scan of IMEI! Automatically add product and pick this IMEI!
                    this.addToCartWithImei(data.product, data.imei);
                    this.searchQuery = '';
                } else {
                    this.products = data;
                }
            } catch (err) {
                console.error(err);
            } finally {
                this.loading = false;
            }
        },

        handleBarcodeScan() {
            this.searchProducts();
        },

        addToCart(product) {
            const existing = this.cart.find(i => i.id === product.id && !i.is_serialized);
            if (existing) {
                existing.quantity++;
            } else {
                this.cart.push({
                    id: product.id,
                    name: product.name,
                    unit_price: product.selling_price,
                    gst_rate: product.gst_rate,
                    quantity: 1,
                    is_serialized: product.is_serialized,
                    available_imeis: product.available_imeis || [],
                    selectedImei: (product.available_imeis && product.available_imeis.length > 0) ? product.available_imeis[0] : '',
                });
            }
        },

        addToCartWithImei(product, imei) {
            this.cart.push({
                id: product.id,
                name: product.name,
                unit_price: product.price,
                gst_rate: product.gst_rate,
                quantity: 1,
                is_serialized: true,
                available_imeis: [imei],
                selectedImei: imei,
            });
        },

        changeQty(idx, delta) {
            const item = this.cart[idx];
            if (item.is_serialized && delta > 0) {
                alert('For serialized devices, each unit requires unique IMEI assignment. Please add another item row.');
                return;
            }
            item.quantity += delta;
            if (item.quantity <= 0) {
                this.cart.splice(idx, 1);
            }
        },

        removeItem(idx) {
            this.cart.splice(idx, 1);
        },

        updateExchangeAllowance() {
            const selectEl = event.target;
            const opt = selectEl.options[selectEl.selectedIndex];
            this.exchangeAllowance = opt && opt.dataset.value ? parseFloat(opt.dataset.value) : 0;
        },

        get subtotal() {
            return this.cart.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
        },

        get totalTax() {
            return this.cart.reduce((sum, item) => {
                const line = item.quantity * item.unit_price;
                return sum + ((line * item.gst_rate) / 100);
            }, 0);
        },

        get grandTotal() {
            const net = (this.subtotal + this.totalTax) - (this.billDiscount || 0) - (this.exchangeAllowance || 0);
            return Math.max(0, net);
        },

        async submitCheckout() {
            // Verify serialized items have IMEIs picked
            for (let item of this.cart) {
                if (item.is_serialized && !item.selectedImei) {
                    alert(`Please select an available IMEI for '${item.name}'`);
                    return;
                }
            }

            this.isSubmitting = true;

            const payload = {
                customer_name: this.customerName || 'Walk-in Customer',
                customer_phone: this.customerPhone || null,
                discount_amount: this.billDiscount || 0,
                exchange_id: this.selectedExchangeId || null,
                payment_method: this.paymentMethod,
                payment_reference: this.paymentReference || null,
                items: this.cart.map(item => ({
                    product_id: item.id,
                    quantity: item.quantity,
                    unit_price: item.unit_price,
                    discount: 0,
                    imeis: item.is_serialized ? [item.selectedImei] : [],
                }))
            };

            try {
                const res = await fetch(`{{ route('pos.checkout') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                if (data.success) {
                    window.location.href = data.receipt_url;
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (err) {
                alert('An error occurred during checkout.');
                console.error(err);
            } finally {
                this.isSubmitting = false;
            }
        }
    };
}
</script>
@endpush
@endsection
