@extends('layouts.app')

@section('content')
<div class="space-y-5">

    <!-- Clean Header Banner (Light Theme) -->
    <div class="bg-white rounded-3xl border border-stone-200/80 p-5 sm:p-6 shadow-2xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 text-[11px] font-bold uppercase tracking-wider mb-1.5 border border-emerald-200">
                <i data-lucide="book-open" class="w-3.5 h-3.5 text-emerald-700"></i>
                <span>Service Menu & Price Catalog</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-stone-900 tracking-tight">
                Nids & Taray Service Menu
            </h1>
            <p class="text-xs text-stone-500 mt-0.5">
                Browse all available nail care, massage, and wellness treatments.
            </p>
        </div>

        <div class="flex items-center space-x-2">
            @if(Auth::user()->role === 'admin')
                <a href="{{ route('services.manage') }}" class="px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 font-bold text-xs shadow-2xs transition inline-flex items-center space-x-1.5 border border-amber-200">
                    <i data-lucide="sliders" class="w-3.5 h-3.5 text-amber-700"></i>
                    <span>Manage Prices</span>
                </a>
            @endif
            <a href="{{ route('transactions.create') }}" class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition inline-flex items-center space-x-1.5">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>New Booking</span>
            </a>
        </div>
    </div>

    @php
        $defaultFilter = 'all';
        if (isset($myProfile) && $myProfile->specialization === 'manicurist') {
            $defaultFilter = 'nails-and-toes';
        } elseif (isset($myProfile) && $myProfile->specialization === 'massage_therapist') {
            $defaultFilter = 'massage-and-wellness';
        }
    @endphp

    <!-- Category Filter Tabs -->
    <div class="flex flex-wrap items-center gap-2">
        <button type="button" onclick="filterMenu('all')" id="menu-tab-all"
            class="menu-filter-btn cursor-pointer px-3.5 py-1.5 rounded-xl text-xs font-black transition shadow-2xs flex items-center space-x-1.5 {{ $defaultFilter === 'all' ? 'bg-stone-900 text-white' : 'bg-white text-stone-700 border border-stone-200 hover:bg-stone-50' }}">
            <span>All Services</span>
        </button>

        <button type="button" onclick="filterMenu('nails-and-toes')" id="menu-tab-nails-and-toes"
            class="menu-filter-btn cursor-pointer px-3.5 py-1.5 rounded-xl text-xs font-black transition shadow-2xs flex items-center space-x-1.5 {{ $defaultFilter === 'nails-and-toes' ? 'bg-rose-700 text-white' : 'bg-white text-rose-800 border border-rose-200 hover:bg-rose-50' }}">
            <span class="w-2 h-2 rounded-full bg-rose-400"></span>
            <span>Nails & Cutics</span>
            <span class="text-[10px] font-normal opacity-80">(Manicurist)</span>
        </button>

        <button type="button" onclick="filterMenu('massage-and-wellness')" id="menu-tab-massage-and-wellness"
            class="menu-filter-btn cursor-pointer px-3.5 py-1.5 rounded-xl text-xs font-black transition shadow-2xs flex items-center space-x-1.5 {{ $defaultFilter === 'massage-and-wellness' ? 'bg-sky-700 text-white' : 'bg-white text-sky-800 border border-sky-200 hover:bg-sky-50' }}">
            <span class="w-2 h-2 rounded-full bg-sky-400"></span>
            <span>Massage & Wellness</span>
            <span class="text-[10px] font-normal opacity-80">(Massage Therapist)</span>
        </button>

        <button type="button" onclick="filterMenu('combo-packages')" id="menu-tab-combo-packages"
            class="menu-filter-btn cursor-pointer px-3.5 py-1.5 rounded-xl text-xs font-black transition shadow-2xs flex items-center space-x-1.5 {{ $defaultFilter === 'combo-packages' ? 'bg-emerald-700 text-white' : 'bg-white text-emerald-800 border border-emerald-200 hover:bg-emerald-50' }}">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>Combo Packages</span>
            <span class="text-[10px] font-normal opacity-80">(All-Around)</span>
        </button>
    </div>

    <!-- Category Sections (Crisp White Cards on Clean Light Background) -->
    <div class="space-y-5">
        @foreach($categories as $cat)
            <div class="space-y-3 category-section" data-category="{{ $cat->slug }}" id="cat-section-{{ $cat->slug }}">
                <div class="flex items-center space-x-2.5">
                    <span class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs">
                        @if($cat->slug === 'nails-and-toes')
                            <i data-lucide="scissors" class="w-4 h-4"></i>
                        @elseif($cat->slug === 'massage-and-wellness')
                            <i data-lucide="heart" class="w-4 h-4"></i>
                        @else
                            <i data-lucide="package" class="w-4 h-4"></i>
                        @endif
                    </span>
                    <h2 class="text-xs sm:text-sm font-black text-stone-900 uppercase tracking-wider">
                        {{ $cat->name }}
                    </h2>
                    <span class="h-px bg-stone-200 flex-1"></span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($cat->services as $srv)
                        <div class="bg-white rounded-2xl p-4 border border-stone-200/80 hover:border-emerald-300 hover:shadow-xs transition flex items-center justify-between gap-3 group">
                            <div class="space-y-1 min-w-0">
                                <h3 class="font-extrabold text-stone-900 text-xs sm:text-sm group-hover:text-emerald-800 transition truncate">
                                    {{ $srv->name }}
                                </h3>
                                <p class="text-[11px] text-stone-500 flex items-center space-x-1.5">
                                    <i data-lucide="clock" class="w-3 h-3 text-stone-400"></i>
                                    <span>{{ $srv->estimated_duration_minutes }} mins</span>
                                    <span>&bull;</span>
                                    <span class="text-emerald-700 font-semibold">In-Shop & Home Service</span>
                                </p>
                            </div>

                            <div class="text-right flex-shrink-0">
                                <span class="text-base sm:text-lg font-black text-emerald-800 block">
                                    ₱{{ number_format($srv->price, 2) }}
                                </span>
                                <a href="{{ route('transactions.create') }}" class="text-[11px] font-bold text-emerald-700 hover:text-emerald-900 hover:underline inline-flex items-center space-x-0.5">
                                    <span>Book</span>
                                    <i data-lucide="arrow-right" class="w-3 h-3"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <script>
        function filterMenu(slug) {
            // 1. Show / hide category sections
            const sections = document.querySelectorAll('.category-section');
            sections.forEach(function(sec) {
                if (slug === 'all' || sec.getAttribute('data-category') === slug) {
                    sec.style.display = 'block';
                } else {
                    sec.style.display = 'none';
                }
            });

            // 2. Tab styling dictionary
            const tabConfigs = {
                'all': {
                    active: ['bg-stone-900', 'text-white'],
                    inactive: ['bg-white', 'text-stone-700', 'border', 'border-stone-200', 'hover:bg-stone-50']
                },
                'nails-and-toes': {
                    active: ['bg-rose-700', 'text-white'],
                    inactive: ['bg-white', 'text-rose-800', 'border', 'border-rose-200', 'hover:bg-rose-50']
                },
                'massage-and-wellness': {
                    active: ['bg-sky-700', 'text-white'],
                    inactive: ['bg-white', 'text-sky-800', 'border', 'border-sky-200', 'hover:bg-sky-50']
                },
                'combo-packages': {
                    active: ['bg-emerald-700', 'text-white'],
                    inactive: ['bg-white', 'text-emerald-800', 'border', 'border-emerald-200', 'hover:bg-emerald-50']
                }
            };

            Object.keys(tabConfigs).forEach(function(key) {
                const btn = document.getElementById('menu-tab-' + key);
                if (!btn) return;
                const config = tabConfigs[key];

                if (key === slug) {
                    config.inactive.forEach(c => btn.classList.remove(c));
                    config.active.forEach(c => btn.classList.add(c));
                } else {
                    config.active.forEach(c => btn.classList.remove(c));
                    config.inactive.forEach(c => btn.classList.add(c));
                }
            });
        }

        // Run filter on initial load if employee has a default category
        document.addEventListener('DOMContentLoaded', function() {
            const initialFilter = "{{ $defaultFilter }}";
            if (initialFilter && initialFilter !== 'all') {
                filterMenu(initialFilter);
            }
        });
    </script>
</div>
@endsection
