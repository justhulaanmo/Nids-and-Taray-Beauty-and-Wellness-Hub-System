@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Digital Menu Header -->
    <div class="relative bg-emerald-950 rounded-3xl p-6 text-white shadow-md shadow-emerald-900/10 overflow-hidden">
        <!-- Background Storefront Photo with Emerald Tint -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/nids-and-taray-bg.jpg') }}" alt="Nids and Taray Storefront" class="w-full h-full object-cover object-center opacity-25 filter blur-[0.5px] scale-105">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/95 via-teal-950/85 to-emerald-900/90"></div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 relative z-10">
            <div>
                <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-white/15 text-emerald-100 text-xs font-bold uppercase tracking-wider mb-1.5 border border-white/20">
                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-emerald-200"></i>
                <span>Service Menu & Price List</span>
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                Nids & Taray Service Menu
            </h1>
            <p class="text-xs sm:text-sm text-emerald-100/90 mt-1">
                Browse available nail care, massage, and wellness treatments.
            </p>
        </div>

        <div class="flex items-center space-x-2">
            @if(Auth::user()->role === 'admin')
                <a href="{{ route('services.manage') }}" class="px-4 py-2.5 rounded-xl bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold text-xs shadow-xs transition inline-flex items-center space-x-1.5 border border-amber-300/70">
                    <i data-lucide="sliders" class="w-4 h-4"></i>
                    <span>Edit Prices</span>
                </a>
            @endif
            <a href="{{ route('transactions.create') }}" class="px-4 py-2.5 rounded-xl bg-white text-emerald-900 hover:bg-emerald-50 font-black text-xs shadow-xs transition inline-flex items-center space-x-1.5">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>New Order</span>
            </a>
        </div>
    </div>

    <!-- Category Sections -->
    <div class="space-y-6">
        @foreach($categories as $cat)
            <div class="space-y-3">
                <div class="flex items-center space-x-3">
                    <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center">
                        @if($cat->slug === 'nails-and-toes')
                            <i data-lucide="scissors" class="w-4 h-4"></i>
                        @elseif($cat->slug === 'massage-and-wellness')
                            <i data-lucide="heart" class="w-4 h-4"></i>
                        @else
                            <i data-lucide="package" class="w-4 h-4"></i>
                        @endif
                    </span>
                    <h2 class="text-base sm:text-lg font-black text-stone-900 uppercase tracking-wider">
                        {{ $cat->name }}
                    </h2>
                    <span class="h-px bg-stone-200 flex-1"></span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                    @foreach($cat->services as $srv)
                        <div class="bg-white rounded-2xl p-4 border border-emerald-900/10 hover:border-emerald-300 hover:shadow-xs transition flex items-center justify-between gap-3 group">
                            <div class="space-y-1">
                                <h3 class="font-extrabold text-stone-900 text-sm group-hover:text-emerald-800 transition">
                                    {{ $srv->name }}
                                </h3>
                                <p class="text-xs text-stone-500 flex items-center space-x-1.5">
                                    <i data-lucide="clock" class="w-3.5 h-3.5 text-stone-400"></i>
                                    <span>{{ $srv->estimated_duration_minutes }} mins</span>
                                    <span>&bull;</span>
                                    <span class="text-emerald-700 font-semibold">In-Shop & Home Service</span>
                                </p>
                            </div>

                            <div class="text-right flex-shrink-0">
                                <span class="text-lg font-black text-emerald-800 block">
                                    ₱{{ number_format($srv->price, 2) }}
                                </span>
                                <a href="{{ route('transactions.create') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 hover:underline inline-flex items-center space-x-1">
                                    <span>Select</span>
                                    <i data-lucide="arrow-right" class="w-3 h-3"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
