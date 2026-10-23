@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Hero / Welcome Banner -->
    <div class="relative bg-emerald-950 rounded-3xl p-6 sm:p-8 text-white shadow-md shadow-emerald-900/10 overflow-hidden">
        <!-- Background Storefront Photo with Emerald Tint -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/nids-and-taray-bg.jpg') }}" alt="Nids and Taray Storefront" class="w-full h-full object-cover object-center opacity-25 filter blur-[0.5px] scale-105">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/95 via-teal-950/85 to-emerald-900/90"></div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5 relative z-10">
            <div>
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-white/15 text-emerald-100 text-xs font-bold uppercase tracking-wider mb-2 border border-white/20">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-emerald-200"></i>
                    <span>Shift Today: {{ now()->format('l, F d, Y') }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    Welcome, {{ Auth::user()->name }}
                </h1>
                <p class="text-xs sm:text-sm text-emerald-100/90 mt-1 max-w-xl">
                    @if(Auth::user()->role === 'admin')
                        Admin Dashboard &bull; Monitor total sales, staff cash remittance, and service queue in real time.
                    @else
                        Staff Portal &bull; Record client orders and view your daily earnings.
                    @endif
                </p>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('transactions.create') }}" class="px-5 py-3 rounded-2xl bg-white text-emerald-900 hover:bg-emerald-50 font-black text-xs sm:text-sm shadow-md transition transform active:scale-95 inline-flex items-center space-x-2">
                    <i data-lucide="plus" class="w-4 h-4 text-emerald-900"></i>
                    <span>New Order</span>
                </a>
                <a href="{{ route('menu.index') }}" class="px-4 py-3 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-xs sm:text-sm transition inline-flex items-center space-x-1.5">
                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                    <span>Service Menu</span>
                </a>
                <a href="{{ route('wallet.index') }}" class="px-4 py-3 rounded-2xl bg-emerald-600/40 hover:bg-emerald-600/60 border border-white/20 text-emerald-50 font-bold text-xs sm:text-sm transition inline-flex items-center space-x-1.5">
                    <i data-lucide="wallet" class="w-4 h-4"></i>
                    <span>My Wallet</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Staff Personal Quick Tally (Visible for Staff & Admin) -->
    @if($myPersonalStats)
        <div class="bg-white rounded-3xl p-5 border border-emerald-900/10 shadow-xs space-y-3">
            <div class="flex items-center justify-between border-b border-stone-100 pb-2">
                <div class="flex items-center space-x-2">
                    <i data-lucide="user-check" class="w-4 h-4 text-emerald-800"></i>
                    <span class="text-xs font-black uppercase tracking-wider text-emerald-900">
                        My Daily Summary ({{ $myPersonalStats['profile']->nickname ?? $myPersonalStats['profile']->name }})
                    </span>
                </div>
                <a href="{{ route('wallet.index') }}" class="text-[11px] font-black text-emerald-700 hover:text-emerald-800 hover:underline inline-flex items-center space-x-1">
                    <span>View Wallet</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                <div class="p-3.5 rounded-2xl bg-stone-50 border border-stone-200/60">
                    <span class="block text-2xl font-black text-stone-900">{{ $myPersonalStats['customers_today'] }}</span>
                    <span class="text-[10px] uppercase font-bold text-stone-500">Clients Served</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200/70">
                    <span class="block text-2xl font-black text-emerald-800">₱{{ number_format($myPersonalStats['commission_today'], 2) }}</span>
                    <span class="text-[10px] uppercase font-bold text-emerald-700">My Commission</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-200/70">
                    <span class="block text-2xl font-black text-amber-900">₱{{ number_format($myPersonalStats['cash_collected'], 2) }}</span>
                    <span class="text-[10px] uppercase font-bold text-amber-700">Cash Collected</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-teal-50/70 border border-teal-200/70">
                    <span class="block text-2xl font-black text-teal-900">₱{{ number_format($myPersonalStats['cash_to_remit'], 2) }}</span>
                    <span class="text-[10px] uppercase font-bold text-teal-700">Cash Due to Shop</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Admin Overview: Shop Financials & Remittance Tally -->
    @if(Auth::user()->role === 'admin')
        <!-- Financial Summary -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-white rounded-2xl p-4 border border-emerald-900/10 shadow-xs">
                <span class="text-[10px] font-black uppercase tracking-wider text-stone-500 block">Today's Clients</span>
                <span class="text-3xl font-black text-stone-900 block mt-1">{{ $totalCustomersToday }}</span>
                <span class="text-[11px] text-stone-500 block mt-0.5">{{ $inShopCustomersToday }} In-Shop &bull; {{ $homeServiceCustomersToday }} Home Service</span>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-emerald-200 bg-gradient-to-br from-white to-emerald-50/50 shadow-xs">
                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700 block">Total Sales</span>
                <span class="text-2xl sm:text-3xl font-black text-emerald-800 block mt-1">₱{{ number_format($totalSalesToday, 2) }}</span>
                <span class="text-[10px] text-stone-500 block mt-0.5">Paid transactions</span>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-amber-200 bg-gradient-to-br from-white to-amber-50/50 shadow-xs">
                <span class="text-[10px] font-black uppercase tracking-wider text-amber-700 block">Cash Sales</span>
                <span class="text-2xl sm:text-3xl font-black text-amber-800 block mt-1">₱{{ number_format($cashSalesToday, 2) }}</span>
                <span class="text-[10px] text-stone-500 block mt-0.5">Collected by staff</span>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-sky-200 bg-gradient-to-br from-white to-sky-50/50 shadow-xs">
                <span class="text-[10px] font-black uppercase tracking-wider text-sky-700 block">PayMongo QR</span>
                <span class="text-2xl sm:text-3xl font-black text-sky-800 block mt-1">₱{{ number_format($qrSalesToday, 2) }}</span>
                <span class="text-[10px] text-stone-500 block mt-0.5">GCash / Maya account</span>
            </div>
        </div>

        <!-- Remittance Tally Table (What each staff remits to Shop) -->
        <div class="bg-white rounded-3xl border border-emerald-900/10 shadow-xs p-5 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-stone-100 pb-3">
                <div>
                    <h2 class="text-sm sm:text-base font-black text-stone-900 flex items-center space-x-2">
                        <i data-lucide="banknote" class="w-4 h-4 text-emerald-800"></i>
                        <span>Staff Cash Remittance Summary</span>
                    </h2>
                    <p class="text-xs text-stone-500">
                        Summary of cash to be turned in to the shop by each staff member at the end of the shift.
                    </p>
                </div>
                <a href="{{ route('services.manage') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 inline-flex items-center space-x-1">
                    <i data-lucide="sliders" class="w-3.5 h-3.5"></i>
                    <span>Price Setter</span>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-stone-50 text-stone-600 font-bold uppercase border-b border-stone-200">
                            <th class="py-2.5 px-3">Staff Member</th>
                            <th class="py-2.5 px-3 text-center">Clients Served</th>
                            <th class="py-2.5 px-3 text-right">Cash Collected</th>
                            <th class="py-2.5 px-3 text-right">Commission Earned</th>
                            <th class="py-2.5 px-3 text-right">Cash Due to Shop</th>
                            <th class="py-2.5 px-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach($staffProfiles as $staff)
                            <tr class="hover:bg-emerald-50/40 transition">
                                <td class="py-3 px-3">
                                    <span class="font-extrabold text-stone-900 text-sm block">{{ $staff->nickname ?? $staff->name }}</span>
                                    <span class="text-[11px] text-stone-500">{{ $staff->name }} ({{ $staff->commission_rate }}%)</span>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-black {{ $staff->customers_count > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-100 text-stone-400' }}">
                                        {{ $staff->customers_count }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right font-bold text-amber-800">
                                    ₱{{ number_format($staff->cash_collected, 2) }}
                                </td>
                                <td class="py-3 px-3 text-right font-bold text-emerald-700">
                                    ₱{{ number_format($staff->commission_total, 2) }}
                                </td>
                                <td class="py-3 px-3 text-right font-black text-teal-800 text-sm">
                                    ₱{{ number_format($staff->cash_to_remit, 2) }}
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $staff->status_badge['class'] }}">
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

    <!-- Live Queue Snapshot Section (Important for both Admin and Staff) -->
    <div class="bg-white rounded-3xl border border-emerald-900/10 shadow-xs p-5 space-y-4">
        <div class="border-b border-stone-100 pb-3 space-y-2">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse flex-shrink-0"></span>
                    <h2 class="text-sm sm:text-base font-black text-stone-900 flex items-center space-x-1.5">
                        <i data-lucide="users" class="w-4 h-4 text-emerald-800"></i>
                        <span>Available Staff in Queue</span>
                    </h2>
                </div>
                <a href="{{ route('queue.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 inline-flex items-center space-x-1 flex-shrink-0">
                    <span>Queue Board</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
            <div class="flex items-center pl-5">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 whitespace-nowrap">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 mr-1.5"></span>
                    {{ $queueAvailable->count() }} Available
                </span>
            </div>
        </div>

        @if($queueAvailable->isEmpty())
            <div class="p-6 text-center text-xs text-stone-500 bg-stone-50 rounded-2xl border border-stone-200/50">
                No staff currently available in queue. All staff are busy with clients, on home service, or on break.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($queueAvailable as $staff)
                    <div class="p-3.5 rounded-2xl bg-stone-50 border border-stone-200/70 flex items-center justify-between hover:border-emerald-300 hover:bg-emerald-50/40 transition">
                        <div class="flex items-center space-x-3">
                            <span class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-xs">
                                #{{ $loop->iteration }}
                            </span>
                            <div>
                                <span class="font-extrabold text-stone-900 text-sm block">{{ $staff->nickname ?? $staff->name }}</span>
                                <span class="text-[10px] text-stone-500 capitalize">{{ str_replace('_', ' ', $staff->specialization) }}</span>
                            </div>
                        </div>
                        <a href="{{ route('transactions.create') }}" class="px-3.5 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-xs transition">
                            Assign
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
