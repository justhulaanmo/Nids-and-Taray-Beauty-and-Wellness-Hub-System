@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    <!-- Top Profile & Date Filter -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-stone-900 tracking-tight flex items-center space-x-2">
                <i data-lucide="wallet" class="w-6 h-6 text-emerald-800"></i>
                <span>My Wallet & Earnings</span>
            </h1>
            <p class="text-xs text-stone-500 mt-0.5">
                Staff: <strong class="text-emerald-800">{{ $staffProfile->nickname ?? $staffProfile->name }}</strong> ({{ $staffProfile->commission_rate }}% Commission Rate)
            </p>
        </div>

        <form method="GET" action="{{ route('wallet.index') }}" class="flex items-center space-x-2 bg-white p-1.5 rounded-2xl border border-emerald-900/10 shadow-xs self-start sm:self-auto">
            <span class="text-xs font-bold text-stone-500 pl-2">Date:</span>
            <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()"
                class="px-2.5 py-1 text-xs font-bold bg-stone-50 text-stone-800 rounded-xl border border-stone-200 focus:outline-hidden focus:ring-1 focus:ring-emerald-500">
            <button type="submit" class="px-3 py-1 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-xs transition">
                Filter
            </button>
        </form>
    </div>

    <!-- Main Wallet Cards (Light Green Wellness Theme) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <!-- Customers Count -->
        <div class="bg-white rounded-3xl p-4 border border-emerald-900/10 shadow-xs space-y-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">Clients Served</span>
            <span class="text-3xl font-black text-stone-900 block">{{ $totalCustomersCount }}</span>
            <span class="text-[11px] text-stone-500 block">{{ $totalServicesCount }} treatments</span>
        </div>

        <!-- My Commission -->
        <div class="bg-emerald-50 rounded-3xl p-4 border border-emerald-200 shadow-xs space-y-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800 block">My Commission</span>
            <span class="text-2xl sm:text-3xl font-black text-emerald-800 block">₱{{ number_format($myTotalCommission, 2) }}</span>
            <span class="text-[10px] text-emerald-700/80 font-bold block">Personal Earnings</span>
        </div>

        <!-- Cash in Hand -->
        <div class="bg-white rounded-3xl p-4 border border-emerald-900/10 shadow-xs space-y-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">Cash Collected</span>
            <span class="text-2xl sm:text-3xl font-black text-stone-900 block">₱{{ number_format($cashCollected, 2) }}</span>
            <span class="text-[10px] text-stone-500 block">From cash clients</span>
        </div>

        <!-- Remit to Shop -->
        <div class="bg-teal-50 rounded-3xl p-4 border border-teal-200 shadow-xs space-y-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-teal-800 block">Cash Due to Shop</span>
            <span class="text-2xl sm:text-3xl font-black text-teal-800 block">₱{{ number_format($cashToRemitToShop, 2) }}</span>
            <span class="text-[10px] text-teal-700/80 font-bold block">End-of-Shift Remit</span>
        </div>
    </div>

    <!-- Quick Formula / Explainer Banner -->
    <div class="bg-emerald-50/60 rounded-3xl p-4 border border-emerald-200 text-xs text-stone-700 flex items-start space-x-3 shadow-xs">
        <i data-lucide="help-circle" class="w-5 h-5 text-emerald-700 flex-shrink-0 mt-0.5"></i>
        <div>
            <strong class="text-stone-900">How is the cash remittance calculated?</strong>
            <p class="mt-0.5 text-stone-600">
                From total cash collected (<strong>₱{{ number_format($cashCollected, 2) }}</strong>), subtract your commission (<strong>{{ $staffProfile->commission_rate }}%</strong>). The remaining balance to turn in to the shop is <strong>₱{{ number_format($cashToRemitToShop, 2) }}</strong>.
            </p>
            @if($qrCommissionOwedToStaff > 0)
                <p class="mt-1 text-emerald-800 font-bold">
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
                <i data-lucide="sparkles" class="w-8 h-8 text-stone-300 mx-auto"></i>
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

</div>
@endsection
