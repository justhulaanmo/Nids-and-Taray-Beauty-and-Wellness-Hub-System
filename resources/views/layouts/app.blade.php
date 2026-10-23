<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f6f9f6]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title ?? 'Nids and Taray Beauty & Wellness Hub' }}</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons (Minimalist Line Icons) -->
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
    </style>
</head>
<body class="h-full flex flex-col bg-[#f6f9f6] text-stone-800 antialiased pb-20 md:pb-0">

    <!-- Top Navigation Header -->
    <header class="bg-white/90 backdrop-blur-md border-b border-emerald-900/10 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-700 to-teal-600 flex items-center justify-center text-white shadow-xs font-black text-sm tracking-wider">
                            NT
                        </div>
                        <div>
                            <span class="text-base font-extrabold tracking-tight text-stone-900 block leading-tight">
                                Nids & Taray
                            </span>
                            <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">
                                Beauty & Wellness Hub
                            </span>
                        </div>
                    </a>

                    <!-- Desktop Nav Links -->
                    <nav class="hidden md:flex ml-8 space-x-1">
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition inline-flex items-center space-x-1.5 {{ request()->routeIs('dashboard') ? 'bg-emerald-100/70 text-emerald-900 border border-emerald-300/60' : 'text-stone-600 hover:text-emerald-800 hover:bg-emerald-50/80' }}">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('queue.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition inline-flex items-center space-x-1.5 {{ request()->routeIs('queue.*') ? 'bg-emerald-100/70 text-emerald-900 border border-emerald-300/60' : 'text-stone-600 hover:text-emerald-800 hover:bg-emerald-50/80' }}">
                            <i data-lucide="users" class="w-4 h-4"></i>
                            <span>Queue Board</span>
                        </a>
                        <a href="{{ route('menu.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition inline-flex items-center space-x-1.5 {{ request()->routeIs('menu.*') ? 'bg-emerald-100/70 text-emerald-900 border border-emerald-300/60' : 'text-stone-600 hover:text-emerald-800 hover:bg-emerald-50/80' }}">
                            <i data-lucide="sparkles" class="w-4 h-4"></i>
                            <span>Service Menu</span>
                        </a>
                        <a href="{{ route('transactions.create') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition inline-flex items-center space-x-1.5 bg-emerald-700 hover:bg-emerald-800 text-white shadow-xs">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>New Order</span>
                        </a>
                        <a href="{{ route('wallet.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition inline-flex items-center space-x-1.5 {{ request()->routeIs('wallet.*') ? 'bg-emerald-100/70 text-emerald-900 border border-emerald-300/60' : 'text-stone-600 hover:text-emerald-800 hover:bg-emerald-50/80' }}">
                            <i data-lucide="wallet" class="w-4 h-4"></i>
                            <span>My Wallet</span>
                        </a>
                        <a href="{{ route('transactions.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition inline-flex items-center space-x-1.5 {{ request()->routeIs('transactions.index', 'transactions.show') ? 'bg-emerald-100/70 text-emerald-900 border border-emerald-300/60' : 'text-stone-600 hover:text-emerald-800 hover:bg-emerald-50/80' }}">
                            <i data-lucide="receipt" class="w-4 h-4"></i>
                            <span>Transactions</span>
                        </a>
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('services.manage') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition inline-flex items-center space-x-1.5 {{ request()->routeIs('services.manage') ? 'bg-amber-100 text-amber-900 border border-amber-300/60' : 'text-amber-800 hover:text-amber-950 hover:bg-amber-50' }}">
                                <i data-lucide="sliders" class="w-4 h-4"></i>
                                <span>Price Setter</span>
                            </a>
                        @endif
                    </nav>
                </div>

                <!-- Right Side User Menu -->
                <div class="flex items-center space-x-3">
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-xs font-bold text-stone-900">{{ Auth::user()->name ?? 'User' }}</span>
                        <span class="text-[10px] uppercase font-bold text-emerald-800">
                            {{ Auth::user()->role === 'admin' ? 'Admin' : 'Staff' }}
                        </span>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-2 rounded-xl text-stone-500 hover:text-rose-600 hover:bg-rose-50 transition text-xs font-semibold flex items-center space-x-1" title="Log out">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            <span class="hidden sm:inline">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-5">
        <!-- Flash Alerts -->
        @if(session('success'))
            <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start space-x-3 shadow-xs">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-700 mt-0.5 flex-shrink-0"></i>
                <div class="text-xs sm:text-sm font-semibold">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('info'))
            <div class="mb-5 p-4 rounded-2xl bg-sky-50 border border-sky-200 text-sky-900 flex items-start space-x-3 shadow-xs">
                <i data-lucide="info" class="w-5 h-5 text-sky-700 mt-0.5 flex-shrink-0"></i>
                <div class="text-xs sm:text-sm font-semibold">{{ session('info') }}</div>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 shadow-xs">
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

    <!-- Mobile Sticky Bottom Navigation Bar (Phone-friendly) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-lg border-t border-emerald-900/10 px-2 py-2 flex justify-around items-center text-center shadow-lg">
        <!-- 1. Home / Dashboard -->
        <a href="{{ route('dashboard') }}" class="flex-1 py-1 flex flex-col items-center {{ request()->routeIs('dashboard') ? 'text-emerald-800 font-bold' : 'text-stone-400 hover:text-stone-700' }}">
            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
            <span class="text-[10px] mt-0.5">Home</span>
        </a>

        <!-- 2. Queue Board -->
        <a href="{{ route('queue.index') }}" class="flex-1 py-1 flex flex-col items-center {{ request()->routeIs('queue.*') ? 'text-emerald-800 font-bold' : 'text-stone-400 hover:text-stone-700' }}">
            <i data-lucide="users" class="w-5 h-5"></i>
            <span class="text-[10px] mt-0.5">Queue</span>
        </a>

        <!-- 3. Record Customer (Big Highlighted Center Button) -->
        <a href="{{ route('transactions.create') }}" class="flex-shrink-0 -mt-6">
            <div class="w-12 h-12 rounded-2xl bg-emerald-700 text-white shadow-md shadow-emerald-800/30 flex items-center justify-center font-black border-2 border-white active:scale-95 transition">
                <i data-lucide="plus" class="w-6 h-6"></i>
            </div>
            <span class="text-[9px] font-bold text-emerald-800 block mt-0.5">Order</span>
        </a>

        <!-- 4. Digital Menu -->
        <a href="{{ route('menu.index') }}" class="flex-1 py-1 flex flex-col items-center {{ request()->routeIs('menu.*') ? 'text-emerald-800 font-bold' : 'text-stone-400 hover:text-stone-700' }}">
            <i data-lucide="sparkles" class="w-5 h-5"></i>
            <span class="text-[10px] mt-0.5">Menu</span>
        </a>

        <!-- 5. My Wallet -->
        <a href="{{ route('wallet.index') }}" class="flex-1 py-1 flex flex-col items-center {{ request()->routeIs('wallet.*') ? 'text-emerald-800 font-bold' : 'text-stone-400 hover:text-stone-700' }}">
            <i data-lucide="wallet" class="w-5 h-5"></i>
            <span class="text-[10px] mt-0.5">Wallet</span>
        </a>
    </nav>

    <!-- Desktop Footer -->
    <footer class="hidden md:block bg-white/80 border-t border-emerald-900/10 py-4 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs text-stone-500">
            &copy; {{ date('Y') }} <strong>Nids and Taray Beauty and Wellness Hub</strong> &bull; Simple & Aesthetic Salon Management System
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
