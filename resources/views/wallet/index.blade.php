@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    @if(Auth::user()->role === 'admin')
        <!-- Admin Notice: Personal Wallet vs Shop Monthly Financials -->
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/90 rounded-2xl p-3.5 shadow-2xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5">
            <div class="flex items-center space-x-2 text-xs">
                <i data-lucide="info" class="w-4 h-4 text-amber-700 shrink-0"></i>
                <span class="text-amber-950 font-medium">
                    <strong class="font-bold text-amber-900">Administrator Notice:</strong>
                    You are in "My Wallet" (personal attendant earnings). To view salon-wide sales, shop expenses (rent, bills, supplies), and monthly profit reports:
                </span>
            </div>
            <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-black text-xs shadow-xs transition flex items-center space-x-1 shrink-0 self-start sm:self-auto">
                <span>View Monthly Net Profit</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>
    @endif

    <!-- Top Profile & Date Filter -->
    <div class="bg-white rounded-3xl border border-stone-200/90 p-4 sm:p-5 shadow-2xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-stone-900 tracking-tight flex items-center space-x-2">
                <i data-lucide="wallet" class="w-6 h-6 text-emerald-800"></i>
                <span>My Wallet & Earnings</span>
            </h1>
            <p class="text-xs text-stone-600 mt-1 font-semibold">
                Attendant: <strong class="text-emerald-900 font-extrabold">{{ $staffProfile->nickname ?? $staffProfile->name }}</strong> 
                <span class="inline-flex items-center px-2 py-0.5 ml-1.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">
                    {{ $staffProfile->commission_rate }}% Commission
                </span>
            </p>
        </div>

        <form method="GET" action="{{ route('wallet.index') }}" class="flex items-center space-x-2 bg-stone-50 p-1.5 rounded-2xl border border-stone-200 shadow-2xs self-start sm:self-auto">
            <span class="text-xs font-bold text-stone-600 pl-2">Date:</span>
            <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()"
                class="px-2.5 py-1 text-xs font-bold bg-white text-stone-900 rounded-xl border border-stone-300 focus:outline-hidden focus:ring-1 focus:ring-emerald-500">
            <button type="submit" class="px-3.5 py-1 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-xs transition">
                Filter
            </button>
        </form>
    </div>

    <!-- Massive Hero Card: My Take-Home Earnings Today -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-800 via-emerald-900 to-teal-950 text-white p-6 sm:p-8 shadow-md border border-emerald-700/40">
        <!-- Background subtle decorative pattern -->
        <div class="absolute -right-8 -bottom-8 w-44 h-44 rounded-full bg-emerald-600/20 blur-2xl pointer-events-none"></div>
        <div class="absolute -left-6 -top-6 w-36 h-36 rounded-full bg-teal-400/10 blur-xl pointer-events-none"></div>

        <div class="relative z-10 space-y-3">
            <div class="flex items-center justify-between">
                <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-[11px] font-black tracking-wide uppercase bg-emerald-700/60 text-emerald-200 border border-emerald-500/30">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-emerald-300"></i>
                    <span>Today's Net Take-Home Pay</span>
                </span>
                <span class="text-xs text-emerald-200/80 font-medium">
                    {{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}
                </span>
            </div>

            <div>
                <span class="text-xs sm:text-sm font-bold text-emerald-200/90 block">My Take-Home Earnings Today</span>
                <div class="flex items-baseline space-x-2 mt-1">
                    <span class="text-4xl sm:text-5xl font-black tracking-tight text-white">
                        ₱{{ number_format($todayTakeHomeEarnings, 2) }}
                    </span>
                    <span class="text-xs font-bold text-emerald-300/80">PHP</span>
                </div>
            </div>

            <p class="text-xs text-emerald-200/70 font-medium pt-1 border-t border-emerald-700/50 flex items-center justify-between">
                <span>Personal take-home commission after platform fees</span>
                <span class="font-bold text-emerald-100">{{ $totalServicesCount }} completed {{ \Illuminate\Support\Str::plural('job', $totalServicesCount) }}</span>
            </p>
        </div>
    </div>

    <!-- Secondary Metrics Row: Clients Served & Available Cashout Balance -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
        <!-- Clients Served Card -->
        <div class="bg-white rounded-3xl p-5 border border-stone-200 shadow-xs flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-stone-500 block">Clients Served Today</span>
                <span class="text-3xl font-black text-stone-900 block">{{ $totalCustomersCount }}</span>
                <span class="text-xs text-stone-600 font-semibold block">{{ $totalServicesCount }} treatments handled</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-stone-100 text-stone-700 flex items-center justify-center shrink-0">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Available for Cashout Tile with Cashout Button Directly Below It -->
        <div class="bg-white rounded-3xl p-5 border-2 border-emerald-200 shadow-xs flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <div class="space-y-0.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800 block">Available for Cashout</span>
                    <span class="text-2xl sm:text-3xl font-black text-emerald-900 block">
                        ₱{{ number_format($availableCommissionBalance, 2) }}
                    </span>
                    <span class="text-[11px] text-stone-500 font-medium block">All-time unwithdrawn commission</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-200">
                    <i data-lucide="coins" class="w-6 h-6"></i>
                </div>
            </div>

            <!-- Cashout Button Positioned Logically Under the Balance Tile -->
            <div>
                @if($availableCommissionBalance >= 50)
                    <button type="button" onclick="openStaffCashoutModal()"
                        class="w-full py-2.5 px-4 rounded-2xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-stone-950 font-black text-xs shadow-xs transition-all active:scale-[0.98] flex items-center justify-center space-x-1.5">
                        <i data-lucide="arrow-up-right" class="w-4 h-4 text-stone-950"></i>
                        <span>Cashout Commission</span>
                    </button>
                @else
                    <button type="button" disabled
                        class="w-full py-2.5 px-4 rounded-2xl bg-stone-100 text-stone-400 font-bold text-xs cursor-not-allowed flex items-center justify-center space-x-1.5">
                        <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                        <span>Cashout (Min. ₱50.00)</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Career / All-Time Summary Bar -->
    <div class="bg-stone-50 rounded-2xl p-3.5 border border-stone-200 flex flex-wrap items-center justify-between gap-3 text-xs">
        <div class="flex items-center space-x-2">
            <i data-lucide="history" class="w-4 h-4 text-stone-500"></i>
            <span class="font-bold text-stone-700">Career Total Earned:</span>
            <span class="font-black text-emerald-800">₱{{ number_format($totalEarnedAllTime, 2) }}</span>
        </div>
        <div class="flex items-center space-x-2">
            <span class="text-stone-500 font-medium">Total Cashed Out:</span>
            <span class="font-bold text-stone-800">₱{{ number_format($totalStaffWithdrawn, 2) }}</span>
        </div>
    </div>

    <!-- Today's Service Log (Strict Employee Net Cut View) -->
    <div class="bg-white rounded-3xl border border-stone-200 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-stone-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-black text-stone-900 uppercase tracking-wider flex items-center space-x-1.5">
                    <i data-lucide="calendar-check" class="w-4 h-4 text-emerald-800"></i>
                    <span>Today's Service Log</span>
                </h2>
                <p class="text-[11px] text-stone-500 font-medium mt-0.5">List of jobs handled and your final net take-home commission</p>
            </div>
            <a href="{{ route('transactions.create') }}" class="px-3.5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition inline-flex items-center space-x-1">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>New Order</span>
            </a>
        </div>

        @if($items->isEmpty())
            <div class="p-8 text-center text-xs text-stone-400 space-y-2">
                <i data-lucide="inbox" class="w-8 h-8 text-stone-300 mx-auto"></i>
                <div class="font-bold text-stone-800 text-sm">No recorded services for this date yet.</div>
                <div>When you service a client, your job and net commission cut will appear here.</div>
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
                                <strong class="text-stone-700">{{ $item->service->name ?? 'Service' }}</strong> &bull; 
                                <span class="text-stone-400">{{ $item->created_at->format('h:i A') }}</span>
                                <span class="text-stone-400 ml-1">(Price: ₱{{ number_format($item->price, 2) }})</span>
                            </p>
                        </div>

                        <div class="text-right">
                            <div class="flex items-center justify-end space-x-1">
                                <span class="text-[10px] font-bold text-emerald-700 uppercase">Net Take-Home:</span>
                                <span class="font-black text-emerald-800 text-base">
                                    +₱{{ number_format($item->net_commission, 2) }}
                                </span>
                            </div>
                            <span class="text-[10px] text-stone-500 inline-flex items-center space-x-1 justify-end mt-0.5">
                                @if($item->transaction->payment_method === 'paymongo_qr')
                                    <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200 font-semibold">
                                        <i data-lucide="qr-code" class="w-3 h-3 text-indigo-700"></i>
                                        <span>PayMongo QR (-2.5% fee)</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-md bg-stone-100 text-stone-600 font-semibold">
                                        <i data-lucide="banknote" class="w-3 h-3 text-emerald-700"></i>
                                        <span>Cash Payment</span>
                                    </span>
                                @endif
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Recent Cashout & Payout History -->
    <div class="bg-white rounded-3xl border border-stone-200 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-stone-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-black text-stone-900 uppercase tracking-wider flex items-center space-x-1.5">
                    <i data-lucide="history" class="w-4 h-4 text-emerald-800"></i>
                    <span>Recent Cashout History</span>
                </h2>
                <p class="text-[11px] text-stone-500 font-medium mt-0.5">Disbursements dispatched to your GCash or Maya</p>
            </div>
            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200">
                PayMongo Disbursements
            </span>
        </div>

        @if($staffPayouts->isEmpty())
            <div class="p-6 text-center text-xs text-stone-400 space-y-1.5">
                <i data-lucide="wallet-cards" class="w-7 h-7 text-stone-300 mx-auto"></i>
                <div class="font-bold text-stone-700">No cashouts requested yet</div>
                <div class="text-[11px]">When you request a commission cashout, it will appear here.</div>
            </div>
        @else
            <div class="divide-y divide-stone-100">
                @foreach($staffPayouts as $payout)
                    <div class="p-4 hover:bg-stone-50 transition flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs">
                        <div class="space-y-1">
                            <div class="flex items-center space-x-2">
                                <span class="font-mono font-bold text-stone-900 text-xs">#{{ $payout->reference_no }}</span>
                                <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-full text-[10px] font-black {{ $payout->payout_method === 'maya' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-blue-50 text-blue-800 border border-blue-200' }}">
                                    <span>{{ strtoupper($payout->payout_method) }}</span>
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-900">
                                    {{ strtoupper($payout->status) }}
                                </span>
                            </div>
                            <p class="text-stone-500">
                                To: <strong class="text-stone-800">{{ $payout->account_name }}</strong> ({{ $payout->account_number }}) &bull; 
                                <span class="text-stone-400">{{ $payout->created_at->format('M d, Y • h:i A') }}</span>
                            </p>
                            @if($payout->paymongo_payout_id)
                                <div class="text-[10px] font-mono text-stone-400">
                                    ID: {{ $payout->paymongo_payout_id }} &bull; Test Mode Dispatched
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center sm:flex-col sm:items-end justify-between sm:justify-center gap-2">
                            <span class="font-black text-stone-900 text-base">
                                ₱{{ number_format($payout->gross_amount, 2) }}
                            </span>
                            <!-- Void / Reset for Testing Button -->
                            <form action="{{ route('payouts.void', $payout->id) }}" method="POST" onsubmit="return confirm('Void this test payout and restore ₱{{ number_format($payout->gross_amount, 2) }} back to your available balance for testing?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 text-[10px] font-bold transition flex items-center space-x-1">
                                    <i data-lucide="rotate-ccw" class="w-3 h-3 text-amber-700"></i>
                                    <span>Void for Re-test</span>
                                </button>
                            </form>
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

                <!-- Test Mode Note -->
                <div class="p-3 rounded-2xl bg-stone-50 border border-stone-200 text-[11px] text-stone-600 space-y-1">
                    <div class="font-bold text-stone-800 flex items-center space-x-1">
                        <i data-lucide="info" class="w-3.5 h-3.5 text-emerald-700"></i>
                        <span>How PayMongo Cashout Works:</span>
                    </div>
                    <p class="leading-relaxed">
                        Payouts use PayMongo's direct disbursement API to credit funds straight to your Maya or GCash account. There is no customer checkout page because this is an outbound transfer to your mobile number.
                    </p>
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

    <!-- PayMongo Payout Receipt Modal (Automatically opens on successful cashout) -->
    @if(session('payout_receipt'))
        @php $receipt = session('payout_receipt'); @endphp
        <div id="payoutReceiptModal" class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl border border-stone-200 shadow-2xl max-w-sm w-full p-6 text-center space-y-4 animate-in fade-in zoom-in-95 duration-200">
                <!-- Success Icon -->
                <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-700 mx-auto flex items-center justify-center">
                    <i data-lucide="check" class="w-8 h-8 stroke-[3]"></i>
                </div>

                <div>
                    <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800">
                        PayMongo Disbursements (Test Mode)
                    </span>
                    <h3 class="text-xl font-black text-stone-900 mt-2">Cashout Dispatched!</h3>
                    <p class="text-xs text-stone-500">Transferred directly to your {{ $receipt['payout_method'] }} wallet</p>
                </div>

                <!-- Receipt Card -->
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
                        <span class="text-stone-500 font-medium">Mobile Number:</span>
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
