@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header & Date Filter Card -->
    <div class="bg-white rounded-3xl border border-stone-200/90 p-5 shadow-2xs flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-stone-900 tracking-tight flex items-center space-x-2.5">
                <span class="w-9 h-9 rounded-xl bg-emerald-100/80 text-emerald-800 flex items-center justify-center">
                    <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                </span>
                <span>Staff Performance & Activity Tracker</span>
            </h1>
            <p class="text-xs text-stone-500 mt-1 pl-11">
                Monitor clients served, active queue roles, and daily commission earned by each employee.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- View Mode Switcher -->
            <div class="inline-flex p-1 bg-stone-100 rounded-2xl border border-stone-200 text-xs font-bold text-stone-600">
                <button type="button" id="btn-view-grid" onclick="switchView('grid')"
                    class="px-3 py-1.5 rounded-xl transition flex items-center space-x-1.5 bg-white text-stone-900 shadow-2xs">
                    <i data-lucide="layout-grid" class="w-3.5 h-3.5"></i>
                    <span>Tile Cards</span>
                </button>
                <button type="button" id="btn-view-table" onclick="switchView('table')"
                    class="px-3 py-1.5 rounded-xl transition flex items-center space-x-1.5 text-stone-500 hover:text-stone-900">
                    <i data-lucide="list" class="w-3.5 h-3.5"></i>
                    <span>Compact Table</span>
                </button>
            </div>

            <!-- Date Filter Form -->
            <form method="GET" action="{{ route('staff-tracker.index') }}" class="flex items-center space-x-2 bg-stone-50 p-1.5 rounded-2xl border border-stone-200">
                <span class="text-xs font-bold text-stone-500 pl-2 flex items-center space-x-1">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-stone-400"></i>
                    <span>Date:</span>
                </span>
                <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()"
                    class="px-2.5 py-1 text-xs font-bold bg-white text-stone-800 rounded-xl border border-stone-200 focus:outline-hidden focus:border-emerald-600">
                <button type="submit" class="px-3.5 py-1 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-xs transition">
                    Filter
                </button>
            </form>
        </div>
    </div>

    <!-- Eye-Friendly Pastel Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
        <!-- Total Clients -->
        <div class="bg-gradient-to-br from-emerald-50/70 via-white to-emerald-50/30 rounded-2xl p-4 border border-emerald-200/80 shadow-2xs flex items-center justify-between">
            <div>
                <div class="text-[10px] font-black text-emerald-800 uppercase tracking-wider">Total Clients Served</div>
                <div class="mt-1 text-2xl sm:text-3xl font-black text-stone-900">
                    {{ $staffList->sum('customers_count') }} <span class="text-xs text-stone-500 font-medium">clients</span>
                </div>
                <div class="mt-0.5 text-[10px] text-stone-400">{{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Total Services Rendered -->
        <div class="bg-gradient-to-br from-teal-50/70 via-white to-teal-50/30 rounded-2xl p-4 border border-teal-200/80 shadow-2xs flex items-center justify-between">
            <div>
                <div class="text-[10px] font-black text-teal-800 uppercase tracking-wider">Total Services Rendered</div>
                <div class="mt-1 text-2xl sm:text-3xl font-black text-teal-900">
                    {{ $staffList->sum('services_count') }} <span class="text-xs text-teal-700/80 font-medium">treatments</span>
                </div>
                <div class="mt-0.5 text-[10px] text-stone-500">Nails, massage, and packages</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-teal-100 text-teal-800 flex items-center justify-center shrink-0">
                <i data-lucide="activity" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Total Staff Commission -->
        <div class="bg-gradient-to-br from-amber-50/70 via-white to-amber-50/30 rounded-2xl p-4 border border-amber-200/80 shadow-2xs flex items-center justify-between">
            <div>
                <div class="text-[10px] font-black text-amber-800 uppercase tracking-wider">Total Staff Commission</div>
                <div class="mt-1 text-2xl sm:text-3xl font-black text-amber-900">
                    ₱{{ number_format($staffList->sum('earned_commission'), 2) }}
                </div>
                <div class="mt-0.5 text-[10px] text-stone-500">Earned commission for {{ \Carbon\Carbon::parse($selectedDate)->format('M d') }}</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                <i data-lucide="briefcase" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Role Filter Filter Pills (Reduces eye fatigue & helps organize tiles) -->
    @php
        $manicuristCount = $staffList->filter(fn($s) => str_contains(strtolower($s->specialization ?? ''), 'mani'))->count();
        $massageCount = $staffList->filter(fn($s) => str_contains(strtolower($s->specialization ?? ''), 'mass'))->count();
        $allAroundCount = $staffList->filter(fn($s) => !str_contains(strtolower($s->specialization ?? ''), 'mani') && !str_contains(strtolower($s->specialization ?? ''), 'mass'))->count();
    @endphp
    <div class="flex flex-wrap items-center gap-2 pt-1 pb-1">
        <span class="text-xs font-bold text-stone-500 mr-1 flex items-center space-x-1">
            <i data-lucide="filter" class="w-3.5 h-3.5"></i>
            <span>Filter Role:</span>
        </span>

        <button type="button" onclick="filterRole('all')" id="role-pill-all"
            class="role-pill px-3 py-1.5 rounded-xl text-xs font-extrabold bg-stone-900 text-white shadow-2xs transition flex items-center space-x-1.5">
            <span>All Staff</span>
            <span class="px-1.5 py-0.2 rounded-full bg-stone-700 text-stone-200 text-[10px]">{{ $staffList->count() }}</span>
        </button>

        <button type="button" onclick="filterRole('manicurist')" id="role-pill-manicurist"
            class="role-pill px-3 py-1.5 rounded-xl text-xs font-extrabold bg-white text-rose-800 border border-rose-200 hover:bg-rose-50 shadow-2xs transition flex items-center space-x-1.5">
            <span class="w-2 h-2 rounded-full bg-rose-400"></span>
            <span>Manicurists</span>
            <span class="px-1.5 py-0.2 rounded-full bg-rose-100 text-rose-800 text-[10px] font-bold">{{ $manicuristCount }}</span>
        </button>

        <button type="button" onclick="filterRole('massage')" id="role-pill-massage"
            class="role-pill px-3 py-1.5 rounded-xl text-xs font-extrabold bg-white text-sky-800 border border-sky-200 hover:bg-sky-50 shadow-2xs transition flex items-center space-x-1.5">
            <span class="w-2 h-2 rounded-full bg-sky-400"></span>
            <span>Massage Therapists</span>
            <span class="px-1.5 py-0.2 rounded-full bg-sky-100 text-sky-800 text-[10px] font-bold">{{ $massageCount }}</span>
        </button>

        <button type="button" onclick="filterRole('all_around')" id="role-pill-all_around"
            class="role-pill px-3 py-1.5 rounded-xl text-xs font-extrabold bg-white text-emerald-800 border border-emerald-200 hover:bg-emerald-50 shadow-2xs transition flex items-center space-x-1.5">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>All-Around</span>
            <span class="px-1.5 py-0.2 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">{{ $allAroundCount }}</span>
        </button>
    </div>

    <!-- View Mode 1: Color-Coded & Soothing Tile Cards -->
    <div id="grid-view-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($staffList as $staff)
            @php
                $specKey = strtolower($staff->specialization ?? '');
                if (str_contains($specKey, 'mani')) {
                    $roleGroup = 'manicurist';
                    $theme = [
                        'card_border' => 'border-rose-200/90 hover:border-rose-300',
                        'accent_bar' => 'border-l-4 border-l-rose-400',
                        'card_bg' => 'bg-gradient-to-b from-rose-50/40 via-white to-white',
                        'avatar' => 'bg-gradient-to-tr from-rose-500 to-pink-500 text-white',
                        'role_pill' => 'bg-rose-50 text-rose-800 border-rose-200',
                        'badge_dot' => 'bg-rose-400',
                    ];
                } elseif (str_contains($specKey, 'mass')) {
                    $roleGroup = 'massage';
                    $theme = [
                        'card_border' => 'border-sky-200/90 hover:border-sky-300',
                        'accent_bar' => 'border-l-4 border-l-sky-400',
                        'card_bg' => 'bg-gradient-to-b from-sky-50/40 via-white to-white',
                        'avatar' => 'bg-gradient-to-tr from-sky-500 to-teal-600 text-white',
                        'role_pill' => 'bg-sky-50 text-sky-800 border-sky-200',
                        'badge_dot' => 'bg-sky-400',
                    ];
                } else {
                    $roleGroup = 'all_around';
                    $theme = [
                        'card_border' => 'border-emerald-200/90 hover:border-emerald-300',
                        'accent_bar' => 'border-l-4 border-l-emerald-500',
                        'card_bg' => 'bg-gradient-to-b from-emerald-50/40 via-white to-white',
                        'avatar' => 'bg-gradient-to-tr from-emerald-600 to-teal-600 text-white',
                        'role_pill' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                        'badge_dot' => 'bg-emerald-500',
                    ];
                }
            @endphp

            <div data-role="{{ $roleGroup }}"
                class="staff-card {{ $theme['card_bg'] }} {{ $theme['card_border'] }} {{ $theme['accent_bar'] }} rounded-3xl border p-5 shadow-2xs hover:shadow-sm transition duration-150 flex flex-col justify-between space-y-4">
                
                <div class="space-y-3.5">
                    <!-- Staff Header: Distinct Avatar & Role Color -->
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-2xl {{ $theme['avatar'] }} flex items-center justify-center font-black text-lg shadow-2xs shrink-0">
                                {{ substr($staff->nickname ?? $staff->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="font-black text-stone-900 text-base leading-tight">
                                    {{ $staff->nickname ?? $staff->name }}
                                </h3>
                                <p class="text-[11px] text-stone-500 font-medium">{{ $staff->name }}</p>
                                
                                <span class="inline-flex items-center space-x-1.5 px-2 py-0.5 mt-1 rounded-md text-[10px] font-extrabold uppercase tracking-wider border {{ $theme['role_pill'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $theme['badge_dot'] }}"></span>
                                    <span>{{ str_replace('_', ' ', $staff->specialization) }}</span>
                                </span>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <span class="text-[10px] px-2.5 py-1 rounded-full font-bold border shrink-0 {{ $staff->status_badge['class'] }}">
                            {{ $staff->status_badge['label'] }}
                        </span>
                    </div>

                    <!-- Soft Toned Metrics Breakdown (Clients + Commission) -->
                    <div class="grid grid-cols-2 gap-2 text-center pt-1">
                        <!-- Clients Served Block -->
                        <div class="p-2.5 bg-stone-50/90 rounded-2xl border border-stone-200/80 shadow-2xs">
                            <span class="block text-2xl font-black text-stone-900 leading-tight">{{ $staff->customers_count }}</span>
                            <span class="text-[9px] text-stone-500 font-extrabold uppercase tracking-wider">Clients Served</span>
                        </div>

                        <!-- Commission Block -->
                        <div class="p-2.5 bg-emerald-50/90 rounded-2xl border border-emerald-200/80 shadow-2xs">
                            <span class="block text-2xl font-black text-emerald-800 leading-tight">₱{{ number_format($staff->earned_commission, 0) }}</span>
                            <span class="text-[9px] text-emerald-700 font-extrabold uppercase tracking-wider">Commission</span>
                        </div>
                    </div>

                    <!-- Service Activity Details or Clean Info Banner -->
                    @if($staff->transactionItems->isNotEmpty())
                        <div class="bg-white/80 rounded-2xl p-3 border border-stone-200/70 space-y-1.5 text-xs shadow-2xs">
                            <div class="flex items-center justify-between text-[10px] font-bold text-stone-500 uppercase tracking-wider border-b border-stone-100 pb-1">
                                <span>Recent Treatments</span>
                                <span>Earned</span>
                            </div>
                            @foreach($staff->transactionItems->take(3) as $item)
                                <div class="text-[11px] text-stone-700 flex justify-between items-center pt-0.5">
                                    <span class="truncate font-medium pr-2">• {{ $item->service->name ?? 'Treatment' }}</span>
                                    <span class="font-bold text-emerald-800 shrink-0">₱{{ number_format($item->staff_commission_amount, 2) }}</span>
                                </div>
                            @endforeach
                            @if($staff->transactionItems->count() > 3)
                                <p class="text-[10px] text-stone-400 text-center pt-1 font-semibold">
                                    +{{ $staff->transactionItems->count() - 3 }} more services rendered
                                </p>
                            @endif
                        </div>
                    @else
                        <div class="text-[11px] text-stone-500 bg-stone-50/80 rounded-2xl py-3 px-3 border border-stone-200/60 flex items-center justify-center space-x-1.5 font-medium">
                            <i data-lucide="clock" class="w-3.5 h-3.5 text-stone-400"></i>
                            <span>No services recorded for this date</span>
                        </div>
                    @endif
                </div>

                <!-- Footer Action Button -->
                <div class="pt-2 border-t border-stone-100">
                    <a href="{{ route('staff-tracker.show', ['id' => $staff->id, 'date' => $selectedDate]) }}"
                        class="w-full py-2.5 px-3.5 bg-white hover:bg-emerald-700 hover:text-white hover:border-emerald-700 text-stone-700 border border-stone-200 rounded-xl font-bold text-xs flex items-center justify-center space-x-2 transition shadow-2xs group">
                        <span>Manage Role & Activity</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform"></i>
                    </a>
                </div>

            </div>
        @endforeach
    </div>

    <!-- View Mode 2: Compact Roster Table (Alternative view if tiles feel repetitive) -->
    <div id="table-view-container" class="hidden bg-white rounded-3xl border border-stone-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-stone-50/80 border-b border-stone-200/80 text-[11px] font-black uppercase tracking-wider text-stone-500">
                        <th class="py-3 px-4">Staff Member</th>
                        <th class="py-3 px-4">Specialization</th>
                        <th class="py-3 px-4">Duty Status</th>
                        <th class="py-3 px-4 text-center">Clients Served</th>
                        <th class="py-3 px-4 text-center">Services Rendered</th>
                        <th class="py-3 px-4 text-right">Commission Earned</th>
                        <th class="py-3 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-xs font-semibold text-stone-800">
                    @foreach($staffList as $staff)
                        @php
                            $specKey = strtolower($staff->specialization ?? '');
                            if (str_contains($specKey, 'mani')) {
                                $roleGroup = 'manicurist';
                                $borderLeft = 'border-l-4 border-l-rose-400';
                                $rolePill = 'bg-rose-50 text-rose-800 border-rose-200';
                                $avatarColor = 'bg-rose-100 text-rose-800';
                            } elseif (str_contains($specKey, 'mass')) {
                                $roleGroup = 'massage';
                                $borderLeft = 'border-l-4 border-l-sky-400';
                                $rolePill = 'bg-sky-50 text-sky-800 border-sky-200';
                                $avatarColor = 'bg-sky-100 text-sky-800';
                            } else {
                                $roleGroup = 'all_around';
                                $borderLeft = 'border-l-4 border-l-emerald-500';
                                $rolePill = 'bg-emerald-50 text-emerald-800 border-emerald-200';
                                $avatarColor = 'bg-emerald-100 text-emerald-800';
                            }
                        @endphp
                        <tr data-role="{{ $roleGroup }}" class="staff-row hover:bg-stone-50/70 transition {{ $borderLeft }}">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-xl {{ $avatarColor }} flex items-center justify-center font-black text-sm shrink-0">
                                        {{ substr($staff->nickname ?? $staff->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-extrabold text-stone-900">{{ $staff->nickname ?? $staff->name }}</div>
                                        <div class="text-[11px] text-stone-500 font-normal">{{ $staff->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wider border {{ $rolePill }}">
                                    {{ str_replace('_', ' ', $staff->specialization) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="text-[10px] px-2.5 py-0.5 rounded-full font-bold border {{ $staff->status_badge['class'] }}">
                                    {{ $staff->status_badge['label'] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center font-black text-stone-900 text-sm">
                                {{ $staff->customers_count }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-stone-600">
                                {{ $staff->services_count }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-emerald-700 text-sm">
                                ₱{{ number_format($staff->earned_commission, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('staff-tracker.show', ['id' => $staff->id, 'date' => $selectedDate]) }}"
                                    class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-xl bg-stone-100 hover:bg-emerald-700 hover:text-white text-stone-700 font-bold text-xs transition border border-stone-200">
                                    <span>Manage</span>
                                    <i data-lucide="arrow-right" class="w-3 h-3"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
    // View Switcher (Grid vs Table)
    function switchView(mode) {
        const gridView = document.getElementById('grid-view-container');
        const tableView = document.getElementById('table-view-container');
        const btnGrid = document.getElementById('btn-view-grid');
        const btnTable = document.getElementById('btn-view-table');

        if (mode === 'grid') {
            gridView.classList.remove('hidden');
            tableView.classList.add('hidden');
            btnGrid.classList.add('bg-white', 'text-stone-900', 'shadow-2xs');
            btnGrid.classList.remove('text-stone-500');
            btnTable.classList.remove('bg-white', 'text-stone-900', 'shadow-2xs');
            btnTable.classList.add('text-stone-500');
        } else {
            gridView.classList.add('hidden');
            tableView.classList.remove('hidden');
            btnTable.classList.add('bg-white', 'text-stone-900', 'shadow-2xs');
            btnTable.classList.remove('text-stone-500');
            btnGrid.classList.remove('bg-white', 'text-stone-900', 'shadow-2xs');
            btnGrid.classList.add('text-stone-500');
        }
    }

    // Role Filter Pills
    function filterRole(role) {
        // Update active tab buttons
        document.querySelectorAll('.role-pill').forEach(btn => {
            btn.classList.remove('bg-stone-900', 'text-white');
            btn.classList.add('bg-white');
        });

        const activePill = document.getElementById('role-pill-' + role);
        if (activePill) {
            activePill.classList.remove('bg-white');
            activePill.classList.add('bg-stone-900', 'text-white');
        }

        // Filter Grid Cards
        document.querySelectorAll('.staff-card').forEach(card => {
            if (role === 'all' || card.getAttribute('data-role') === role) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });

        // Filter Table Rows
        document.querySelectorAll('.staff-row').forEach(row => {
            if (role === 'all' || row.getAttribute('data-role') === role) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush
@endsection
