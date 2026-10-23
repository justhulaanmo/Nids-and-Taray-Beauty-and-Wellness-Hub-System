@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-stone-900 tracking-tight flex items-center space-x-2">
                <i data-lucide="plus-circle" class="w-6 h-6 text-emerald-800"></i>
                <span>New Service Order</span>
            </h1>
            <p class="text-xs text-stone-500 mt-0.5">
                Select services, assign staff, and record payment.
            </p>
        </div>
        <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-xl bg-white text-stone-700 hover:bg-stone-50 border border-stone-200 font-bold text-xs transition shadow-2xs inline-flex items-center space-x-1">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Back</span>
        </a>
    </div>

    <form action="{{ route('transactions.store') }}" method="POST" id="transactionForm" class="space-y-4">
        @csrf

        <!-- 1. Customer & Location -->
        <div class="bg-white rounded-3xl border border-emerald-900/10 p-5 sm:p-6 shadow-xs space-y-4">
            <h2 class="text-xs sm:text-sm font-black text-emerald-900 uppercase tracking-wider flex items-center space-x-2">
                <span class="w-5 h-5 rounded-lg bg-emerald-700 text-white flex items-center justify-center text-[11px] font-black">1</span>
                <span>Client Details & Service Location</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Client Name *</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" required
                        placeholder="e.g. Maria Santos"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-stone-900 text-xs font-medium placeholder-stone-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Phone Number (Optional)</label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}"
                        placeholder="0917-xxx-xxxx"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-stone-900 text-xs font-medium placeholder-stone-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                </div>
            </div>

            <!-- In-Shop vs Home Service -->
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1.5">Service Location</label>
                <div class="grid grid-cols-2 gap-2.5">
                    <label class="cursor-pointer border border-stone-200 rounded-2xl p-3 flex items-center space-x-2.5 bg-stone-50 hover:border-emerald-400 transition has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/60">
                        <input type="radio" name="service_type" value="in_shop" {{ old('service_type', 'in_shop') === 'in_shop' ? 'checked' : '' }} onchange="toggleHomeService(false)" class="text-emerald-700 focus:ring-emerald-500">
                        <div>
                            <span class="font-bold text-stone-900 text-xs flex items-center space-x-1.5">
                                <i data-lucide="store" class="w-3.5 h-3.5 text-emerald-800"></i>
                                <span>In-Shop (Salon)</span>
                            </span>
                            <span class="text-[10px] text-stone-500">Service at salon branch</span>
                        </div>
                    </label>

                    <label class="cursor-pointer border border-stone-200 rounded-2xl p-3 flex items-center space-x-2.5 bg-stone-50 hover:border-sky-400 transition has-[:checked]:border-sky-600 has-[:checked]:bg-sky-50/60">
                        <input type="radio" name="service_type" value="home_service" {{ old('service_type') === 'home_service' ? 'checked' : '' }} onchange="toggleHomeService(true)" class="text-sky-600 focus:ring-sky-500">
                        <div>
                            <span class="font-bold text-stone-900 text-xs flex items-center space-x-1.5">
                                <i data-lucide="home" class="w-3.5 h-3.5 text-sky-700"></i>
                                <span>Home Service</span>
                            </span>
                            <span class="text-[10px] text-sky-700">Service at client's home</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Home Service Address Section -->
            <div id="homeServiceSection" class="{{ old('service_type') === 'home_service' ? '' : 'hidden' }} bg-sky-50/60 border border-sky-200 rounded-2xl p-3.5 space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-sky-900 mb-1">Home Service Address *</label>
                        <input type="text" name="home_service_address" id="home_service_address" value="{{ old('home_service_address') }}"
                            placeholder="House No., Street, Barangay / Landmark"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-sky-300 text-xs text-stone-900">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-sky-900 mb-1">Travel Fee (₱)</label>
                        <input type="number" step="10" name="home_service_fee" id="home_service_fee" value="{{ old('home_service_fee', '150.00') }}" oninput="calculateTotal()"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-sky-300 text-xs font-black text-emerald-800">
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Services & Staff Selection -->
        <div class="bg-white rounded-3xl border border-emerald-900/10 p-5 sm:p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-stone-100 pb-2">
                <h2 class="text-xs sm:text-sm font-black text-emerald-900 uppercase tracking-wider flex items-center space-x-2">
                    <span class="w-5 h-5 rounded-lg bg-emerald-700 text-white flex items-center justify-center text-[11px] font-black">2</span>
                    <span>Select Services & Assign Staff</span>
                </h2>
                <a href="{{ route('menu.index') }}" class="text-[11px] font-bold text-emerald-700 hover:underline inline-flex items-center space-x-1">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                    <span>View Menu</span>
                </a>
            </div>

            <div class="space-y-4">
                @foreach($categories as $cat)
                    <div class="space-y-2">
                        <span class="text-xs font-black uppercase tracking-wider block text-emerald-800">
                            {{ $cat->name }}
                        </span>

                        <div class="space-y-2">
                            @foreach($cat->services as $srv)
                                <div class="p-3 rounded-2xl bg-stone-50/60 border border-stone-200/60 hover:border-emerald-300 hover:bg-emerald-50/30 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <label class="flex items-start space-x-2.5 cursor-pointer">
                                        <input type="checkbox" name="services[{{ $srv->id }}][service_id]" value="{{ $srv->id }}" 
                                            data-price="{{ $srv->price }}" onchange="toggleServiceRow({{ $srv->id }})" 
                                            class="srv-checkbox rounded-sm text-emerald-700 focus:ring-emerald-500 w-4 h-4 mt-0.5">
                                        <div>
                                            <span class="font-extrabold text-stone-900 text-xs block">{{ $srv->name }}</span>
                                            <span class="text-[10px] text-stone-500">{{ $srv->estimated_duration_minutes }} mins</span>
                                        </div>
                                    </label>

                                    <div class="flex items-center space-x-3 self-end sm:self-center">
                                        <span class="text-xs font-black text-emerald-800">₱{{ number_format($srv->price, 2) }}</span>

                                        <div class="w-44">
                                            <select name="services[{{ $srv->id }}][staff_id]" id="staff_select_{{ $srv->id }}" disabled
                                                class="staff-select w-full px-2 py-1.5 text-xs rounded-xl bg-white border border-stone-200 text-stone-800 disabled:opacity-40 font-medium">
                                                <option value="">-- Select Staff --</option>
                                                @foreach($staffList as $staff)
                                                    <option value="{{ $staff->id }}" {{ (isset($myStaffProfile) && $myStaffProfile->id == $staff->id) ? 'selected' : '' }}>
                                                        {{ $staff->nickname ?? $staff->name }} ({{ $staff->status === 'available' ? 'Queue #' . $staff->queue_position : ucfirst(str_replace('_', ' ', $staff->status)) }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 3. Payment Method & Customer Satisfaction Rating (SOP Step) -->
        <div class="bg-white rounded-3xl border border-emerald-900/10 p-5 sm:p-6 shadow-xs space-y-4">
            <h2 class="text-xs sm:text-sm font-black text-emerald-900 uppercase tracking-wider flex items-center space-x-2">
                <span class="w-5 h-5 rounded-lg bg-emerald-700 text-white flex items-center justify-center text-[11px] font-black">3</span>
                <span>Payment & Customer Satisfaction</span>
            </h2>

            <div class="grid grid-cols-2 gap-2.5">
                <label class="cursor-pointer border border-stone-200 rounded-2xl p-3 flex items-center space-x-2.5 bg-stone-50 hover:border-amber-400 transition has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50/60">
                    <input type="radio" name="payment_method" value="cash" checked class="text-amber-600 focus:ring-amber-500">
                    <div>
                        <span class="font-bold text-stone-900 text-xs flex items-center space-x-1.5">
                            <i data-lucide="banknote" class="w-3.5 h-3.5 text-amber-700"></i>
                            <span>Cash Payment</span>
                        </span>
                        <span class="text-[10px] text-stone-500">Direct cash payment</span>
                    </div>
                </label>

                <label class="cursor-pointer border border-stone-200 rounded-2xl p-3 flex items-center space-x-2.5 bg-stone-50 hover:border-emerald-400 transition has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/60">
                    <input type="radio" name="payment_method" value="paymongo_qr" class="text-emerald-700 focus:ring-emerald-500">
                    <div>
                        <span class="font-bold text-stone-900 text-xs flex items-center space-x-1.5">
                            <i data-lucide="qr-code" class="w-3.5 h-3.5 text-emerald-800"></i>
                            <span>PayMongo QR</span>
                        </span>
                        <span class="text-[10px] text-stone-500">GCash / Maya / QRPh</span>
                    </div>
                </label>
            </div>

            <!-- Customer Satisfaction Feedback Rating -->
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Customer Satisfaction Rating *</label>
                <div class="flex items-center space-x-3 bg-stone-50 p-2.5 rounded-xl border border-stone-200/60">
                    <label class="flex items-center space-x-1.5 cursor-pointer text-xs">
                        <input type="radio" name="satisfaction_rating" value="5" checked class="text-emerald-600 focus:ring-emerald-500">
                        <span class="font-medium text-stone-700 flex items-center space-x-0.5">
                            <i data-lucide="star" class="w-3.5 h-3.5 text-amber-500 fill-amber-400"></i>
                            <i data-lucide="star" class="w-3.5 h-3.5 text-amber-500 fill-amber-400"></i>
                            <i data-lucide="star" class="w-3.5 h-3.5 text-amber-500 fill-amber-400"></i>
                            <i data-lucide="star" class="w-3.5 h-3.5 text-amber-500 fill-amber-400"></i>
                            <i data-lucide="star" class="w-3.5 h-3.5 text-amber-500 fill-amber-400"></i>
                            <span class="ml-1 text-[11px] font-bold text-stone-800">Excellent (5/5)</span>
                        </span>
                    </label>
                    <label class="flex items-center space-x-1.5 cursor-pointer text-xs">
                        <input type="radio" name="satisfaction_rating" value="4" class="text-emerald-600 focus:ring-emerald-500">
                        <span class="font-medium text-stone-700 flex items-center space-x-0.5">
                            <i data-lucide="star" class="w-3.5 h-3.5 text-amber-500 fill-amber-400"></i>
                            <i data-lucide="star" class="w-3.5 h-3.5 text-amber-500 fill-amber-400"></i>
                            <i data-lucide="star" class="w-3.5 h-3.5 text-amber-500 fill-amber-400"></i>
                            <i data-lucide="star" class="w-3.5 h-3.5 text-amber-500 fill-amber-400"></i>
                            <span class="ml-1 text-[11px] font-bold text-stone-800">Good (4/5)</span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- Total Bar -->
            <div class="bg-emerald-50/80 rounded-2xl p-4 space-y-1.5 border border-emerald-200/80 text-xs">
                <div class="flex justify-between text-stone-600">
                    <span>Subtotal:</span>
                    <span class="font-bold text-stone-900" id="subtotalDisplay">₱0.00</span>
                </div>
                <div class="flex justify-between text-sky-800" id="homeFeeRow" style="display: none;">
                    <span>Home Service Fee:</span>
                    <span class="font-bold" id="homeFeeDisplay">₱0.00</span>
                </div>
                <div class="pt-2 border-t border-emerald-200 flex justify-between items-baseline">
                    <span class="text-xs font-black uppercase text-emerald-900">Total Amount:</span>
                    <span class="text-2xl font-black text-emerald-800" id="grandTotalDisplay">₱0.00</span>
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 rounded-2xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm shadow-md shadow-emerald-700/20 transition transform active:scale-95 flex items-center justify-center space-x-2">
                <span>Save Order & Update Queue</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
    function toggleHomeService(isHome) {
        const sec = document.getElementById('homeServiceSection');
        const feeRow = document.getElementById('homeFeeRow');
        const addr = document.getElementById('home_service_address');
        
        if (isHome) {
            sec.classList.remove('hidden');
            feeRow.style.display = 'flex';
            addr.required = true;
        } else {
            sec.classList.add('hidden');
            feeRow.style.display = 'none';
            addr.required = false;
        }
        calculateTotal();
    }

    function toggleServiceRow(srvId) {
        const cb = document.querySelector(`input[value="${srvId}"]`);
        const sel = document.getElementById(`staff_select_${srvId}`);
        if (cb.checked) {
            sel.disabled = false;
            sel.required = true;
        } else {
            sel.disabled = true;
            sel.required = false;
            sel.value = "";
        }
        calculateTotal();
    }

    function calculateTotal() {
        let subtotal = 0;
        document.querySelectorAll('.srv-checkbox:checked').forEach(cb => {
            subtotal += parseFloat(cb.dataset.price || 0);
        });

        const isHome = document.querySelector('input[name="service_type"]:checked')?.value === 'home_service';
        let homeFee = 0;
        if (isHome) {
            homeFee = parseFloat(document.getElementById('home_service_fee')?.value || 0);
        }

        const total = subtotal + homeFee;

        document.getElementById('subtotalDisplay').textContent = '₱' + subtotal.toFixed(2);
        document.getElementById('homeFeeDisplay').textContent = '₱' + homeFee.toFixed(2);
        document.getElementById('grandTotalDisplay').textContent = '₱' + total.toFixed(2);
    }

    document.addEventListener('DOMContentLoaded', () => {
        calculateTotal();
        if (window.lucide) {
            lucide.createIcons();
        }
    });
</script>
@endpush
@endsection
