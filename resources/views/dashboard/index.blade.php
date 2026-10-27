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
        <!-- Monthly Net Profit & Expense Tracker Hero Card -->
        <div class="bg-gradient-to-br from-stone-900 via-emerald-950 to-stone-900 text-white rounded-3xl p-5 sm:p-6 shadow-md border border-emerald-800/60 space-y-5">
            <!-- Header with Month Selector & Action Buttons -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 border-b border-white/10 pb-4">
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="w-8 h-8 rounded-xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center shrink-0">
                            <i data-lucide="calculator" class="w-4 h-4 text-emerald-300"></i>
                        </span>
                        <h2 class="text-base sm:text-lg font-black tracking-tight text-white flex items-center space-x-2">
                            <span>Monthly Net Profit & Expense Tracker</span>
                        </h2>
                    </div>
                    <p class="text-[11px] text-stone-300 mt-1">
                        Gross Sales &minus; Deductions (Commissions & Gateway Fees) = <strong class="text-emerald-300">Gross Profit</strong> &minus; Shop Expenses (Rent, Bills, Supplies) = <strong class="text-emerald-200">True Net Profit (Tubo)</strong>.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2 self-start lg:self-auto">
                    <!-- Month Selector -->
                    <form method="GET" action="{{ route('dashboard') }}" class="flex items-center space-x-1.5 bg-white/10 p-1.5 rounded-2xl border border-white/15">
                        <span class="text-[11px] font-bold text-emerald-200 pl-1.5">Month:</span>
                        <input type="month" name="month" value="{{ $selectedMonth }}" onchange="this.form.submit()"
                            class="px-2.5 py-1 text-xs font-bold bg-white text-stone-900 rounded-xl border-none focus:outline-hidden">
                    </form>

                    <!-- Add Expense Button -->
                    <button type="button" onclick="openExpenseModal()"
                        class="px-3.5 py-2 rounded-2xl bg-white/15 hover:bg-white/20 text-white border border-white/20 font-black text-xs shadow-xs transition active:scale-95 flex items-center space-x-1.5 cursor-pointer">
                        <i data-lucide="plus-circle" class="w-4 h-4 text-emerald-300"></i>
                        <span>+ Add Expense</span>
                    </button>

                    <!-- Cashout Button (Outside Tiles / In Header) -->
                    <button type="button" onclick="openAdminCashoutModal()"
                        class="px-3.5 py-2 rounded-2xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-stone-950 font-black text-xs shadow-xs transition active:scale-95 flex items-center space-x-1.5 cursor-pointer">
                        <i data-lucide="arrow-up-right" class="w-4 h-4 text-stone-950"></i>
                        <span>Cashout (₱{{ number_format($availableForCashout, 0) }})</span>
                    </button>
                </div>
            </div>

            <!-- Financial Formula 5-Card Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-2.5">
                <!-- 1. Gross Sales -->
                <div class="p-3.5 bg-white/5 rounded-2xl border border-white/10">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-stone-300 block">1. Gross Sales</span>
                    <span class="text-xl sm:text-2xl font-black text-white block mt-0.5">
                        ₱{{ number_format($monthGrossSales, 2) }}
                    </span>
                    <span class="text-[10px] text-stone-400 block mt-0.5">All client payments</span>
                </div>

                <!-- 2. Total Deductions -->
                <div class="p-3.5 bg-white/5 rounded-2xl border border-white/10 flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-300 block">2. Total Deductions</span>
                        <span class="text-xl sm:text-2xl font-black text-amber-200 block mt-0.5">
                            -₱{{ number_format($monthTotalDeductions, 2) }}
                        </span>
                    </div>
                    <div class="mt-2 pt-1.5 border-t border-white/10 space-y-0.5 text-[10px]">
                        <div class="flex items-center justify-between text-stone-300">
                            <span>Staff Commission:</span>
                            <span class="font-bold text-white">₱{{ number_format($monthCommissions, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-stone-300">
                            <span>PayMongo Fee (2.5%):</span>
                            <span class="font-bold text-amber-300">₱{{ number_format($monthPaymongoFees, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-0.5 border-t border-white/10 font-extrabold text-amber-200">
                            <span>Total Deductions:</span>
                            <span>-₱{{ number_format($monthTotalDeductions, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Gross Profit -->
                <div class="p-3.5 bg-white/5 rounded-2xl border border-white/10">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-300 block">3. Gross Profit</span>
                    <span class="text-xl sm:text-2xl font-black text-emerald-200 block mt-0.5">
                        ₱{{ number_format($monthGrossProfit, 2) }}
                    </span>
                    <span class="text-[10px] text-stone-400 block mt-0.5">Shop share from sales</span>
                </div>

                <!-- 4. Shop Expenses (Rent & Bills) -->
                <div class="p-3.5 bg-white/5 rounded-2xl border border-white/10">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-rose-300 block">4. Shop Expenses</span>
                    <span class="text-xl sm:text-2xl font-black text-rose-200 block mt-0.5">
                        -₱{{ number_format($monthTotalExpenses, 2) }}
                    </span>
                    <span class="text-[10px] text-stone-400 block mt-0.5">Rent, Bills, Supplies</span>
                </div>

                <!-- 5. TRUE NET PROFIT (HERO CARD) -->
                <div class="col-span-2 lg:col-span-1 p-3.5 rounded-2xl border {{ $monthTrueNetProfit >= 0 ? 'bg-emerald-600/30 border-emerald-400/50 text-emerald-100' : 'bg-rose-600/30 border-rose-400/50 text-rose-100' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-wider block">5. True Net Profit</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded uppercase {{ $monthTrueNetProfit >= 0 ? 'bg-emerald-400/30 text-emerald-200' : 'bg-rose-400/30 text-rose-200' }}">
                            {{ $monthTrueNetProfit >= 0 ? 'May Tubo' : 'Deficit' }}
                        </span>
                    </div>
                    <span class="text-2xl font-black block mt-0.5 {{ $monthTrueNetProfit >= 0 ? 'text-white' : 'text-rose-200' }}">
                        {{ $monthTrueNetProfit < 0 ? '-' : '' }}₱{{ number_format(abs($monthTrueNetProfit), 2) }}
                    </span>
                    <span class="text-[10px] block mt-0.5 {{ $monthTrueNetProfit >= 0 ? 'text-emerald-200/90' : 'text-rose-200/90' }}">
                        {{ $monthTrueNetProfit >= 0 ? 'Malinis na kita ng shop' : 'Kulang sa bayad ng gastusin' }}
                    </span>
                </div>
            </div>

            <!-- Recent Cashout & Available for Withdrawal Status Bar -->
            <div class="pt-2 border-t border-white/10 flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="flex items-center flex-wrap gap-2">
                    <span class="text-stone-300 font-medium">Available for Bank/E-Wallet Cashout:</span>
                    <span class="font-black text-emerald-300 text-sm">₱{{ number_format($availableForCashout, 2) }}</span>
                    <span class="text-stone-400 text-[10px]">(All-time net profit minus withdrawn: ₱{{ number_format($totalCashedOut, 2) }})</span>
                    <button type="button" onclick="openAdminCashoutModal()"
                        class="px-2.5 py-1 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-stone-950 font-black text-[11px] shadow-xs transition active:scale-95 flex items-center space-x-1 cursor-pointer">
                        <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-stone-950"></i>
                        <span>Cashout Now</span>
                    </button>
                </div>

                @if(isset($recentPayouts) && $recentPayouts->isNotEmpty())
                    <div class="flex items-center space-x-2">
                        <span class="text-stone-300 text-[11px]">Last Cashout: #{{ $recentPayouts->first()->reference_no }} (₱{{ number_format($recentPayouts->first()->net_amount, 2) }} to {{ strtoupper($recentPayouts->first()->payout_method) }})</span>
                        <!-- Quick Void for Testing Button -->
                        <form action="{{ route('payouts.void', $recentPayouts->first()->id) }}" method="POST" onsubmit="return confirm('Void this test payout and restore ₱{{ number_format($recentPayouts->first()->gross_amount, 2) }} back to your available balance for testing?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-2 py-0.5 rounded bg-amber-400/20 hover:bg-amber-400/30 text-amber-200 text-[10px] font-bold border border-amber-300/30 transition">
                                Void for Re-test
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <!-- Monthly Shop Expenses List -->
        <div class="bg-white rounded-3xl border border-stone-200 p-4 sm:p-5 shadow-2xs space-y-3">
            <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                <div class="flex items-center space-x-2">
                    <i data-lucide="receipt" class="w-4 h-4 text-emerald-800"></i>
                    <h3 class="text-xs font-black uppercase tracking-wider text-stone-900">
                        Monthly Shop Expenses Log ({{ \Carbon\Carbon::parse($selectedMonth . '-01')->format('F Y') }})
                    </h3>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs font-bold text-stone-600">Total Expenses:</span>
                    <span class="text-sm font-black text-rose-700">₱{{ number_format($monthTotalExpenses, 2) }}</span>
                </div>
            </div>

            @if($monthExpensesList->isEmpty())
                <div class="p-6 text-center text-xs text-stone-400 space-y-2">
                    <i data-lucide="file-spreadsheet" class="w-8 h-8 text-stone-300 mx-auto"></i>
                    <div class="font-bold text-stone-700">No expenses recorded for {{ \Carbon\Carbon::parse($selectedMonth . '-01')->format('F Y') }} yet.</div>
                    <div class="text-[11px] text-stone-500">
                        Click <button type="button" onclick="openExpenseModal()" class="text-emerald-700 font-bold underline">+ Add Expense</button> to record Rent, Electricity, Water, WiFi, or Supplies.
                    </div>
                </div>
            @else
                <div class="divide-y divide-stone-100">
                    @foreach($monthExpensesList as $exp)
                        <div class="py-2.5 flex items-center justify-between text-xs hover:bg-stone-50/60 px-2 rounded-xl transition">
                            <div class="space-y-0.5">
                                <div class="flex items-center space-x-2">
                                    <span class="font-extrabold text-stone-900 text-sm">{{ $exp->description }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                        {{ $exp->category === 'Rent' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $exp->category === 'Utilities' ? 'bg-sky-100 text-sky-800' : '' }}
                                        {{ $exp->category === 'Supplies' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                        {{ $exp->category === 'Others' ? 'bg-stone-100 text-stone-800' : '' }}
                                    ">
                                        {{ $exp->category }}
                                    </span>
                                </div>
                                <span class="text-[11px] text-stone-400">Date: {{ $exp->expense_date->format('M d, Y') }}</span>
                            </div>

                            <div class="flex items-center space-x-3">
                                <span class="font-black text-rose-700 text-sm">
                                    -₱{{ number_format($exp->amount, 2) }}
                                </span>
                                <form action="{{ route('expenses.destroy', $exp->id) }}" method="POST" onsubmit="return confirm('Delete expense {{ $exp->description }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 rounded-lg text-stone-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Delete Expense">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
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
                            <button type="button" onclick="setCashoutMax()" class="px-2.5 py-1 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-[10px] font-bold text-emerald-800">Max</button>
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
        <!-- Add Operational Expense Modal -->
        <div id="addExpenseModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl border border-stone-200 shadow-xl max-w-md w-full p-6 space-y-4 relative animate-in fade-in zoom-in-95 duration-150">
                <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-800 flex items-center justify-center shrink-0">
                            <i data-lucide="receipt" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-black text-stone-900">Record Operational Expense</h3>
                            <p class="text-[11px] text-stone-500 font-semibold">Rent, utilities, supplies, or salon bills</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeExpenseModal()" class="p-1 rounded-xl text-stone-400 hover:text-stone-700 hover:bg-stone-100">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form action="{{ route('expenses.store') }}" method="POST" class="space-y-3.5">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Expense Category *</label>
                        <select name="category" required class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-xs font-bold text-stone-900 focus:bg-white focus:outline-hidden">
                            <option value="Rent">Rent (Upa sa Pwesto)</option>
                            <option value="Utilities" selected>Utilities (Kuryente, Tubig, WiFi)</option>
                            <option value="Supplies">Supplies (Shampoo, Lotion, Polish, Towels)</option>
                            <option value="Others">Others (Maintenance, Cleaning, Etc.)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Description / Bill Name *</label>
                        <input type="text" name="description" required placeholder="e.g. Meralco Electric Bill, PLDT WiFi, October Rent"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-xs font-bold text-stone-900 focus:bg-white focus:outline-hidden">
                    </div>

                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-xs font-bold text-stone-700 mb-1">Amount (₱) *</label>
                            <input type="number" step="0.01" min="1" name="amount" required placeholder="0.00"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-sm font-black text-stone-900 focus:bg-white focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-stone-700 mb-1">Expense Date *</label>
                            <input type="date" name="expense_date" value="{{ now()->toDateString() }}" required
                                class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-xs font-bold text-stone-900 focus:bg-white focus:outline-hidden">
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="w-full py-3.5 px-4 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-black text-xs shadow-md transition-all active:scale-[0.98] flex items-center justify-center space-x-2">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            <span>Save Expense Record</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- PayMongo Admin Payout Receipt Modal -->
        @if(session('payout_receipt'))
            @php $receipt = session('payout_receipt'); @endphp
            <div id="payoutReceiptModal" class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                <div class="bg-white rounded-3xl border border-stone-200 shadow-2xl max-w-sm w-full p-6 text-center space-y-4 animate-in fade-in zoom-in-95 duration-200">
                    <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-700 mx-auto flex items-center justify-center">
                        <i data-lucide="check" class="w-8 h-8 stroke-[3]"></i>
                    </div>

                    <div>
                        <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800">
                            PayMongo Disbursements (Test Mode)
                        </span>
                        <h3 class="text-xl font-black text-stone-900 mt-2">Cashout Dispatched!</h3>
                        <p class="text-xs text-stone-500">Transferred directly to your {{ $receipt['payout_method'] }} account</p>
                    </div>

                    <div class="bg-stone-50 rounded-2xl p-4 border border-stone-200 text-xs space-y-2 text-left">
                        <div class="flex justify-between">
                            <span class="text-stone-500 font-medium">Reference:</span>
                            <span class="font-mono font-bold text-stone-900">{{ $receipt['reference_no'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-stone-500 font-medium">Channel:</span>
                            <span class="font-bold text-stone-900">{{ $receipt['payout_method'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-stone-500 font-medium">Recipient:</span>
                            <span class="font-bold text-stone-900">{{ $receipt['account_name'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-stone-500 font-medium">Account / Mobile:</span>
                            <span class="font-mono font-bold text-stone-900">{{ $receipt['account_number'] }}</span>
                        </div>
                        <div class="border-t border-stone-200 pt-2 flex justify-between items-baseline">
                            <span class="text-stone-700 font-bold">Total Transferred:</span>
                            <span class="text-lg font-black text-emerald-800">₱{{ number_format($receipt['amount'], 2) }}</span>
                        </div>
                    </div>

                    <button type="button" onclick="document.getElementById('payoutReceiptModal').remove()"
                        class="w-full py-3 px-4 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-black text-xs shadow-xs transition active:scale-95">
                        Close Receipt
                    </button>
                </div>
            </div>
        @endif
    @endif

</div>

@push('scripts')
<script>
    function openExpenseModal() {
        const modal = document.getElementById('addExpenseModal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeExpenseModal() {
        const modal = document.getElementById('addExpenseModal');
        if (modal) modal.classList.add('hidden');
    }

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

    function setCashoutMax() {
        const input = document.getElementById('cashout_amount');
        if (input && input.max) input.value = input.max;
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
