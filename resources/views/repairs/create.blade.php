@extends('layouts.app')

@section('title', 'New Repair Job Card - Webotapp Mobile Shop ERP')

@section('content')
<div class="max-w-3xl mx-auto space-y-4" x-data="repairCreateComponent()">
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-base font-bold text-slate-900 tracking-tight">Create Customer Repair Job Card</h1>
            <p class="text-xs text-slate-500">Record customer complaint, physical device condition, and assign technician</p>
        </div>
        <a href="{{ route('repairs.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">&larr; Back to Repairs</a>
    </div>

    <!-- Alert Banner -->
    <template x-if="successMsg">
        <div class="p-2.5 rounded bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center justify-between">
            <span x-text="successMsg"></span>
            <button type="button" @click="successMsg = ''" class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
        </div>
    </template>

    <form action="{{ route('repairs.store') }}" method="POST" class="bg-white rounded border border-slate-200 p-4 shadow-sm text-xs space-y-4">
        @csrf

        <!-- 1. Customer Selection & Search & Create -->
        <div>
            <div class="flex items-center justify-between mb-2 pb-1 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">1. Customer Information</h3>
                <button type="button" @click="openCustomerModal()" class="inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    + Quick Add New Customer
                </button>
            </div>

            <!-- Hidden customer_id field for form submission -->
            <input type="hidden" name="customer_id" :value="selectedCustomer ? selectedCustomer.id : ''" required>

            <!-- Customer Search & Selected State -->
            <div class="space-y-2">
                <!-- If No Customer Selected: Search Box -->
                <div x-show="!selectedCustomer" class="relative">
                    <label class="block font-semibold text-slate-700 mb-1">Search Customer by Name, Mobile Phone or GSTIN *</label>
                    <div class="relative">
                        <input type="text" 
                               x-model="searchQuery" 
                               @input="filterCustomers()" 
                               @focus="showDropdown = true" 
                               @keydown.escape="showDropdown = false"
                               placeholder="Type customer name or mobile (e.g. Rahul, 98200...)" 
                               class="w-full pl-8 pr-8 py-2 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                        <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <button type="button" x-show="searchQuery" @click="searchQuery = ''; filterCustomers()" class="absolute right-2.5 top-2 text-slate-400 hover:text-slate-600 font-bold text-sm">&times;</button>
                    </div>

                    <!-- Autocomplete Dropdown List -->
                    <div x-show="showDropdown && filteredCustomers.length > 0" 
                         @click.away="showDropdown = false" 
                         x-cloak 
                         class="absolute z-20 left-0 right-0 mt-1 max-h-52 overflow-y-auto bg-white border border-slate-200 rounded-md shadow-lg divide-y divide-slate-100">
                        <template x-for="c in filteredCustomers" :key="c.id">
                            <div @click="selectCustomer(c)" class="p-2.5 hover:bg-indigo-50 cursor-pointer flex items-center justify-between transition">
                                <div>
                                    <div class="font-bold text-slate-800" x-text="c.name"></div>
                                    <div class="text-[11px] text-slate-500 flex items-center gap-2 mt-0.5">
                                        <span class="font-mono" x-text="c.phone"></span>
                                        <span x-show="c.city" class="text-slate-400">&bull; <span x-text="c.city"></span></span>
                                    </div>
                                </div>
                                <span class="badge badge-info text-[9px] uppercase tracking-wider" x-text="c.customer_type || 'retail'"></span>
                            </div>
                        </template>
                    </div>

                    <!-- If Search Has No Results -->
                    <div x-show="showDropdown && searchQuery && filteredCustomers.length === 0" 
                         x-cloak 
                         class="absolute z-20 left-0 right-0 mt-1 p-3 bg-white border border-slate-200 rounded-md shadow-lg text-center text-slate-500 text-xs">
                        <p>No customer found matching "<strong x-text="searchQuery"></strong>"</p>
                        <button type="button" @click="openCustomerModalWithQuery()" class="mt-2 text-xs font-semibold text-indigo-600 hover:text-indigo-800 underline">
                            + Create "<span x-text="searchQuery"></span>" as New Customer
                        </button>
                    </div>
                </div>

                <!-- If Customer IS Selected: Clean Compact Card -->
                <div x-show="selectedCustomer" x-cloak class="p-3 bg-indigo-50/70 border border-indigo-200 rounded-lg flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs uppercase" x-text="selectedCustomer ? selectedCustomer.name.substring(0,2) : ''"></div>
                        <div>
                            <div class="font-bold text-slate-900 text-xs flex items-center gap-2">
                                <span x-text="selectedCustomer ? selectedCustomer.name : ''"></span>
                                <span class="badge badge-indigo text-[9px] uppercase" x-text="selectedCustomer ? (selectedCustomer.customer_type || 'retail') : ''"></span>
                            </div>
                            <div class="text-[11px] text-slate-600 font-mono mt-0.5 flex items-center gap-2">
                                <span>Phone: <strong x-text="selectedCustomer ? selectedCustomer.phone : ''"></strong></span>
                                <span x-show="selectedCustomer && selectedCustomer.email" class="text-slate-400">&bull; <span x-text="selectedCustomer ? selectedCustomer.email : ''"></span></span>
                                <span x-show="selectedCustomer && selectedCustomer.city" class="text-slate-400">&bull; <span x-text="selectedCustomer ? selectedCustomer.city : ''"></span></span>
                            </div>
                        </div>
                    </div>
                    <button type="button" @click="clearCustomer()" class="text-xs font-medium text-slate-600 hover:text-red-600 px-2.5 py-1 bg-white rounded border border-slate-200 shadow-2xs hover:border-red-200 transition">
                        Change Customer
                    </button>
                </div>
            </div>
        </div>

        <!-- 2. Device Specifications -->
        <div>
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px] mb-2 pb-1 border-b border-slate-100">2. Device Information</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Device Brand *</label>
                    <input type="text" name="brand" required placeholder="e.g. Apple, Samsung, OnePlus" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Device Model *</label>
                    <input type="text" name="model" required placeholder="e.g. iPhone 13 Pro, Galaxy S22" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Device IMEI (15 digits)</label>
                    <input type="text" name="imei" placeholder="Primary IMEI" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 font-mono text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-2">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Serial Number</label>
                    <input type="text" name="serial_number" placeholder="Optional S/N" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 font-mono text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Warranty Status *</label>
                    <select name="warranty_status" required class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                        <option value="out_of_warranty" selected>Out of Warranty</option>
                        <option value="under_store_warranty">Under Store Warranty</option>
                        <option value="under_brand_warranty">Under Brand Warranty</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 3. Problem Diagnosis & Physical Condition -->
        <div>
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px] mb-2 pb-1 border-b border-slate-100">3. Problem & Physical Condition</h3>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Customer Reported Problem / Fault *</label>
                <textarea name="reported_problem" rows="2" required placeholder="Describe problem: Screen broken, not charging, battery drains fast, water damage, restart loop, etc." class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3 mt-2">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Physical Condition at Reception</label>
                    <input type="text" name="device_condition" placeholder="e.g. Scratched back, dents on corners, screen cracked" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Accessories Received with Phone</label>
                    <input type="text" name="accessories_received" placeholder="e.g. SIM Tray, Back Cover, Memory Card" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
            </div>
        </div>

        <!-- 4. Technician & Estimate -->
        <div>
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[11px] mb-2 pb-1 border-b border-slate-100">4. Technician & Estimate</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block font-semibold text-slate-700">Assign Service Engineer</label>
                        <button type="button" @click="openTechnicianModal()" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span>+ Add Engineer</span>
                        </button>
                    </div>
                    <select name="technician_id" x-model="selectedTechnicianId" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                        <option value="">-- Unassigned (Store Queue) --</option>
                        <template x-for="tech in technicianList" :key="tech.id">
                            <option :value="tech.id" x-text="tech.name + (tech.phone ? ' (' + tech.phone + ')' : '')"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Estimated Repair Cost (₹)</label>
                    <input type="number" step="0.01" name="estimated_cost" placeholder="0.00" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Advance Received (₹)</label>
                    <input type="number" step="0.01" name="advance_paid" placeholder="0.00" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>
            </div>

            <div class="mt-2">
                <label class="block font-semibold text-slate-700 mb-1">Estimated Completion Date</label>
                <input type="date" name="estimated_delivery_at" value="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
            </div>
        </div>

        <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
            <a href="{{ route('repairs.index') }}" class="px-3 py-1.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700">Cancel</a>
            <button type="submit" class="px-4 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-sm">
                Create Job Card Ticket
            </button>
        </div>
    </form>

    <!-- Modal: Quick Add Customer -->
    <div x-show="showCustomerModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-2xs"
         @keydown.escape.window="showCustomerModal = false">
        <div class="bg-white rounded-lg shadow-xl border border-slate-200 max-w-md w-full overflow-hidden text-xs" @click.away="showCustomerModal = false">
            <div class="px-4 py-3 bg-slate-800 text-white flex items-center justify-between">
                <h3 class="font-bold text-xs uppercase tracking-wider">Add New Customer</h3>
                <button type="button" @click="showCustomerModal = false" class="text-slate-400 hover:text-white font-bold text-sm">&times;</button>
            </div>

            <form @submit.prevent="saveNewCustomer()" class="p-4 space-y-3">
                <template x-if="modalError">
                    <div class="p-2 bg-red-50 text-red-700 border border-red-200 rounded text-[11px]" x-text="modalError"></div>
                </template>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Customer Full Name *</label>
                    <input type="text" x-model="newCustomer.name" required placeholder="e.g. Ramesh Patel" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Mobile Phone *</label>
                        <input type="text" x-model="newCustomer.phone" required placeholder="10-digit mobile" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 font-mono text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Customer Type *</label>
                        <select x-model="newCustomer.customer_type" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                            <option value="retail">Retail (B2C)</option>
                            <option value="wholesale">Wholesale (B2B)</option>
                            <option value="corporate">Corporate</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Email (Optional)</label>
                        <input type="email" x-model="newCustomer.email" placeholder="email@example.com" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">City</label>
                        <input type="text" x-model="newCustomer.city" placeholder="e.g. Mumbai" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Street Address</label>
                    <input type="text" x-model="newCustomer.address" placeholder="Shop / House No, Area" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs">
                </div>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
                    <button type="button" @click="showCustomerModal = false" class="px-3 py-1.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700">Cancel</button>
                    <button type="submit" :disabled="savingCustomer" class="px-4 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-sm inline-flex items-center gap-1">
                        <span x-show="!savingCustomer">Save & Select Customer</span>
                        <span x-show="savingCustomer">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Quick Add Service Engineer -->
    <div x-show="showTechnicianModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-2xs"
         @keydown.escape.window="showTechnicianModal = false">
        <div class="bg-white rounded-lg shadow-xl border border-slate-200 max-w-md w-full overflow-hidden text-xs" @click.away="showTechnicianModal = false">
            <div class="px-4 py-3 bg-slate-800 text-white flex items-center justify-between">
                <div class="flex items-center gap-1.5 font-bold text-xs uppercase tracking-wider">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span>Add New Service Engineer</span>
                </div>
                <button type="button" @click="showTechnicianModal = false" class="text-slate-400 hover:text-white font-bold text-sm">&times;</button>
            </div>

            <form @submit.prevent="saveNewTechnician()" class="p-4 space-y-3">
                <template x-if="technicianModalError">
                    <div class="p-2 bg-red-50 text-red-700 border border-red-200 rounded text-[11px]" x-text="technicianModalError"></div>
                </template>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Service Engineer Name *</label>
                    <input type="text" x-model="newTechnician.name" required placeholder="e.g. Ramesh Sharma" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Mobile Number *</label>
                        <input type="text" x-model="newTechnician.phone" placeholder="+91 98201 11222" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Email (Optional)</label>
                        <input type="email" x-model="newTechnician.email" placeholder="ramesh@webotapp.com" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Assigned Branch *</label>
                    <select x-model="newTechnician.branch_id" required class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">System Password</label>
                    <input type="text" x-model="newTechnician.password" placeholder="Leave blank for password123" class="w-full px-2.5 py-1.5 rounded bg-slate-50 border border-slate-300 text-xs focus:bg-white focus:outline-none focus:border-indigo-500">
                    <p class="text-[10px] text-slate-400 mt-0.5">Defaults to password123 if not specified</p>
                </div>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
                    <button type="button" @click="showTechnicianModal = false" class="px-3 py-1.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700">Cancel</button>
                    <button type="submit" :disabled="savingTechnician" class="px-4 py-1.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-sm inline-flex items-center gap-1">
                        <span x-show="!savingTechnician">Create &amp; Assign Engineer</span>
                        <span x-show="savingTechnician">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function repairCreateComponent() {
    return {
        allCustomers: @json($customers),
        filteredCustomers: [],
        searchQuery: '',
        showDropdown: false,
        selectedCustomer: null,
        showCustomerModal: false,
        modalError: '',
        savingCustomer: false,
        successMsg: '',
        newCustomer: {
            name: '',
            phone: '',
            email: '',
            city: '',
            address: '',
            customer_type: 'retail'
        },

        // Service Engineer state
        technicianList: @json($technicians),
        selectedTechnicianId: '',
        showTechnicianModal: false,
        technicianModalError: '',
        savingTechnician: false,
        newTechnician: {
            name: '',
            phone: '',
            email: '',
            branch_id: '{{ $branchId ?: ($branches->first()?->id ?? 1) }}',
            password: ''
        },

        init() {
            this.filteredCustomers = this.allCustomers;
            const urlParams = new URLSearchParams(window.location.search);
            const preselectedId = urlParams.get('customer_id');
            if (preselectedId) {
                const found = this.allCustomers.find(c => c.id == preselectedId);
                if (found) {
                    this.selectedCustomer = found;
                }
            }
        },

        filterCustomers() {
            const q = this.searchQuery.toLowerCase().trim();
            if (!q) {
                this.filteredCustomers = this.allCustomers;
                this.showDropdown = true;
                return;
            }
            this.filteredCustomers = this.allCustomers.filter(c => {
                const name = (c.name || '').toLowerCase();
                const phone = (c.phone || '').toLowerCase();
                const gstin = (c.gstin || '').toLowerCase();
                return name.includes(q) || phone.includes(q) || gstin.includes(q);
            });
            this.showDropdown = true;
        },

        selectCustomer(customer) {
            this.selectedCustomer = customer;
            this.showDropdown = false;
            this.searchQuery = '';
        },

        clearCustomer() {
            this.selectedCustomer = null;
            this.searchQuery = '';
            this.filteredCustomers = this.allCustomers;
        },

        openCustomerModal() {
            this.modalError = '';
            this.newCustomer = {
                name: '',
                phone: '',
                email: '',
                city: '',
                address: '',
                customer_type: 'retail'
            };
            this.showCustomerModal = true;
        },

        openCustomerModalWithQuery() {
            this.openCustomerModal();
            if (/^\d+$/.test(this.searchQuery.trim())) {
                this.newCustomer.phone = this.searchQuery.trim();
            } else {
                this.newCustomer.name = this.searchQuery.trim();
            }
        },

        async saveNewCustomer() {
            this.modalError = '';
            this.savingCustomer = true;
            try {
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '{{ csrf_token() }}';

                const res = await fetch('{{ route("customers.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(this.newCustomer)
                });

                const data = await res.json();

                if (!res.ok) {
                    if (data.errors) {
                        this.modalError = Object.values(data.errors).flat().join(' ');
                    } else {
                        this.modalError = data.message || 'Error saving customer profile.';
                    }
                    this.savingCustomer = false;
                    return;
                }

                const customer = data.customer;
                this.allCustomers.unshift(customer);
                this.selectedCustomer = customer;
                this.showCustomerModal = false;
                this.successMsg = `Customer "${customer.name}" created and selected successfully!`;
                this.searchQuery = '';
            } catch (err) {
                this.modalError = 'Network error while creating customer: ' + err.message;
            } finally {
                this.savingCustomer = false;
            }
        },

        openTechnicianModal() {
            this.technicianModalError = '';
            this.newTechnician = {
                name: '',
                phone: '',
                email: '',
                branch_id: '{{ $branchId ?: ($branches->first()?->id ?? 1) }}',
                password: ''
            };
            this.showTechnicianModal = true;
        },

        async saveNewTechnician() {
            this.technicianModalError = '';
            this.savingTechnician = true;
            try {
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '{{ csrf_token() }}';

                const res = await fetch('{{ route("repairs.technicians.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(this.newTechnician)
                });

                const data = await res.json();

                if (!res.ok) {
                    if (data.errors) {
                        this.technicianModalError = Object.values(data.errors).flat().join(' ');
                    } else {
                        this.technicianModalError = data.message || 'Error creating service engineer.';
                    }
                    this.savingTechnician = false;
                    return;
                }

                const tech = data.technician;
                this.technicianList.push(tech);
                this.selectedTechnicianId = tech.id;
                this.showTechnicianModal = false;
                this.successMsg = `Service Engineer "${tech.name}" registered and assigned to this ticket!`;
            } catch (err) {
                this.technicianModalError = 'Network error while creating service engineer: ' + err.message;
            } finally {
                this.savingTechnician = false;
            }
        }
    };
}
</script>
@endsection
