@extends('layouts.app')

@section('content')
<div class="space-y-4">

    <!-- Top Header & At-a-Glance Stat Pills (Solid Card) -->
    <div class="bg-white rounded-3xl border border-stone-200/90 p-4 sm:p-5 shadow-2xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-stone-900 tracking-tight flex items-center space-x-2">
                <i data-lucide="users" class="w-5 h-5 text-emerald-800"></i>
                <span>Queue Board & Staff Roster</span>
            </h1>
            <p class="text-xs text-stone-600 mt-0.5 font-medium">
                Live rotation queue, attendant assignments, and fair turn order.
            </p>
        </div>

        <!-- High-Density Quick Counters Strip -->
        <div class="flex flex-wrap items-center gap-1.5 text-[11px]">
            <span class="inline-flex items-center px-2.5 py-1 rounded-lg font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                <strong>{{ $availableQueue->count() }}</strong>&nbsp;Ready
            </span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-lg font-bold bg-teal-50 text-teal-800 border border-teal-200 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-teal-500 mr-1.5"></span>
                <strong>{{ $servingInShop->count() }}</strong>&nbsp;In-Shop
            </span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-lg font-bold bg-sky-50 text-sky-800 border border-sky-200 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-sky-500 mr-1.5"></span>
                <strong>{{ $onHomeService->count() }}</strong>&nbsp;Home Svc
            </span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-lg font-bold bg-amber-50 text-amber-800 border border-amber-200 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-amber-500 mr-1.5"></span>
                <strong>{{ $onBreak->count() }}</strong>&nbsp;Break
            </span>
            @if($offDuty->isNotEmpty())
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg font-semibold bg-stone-100 text-stone-600 border border-stone-200 shadow-2xs">
                    <strong>{{ $offDuty->count() }}</strong>&nbsp;Off-Duty
                </span>
            @endif
        </div>
    </div>

    <!-- Main Content: Compact Two-Column Grid (Available Queue vs Active Lanes) -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-4">

        <!-- Left Column: Available Turn Queue - Compact List View (Takes 7 cols on XL) -->
        <div class="xl:col-span-7 bg-white rounded-2xl border border-stone-200/80 shadow-2xs overflow-hidden flex flex-col">
            
            <!-- Section Header -->
            <div class="p-3.5 bg-gradient-to-r from-emerald-50/70 to-teal-50/40 border-b border-emerald-100 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h2 class="text-xs sm:text-sm font-black text-stone-900 tracking-tight flex items-center space-x-1.5">
                        <span>Available Queue (Turn Order)</span>
                    </h2>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                        {{ $availableQueue->count() }}
                    </span>
                </div>
                <span class="text-[10px] text-stone-500 hidden sm:inline">Priority: Top (#1) gets next customer</span>
            </div>

            <!-- Compact Table / Dense List -->
            @if($availableQueue->isEmpty())
                <div class="p-8 text-center text-xs text-stone-500 space-y-1.5">
                    <i data-lucide="clock" class="w-6 h-6 text-stone-300 mx-auto"></i>
                    <div class="font-bold text-stone-700">No staff currently waiting in the queue.</div>
                    <div class="text-[11px] text-stone-400">All staff are currently serving or on break.</div>
                </div>
            @else
                <div class="divide-y divide-stone-100 overflow-x-auto">
                    @foreach($availableQueue as $staff)
                        <div class="p-2.5 sm:px-3.5 hover:bg-emerald-50/30 transition flex items-center justify-between gap-2 text-xs {{ $loop->first ? 'bg-emerald-50/20' : '' }}">
                            
                            <!-- Left: Rank + Name + Role -->
                            <div class="flex items-center space-x-2.5 min-w-0">
                                <!-- Rank Badge -->
                                <span class="w-7 h-7 rounded-lg flex items-center justify-center font-black text-xs flex-shrink-0 {{ $loop->first ? 'bg-emerald-700 text-white shadow-2xs ring-2 ring-emerald-300/60' : 'bg-stone-100 text-stone-700 border border-stone-200' }}" title="Queue Turn #{{ $loop->iteration }}">
                                    #{{ $loop->iteration }}
                                </span>

                                <div class="min-w-0">
                                    <div class="flex items-center space-x-1.5">
                                        <span class="font-black text-stone-900 text-xs sm:text-sm truncate">
                                            {{ $staff->nickname ?? $staff->name }}
                                        </span>
                                        @if($loop->first)
                                            <span class="text-[9px] px-1.5 py-0.2 rounded font-extrabold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200 whitespace-nowrap">
                                                Next Up
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-2 text-[10px] text-stone-500">
                                        <span class="font-semibold text-emerald-800 capitalize">
                                            {{ str_replace('_', ' ', $staff->specialization) }}
                                        </span>
                                        <span>&bull;</span>
                                        <span>Served today: <strong class="text-stone-800">{{ $staff->customers_served_today }}</strong></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Reorder + Quick Dispatch Actions -->
                            <div class="flex items-center space-x-1.5 flex-shrink-0">
                                
                                <!-- Admin Reorder Controls (Move Up/Down) -->
                                @if(Auth::user()->role === 'admin' && $availableQueue->count() > 1)
                                    <div class="flex items-center space-x-0.5 pr-1 border-r border-stone-200">
                                        @if(!$loop->first)
                                            <form action="{{ route('queue.move', ['id' => $staff->id, 'direction' => 'up']) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="p-1 rounded hover:bg-stone-200 text-stone-500 hover:text-stone-800" title="Move Up">
                                                    <i data-lucide="chevron-up" class="w-3.5 h-3.5"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if(!$loop->last)
                                            <form action="{{ route('queue.move', ['id' => $staff->id, 'direction' => 'down']) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="p-1 rounded hover:bg-stone-200 text-stone-500 hover:text-stone-800" title="Move Down">
                                                    <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif

                                <!-- Dispatch Actions: Admin Only; Employees View Only -->
                                @if(Auth::user()->role === 'admin')
                                    <div class="flex items-center space-x-1">
                                        <form action="{{ route('queue.update-status', $staff->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="serving_in_shop">
                                            <button type="submit" class="px-2 py-1 rounded-md bg-teal-50 hover:bg-teal-100 text-teal-800 border border-teal-200/80 font-bold text-[10px] transition inline-flex items-center space-x-1" title="Set to Serving In-Shop">
                                                <i data-lucide="store" class="w-3 h-3 text-teal-700"></i>
                                                <span class="hidden sm:inline">In-Shop</span>
                                            </button>
                                        </form>

                                        <form action="{{ route('queue.update-status', $staff->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="on_home_service">
                                            <button type="submit" class="px-2 py-1 rounded-md bg-sky-50 hover:bg-sky-100 text-sky-800 border border-sky-200/80 font-bold text-[10px] transition inline-flex items-center space-x-1" title="Set to Out on Home Service">
                                                <i data-lucide="home" class="w-3 h-3 text-sky-700"></i>
                                                <span class="hidden sm:inline">Home Svc</span>
                                            </button>
                                        </form>

                                        <form action="{{ route('queue.update-status', $staff->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="on_break">
                                            <button type="submit" class="px-1.5 py-1 rounded-md bg-stone-100 hover:bg-amber-100 text-stone-600 hover:text-amber-800 font-bold text-[10px] transition inline-flex items-center" title="Send to Break">
                                                <i data-lucide="coffee" class="w-3 h-3"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-[10px] px-2.5 py-1 rounded-lg font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Ready
                                    </span>
                                @endif

                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Right Column: Currently Active Staff Lanes (Takes 5 cols on XL) -->
        <div class="xl:col-span-5 space-y-3.5">
            
            <!-- Lane 1: Serving In-Shop (Teal) -->
            <div class="bg-white rounded-2xl border border-stone-200/80 shadow-2xs overflow-hidden">
                <div class="px-3.5 py-2.5 bg-teal-50/60 border-b border-teal-100 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                        <h3 class="text-xs font-black text-stone-900 flex items-center space-x-1.5">
                            <i data-lucide="store" class="w-3.5 h-3.5 text-teal-700"></i>
                            <span>Serving In-Shop</span>
                        </h3>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.2 rounded-full bg-teal-100 text-teal-800 border border-teal-200">
                        {{ $servingInShop->count() }} active
                    </span>
                </div>

                <div class="divide-y divide-stone-100">
                    @forelse($servingInShop as $staff)
                        <div class="p-2.5 px-3.5 flex items-center justify-between gap-2 text-xs hover:bg-stone-50/50">
                            <div class="min-w-0">
                                <span class="font-bold text-stone-900 block truncate">{{ $staff->nickname ?? $staff->name }}</span>
                                <span class="text-[10px] text-teal-700 font-medium capitalize">{{ str_replace('_', ' ', $staff->specialization) }}</span>
                            </div>
                            @if(Auth::user()->role === 'admin')
                                <form action="{{ route('queue.update-status', $staff->id) }}" method="POST" class="flex-shrink-0">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="available">
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-[10px] shadow-2xs transition inline-flex items-center space-x-1">
                                        <i data-lucide="check" class="w-3 h-3"></i>
                                        <span>Finish &bull; Requeue</span>
                                    </button>
                                </form>
                            @else
                                <span class="text-[10px] px-2 py-0.5 rounded-md font-bold bg-teal-100 text-teal-800 border border-teal-200">
                                    Serving
                                </span>
                            @endif
                        </div>
                    @empty
                        <div class="p-3 text-center text-[11px] text-stone-400">
                            No staff currently serving inside the salon.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Lane 2: Out on Home Service (Sky) -->
            <div class="bg-white rounded-2xl border border-stone-200/80 shadow-2xs overflow-hidden">
                <div class="px-3.5 py-2.5 bg-sky-50/60 border-b border-sky-100 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                        <h3 class="text-xs font-black text-stone-900 flex items-center space-x-1.5">
                            <i data-lucide="home" class="w-3.5 h-3.5 text-sky-700"></i>
                            <span>Out on Home Service</span>
                        </h3>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.2 rounded-full bg-sky-100 text-sky-800 border border-sky-200">
                        {{ $onHomeService->count() }} out
                    </span>
                </div>

                <div class="divide-y divide-stone-100">
                    @forelse($onHomeService as $staff)
                        <div class="p-2.5 px-3.5 flex items-center justify-between gap-2 text-xs hover:bg-stone-50/50">
                            <div class="min-w-0">
                                <span class="font-bold text-stone-900 block truncate">{{ $staff->nickname ?? $staff->name }}</span>
                                <span class="text-[10px] text-sky-700 font-medium capitalize">{{ str_replace('_', ' ', $staff->specialization) }}</span>
                            </div>
                            @if(Auth::user()->role === 'admin')
                                <form action="{{ route('queue.update-status', $staff->id) }}" method="POST" class="flex-shrink-0">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="available">
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-[10px] shadow-2xs transition inline-flex items-center space-x-1">
                                        <i data-lucide="check" class="w-3 h-3"></i>
                                        <span>Returned &bull; Requeue</span>
                                    </button>
                                </form>
                            @else
                                <span class="text-[10px] px-2 py-0.5 rounded-md font-bold bg-sky-100 text-sky-800 border border-sky-200">
                                    Home Svc
                                </span>
                            @endif
                        </div>
                    @empty
                        <div class="p-3 text-center text-[11px] text-stone-400">
                            No staff currently on home service.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Lane 3: On Break & Off Duty (Amber/Stone) -->
            <div class="bg-white rounded-2xl border border-stone-200/80 shadow-2xs overflow-hidden">
                <div class="px-3.5 py-2.5 bg-amber-50/50 border-b border-amber-100 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <i data-lucide="coffee" class="w-3.5 h-3.5 text-amber-700"></i>
                        <h3 class="text-xs font-black text-stone-900">On Break / Off-Duty</h3>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.2 rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                        {{ $onBreak->count() + $offDuty->count() }}
                    </span>
                </div>

                <div class="divide-y divide-stone-100">
                    @forelse($onBreak->concat($offDuty) as $staff)
                        <div class="p-2.5 px-3.5 flex items-center justify-between gap-2 text-xs hover:bg-stone-50/50">
                            <div class="min-w-0">
                                <div class="flex items-center space-x-1.5">
                                    <span class="font-bold text-stone-900 truncate">{{ $staff->nickname ?? $staff->name }}</span>
                                    <span class="text-[9px] px-1.5 py-0.2 rounded font-bold uppercase {{ $staff->status === 'on_break' ? 'bg-amber-100 text-amber-800' : 'bg-stone-100 text-stone-600' }}">
                                        {{ $staff->status === 'on_break' ? 'Break' : 'Off-Duty' }}
                                    </span>
                                </div>
                                <span class="text-[10px] text-stone-400 capitalize">{{ str_replace('_', ' ', $staff->specialization) }}</span>
                            </div>
                            @if(Auth::user()->role === 'admin')
                                <form action="{{ route('queue.update-status', $staff->id) }}" method="POST" class="flex-shrink-0">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="available">
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-[10px] shadow-2xs transition inline-flex items-center space-x-1">
                                        <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                                        <span>Set Ready</span>
                                    </button>
                                </form>
                            @else
                                <span class="text-[10px] px-2 py-0.5 rounded-md font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                    {{ $staff->status === 'on_break' ? 'On Break' : 'Off-Duty' }}
                                </span>
                            @endif
                        </div>
                    @empty
                        <div class="p-3 text-center text-[11px] text-stone-400">
                            No staff on break or off-duty.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

    <!-- Full Staff Roster Matrix (Compact Admin Grid - All Staff at a Glance) -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-2xs overflow-hidden">
        <div class="p-3.5 bg-stone-50 border-b border-stone-200/80 flex items-center justify-between">
            <div>
                <h3 class="text-xs sm:text-sm font-black text-stone-900 tracking-tight flex items-center space-x-2">
                    <i data-lucide="list-checks" class="w-4 h-4 text-emerald-800"></i>
                    <span>All Staff Status Matrix (Full Roster)</span>
                </h3>
            </div>
            <span class="text-[11px] text-stone-500 font-semibold">
                {{ $allStaff->count() }} Total Staff Members
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-stone-200 bg-stone-50/50 text-stone-500 text-[10px] font-bold uppercase tracking-wider">
                        <th class="py-2.5 px-3">Queue #</th>
                        <th class="py-2.5 px-3">Attendant</th>
                        <th class="py-2.5 px-3">Specialization</th>
                        <th class="py-2.5 px-3">Current Status</th>
                        <th class="py-2.5 px-3 text-center">Served Today</th>
                        @if(Auth::user()->role === 'admin')
                            <th class="py-2.5 px-3 text-right">Quick Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach($allStaff as $staff)
                        <tr class="hover:bg-stone-50/60 transition">
                            <!-- Queue Rank -->
                            <td class="py-2.5 px-3 font-mono font-bold text-stone-600">
                                @if($staff->status === 'available')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-black bg-emerald-100 text-emerald-900">
                                        #{{ $staff->queue_position }}
                                    </span>
                                @else
                                    <span class="text-stone-300 text-[11px]">—</span>
                                @endif
                            </td>

                            <!-- Attendant Name -->
                            <td class="py-2.5 px-3">
                                <div class="font-bold text-stone-900 text-xs">{{ $staff->nickname ?? $staff->name }}</div>
                                <div class="text-[10px] text-stone-400">{{ $staff->name }}</div>
                            </td>

                            <!-- Specialization -->
                            <td class="py-2.5 px-3">
                                <span class="capitalize text-stone-700 text-[11px] font-semibold">
                                    {{ str_replace('_', ' ', $staff->specialization) }}
                                </span>
                            </td>

                            <!-- Current Status Badge -->
                            <td class="py-2.5 px-3">
                                @php
                                    $badgeStyle = match($staff->status) {
                                        'available' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                        'serving_in_shop' => 'bg-teal-50 text-teal-800 border-teal-200',
                                        'on_home_service' => 'bg-sky-50 text-sky-800 border-sky-200',
                                        'on_break' => 'bg-amber-50 text-amber-800 border-amber-200',
                                        default => 'bg-stone-100 text-stone-600 border-stone-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $badgeStyle }}">
                                    {{ $staff->status_badge['label'] }}
                                </span>
                            </td>

                            <!-- Served Today -->
                            <td class="py-2.5 px-3 text-center font-bold text-stone-800">
                                {{ $staff->customers_served_today }}
                            </td>

                            <!-- Quick Action Form (Admin Only) -->
                            @if(Auth::user()->role === 'admin')
                                <td class="py-2.5 px-3 text-right">
                                    <form action="{{ route('queue.update-status', $staff->id) }}" method="POST" class="inline-flex items-center space-x-1">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()" class="text-[10px] font-bold py-1 px-2.5 rounded-lg border-2 border-emerald-300 bg-white text-emerald-950 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 shadow-2xs hover:border-emerald-400 cursor-pointer transition">
                                            <option value="available" {{ $staff->status === 'available' ? 'selected' : '' }}>Available (Ready)</option>
                                            <option value="serving_in_shop" {{ $staff->status === 'serving_in_shop' ? 'selected' : '' }}>Serving In-Shop</option>
                                            <option value="on_home_service" {{ $staff->status === 'on_home_service' ? 'selected' : '' }}>Home Service</option>
                                            <option value="on_break" {{ $staff->status === 'on_break' ? 'selected' : '' }}>On Break</option>
                                            <option value="off_duty" {{ $staff->status === 'off_duty' ? 'selected' : '' }}>Off Duty</option>
                                        </select>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
