@extends('layouts.app')

@section('content')
<div class="space-y-5">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-stone-900 tracking-tight flex items-center space-x-2">
                <i data-lucide="receipt" class="w-6 h-6 text-emerald-800"></i>
                @if($isStaff && $staffProfile)
                    <span>My Services & Orders</span>
                @else
                    <span>Order History & Transactions</span>
                @endif
            </h1>
            <p class="text-xs text-stone-500 mt-0.5">
                @if($isStaff && $staffProfile)
                    Showing client visits and treatments serviced by <strong class="text-emerald-900 font-extrabold">{{ $staffProfile->nickname ?? $staffProfile->name }}</strong>.
                @else
                    Overview of client visits, assigned staff, services, and payment status across the salon.
                @endif
            </p>
        </div>
        <a href="{{ route('transactions.create') }}" class="px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition inline-flex items-center space-x-1.5 self-start sm:self-auto">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>New Order</span>
        </a>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white rounded-3xl border border-stone-200/90 p-4 shadow-xs">
        <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-{{ !$isStaff ? '5' : '4' }} gap-3">
            <div>
                <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Date</label>
                <input type="date" name="date" value="{{ request('date') }}"
                    class="w-full px-3 py-2 text-xs rounded-xl bg-stone-50 border border-stone-200 text-stone-900 font-medium focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-hidden">
            </div>

            @if(!$isStaff)
            <div>
                <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Attendant</label>
                <select name="staff_id" class="w-full px-3 py-2 text-xs rounded-xl bg-stone-50 border border-stone-200 text-stone-900 font-medium focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-hidden">
                    <option value="">All Attendants</option>
                    @foreach($allStaff as $s)
                        <option value="{{ $s->id }}" {{ request('staff_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->nickname ?? $s->name }} ({{ ucfirst(str_replace('_', ' ', $s->specialization)) }})
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Service Type</label>
                <select name="service_type" class="w-full px-3 py-2 text-xs rounded-xl bg-stone-50 border border-stone-200 text-stone-900 font-medium focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-hidden">
                    <option value="">All Types</option>
                    <option value="in_shop" {{ request('service_type') === 'in_shop' ? 'selected' : '' }}>In-Shop</option>
                    <option value="home_service" {{ request('service_type') === 'home_service' ? 'selected' : '' }}>Home Service</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Payment Status</label>
                <select name="payment_status" class="w-full px-3 py-2 text-xs rounded-xl bg-stone-50 border border-stone-200 text-stone-900 font-medium focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-hidden">
                    <option value="">All Statuses</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition">
                    Filter
                </button>
                <a href="{{ route('transactions.index') }}" class="py-2 px-3 rounded-xl border border-stone-200 hover:bg-stone-100 text-stone-600 text-xs font-semibold transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Transactions Cards / Table -->
    <div class="bg-white rounded-3xl border border-stone-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-stone-50/80 text-stone-500 font-bold uppercase tracking-wider text-[11px] border-b border-stone-100">
                        <th class="py-3 px-4">Tracking #</th>
                        <th class="py-3 px-4">Client</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4">Services & Staff</th>
                        <th class="py-3 px-4 text-right">
                            @if($isStaff)
                                Total / Your Cut
                            @else
                                Total Amount
                            @endif
                        </th>
                        <th class="py-3 px-4 text-center">Method</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($transactions as $tx)
                        @php
                            $myItems = ($isStaff && $staffProfile) ? $tx->items->where('staff_id', $staffProfile->id) : collect();
                            $myCut = 0;
                            if ($isStaff && $staffProfile) {
                                foreach ($myItems as $mItem) {
                                    $isQR = in_array($tx->payment_method, ['paymongo_qr', 'paymongo_link']);
                                    $pFee = $isQR ? round((float) $mItem->price * 0.025, 2) : 0;
                                    $netBase = max(0, (float) $mItem->price - $pFee);
                                    $rate = (float) ($mItem->commission_rate ?: $staffProfile->commission_rate);
                                    $myCut += round(($netBase * $rate) / 100, 2);
                                }
                            }
                        @endphp
                        <tr class="hover:bg-emerald-50/40 transition">
                            <td class="py-3 px-4 font-mono font-bold text-stone-900">
                                {{ $tx->tracking_number }}
                                <span class="block text-[10px] text-stone-400 font-sans font-normal mt-0.5">
                                    {{ $tx->created_at->format('M d, Y • h:i A') }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-extrabold text-stone-900 text-sm block">{{ $tx->customer_name }}</span>
                                @if($tx->customer_phone)
                                    <span class="text-[10px] text-stone-400">{{ $tx->customer_phone }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($tx->service_type === 'home_service')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200 whitespace-nowrap inline-flex items-center space-x-1">
                                        <i data-lucide="home" class="w-3 h-3"></i>
                                        <span>Home Service</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200 whitespace-nowrap inline-flex items-center space-x-1">
                                        <i data-lucide="store" class="w-3 h-3"></i>
                                        <span>In-Shop</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 space-y-1">
                                @foreach($tx->items as $item)
                                    @php
                                        $isMine = ($isStaff && $staffProfile && $item->staff_id === $staffProfile->id);
                                    @endphp
                                    <div class="text-[11px] flex items-center space-x-1.5 {{ $isMine ? 'font-bold text-stone-900' : 'text-stone-500' }}">
                                        <span>&bull; {{ $item->service->name ?? 'Service' }}</span>
                                        @if($isMine)
                                            <span class="px-1.5 py-0.2 rounded-md bg-emerald-100 text-emerald-800 text-[9px] font-black">You</span>
                                        @else
                                            <span class="text-stone-400 font-medium">({{ $item->staff->nickname ?? 'Staff' }})</span>
                                        @endif
                                    </div>
                                @endforeach
                            </td>
                            <td class="py-3 px-4 text-right">
                                <span class="font-black text-stone-900 text-sm block">
                                    ₱{{ number_format($tx->total_amount, 2) }}
                                </span>
                                @if($isStaff && $staffProfile)
                                    <span class="text-[10px] font-black text-emerald-700 inline-block">
                                        Your Cut: +₱{{ number_format($myCut, 2) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($tx->payment_method === 'paymongo_qr')
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        <i data-lucide="qr-code" class="w-3 h-3"></i>
                                        <span>PayMongo QR</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        <i data-lucide="banknote" class="w-3 h-3"></i>
                                        <span>Cash</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($tx->payment_status === 'paid')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        PAID
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200">
                                        PENDING
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('transactions.show', $tx->id) }}" class="px-3 py-1.5 rounded-xl bg-stone-50 hover:bg-emerald-50 text-stone-700 hover:text-emerald-800 border border-stone-200 font-bold text-xs transition inline-flex items-center space-x-1">
                                    <span>Details</span>
                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-xs text-stone-400">
                                @if($isStaff)
                                    No services or client visits assigned to you yet for this criteria.
                                @else
                                    No transactions found matching your filters.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="p-3 border-t border-stone-100">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
