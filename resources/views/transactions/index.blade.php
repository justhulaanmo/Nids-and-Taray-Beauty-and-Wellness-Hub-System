@extends('layouts.app')

@section('content')
<div class="space-y-5">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-stone-900 tracking-tight flex items-center space-x-2">
                <i data-lucide="receipt" class="w-6 h-6 text-emerald-800"></i>
                <span>Order History & Transactions</span>
            </h1>
            <p class="text-xs text-stone-500 mt-0.5">
                Overview of client visits, assigned staff, services, and payment status.
            </p>
        </div>
        <a href="{{ route('transactions.create') }}" class="px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition inline-flex items-center space-x-1.5 self-start sm:self-auto">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>New Order</span>
        </a>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white rounded-3xl border border-emerald-900/10 p-4 shadow-xs">
        <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Date</label>
                <input type="date" name="date" value="{{ request('date') }}"
                    class="w-full px-3 py-2 text-xs rounded-xl bg-stone-50 border border-stone-200 text-stone-900 font-medium focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-hidden">
            </div>

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
    <div class="bg-white rounded-3xl border border-emerald-900/10 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-stone-50/80 text-stone-500 font-bold uppercase tracking-wider text-[11px] border-b border-stone-100">
                        <th class="py-3 px-4">Tracking #</th>
                        <th class="py-3 px-4">Client</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4">Services & Staff</th>
                        <th class="py-3 px-4 text-right">Total Amount</th>
                        <th class="py-3 px-4 text-center">Method</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-emerald-50/40 transition">
                            <td class="py-3 px-4 font-mono font-bold text-stone-900">
                                {{ $tx->tracking_number }}
                                <span class="block text-[10px] text-stone-400 font-sans font-normal">{{ $tx->created_at->format('M d &bull; h:i A') }}</span>
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
                            <td class="py-3 px-4 space-y-0.5">
                                @foreach($tx->items as $item)
                                    <div class="text-[11px] text-stone-600">
                                        &bull; {{ $item->service->name ?? 'Service' }}
                                        <span class="text-emerald-700 font-bold">({{ $item->staff->nickname ?? 'Staff' }})</span>
                                    </div>
                                @endforeach
                            </td>
                            <td class="py-3 px-4 text-right font-black text-emerald-800 text-sm">
                                ₱{{ number_format($tx->total_amount, 2) }}
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
                                No transactions found matching your filters.
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
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-xs text-stone-400">
                                No transactions found matching your filters.
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
