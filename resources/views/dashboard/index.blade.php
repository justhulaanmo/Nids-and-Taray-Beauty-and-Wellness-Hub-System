@extends('layouts.app')

@section('content')
<div class="space-y-4 sm:space-y-6">

    <!-- ========================================== -->
    <!-- STAFF MOBILE-APP HERO CARD (Mobile-First)   -->
    <!-- ========================================== -->
    @if($myProfile)
        <div class="bg-white rounded-3xl border-2 border-emerald-300/90 p-4 sm:p-6 shadow-sm space-y-4">
            
            <!-- Staff Profile & Current Status Header -->
            <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                <div class="flex items-center space-x-3">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-emerald-700 to-teal-600 text-white font-black text-base flex items-center justify-center shadow-xs">
                        {{ strtoupper(substr($myProfile->nickname ?? $myProfile->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <h2 class="text-base sm:text-lg font-black text-stone-900 tracking-tight leading-tight">
                                {{ $myProfile->nickname ?? $myProfile->name }}
                            </h2>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold capitalize bg-emerald-50 text-emerald-800 border border-emerald-200">
                                {{ str_replace('_', ' ', $myProfile->specialization) }}
                            </span>
                        </div>
                        <span class="text-[11px] text-stone-400 block font-medium">
                            {{ $myProfile->commission_rate }}% commission rate
                        </span>
                    </div>
                </div>

                <!-- Live Status Badge -->
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black border {{ $myProfile->status_badge['class'] }}">
                        <span class="w-2 h-2 rounded-full mr-1.5 {{ $myProfile->status === 'available' ? 'bg-emerald-500 animate-pulse' : 'bg-stone-400' }}"></span>
                        {{ $myProfile->status_badge['label'] }}
                    </span>
                </div>
            </div>

            <!-- 1. QUEUE VISIBILITY BANNER -->
            @if($myProfile->status === 'available')
                <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-2xl p-4 sm:p-5 text-white shadow-md flex items-center justify-between">
                    <div class="space-y-0.5">
                        <span class="text-[10px] sm:text-xs uppercase tracking-wider font-extrabold text-emerald-100 block">
                            Your Queue Status
                        </span>
                        <div class="text-xl sm:text-3xl font-black tracking-tight flex items-center space-x-2">
                            @if($myQueueTurn === 1)
                                <span>YOU ARE #1 (NEXT IN LINE)</span>
                            @elseif($myQueueTurn)
                                <span>YOU ARE #{{ $myQueueTurn }} IN LINE</span>
                            @else
                                <span>READY TO SERVE</span>
                            @endif
                        </div>
                        <p class="text-[11px] text-emerald-100 font-medium">
                            {{ $myQueueTurn === 1 ? 'You will be assigned the next incoming client.' : 'Clients are assigned according to fair queue order.' }}
                        </p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-white/15 border border-white/20 flex flex-col items-center justify-center text-white flex-shrink-0">
                        <span class="text-[9px] uppercase font-bold text-emerald-200">Turn</span>
                        <span class="text-2xl font-black">#{{ $myQueueTurn ?? 1 }}</span>
                    </div>
                </div>
            @elseif($myProfile->status === 'serving_in_shop' || $myProfile->status === 'on_home_service')
                <div class="bg-gradient-to-r from-teal-700 to-cyan-800 rounded-2xl p-4 sm:p-5 text-white shadow-md flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-extrabold text-teal-200 block">Current Status</span>
                        <div class="text-lg sm:text-xl font-black tracking-tight flex items-center space-x-2 mt-0.5">
                            <i data-lucide="{{ $myProfile->status === 'on_home_service' ? 'home' : 'store' }}" class="w-5 h-5"></i>
                            <span>{{ $myProfile->status === 'on_home_service' ? 'OUT ON HOME SERVICE' : 'SERVING CLIENT IN SALON' }}</span>
                        </div>
                        <p class="text-[11px] text-teal-100 mt-0.5">Complete client service below to return to the available queue.</p>
                    </div>
                </div>
            @elseif($myProfile->status === 'on_break')
                <div class="bg-gradient-to-r from-amber-600 to-amber-700 rounded-2xl p-4 text-white shadow-md flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-extrabold text-amber-100 block">Rest Period</span>
                        <div class="text-lg sm:text-xl font-black tracking-tight flex items-center space-x-2 mt-0.5">
                            <i data-lucide="coffee" class="w-5 h-5"></i>
                            <span>YOU ARE CURRENTLY ON BREAK</span>
                        </div>
                        <p class="text-[11px] text-amber-100 mt-0.5">Click below when ready to return to the available queue.</p>
                    </div>
                </div>
            @endif

            <!-- 2. ACTIVE JOB ALERT (If staff is assigned to an active order) -->
            @if($myActiveJob)
                <div class="bg-teal-50 border-2 border-teal-300 rounded-2xl p-4 sm:p-5 shadow-xs space-y-3">
                    <div class="flex items-center justify-between border-b border-teal-200/80 pb-2.5">
                        <div class="flex items-center space-x-2 text-teal-950 font-black text-xs sm:text-sm">
                            <i data-lucide="bell" class="w-4 h-4 text-teal-700"></i>
                            <span>ACTIVE CLIENT ASSIGNMENT</span>
                        </div>
                        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-teal-100 text-teal-800 border border-teal-200">
                            {{ $myActiveJob->transaction->tracking_number ?? 'Order' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-stone-500 text-[11px] block">Client Name:</span>
                            <span class="font-black text-stone-900 text-sm sm:text-base block">
                                {{ $myActiveJob->transaction->customer_name }}
                            </span>
                            @if($myActiveJob->transaction->customer_phone)
                                <span class="text-stone-500 text-[11px]">{{ $myActiveJob->transaction->customer_phone }}</span>
                            @endif
                        </div>

                        <div>
                            <span class="text-stone-500 text-[11px] block">Service Treatment:</span>
                            <span class="font-extrabold text-teal-900 text-sm block">
                                {{ $myActiveJob->service->name ?? 'Treatment' }}
                            </span>
                            <span class="text-emerald-800 font-bold text-[11px]">
                                Your Commission: ₱{{ number_format($myActiveJob->staff_commission_amount, 2) }}
                            </span>
                        </div>
                    </div>

                    <!-- Prominent Complete & Return to Queue Button -->
                    <form action="{{ route('transactions.mark-completed', $myActiveJob->transaction_id) }}" method="POST" class="pt-1">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-emerald-700 to-teal-700 hover:from-emerald-800 hover:to-teal-800 text-white font-black text-xs sm:text-sm shadow-md transition-all active:scale-[0.98] flex items-center justify-center space-x-2">
                            <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                            <span>Mark as Done (Return to Queue)</span>
                        </button>
                    </form>
                </div>
            @endif

            <!-- 3. ONE-TAP STATUS CONTROLS (Available vs On Break) -->
            <div class="pt-1">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-stone-400 block mb-2">
                    Quick Status Toggle
                </span>
                
                @if($myProfile->status === 'available')
                    <form action="{{ route('queue.update-status', $myProfile->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="on_break">
                        <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border-2 border-amber-300 font-bold text-xs sm:text-sm shadow-2xs transition flex items-center justify-center space-x-2 active:scale-[0.98]">
                            <i data-lucide="coffee" class="w-4 h-4 text-amber-700"></i>
                            <span>Take a Break</span>
                        </button>
                    </form>
                @else
                    <form action="{{ route('queue.update-status', $myProfile->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="available">
                        <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white border-2 border-emerald-600 font-bold text-xs sm:text-sm shadow-md transition flex items-center justify-center space-x-2 active:scale-[0.98]">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            <span>Set Available (Ready for Clients)</span>
                        </button>
                    </form>
                @endif
            </div>

            <!-- 4. Staff Daily Earnings Overview (Large Tap-Friendly Cards) -->
            @if($myPersonalStats)
                <div class="grid grid-cols-2 gap-2.5 pt-2">
                    <a href="{{ route('wallet.index') }}" class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200 text-center hover:bg-emerald-100/60 transition block">
                        <span class="text-xs uppercase font-extrabold text-emerald-800 block">Today's Earnings</span>
                        <span class="text-xl sm:text-2xl font-black text-emerald-900 block mt-0.5">
                            ₱{{ number_format($myPersonalStats['commission_today'], 2) }}
                        </span>
                        <span class="text-[10px] text-emerald-700 font-medium">Tap to see wallet &rarr;</span>
                    </a>

                    <div class="p-3.5 rounded-2xl bg-stone-50 border border-stone-200 text-center">
                        <span class="text-xs uppercase font-extrabold text-stone-500 block">Clients Served</span>
                        <span class="text-xl sm:text-2xl font-black text-stone-900 block mt-0.5">
                            {{ $myPersonalStats['customers_today'] }}
                        </span>
                        <span class="text-[10px] text-stone-500 font-medium">Completed today</span>
                    </div>
                </div>
            @endif

        </div>
    @endif

    <!-- ========================================== -->
    <!-- ADMIN OVERVIEW SECTION (Financials & Queue)-->
    <!-- ========================================== -->
    @if(Auth::user()->role === 'admin')
        <!-- Shop Net Profit & PayMongo Cashout Hero Card -->
        <div class="bg-gradient-to-br from-emerald-900 via-teal-900 to-emerald-950 text-white rounded-3xl p-5 sm:p-6 shadow-md border border-emerald-800 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-white/10 pb-3">
                <div class="flex items-center space-x-2.5">
                    <span class="w-9 h-9 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center shrink-0">
                        <i data-lucide="wallet" class="w-5 h-5 text-emerald-300"></i>
                    </span>
                    <div>
                        <h3 class="text-sm sm:text-base font-black tracking-tight text-white flex items-center space-x-2">
                            <span>Shop Net Earnings & Cashout</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                                PayMongo Test Mode
                            </span>
                        </h3>
                        <p class="text-[11px] text-emerald-100/70">
                            Clean salon revenue after deducting staff commissions and PayMongo 2.5% gateway fees.
                        </p>
                    </div>
                </div>

                <!-- Cashout Trigger Button -->
                <div>
                    <button type="button" onclick="openAdminCashoutModal()"
                        class="px-4 py-2.5 rounded-2xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-stone-950 font-black text-xs shadow-md transition-all active:scale-95 flex items-center space-x-2">
                        <i data-lucide="arrow-up-right" class="w-4 h-4 text-stone-950"></i>
                        <span>Cashout / Withdraw</span>
                    </button>
                </div>
            </div>

            <!-- Balances Row -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <!-- Available for Cashout -->
                <div class="p-3.5 bg-white/10 rounded-2xl border border-white/10 backdrop-blur-xs">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-200 block">Available for Cashout</span>
                    <span class="text-2xl sm:text-3xl font-black text-white block mt-0.5">
                        ₱{{ number_format($availableForCashout, 2) }}
                    </span>
                    <span class="text-[10px] text-emerald-200/80 block mt-0.5">
                        All-time net profit minus withdrawn (₱{{ number_format($totalCashedOut, 2) }})
                    </span>
                </div>

                <!-- Today's Net Profit (Clean Shop Share) -->
                <div class="p-3.5 bg-emerald-800/40 rounded-2xl border border-emerald-400/20 backdrop-blur-xs">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-300 block">Today's Net Shop Profit</span>
                    <span class="text-2xl sm:text-3xl font-black text-emerald-200 block mt-0.5">
                        ₱{{ number_format($todayNetShopProfit, 2) }}
                    </span>
                    <span class="text-[10px] text-emerald-100/70 block mt-0.5">
                        Gross (₱{{ number_format($totalSalesToday, 2) }}) less Commission & Fees
                    </span>
                </div>

                <!-- Today's Fee & Commission Breakdown -->
                <div class="p-3 bg-white/5 rounded-2xl border border-white/10 text-xs space-y-1">
                    <div class="flex justify-between text-emerald-100/70 text-[11px]">
                        <span>Gross Sales (Today):</span>
                        <span class="font-bold text-white">₱{{ number_format($totalSalesToday, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-amber-300 text-[11px]">
                        <span>Less Staff Commission:</span>
                        <span class="font-bold">-₱{{ number_format($todayCommissions, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-rose-300 text-[11px]">
                        <span>Less PayMongo Fee (2.5%):</span>
                        <span class="font-bold">-₱{{ number_format($todayPaymongoFees, 2) }}</span>
                    </div>
                    <div class="pt-1 border-t border-white/10 flex justify-between text-emerald-200 font-extrabold text-[11px]">
                        <span>Net Shop Takehome:</span>
                        <span>₱{{ number_format($todayNetShopProfit, 2) }}</span>
                    </div>
                </div>
            </div>

            @if(isset($recentPayouts) && $recentPayouts->isNotEmpty())
                <div class="pt-2 border-t border-white/10 text-xs flex flex-wrap items-center justify-between gap-2">
                    <span class="text-[11px] text-emerald-200/80 font-bold flex items-center space-x-1.5">
                        <i data-lucide="history" class="w-3.5 h-3.5"></i>
                        <span>Recent Cashout: {{ $recentPayouts->first()->reference_no }} &bull; ₱{{ number_format($recentPayouts->first()->net_amount, 2) }} to {{ strtoupper($recentPayouts->first()->payout_method) }}</span>
                    </span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/30 text-emerald-200 font-extrabold">
                        {{ ucfirst($recentPayouts->first()->status) }}
                    </span>
                </div>
            @endif
        </div>

        <!-- Store / Daily Metrics -->
        <div class="bg-white rounded-3xl border border-stone-200/80 p-4 sm:p-5 shadow-2xs space-y-3">
            <div class="flex items-center justify-between border-b border-stone-100 pb-2">
                <h3 class="text-xs font-black uppercase tracking-wider text-stone-800 flex items-center space-x-2">
                    <i data-lucide="store" class="w-4 h-4 text-emerald-800"></i>
                    <span>Shop Operations Overview (Admin)</span>
                </h3>
                <span class="text-[11px] font-bold text-stone-400">{{ now()->format('M d, Y') }}</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                <div class="p-3 bg-stone-50 rounded-2xl border border-stone-200/70 text-center">
                    <span class="text-[10px] font-bold uppercase text-stone-500 block">Total Clients</span>
                    <span class="text-xl sm:text-2xl font-black text-stone-900 block mt-0.5">{{ $totalCustomersToday }}</span>
                    <span class="text-[9px] text-stone-400 block">{{ $inShopCustomersToday }} Salon &bull; {{ $homeServiceCustomersToday }} Home</span>
                </div>

                <div class="p-3 bg-emerald-50/60 rounded-2xl border border-emerald-200 text-center">
                    <span class="text-[10px] font-bold uppercase text-emerald-800 block">Total Sales</span>
                    <span class="text-xl sm:text-2xl font-black text-emerald-900 block mt-0.5">₱{{ number_format($totalSalesToday, 2) }}</span>
                    <span class="text-[9px] text-emerald-700 block">Paid Orders</span>
                </div>

                <div class="p-3 bg-amber-50/60 rounded-2xl border border-amber-200 text-center">
                    <span class="text-[10px] font-bold uppercase text-amber-800 block">Cash Sales</span>
                    <span class="text-xl sm:text-2xl font-black text-amber-900 block mt-0.5">₱{{ number_format($cashSalesToday, 2) }}</span>
                    <span class="text-[9px] text-amber-700 block">On-hand cash</span>
                </div>

                <div class="p-3 bg-indigo-50/60 rounded-2xl border border-indigo-200 text-center">
                    <span class="text-[10px] font-bold uppercase text-indigo-800 block">PayMongo QR</span>
                    <span class="text-xl sm:text-2xl font-black text-indigo-900 block mt-0.5">₱{{ number_format($qrSalesToday, 2) }}</span>
                    <span class="text-[9px] text-indigo-700 block">GCash / Maya</span>
                </div>
            </div>
        </div>

        <!-- Staff Remittance Summary Table (Compact) -->
        <div class="bg-white rounded-3xl border border-stone-200/80 p-4 sm:p-5 shadow-2xs space-y-3">
            <div class="flex items-center justify-between border-b border-stone-100 pb-2">
                <h3 class="text-xs font-black uppercase tracking-wider text-stone-800 flex items-center space-x-2">
                    <i data-lucide="banknote" class="w-4 h-4 text-emerald-800"></i>
                    <span>Staff Remittance Due to Shop</span>
                </h3>
                <a href="{{ route('staff-tracker.index') }}" class="text-[11px] font-bold text-emerald-700 hover:underline">
                    View Tracker &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-stone-200 bg-stone-50/50 text-[10px] font-bold text-stone-500 uppercase">
                            <th class="py-2 px-2.5">Staff</th>
                            <th class="py-2 px-2.5 text-center">Clients</th>
                            <th class="py-2 px-2.5 text-right">Cash In Hand</th>
                            <th class="py-2 px-2.5 text-right">Due to Shop</th>
                            <th class="py-2 px-2.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach($staffProfiles as $staff)
                            <tr class="hover:bg-stone-50/50">
                                <td class="py-2.5 px-2.5">
                                    <span class="font-extrabold text-stone-900 block text-xs">{{ $staff->nickname ?? $staff->name }}</span>
                                    <span class="text-[10px] text-stone-400 capitalize">{{ str_replace('_', ' ', $staff->specialization) }}</span>
                                </td>
                                <td class="py-2.5 px-2.5 text-center font-bold text-stone-800">
                                    {{ $staff->customers_count }}
                                </td>
                                <td class="py-2.5 px-2.5 text-right font-bold text-amber-800">
                                    ₱{{ number_format($staff->cash_collected, 2) }}
                                </td>
                                <td class="py-2.5 px-2.5 text-right font-black text-emerald-800">
                                    ₱{{ number_format($staff->cash_to_remit, 2) }}
                                </td>
                                <td class="py-2.5 px-2.5 text-center">
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-bold border {{ $staff->status_badge['class'] }}">
                                        {{ $staff->status_badge['label'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Available Queue Strip -->
    <div class="bg-white rounded-3xl border border-stone-200/80 p-4 sm:p-5 shadow-2xs space-y-3">
        <div class="flex items-center justify-between border-b border-stone-100 pb-2">
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <h3 class="text-xs font-black uppercase tracking-wider text-stone-800">
                    Live Available Queue ({{ $queueAvailable->count() }})
                </h3>
            </div>
            <a href="{{ route('queue.index') }}" class="text-[11px] font-bold text-emerald-700 hover:underline">
                Full Queue Board &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
            @forelse($queueAvailable as $staff)
                <div class="p-2.5 rounded-xl bg-stone-50 border border-stone-200/70 flex items-center justify-between text-xs hover:bg-emerald-50/30 transition">
                    <div class="flex items-center space-x-2.5 min-w-0">
                        <span class="w-6 h-6 rounded-lg bg-emerald-700 text-white font-black text-xs flex items-center justify-center flex-shrink-0">
                            #{{ $loop->iteration }}
                        </span>
                        <div class="truncate">
                            <span class="font-extrabold text-stone-900 block truncate text-xs">{{ $staff->nickname ?? $staff->name }}</span>
                            <span class="text-[10px] text-stone-400 capitalize">{{ str_replace('_', ' ', $staff->specialization) }}</span>
                        </div>
                    </div>
                    <span class="text-[10px] px-2 py-0.5 rounded-md font-bold bg-emerald-100 text-emerald-800">Ready</span>
                </div>
            @empty
                <div class="p-3 text-center text-xs text-stone-400 col-span-3">
                    No staff currently waiting in queue.
                </div>
            @endforelse
        </div>
    </div>

    @if(Auth::user()->role === 'admin')
        <!-- Admin Cashout Modal -->
        <div id="adminCashoutModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl border border-stone-200 shadow-xl max-w-md w-full p-6 space-y-4 relative animate-in fade-in zoom-in-95 duration-150">
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                            <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-black text-stone-900">Cashout Shop Net Profit</h3>
                            <p class="text-[11px] text-stone-500 font-semibold">PayMongo Test Mode Simulation</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeAdminCashoutModal()" class="p-1 rounded-xl text-stone-400 hover:text-stone-700 hover:bg-stone-100">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form action="{{ route('payouts.admin') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Available Balance Notice -->
                    <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-between">
                        <span class="text-xs font-bold text-emerald-800">Available Net Profit:</span>
                        <span class="text-base font-black text-emerald-900">₱{{ number_format($availableForCashout, 2) }}</span>
                    </div>

                    <!-- Amount Input -->
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Cashout Amount (₱) *</label>
                        <input type="number" step="0.01" min="100" max="{{ $availableForCashout }}" name="amount" id="cashout_amount"
                            value="{{ min($availableForCashout, 500) }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-sm font-black text-stone-900 focus:bg-white focus:outline-hidden focus:border-emerald-600">
                        <div class="flex items-center gap-1.5 mt-2">
                            <button type="button" onclick="setCashoutAmount(100)" class="px-2.5 py-1 rounded-lg bg-stone-100 hover:bg-stone-200 text-[10px] font-bold text-stone-700">₱100</button>
                            <button type="button" onclick="setCashoutAmount(500)" class="px-2.5 py-1 rounded-lg bg-stone-100 hover:bg-stone-200 text-[10px] font-bold text-stone-700">₱500</button>
                            <button type="button" onclick="setCashoutAmount(1000)" class="px-2.5 py-1 rounded-lg bg-stone-100 hover:bg-stone-200 text-[10px] font-bold text-stone-700">₱1,000</button>
                            <button type="button" onclick="setCashoutAmount({{ $availableForCashout }})" class="px-2.5 py-1 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-[10px] font-bold text-emerald-800">Max</button>
                        </div>
                    </div>

                    <!-- Withdrawal Channel -->
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Transfer Destination *</label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="cursor-pointer border border-stone-200 rounded-xl p-2.5 text-center bg-stone-50 hover:border-emerald-500 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50">
                                <input type="radio" name="payout_method" value="gcash" checked onchange="toggleBankFields(false)" class="sr-only">
                                <span class="block text-xs font-black text-stone-900">GCash</span>
                                <span class="block text-[9px] text-stone-500 font-semibold">Fee: ₱0.00</span>
                            </label>
                            <label class="cursor-pointer border border-stone-200 rounded-xl p-2.5 text-center bg-stone-50 hover:border-emerald-500 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50">
                                <input type="radio" name="payout_method" value="maya" onchange="toggleBankFields(false)" class="sr-only">
                                <span class="block text-xs font-black text-stone-900">Maya</span>
                                <span class="block text-[9px] text-stone-500 font-semibold">Fee: ₱0.00</span>
                            </label>
                            <label class="cursor-pointer border border-stone-200 rounded-xl p-2.5 text-center bg-stone-50 hover:border-emerald-500 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50">
                                <input type="radio" name="payout_method" value="bank" onchange="toggleBankFields(true)" class="sr-only">
                                <span class="block text-xs font-black text-stone-900">Bank</span>
                                <span class="block text-[9px] text-stone-500 font-semibold">Fee: ₱15.00</span>
                            </label>
                        </div>
                    </div>

                    <!-- Bank Name Selection (conditional) -->
                    <div id="bankNameContainer" class="hidden">
                        <label class="block text-xs font-bold text-stone-700 mb-1">Bank Name *</label>
                        <select name="bank_name" class="w-full px-3 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-xs font-bold text-stone-900">
                            <option value="BDO Unibank">BDO Unibank</option>
                            <option value="BPI">Bank of the Philippine Islands (BPI)</option>
                            <option value="UnionBank">UnionBank of the Philippines</option>
                            <option value="Metrobank">Metrobank</option>
                            <option value="Landbank">Landbank of the Philippines</option>
                            <option value="RCBC">RCBC</option>
                        </select>
                    </div>

                    <!-- Account Name & Number -->
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-stone-700 mb-1">Account Holder Name *</label>
                            <input type="text" name="account_name" required placeholder="e.g. Nida Santos"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-xs font-bold text-stone-900">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-stone-700 mb-1">Mobile / Account Number *</label>
                            <input type="text" name="account_number" required placeholder="e.g. 0917-xxx-xxxx"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-xs font-bold text-stone-900">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit"
                            class="w-full py-3.5 px-4 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-black text-xs shadow-md transition-all active:scale-[0.98] flex items-center justify-center space-x-2">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            <span>Process Cashout (Test Mode Simulation)</span>
                        </button>
                        <p class="text-[10px] text-stone-400 text-center mt-2 font-medium">
                            Simulates instant PayMongo payout dispatch and balance deduction.
                        </p>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>

@push('scripts')
<script>
    function openAdminCashoutModal() {
        const modal = document.getElementById('adminCashoutModal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeAdminCashoutModal() {
        const modal = document.getElementById('adminCashoutModal');
        if (modal) modal.classList.add('hidden');
    }

    function setCashoutAmount(amount) {
        const input = document.getElementById('cashout_amount');
        if (input) input.value = amount;
    }

    function toggleBankFields(isBank) {
        const bankContainer = document.getElementById('bankNameContainer');
        if (bankContainer) {
            if (isBank) {
                bankContainer.classList.remove('hidden');
            } else {
                bankContainer.classList.add('hidden');
            }
        }
    }
</script>
@endpush
@endsection
