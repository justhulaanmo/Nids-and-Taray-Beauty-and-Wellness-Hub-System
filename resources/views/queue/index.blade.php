@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-stone-900 tracking-tight flex items-center space-x-2">
                <i data-lucide="users" class="w-6 h-6 text-emerald-800"></i>
                <span>Queue Board & Staff Availability</span>
            </h1>
            <p class="text-xs text-stone-500 mt-0.5">
                Fair rotation queue: next staff in line, current service assignments, and availability.
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('transactions.create') }}" class="px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition inline-flex items-center space-x-1.5">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>New Order</span>
            </a>
        </div>
    </div>

    <!-- Live Available Queue -->
    <div class="bg-white rounded-3xl border-2 border-emerald-300/80 p-5 sm:p-6 shadow-xs space-y-4">
        <div class="border-b border-stone-100 pb-3 space-y-2">
            <div class="flex items-center space-x-2.5">
                <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse flex-shrink-0"></span>
                <h2 class="text-base sm:text-lg font-black text-stone-900 leading-snug">
                    Available in Queue (Ready to Serve)
                </h2>
            </div>
            <div class="flex items-center justify-between flex-wrap gap-2 pt-0.5 pl-5.5">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-2xs whitespace-nowrap">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 mr-1.5"></span>
                    {{ $availableQueue->count() }} Ready
                </span>
                <span class="text-[11px] text-stone-400 font-medium">
                    Queue order (Turn 1, 2, 3...)
                </span>
            </div>
        </div>

        @if($availableQueue->isEmpty())
            <div class="p-8 text-center text-xs text-stone-500 bg-stone-50 rounded-2xl space-y-2 border border-stone-200/60">
                <i data-lucide="clock" class="w-8 h-8 text-stone-300 mx-auto"></i>
                <div class="font-bold text-stone-800 text-sm">No staff currently available in the queue.</div>
                <div>All staff are currently serving clients, on home service, or on break.</div>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($availableQueue as $staff)
                    <div class="bg-emerald-50/40 rounded-2xl border border-emerald-200/60 p-4 shadow-2xs hover:border-emerald-400 transition space-y-3">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center space-x-3">
                                <span class="w-8 h-8 rounded-xl bg-emerald-700 text-white flex items-center justify-center font-black text-sm shadow-xs">
                                    #{{ $loop->iteration }}
                                </span>
                                <div>
                                    <h3 class="font-black text-stone-900 text-sm leading-tight">
                                        {{ $staff->nickname ?? $staff->name }}
                                    </h3>
                                    <span class="text-[11px] text-stone-500">{{ $staff->name }}</span>
                                </div>
                            </div>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Next Turn
                            </span>
                        </div>

                        <div class="text-[11px] text-stone-600 flex items-center justify-between pt-1 border-t border-emerald-100">
                            <span class="text-emerald-800 font-bold capitalize flex items-center space-x-1">
                                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-emerald-700"></i>
                                <span>{{ str_replace('_', ' ', $staff->specialization) }}</span>
                            </span>
                            <span class="text-stone-500">Served today: <strong class="text-stone-800">{{ $staff->customers_served_today }}</strong></span>
                        </div>

                        <!-- 1-Tap Status Switch -->
                        <div class="pt-2 border-t border-emerald-100 flex flex-wrap gap-1.5 text-xs">
                            <form action="{{ route('queue.update-status', $staff->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="serving_in_shop">
                                <button type="submit" class="px-2.5 py-1 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-800 border border-teal-200 text-[11px] font-bold transition inline-flex items-center space-x-1">
                                    <i data-lucide="store" class="w-3 h-3"></i>
                                    <span>In-Shop</span>
                                </button>
                            </form>

                            <form action="{{ route('queue.update-status', $staff->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="on_home_service">
                                <button type="submit" class="px-2.5 py-1 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-800 border border-sky-200 text-[11px] font-bold transition inline-flex items-center space-x-1">
                                    <i data-lucide="home" class="w-3 h-3"></i>
                                    <span>Home Svc</span>
                                </button>
                            </form>

                            <form action="{{ route('queue.update-status', $staff->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="on_break">
                                <button type="submit" class="px-2.5 py-1 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-[11px] font-bold transition inline-flex items-center space-x-1">
                                    <i data-lucide="coffee" class="w-3 h-3"></i>
                                    <span>Break</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Active Serving & Home Service Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        <!-- Serving In-Shop (Teal) -->
        <div class="bg-white rounded-3xl border border-teal-200/80 p-5 shadow-xs space-y-3">
            <div class="border-b border-stone-100 pb-2.5 space-y-1.5">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-teal-500 flex-shrink-0"></span>
                    <h2 class="text-sm font-black text-stone-900 flex items-center space-x-1.5">
                        <i data-lucide="store" class="w-4 h-4 text-teal-800"></i>
                        <span>Currently Serving In-Shop</span>
                    </h2>
                </div>
                <div class="flex items-center pl-4.5">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-teal-50 text-teal-800 border border-teal-200 whitespace-nowrap">
                        <span class="w-1.5 h-1.5 rounded-full bg-teal-600 mr-1.5"></span>
                        {{ $servingInShop->count() }} Active
                    </span>
                </div>
            </div>

            @forelse($servingInShop as $staff)
                <div class="p-3 bg-stone-50 rounded-2xl border border-stone-200/60 flex items-center justify-between text-xs">
                    <div>
                        <span class="font-extrabold text-stone-900 text-sm block">{{ $staff->nickname ?? $staff->name }}</span>
                        <span class="text-[10px] text-teal-700 font-semibold">Serving client in salon</span>
                    </div>
                    <form action="{{ route('queue.update-status', $staff->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="available">
                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition inline-flex items-center space-x-1">
                            <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                            <span>Done &bull; Return</span>
                        </button>
                    </form>
                </div>
            @empty
                <div class="p-4 text-center text-xs text-stone-400 bg-stone-50 rounded-xl">
                    No staff currently serving clients in-shop.
                </div>
            @endforelse
        </div>

        <!-- On Home Service (Sky) -->
        <div class="bg-white rounded-3xl border border-sky-200/80 p-5 shadow-xs space-y-3">
            <div class="border-b border-stone-100 pb-2.5 space-y-1.5">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-sky-500 flex-shrink-0"></span>
                    <h2 class="text-sm font-black text-stone-900 flex items-center space-x-1.5">
                        <i data-lucide="home" class="w-4 h-4 text-sky-800"></i>
                        <span>Out on Home Service</span>
                    </h2>
                </div>
                <div class="flex items-center pl-4.5">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-sky-50 text-sky-800 border border-sky-200 whitespace-nowrap">
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-600 mr-1.5"></span>
                        {{ $onHomeService->count() }} Out
                    </span>
                </div>
            </div>

            @forelse($onHomeService as $staff)
                <div class="p-3 bg-stone-50 rounded-2xl border border-stone-200/60 flex items-center justify-between text-xs">
                    <div>
                        <span class="font-extrabold text-stone-900 text-sm block">{{ $staff->nickname ?? $staff->name }}</span>
                        <span class="text-[10px] text-sky-700 font-semibold">On home service appointment</span>
                    </div>
                    <form action="{{ route('queue.update-status', $staff->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="available">
                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition inline-flex items-center space-x-1">
                            <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                            <span>Returned &bull; Return</span>
                        </button>
                    </form>
                </div>
            @empty
                <div class="p-4 text-center text-xs text-stone-400 bg-stone-50 rounded-xl">
                    No staff currently on home service.
                </div>
            @endforelse
        </div>

    </div>

    <!-- On Break Section -->
    @if($onBreak->isNotEmpty())
        <div class="bg-white rounded-3xl border border-amber-200/80 p-5 shadow-xs space-y-3">
            <div class="border-b border-stone-100 pb-2.5 space-y-1.5">
                <div class="flex items-center space-x-2">
                    <h2 class="text-sm font-black text-amber-900 flex items-center space-x-2">
                        <i data-lucide="coffee" class="w-4 h-4 text-amber-700"></i>
                        <span>On Break</span>
                    </h2>
                </div>
                <div class="flex items-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200 whitespace-nowrap">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600 mr-1.5"></span>
                        {{ $onBreak->count() }} on break
                    </span>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                @foreach($onBreak as $staff)
                    <div class="p-3 bg-stone-50 rounded-2xl border border-stone-200/60 flex items-center justify-between text-xs">
                        <span class="font-extrabold text-stone-900">{{ $staff->nickname ?? $staff->name }}</span>
                        <form action="{{ route('queue.update-status', $staff->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="available">
                            <button type="submit" class="px-3 py-1 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs transition inline-flex items-center space-x-1">
                                <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                                <span>Back to Queue</span>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
