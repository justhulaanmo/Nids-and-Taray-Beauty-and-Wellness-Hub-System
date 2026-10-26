@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    <!-- Top Profile & Date Filter (Crisp White Card for 100% Clarity) -->
    <div class="bg-white rounded-3xl border border-stone-200/90 p-4 sm:p-5 shadow-2xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-stone-900 tracking-tight flex items-center space-x-2">
                <i data-lucide="wallet" class="w-6 h-6 text-emerald-800"></i>
                <span>My Wallet & Earnings</span>
            </h1>
            <p class="text-xs text-stone-600 mt-1 font-semibold">
                Attendant: <strong class="text-emerald-900 font-extrabold">{{ $staffProfile->nickname ?? $staffProfile->name }}</strong> ({{ $staffProfile->commission_rate }}% Commission Rate)
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto">
            <button type="button" onclick="openStaffCashoutModal()"
                class="px-4 py-2 rounded-2xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-stone-950 font-black text-xs shadow-xs transition-all active:scale-95 flex items-center space-x-1.5">
                <i data-lucide="arrow-up-right" class="w-4 h-4 text-stone-950"></i>
                <span>Cashout (₱{{ number_format($availableCommissionBalance, 0) }})</span>
            </button>

            <form method="GET" action="{{ route('wallet.index') }}" class="flex items-center space-x-2 bg-stone-50 p-1.5 rounded-2xl border border-stone-200 shadow-2xs">
                <span class="text-xs font-bold text-stone-600 pl-2">Date:</span>
                <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()"
                    class="px-2.5 py-1 text-xs font-bold bg-white text-stone-900 rounded-xl border border-stone-300 focus:outline-hidden focus:ring-1 focus:ring-emerald-500">
                <button type="submit" class="px-3.5 py-1 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-xs transition">
                    Filter
                </button>
            </form>
        </div>
    </div>

    <!-- Main Wallet Cards (Solid High-Contrast Wellness Theme) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <!-- Customers Count -->
        <div class="bg-white rounded-3xl p-4 border border-stone-200 shadow-xs space-y-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-stone-500 block">Clients Served</span>
            <span class="text-3xl font-black text-stone-900 block">{{ $totalCustomersCount }}</span>
            <span class="text-[11px] text-stone-600 font-medium block">{{ $totalServicesCount }} treatments</span>
        </div>

        <!-- My Commission -->
        <div class="bg-emerald-50 rounded-3xl p-4 border-2 border-emerald-300 shadow-xs space-y-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-900 block">My Commission</span>
            <span class="text-2xl sm:text-3xl font-black text-emerald-800 block">₱{{ number_format($myTotalCommission, 2) }}</span>
            <span class="text-[10px] text-emerald-700 font-bold block">Personal Earnings</span>
        </div>

        <!-- Cash in Hand -->
        <div class="bg-white rounded-3xl p-4 border border-stone-200 shadow-xs space-y-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-stone-500 block">Cash Collected</span>
            <span class="text-2xl sm:text-3xl font-black text-stone-900 block">₱{{ number_format($cashCollected, 2) }}</span>
            <span class="text-[10px] text-stone-600 font-medium block">From cash clients</span>
        </div>

        <!-- Remit to Shop -->
        <div class="bg-teal-50 rounded-3xl p-4 border-2 border-teal-300 shadow-xs space-y-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-teal-900 block">Cash Due to Shop</span>
            <span class="text-2xl sm:text-3xl font-black text-teal-800 block">₱{{ number_format($cashToRemitToShop, 2) }}</span>
            <span class="text-[10px] text-teal-700 font-bold block">End-of-Shift Remit</span>
        </div>
    </div>

    <!-- Quick Formula / Explainer Banner (Solid White Card for Crisp Legibility) -->
    <div class="bg-white rounded-3xl p-4 sm:p-5 border-2 border-emerald-200 text-xs text-stone-700 flex items-start space-x-3 shadow-xs">
        <i data-lucide="help-circle" class="w-5 h-5 text-emerald-700 flex-shrink-0 mt-0.5"></i>
        <div>
            <strong class="text-stone-900 font-extrabold text-sm block">How is the cash remittance calculated?</strong>
            <p class="mt-1 text-stone-600 leading-relaxed">
                From total cash collected (<strong>₱{{ number_format($cashCollected, 2) }}</strong>), subtract your commission (<strong>{{ $staffProfile->commission_rate }}%</strong>). The remaining balance to turn in to the shop is <strong class="text-emerald-900 font-black">₱{{ number_format($cashToRemitToShop, 2) }}</strong>.
            </p>
            @if($qrCommissionOwedToStaff > 0)
                <p class="mt-1.5 text-emerald-800 font-bold">
                    You also have PayMongo QR earnings (₱{{ number_format($qrCommissionOwedToStaff, 2) }} commission) deposited directly to the shop account.
                </p>
            @endif
        </div>
    </div>

    <!-- Customer Breakdown Log -->
    <div class="bg-white rounded-3xl border border-emerald-900/10 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-stone-100 flex items-center justify-between">
            <h2 class="text-sm font-black text-stone-900 uppercase tracking-wider flex items-center space-x-1.5">
                <i data-lucide="calendar-check" class="w-4 h-4 text-emerald-800"></i>
                <span>Today's Service Log</span>
            </h2>
            <a href="{{ route('transactions.create') }}" class="px-3 py-1.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition inline-flex items-center space-x-1">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>New Order</span>
            </a>
        </div>

        @if($items->isEmpty())
            <div class="p-8 text-center text-xs text-stone-400 space-y-2">
                <i data-lucide="inbox" class="w-8 h-8 text-stone-300 mx-auto"></i>
                <div class="font-bold text-stone-800 text-sm">No recorded services for this date yet.</div>
                <div>Recorded services and commissions will automatically appear here once added.</div>
            </div>
        @else
            <div class="divide-y divide-stone-100">
                @foreach($items as $item)
                    <div class="p-4 hover:bg-emerald-50/40 transition flex items-center justify-between gap-3 text-xs">
                        <div class="space-y-1">
                            <div class="flex items-center space-x-2">
                                <span class="font-bold text-stone-900 text-sm">{{ $item->transaction->customer_name ?? 'Walk-in' }}</span>
                                @if($item->transaction->service_type === 'home_service')
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                        <i data-lucide="home" class="w-3 h-3"></i>
                                        <span>Home Service</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                        <i data-lucide="store" class="w-3 h-3"></i>
                                        <span>In-Shop</span>
                                    </span>
                                @endif
                            </div>
                            <p class="text-stone-500">
                                {{ $item->service->name ?? 'Service' }} &bull; 
                                <span class="text-stone-400">{{ $item->created_at->format('h:i A') }}</span>
                            </p>
                        </div>

                        <div class="text-right">
                            <span class="font-black text-emerald-800 text-sm block">
                                +₱{{ number_format($item->staff_commission_amount, 2) }}
                            </span>
                            <span class="text-[10px] text-stone-500 inline-flex items-center space-x-1 justify-end">
                                @if($item->transaction->payment_method === 'paymongo_qr')
                                    <i data-lucide="qr-code" class="w-3 h-3 text-indigo-700"></i>
                                    <span>PayMongo QR</span>
                                @else
                                    <i data-lucide="banknote" class="w-3 h-3 text-emerald-700"></i>
                                    <span>Cash (₱{{ number_format($item->price, 0) }})</span>
                                @endif
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Staff Cashout Modal -->
    <div id="staffCashoutModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl border border-stone-200 shadow-xl max-w-md w-full p-6 space-y-4 relative animate-in fade-in zoom-in-95 duration-150">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                        <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-black text-stone-900">Cashout Commission</h3>
                        <p class="text-[11px] text-stone-500 font-semibold">Withdraw your personal earnings</p>
                    </div>
                </div>
                <button type="button" onclick="closeStaffCashoutModal()" class="p-1 rounded-xl text-stone-400 hover:text-stone-700 hover:bg-stone-100">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('payouts.staff') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Available Balance Notice -->
                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-800">Available Commission:</span>
                    <span class="text-base font-black text-emerald-900">₱{{ number_format($availableCommissionBalance, 2) }}</span>
                </div>

                <!-- Amount Input -->
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Cashout Amount (₱) *</label>
                    <input type="number" step="0.01" min="50" max="{{ $availableCommissionBalance }}" name="amount" id="staff_cashout_amount"
                        value="{{ min($availableCommissionBalance, 200) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-sm font-black text-stone-900 focus:bg-white focus:outline-hidden focus:border-emerald-600">
                    <div class="flex items-center gap-1.5 mt-2">
                        <button type="button" onclick="setStaffCashoutAmount(100)" class="px-2.5 py-1 rounded-lg bg-stone-100 hover:bg-stone-200 text-[10px] font-bold text-stone-700">₱100</button>
                        <button type="button" onclick="setStaffCashoutAmount(200)" class="px-2.5 py-1 rounded-lg bg-stone-100 hover:bg-stone-200 text-[10px] font-bold text-stone-700">₱200</button>
                        <button type="button" onclick="setStaffCashoutMax()" class="px-2.5 py-1 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-[10px] font-bold text-emerald-800">Max</button>
                    </div>
                </div>

                <!-- Withdrawal Channel -->
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Payout Destination *</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="cursor-pointer border border-stone-200 rounded-xl p-2.5 text-center bg-stone-50 hover:border-emerald-500 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50">
                            <input type="radio" name="payout_method" value="gcash" checked class="sr-only">
                            <span class="block text-xs font-black text-stone-900">GCash</span>
                            <span class="block text-[9px] text-stone-500 font-semibold">Instant E-Wallet</span>
                        </label>
                        <label class="cursor-pointer border border-stone-200 rounded-xl p-2.5 text-center bg-stone-50 hover:border-emerald-500 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50">
                            <input type="radio" name="payout_method" value="maya" class="sr-only">
                            <span class="block text-xs font-black text-stone-900">Maya</span>
                            <span class="block text-[9px] text-stone-500 font-semibold">Instant E-Wallet</span>
                        </label>
                    </div>
                </div>

                <!-- Account Name & Number -->
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">GCash / Maya Name *</label>
                        <input type="text" name="account_name" value="{{ $staffProfile->name }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-xs font-bold text-stone-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">GCash / Maya Mobile Number *</label>
                        <input type="text" name="account_number" value="{{ $staffProfile->phone ?? '09' }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-xs font-bold text-stone-900">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit"
                        class="w-full py-3.5 px-4 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-black text-xs shadow-md transition-all active:scale-[0.98] flex items-center justify-center space-x-2">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        <span>Confirm Commission Cashout</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function openStaffCashoutModal() {
        const modal = document.getElementById('staffCashoutModal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeStaffCashoutModal() {
        const modal = document.getElementById('staffCashoutModal');
        if (modal) modal.classList.add('hidden');
    }

    function setStaffCashoutAmount(amount) {
        const input = document.getElementById('staff_cashout_amount');
        if (input) input.value = amount;
    }

    function setStaffCashoutMax() {
        const input = document.getElementById('staff_cashout_amount');
        if (input && input.max) input.value = input.max;
    }
</script>
@endpush
@endsection
