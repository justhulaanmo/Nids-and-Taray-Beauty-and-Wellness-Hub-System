@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('staff-tracker.index', ['date' => $selectedDate]) }}" class="text-xs font-bold text-stone-600 hover:text-stone-900 bg-white border border-stone-200 px-3.5 py-2 rounded-xl transition shadow-xs inline-flex items-center space-x-1.5">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Back to Staff Tracker</span>
        </a>

        <span class="text-xs font-bold text-stone-500">
            Date: {{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}
        </span>
    </div>

    <!-- Staff Profile Card -->
    <div class="bg-white rounded-2xl border border-emerald-900/10 p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center font-black text-2xl shadow-xs">
                {{ substr($staff->nickname ?? $staff->name, 0, 1) }}
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-stone-900">{{ $staff->nickname ?? $staff->name }}</h1>
                <p class="text-xs text-stone-500">{{ $staff->name }} &bull; {{ $staff->phone ?? 'No contact number' }}</p>
                <div class="flex items-center space-x-2 mt-1">
                    <span class="inline-flex items-center space-x-1 text-xs font-bold text-emerald-800 capitalize">
                        <i data-lucide="briefcase" class="w-3 h-3 text-emerald-700"></i>
                        <span>{{ str_replace('_', ' ', $staff->specialization) }}</span>
                    </span>
                    <span class="text-stone-300">&bull;</span>
                    <span class="text-xs font-semibold text-stone-600">
                        Commission Rate: <strong>{{ $staff->commission_rate }}%</strong>
                    </span>
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-3 text-right">
            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl">
                <span class="block text-xs font-bold text-emerald-800 uppercase">Total Commission</span>
                <span class="block text-2xl font-black text-emerald-700">
                    ₱{{ number_format($items->where('transaction.payment_status', 'paid')->sum('staff_commission_amount'), 2) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Service Records Table -->
    <div class="bg-white rounded-2xl border border-emerald-900/10 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-stone-100 flex items-center justify-between">
            <h2 class="text-base font-extrabold text-stone-900 flex items-center space-x-2">
                <i data-lucide="calendar-check" class="w-4 h-4 text-emerald-800"></i>
                <span>Service Activity Log</span>
            </h2>
            <span class="text-xs font-bold px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full">
                {{ $items->count() }} Services Rendered
            </span>
        </div>

        @if($items->isEmpty())
            <div class="p-8 text-center text-sm text-stone-500 bg-stone-50">
                No services recorded for {{ $staff->nickname ?? $staff->name }} on the selected date.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-stone-50 text-stone-600 font-bold uppercase border-b border-stone-200">
                            <th class="py-3 px-4">Time</th>
                            <th class="py-3 px-4">Tracking #</th>
                            <th class="py-3 px-4">Client Name</th>
                            <th class="py-3 px-4">Service</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4 text-right">Price</th>
                            <th class="py-3 px-4 text-right">Commission</th>
                            <th class="py-3 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach($items as $item)
                            <tr class="hover:bg-emerald-50/40 transition">
                                <td class="py-3.5 px-4 text-stone-600">
                                    {{ $item->created_at->format('h:i A') }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-stone-900">
                                    <a href="{{ route('transactions.show', $item->transaction_id) }}" class="text-emerald-700 hover:text-emerald-800 hover:underline">
                                        {{ $item->transaction->tracking_number ?? 'N/A' }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-stone-900">
                                    {{ $item->transaction->customer_name ?? 'Walk-in' }}
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-stone-800">
                                    {{ $item->service->name ?? 'N/A' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($item->transaction->service_type === 'home_service')
                                        <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 text-sky-800 border border-sky-200">
                                            <i data-lucide="home" class="w-3 h-3"></i>
                                            <span>Home Service</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 text-teal-800 border border-teal-200">
                                            <i data-lucide="store" class="w-3 h-3"></i>
                                            <span>In-Shop</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right text-stone-900 font-semibold">
                                    ₱{{ number_format($item->price, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-emerald-700 text-sm">
                                    ₱{{ number_format($item->staff_commission_amount, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($item->transaction->payment_status === 'paid')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            PAID
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            PENDING
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
