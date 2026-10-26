@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header & Date Picker -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-stone-900 tracking-tight flex items-center space-x-2">
                <i data-lucide="bar-chart-3" class="w-6 h-6 text-emerald-800"></i>
                <span>Staff Performance & Activity Tracker</span>
            </h1>
            <p class="text-xs text-stone-500 mt-0.5">
                Monitor clients served and total commission earned by each staff member.
            </p>
        </div>

        <!-- Date Filter -->
        <form method="GET" action="{{ route('staff-tracker.index') }}" class="flex items-center space-x-2 bg-white p-1.5 rounded-2xl border border-stone-200 shadow-2xs">
            <span class="text-xs font-bold text-stone-500 pl-2">Date:</span>
            <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()"
                class="px-2.5 py-1 text-xs font-bold bg-stone-50 text-stone-800 rounded-xl border border-stone-200">
            <button type="submit" class="px-3.5 py-1 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-xs transition">
                Filter
            </button>
        </form>
    </div>

    <!-- Active Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-white rounded-2xl p-4 border border-emerald-900/10 shadow-xs">
            <div class="text-[10px] font-black text-stone-500 uppercase tracking-wider">Total Clients Served</div>
            <div class="mt-1 text-3xl font-black text-stone-900">
                {{ $staffList->sum('customers_count') }} <span class="text-xs text-stone-500 font-medium">clients</span>
            </div>
            <div class="mt-0.5 text-[10px] text-stone-400">{{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}</div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-teal-200 bg-gradient-to-br from-white to-teal-50/50 shadow-xs">
            <div class="text-[10px] font-black text-teal-700 uppercase tracking-wider">Total Services Rendered</div>
            <div class="mt-1 text-3xl font-black text-teal-800">
                {{ $staffList->sum('services_count') }} <span class="text-xs text-teal-700/80 font-medium">treatments</span>
            </div>
            <div class="mt-0.5 text-[10px] text-stone-500">Nails, massage, and packages</div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-emerald-200 bg-gradient-to-br from-white to-emerald-50/50 shadow-xs">
            <div class="text-[10px] font-black text-emerald-700 uppercase tracking-wider">Total Staff Commission</div>
            <div class="mt-1 text-3xl font-black text-emerald-800">
                ₱{{ number_format($staffList->sum('earned_commission'), 2) }}
            </div>
            <div class="mt-0.5 text-[10px] text-stone-500">Total commission earned by staff</div>
        </div>
    </div>

    <!-- Staff Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($staffList as $staff)
            <div class="bg-white rounded-3xl border border-emerald-900/10 p-5 shadow-xs hover:border-emerald-300 hover:shadow-xs transition flex flex-col justify-between space-y-4">
                
                <div class="space-y-3">
                    <!-- Staff Header -->
                    <div class="flex items-start justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center font-black text-base shadow-xs">
                                {{ substr($staff->nickname ?? $staff->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="font-black text-stone-900 text-base leading-tight">
                                    {{ $staff->nickname ?? $staff->name }}
                                </h3>
                                <p class="text-[11px] text-stone-500">{{ $staff->name }}</p>
                                <span class="inline-flex items-center space-x-1 text-[11px] font-bold text-emerald-800 capitalize">
                                    <i data-lucide="briefcase" class="w-3 h-3 text-emerald-700"></i>
                                    <span>{{ str_replace('_', ' ', $staff->specialization) }}</span>
                                </span>
                            </div>
                        </div>

                        <span class="text-[10px] px-2.5 py-0.5 rounded-full font-bold border {{ $staff->status_badge['class'] }}">
                            {{ $staff->status_badge['label'] }}
                        </span>
                    </div>

                    <!-- Metrics Breakdown -->
                    <div class="bg-stone-50 rounded-2xl p-3 grid grid-cols-2 gap-2 text-center border border-stone-200/60">
                        <div class="p-2 bg-white rounded-xl shadow-2xs">
                            <span class="block text-2xl font-black text-stone-900">{{ $staff->customers_count }}</span>
                            <span class="text-[9px] text-stone-500 font-bold uppercase tracking-wider">Clients Served</span>
                        </div>
                        <div class="p-2 bg-white rounded-xl shadow-2xs">
                            <span class="block text-2xl font-black text-emerald-700">₱{{ number_format($staff->earned_commission, 0) }}</span>
                            <span class="text-[9px] text-stone-500 font-bold uppercase tracking-wider">Commission</span>
                        </div>
                    </div>

                    @if($staff->transactionItems->isNotEmpty())
                        <div class="text-xs space-y-1">
                            <span class="font-bold text-stone-500 text-[10px] uppercase tracking-wider block">Recent Services:</span>
                            @foreach($staff->transactionItems->take(3) as $item)
                                <div class="text-[11px] text-stone-700 flex justify-between">
                                    <span class="truncate">&bull; {{ $item->service->name ?? 'Service' }}</span>
                                    <span class="font-bold text-emerald-800">₱{{ number_format($item->staff_commission_amount, 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-xs text-stone-400 italic text-center py-2">
                            No services recorded for this date.
                        </div>
                    @endif
                </div>

                <!-- Footer Link -->
                <div class="pt-2 border-t border-stone-100">
                    <a href="{{ route('staff-tracker.show', ['id' => $staff->id, 'date' => $selectedDate]) }}" class="w-full py-2 px-3 bg-stone-50 hover:bg-emerald-700 hover:text-white text-stone-700 border border-stone-200 font-bold text-xs rounded-xl flex items-center justify-center space-x-1.5 transition">
                        <span>Manage Role & Activity</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

            </div>
        @endforeach
    </div>

</div>
@endsection
