<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f6f9f6]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title ?? 'Nids and Taray Beauty & Wellness Hub' }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpg') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.jpg') }}">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sage: {
                            50: '#f4f8f4',
                            100: '#e5efe5',
                            200: '#cde1cd',
                            300: '#a6cca6',
                            400: '#7bb07c',
                            500: '#569358',
                            600: '#3d7740',
                            700: '#325f34',
                            800: '#2a4c2c',
                            900: '#233f25',
                        },
                        cream: {
                            50: '#fdfbf7',
                            100: '#f8f4eb',
                            200: '#ede3d1',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .lucide { stroke-width: 1.75px; vertical-align: middle; }
        
        /* Custom scrollbar for clean compact lists */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #f1f5f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5cb; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a894; }
    </style>
</head>
<body class="min-h-full flex flex-col md:flex-row bg-[#f6f9f6] text-stone-800 antialiased selection:bg-emerald-100 selection:text-emerald-900 relative">

    <!-- App Splash Screen / Logo Flashing Intro -->
    <div id="app-splash-screen" class="fixed inset-0 z-[9999] bg-emerald-950 flex flex-col items-center justify-center transition-opacity duration-500 ease-out pointer-events-none opacity-100">
        <div class="flex flex-col items-center animate-pulse">
            <img src="{{ asset('images/logo.jpg') }}" alt="Nids & Taray" class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl object-cover shadow-2xl ring-4 ring-emerald-400/40 mb-3 border-2 border-white/20">
            <h1 class="text-white font-black text-xl sm:text-2xl tracking-tight">Nids & Taray</h1>
            <span class="text-emerald-300 text-[11px] font-extrabold tracking-widest uppercase mt-0.5">Beauty & Wellness Hub</span>
        </div>
    </div>
    <script>
        // Smooth dismissal of logo flash splash screen
        window.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const splash = document.getElementById('app-splash-screen');
                if (splash) {
                    splash.classList.add('opacity-0');
                    setTimeout(() => splash.remove(), 500);
                }
            }, 450);
        });
    </script>

    @php
        $isAdminUser = Auth::check() && Auth::user()->role === 'admin';
        $bgImage = $isAdminUser
            ? (file_exists(public_path('images/admin-bg.jpg')) ? asset('images/admin-bg.jpg') : asset('images/nids-and-taray-bg.jpg'))
            : (file_exists(public_path('images/employee-bg.jpg')) ? asset('images/employee-bg.jpg') : asset('images/nids-and-taray-bg.jpg'));
    @endphp

    <!-- Subtle Luxury Spa Background Wallpaper Layer (Adaptive to Admin vs Employee) -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden select-none">
        <img src="{{ $bgImage }}" alt="" class="w-full h-full object-cover filter brightness-[0.96] contrast-[1.02] scale-105">
        <div class="absolute inset-0 bg-[#f6f9f6]/88 backdrop-blur-[1px]"></div>
    </div>

    <!-- Mobile Top Bar (Visible only on small screens) -->
    <header class="md:hidden sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-stone-200/80 px-4 py-3 flex items-center justify-between shadow-2xs">
        <div class="flex items-center space-x-2.5">
            <button type="button" id="mobile-sidebar-toggle" class="p-2 rounded-xl text-stone-600 hover:text-emerald-800 hover:bg-stone-100 transition focus:outline-none" aria-label="Toggle Navigation">
                <i data-lucide="menu" class="w-5 h-5"></i>
            </button>
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                <img src="{{ asset('images/logo.jpg') }}" alt="Nids & Taray" class="w-8 h-8 rounded-lg object-cover shadow-xs border border-emerald-200 flex-shrink-0">
                <div>
                    <span class="text-sm font-black text-stone-900 tracking-tight leading-tight block">Nids & Taray</span>
                    <span class="text-[9px] font-bold text-emerald-800 uppercase tracking-wider block">Beauty & Wellness</span>
                </div>
            </a>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('transactions.create') }}" class="px-2.5 py-1.5 rounded-lg bg-emerald-700 text-white font-bold text-xs inline-flex items-center space-x-1 shadow-xs hover:bg-emerald-800 transition">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>Order</span>
            </a>
        </div>
    </header>

    <!-- Mobile Sidebar Drawer Backdrop -->
    <div id="mobile-sidebar-backdrop" class="fixed inset-0 bg-stone-900/40 backdrop-blur-xs z-40 hidden md:hidden transition-opacity duration-200"></div>

    <!-- Responsive Left Sidebar -->
    <aside id="app-sidebar" class="fixed inset-y-0 left-0 z-50 w-64 lg:w-68 bg-white border-r border-stone-200/80 flex flex-col justify-between transition-transform duration-200 -translate-x-full md:translate-x-0 md:static md:h-screen md:sticky md:top-0 shadow-lg md:shadow-none">
        
        <!-- Top Section: Brand + Sticky Action Button + Navigation -->
        <div class="flex flex-col flex-1 min-h-0 overflow-y-auto">
            
            <!-- Sidebar Header / Brand with Upper Corner Logo -->
            <div class="p-4 lg:p-5 border-b border-stone-100 flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Nids & Taray" class="w-10 h-10 rounded-2xl object-cover shadow-xs border border-emerald-200 flex-shrink-0">
                    <div>
                        <span class="text-base font-extrabold tracking-tight text-stone-900 block leading-tight">
                            Nids & Taray
                        </span>
                        <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">
                            Beauty & Wellness Hub
                        </span>
                    </div>
                </a>
                <!-- Close button for mobile drawer -->
                <button type="button" id="mobile-sidebar-close" class="md:hidden p-1.5 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100" aria-label="Close Sidebar">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Primary Action: + New Order Button (Top of Sidebar) -->
            <div class="p-3 lg:p-4">
                <a href="{{ route('transactions.create') }}" class="w-full py-2.5 px-3.5 rounded-xl bg-gradient-to-r from-emerald-700 to-teal-700 hover:from-emerald-800 hover:to-teal-800 text-white font-bold text-xs shadow-md shadow-emerald-800/15 flex items-center justify-center space-x-2 transition-all active:scale-[0.98]">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>New Booking</span>
                </a>
            </div>

            <!-- Grouped Sidebar Navigation Menu -->
            <nav class="flex-1 px-3 space-y-4 text-xs font-medium pb-4">
                
                <!-- Group 1: Core Essentials -->
                <div class="space-y-1">
                    <div class="px-2 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-stone-400">
                        Overview
                    </div>
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-xl transition font-semibold {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-900 border border-emerald-200/80 font-bold' : 'text-stone-600 hover:text-emerald-900 hover:bg-stone-50' }}">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-emerald-700' : 'text-stone-400' }}"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('queue.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl transition font-semibold {{ request()->routeIs('queue.*') ? 'bg-emerald-50 text-emerald-900 border border-emerald-200/80 font-bold' : 'text-stone-600 hover:text-emerald-900 hover:bg-stone-50' }}">
                        <span class="flex items-center space-x-2.5">
                            <i data-lucide="users" class="w-4 h-4 {{ request()->routeIs('queue.*') ? 'text-emerald-700' : 'text-stone-400' }}"></i>
                            <span>Queue Board</span>
                        </span>
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    </a>
                </div>

                <!-- Group 2: Consolidated Services Section -->
                <div class="space-y-1">
                    <div class="px-2 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-stone-400">
                        Services
                    </div>
                    <a href="{{ route('menu.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-xl transition font-semibold {{ request()->routeIs('menu.*') ? 'bg-emerald-50 text-emerald-900 border border-emerald-200/80 font-bold' : 'text-stone-600 hover:text-emerald-900 hover:bg-stone-50' }}">
                        <i data-lucide="book-open" class="w-4 h-4 {{ request()->routeIs('menu.*') ? 'text-emerald-700' : 'text-stone-400' }}"></i>
                        <span>Service Menu</span>
                    </a>
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('services.manage') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-xl transition font-semibold {{ request()->routeIs('services.manage') ? 'bg-amber-50 text-amber-900 border border-amber-200 font-bold' : 'text-amber-800 hover:text-amber-950 hover:bg-amber-50/60' }}">
                            <i data-lucide="sliders" class="w-4 h-4 {{ request()->routeIs('services.manage') ? 'text-amber-600' : 'text-amber-500' }}"></i>
                            <span>Price Setter</span>
                            <span class="ml-auto text-[9px] px-1.5 py-0.5 rounded-md font-bold bg-amber-100 text-amber-800">Admin</span>
                        </a>
                    @endif
                </div>

                <!-- Group 3: Operations & Finances -->
                <div class="space-y-1">
                    <div class="px-2 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-stone-400">
                        Operations & Sales
                    </div>
                    <a href="{{ route('transactions.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-xl transition font-semibold {{ request()->routeIs('transactions.index', 'transactions.show') ? 'bg-emerald-50 text-emerald-900 border border-emerald-200/80 font-bold' : 'text-stone-600 hover:text-emerald-900 hover:bg-stone-50' }}">
                        <i data-lucide="receipt" class="w-4 h-4 {{ request()->routeIs('transactions.index', 'transactions.show') ? 'text-emerald-700' : 'text-stone-400' }}"></i>
                        <span>Transactions</span>
                    </a>
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('staff-tracker.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-xl transition font-semibold {{ request()->routeIs('staff-tracker.*') ? 'bg-emerald-50 text-emerald-900 border border-emerald-200/80 font-bold' : 'text-stone-600 hover:text-emerald-900 hover:bg-stone-50' }}">
                            <i data-lucide="user-check" class="w-4 h-4 {{ request()->routeIs('staff-tracker.*') ? 'text-emerald-700' : 'text-stone-400' }}"></i>
                            <span>Staff Tracker</span>
                            <span class="ml-auto text-[9px] px-1.5 py-0.5 rounded-md font-bold bg-amber-100 text-amber-800">Admin</span>
                        </a>
                    @endif
                    <a href="{{ route('wallet.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-xl transition font-semibold {{ request()->routeIs('wallet.*') ? 'bg-emerald-50 text-emerald-900 border border-emerald-200/80 font-bold' : 'text-stone-600 hover:text-emerald-900 hover:bg-stone-50' }}">
                        <i data-lucide="wallet" class="w-4 h-4 {{ request()->routeIs('wallet.*') ? 'text-emerald-700' : 'text-stone-400' }}"></i>
                        <span>My Wallet</span>
                    </a>
                </div>

            </nav>
        </div>

        <!-- Bottom User Profile Card & Logout -->
        <div class="p-3 border-t border-stone-100 bg-stone-50/70">
            <div class="flex items-center justify-between">
                <a href="{{ route('profile.show') }}" class="flex items-center space-x-2.5 min-w-0 hover:opacity-85 transition group flex-1 mr-1" title="Account Settings">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-800 font-bold text-xs flex items-center justify-center flex-shrink-0 group-hover:ring-2 group-hover:ring-emerald-400">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="truncate">
                        <div class="text-xs font-bold text-stone-900 truncate group-hover:text-emerald-800">{{ Auth::user()->name ?? 'User' }}</div>
                        <div class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider">
                            @if(Auth::user()->role === 'admin')
                                Administrator
                            @elseif(Auth::user()->staffProfile && Auth::user()->staffProfile->specialization)
                                {{ strtoupper(str_replace('_', ' ', Auth::user()->staffProfile->specialization)) }}
                            @else
                                Employee
                            @endif
                        </div>
                    </div>
                </a>

                <div class="flex items-center space-x-0.5">
                    <a href="{{ route('profile.show') }}" class="p-1.5 rounded-lg text-stone-400 hover:text-emerald-800 hover:bg-emerald-50 transition" title="Account Settings" aria-label="Account Settings">
                        <i data-lucide="settings" class="w-4 h-4"></i>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-1.5 rounded-lg text-stone-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Log out" aria-label="Log out">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 relative z-10">
        <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto pb-24 md:pb-10">
            
            <!-- Flash Alerts -->
            @if(session('success'))
                <div class="mb-5 p-3.5 sm:p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start space-x-3 shadow-xs">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-700 mt-0.5 flex-shrink-0"></i>
                    <div class="text-xs sm:text-sm font-semibold">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-5 p-3.5 sm:p-4 rounded-2xl bg-sky-50 border border-sky-200 text-sky-900 flex items-start space-x-3 shadow-xs">
                    <i data-lucide="info" class="w-5 h-5 text-sky-700 mt-0.5 flex-shrink-0"></i>
                    <div class="text-xs sm:text-sm font-semibold">{{ session('info') }}</div>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="mb-5 p-3.5 sm:p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 shadow-xs">
                    <div class="text-xs font-bold mb-1 flex items-center space-x-1.5">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-700"></i>
                        <span>Please check the following errors:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Clean Desktop Sub-Footer -->
        <footer class="hidden md:block py-3 px-8 text-xs text-stone-400 border-t border-stone-200/50 bg-white/40 text-center">
            &copy; {{ date('Y') }} <strong>Nids & Taray Beauty and Wellness Hub</strong> &bull; Salon & Spa Management
        </footer>
    </div>

    <!-- Sticky Floating Action Button (FAB) for Mobile/Tablet Screens -->
    <a href="{{ route('transactions.create') }}" class="md:hidden fixed bottom-5 right-5 z-40 w-13 h-13 rounded-2xl bg-emerald-700 text-white shadow-lg shadow-emerald-900/30 flex items-center justify-center active:scale-95 transition-all border-2 border-white" aria-label="New Order">
        <i data-lucide="plus" class="w-6 h-6"></i>
    </a>

    <!-- Scripts for Icons and Mobile Drawer Toggle -->
    <script>
        lucide.createIcons();

        // Responsive Mobile Drawer Toggle
        const sidebar = document.getElementById('app-sidebar');
        const backdrop = document.getElementById('mobile-sidebar-backdrop');
        const openBtn = document.getElementById('mobile-sidebar-toggle');
        const closeBtn = document.getElementById('mobile-sidebar-close');

        function toggleSidebar(open) {
            if (open) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                document.body.classList.add('overflow-hidden', 'md:overflow-auto');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden', 'md:overflow-auto');
            }
        }

        if (openBtn) openBtn.addEventListener('click', () => toggleSidebar(true));
        if (closeBtn) closeBtn.addEventListener('click', () => toggleSidebar(false));
        if (backdrop) backdrop.addEventListener('click', () => toggleSidebar(false));
    </script>
    @stack('scripts')
</body>
</html>
