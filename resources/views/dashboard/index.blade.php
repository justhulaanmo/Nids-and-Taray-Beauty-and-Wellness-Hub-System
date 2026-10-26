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

            <!-- 1. BIG QUEUE VISIBILITY BANNER -->
            @if($myProfile->status === 'available')
                <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-2xl p-4 sm:p-5 text-white shadow-md flex items-center justify-between">
                    <div class="space-y-0.5">
                        <span class="text-[10px] sm:text-xs uppercase tracking-wider font-extrabold text-emerald-100 block">
                            Your Queue Position (Pila)
                        </span>
                        <div class="text-2xl sm:text-4xl font-black tracking-tight flex items-center space-x-2">
                            @if($myQueueTurn === 1)
                                <span>⭐ YOU ARE #1 (NEXT UP!)</span>
                            @elseif($myQueueTurn)
                                <span>YOU ARE #{{ $myQueueTurn }} IN LINE</span>
                            @else
                                <span>READY TO SERVE</span>
                            @endif
                        </div>
                        <p class="text-[11px] text-emerald-100 font-medium">
                            {{ $myQueueTurn === 1 ? 'Get ready! You will be assigned the next incoming client.' : 'Clients are assigned in fair rotation order.' }}
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
                        <div class="text-xl sm:text-2xl font-black tracking-tight flex items-center space-x-2 mt-0.5">
                            <i data-lucide="{{ $myProfile->status === 'on_home_service' ? 'home' : 'store' }}" class="w-5 h-5"></i>
                            <span>{{ $myProfile->status === 'on_home_service' ? 'OUT ON HOME SERVICE' : 'SERVING CLIENT IN-SHOP' }}</span>
                        </div>
                        <p class="text-[11px] text-teal-100 mt-0.5">Finish client service below to return to the available queue.</p>
                    </div>
                </div>
            @elseif($myProfile->status === 'on_break')
                <div class="bg-gradient-to-r from-amber-600 to-amber-700 rounded-2xl p-4 text-white shadow-md flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-extrabold text-amber-100 block">Rest Period</span>
                        <div class="text-xl font-black tracking-tight flex items-center space-x-2 mt-0.5">
                            <i data-lucide="coffee" class="w-5 h-5"></i>
                            <span>YOU ARE CURRENTLY ON BREAK</span>
                        </div>
                        <p class="text-[11px] text-amber-100 mt-0.5">Tap below when you are ready to rejoin the available queue.</p>
                    </div>
                </div>
            @endif

            <!-- 2. ACTIVE JOB ALERT (If staff is assigned to an active order) -->
            @if($myActiveJob)
                <div class="bg-teal-50 border-2 border-teal-300 rounded-2xl p-4 sm:p-5 shadow-xs space-y-3">
                    <div class="flex items-center justify-between border-b border-teal-200/80 pb-2.5">
                        <div class="flex items-center space-x-2 text-teal-950 font-black text-xs sm:text-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-teal-600 animate-ping"></span>
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
                            <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                            <span>MARK AS DONE &bull; RETURN TO QUEUE</span>
                        </button>
                    </form>
                </div>
            @endif

            <!-- 3. PROMINENT ONE-TAP STATUS CONTROLS (Available vs On Break) -->
            <div class="pt-1">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-stone-400 block mb-2">
                    Quick Status Toggle
                </span>
                
                @if($myProfile->status === 'available')
                    <form action="{{ route('queue.update-status', $myProfile->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="on_break">
                        <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border-2 border-amber-300 font-black text-xs sm:text-sm shadow-2xs transition flex items-center justify-center space-x-2 active:scale-[0.98]">
                            <i data-lucide="coffee" class="w-4 h-4 text-amber-700"></i>
                            <span>TAKE A BREAK (PAHINGA MUNA)</span>
                        </button>
                    </form>
                @else
                    <form action="{{ route('queue.update-status', $myProfile->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="available">
                        <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white border-2 border-emerald-600 font-black text-xs sm:text-sm shadow-md transition flex items-center justify-center space-x-2 active:scale-[0.98]">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            <span>I AM READY &bull; SET TO AVAILABLE (PILA NA)</span>
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

</div>
@endsection
