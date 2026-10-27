<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f2f6f3]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login &bull; Nids and Taray Beauty & Wellness Hub</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpg') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.jpg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .lucide { stroke-width: 1.75px; vertical-align: middle; }
    </style>
</head>
<body class="min-h-screen relative flex items-center justify-center p-4 bg-stone-900 overflow-x-hidden">

    <!-- Storefront Background Image with Elegant Dimming & Tint -->
    <div class="fixed inset-0 z-0">
        <img src="{{ asset('images/nids-and-taray-bg.jpg') }}" 
             alt="Nids & Taray Beauty & Wellness Hub" 
             class="w-full h-full object-cover object-center filter brightness-[0.60] contrast-[1.05] scale-105 transform">
        <!-- Deep Emerald & Warm Stone Gradient Veil -->
        <div class="absolute inset-0 bg-gradient-to-t from-stone-950/90 via-emerald-950/75 to-stone-900/80 backdrop-blur-[2px]"></div>
    </div>

    <!-- Login Card (Glassmorphism / Frosted Card) -->
    <div class="relative z-10 max-w-md w-full bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl shadow-black/50 border border-white/60 overflow-hidden text-stone-800 my-6">
        
        <!-- Header Banner -->
        <div class="px-6 pt-8 pb-6 text-center border-b border-emerald-900/10 bg-gradient-to-b from-emerald-50/90 to-white">
            <img src="{{ asset('images/logo.jpg') }}" alt="Nids and Taray" class="w-16 h-16 rounded-2xl object-cover shadow-md mx-auto mb-3 border border-emerald-100 ring-2 ring-emerald-500/20">
            <h1 class="text-xl font-black tracking-tight text-stone-900">Nids and Taray</h1>
            <p class="text-emerald-800 text-[11px] font-extrabold uppercase tracking-widest mt-0.5">Beauty & Wellness Hub</p>
            <p class="text-stone-500 text-xs mt-1 font-medium">Staff & Admin Mobile Portal</p>
        </div>

        <!-- Form Body -->
        <div class="p-6 sm:p-8 space-y-4">
            @if(session('info'))
                <div class="p-3 bg-sky-50 border border-sky-200 text-sky-800 text-xs rounded-xl font-medium flex items-center space-x-1.5">
                    <i data-lucide="info" class="w-4 h-4 text-sky-700"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl font-medium flex items-center space-x-1.5">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-700"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="p-3 bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl font-medium flex items-center space-x-1.5">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-700"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4" onsubmit="sessionStorage.removeItem('nids_splash_shown')">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Email Address</label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-4 h-4 text-stone-400 absolute left-3.5 top-3.5"></i>
                        <input type="email" name="email" id="email" value="{{ old('email', 'admin@nidsandtaray.com') }}" required 
                            class="w-full pl-10 pr-4 py-3 rounded-xl bg-stone-50 border border-stone-200 text-stone-900 placeholder-stone-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 text-sm font-medium transition" 
                            placeholder="name@nidsandtaray.com">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Password</label>
                        <a href="{{ route('password.request') }}" class="text-[11px] font-bold text-emerald-800 hover:text-emerald-950 hover:underline">
                            Forgot password?
                        </a>
                    </div>
                    <div class="relative">
                        <i data-lucide="lock" class="w-4 h-4 text-stone-400 absolute left-3.5 top-3.5"></i>
                        <input type="password" name="password" id="password" value="admin123" required 
                            class="w-full pl-10 pr-4 py-3 rounded-xl bg-stone-50 border border-stone-200 text-stone-900 placeholder-stone-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 text-sm font-medium transition" 
                            placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 px-4 bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold rounded-xl shadow-md shadow-emerald-700/20 transition transform active:scale-[0.98] text-sm inline-flex items-center justify-center space-x-1.5">
                    <span>Sign In</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>

            <!-- Employee Registration Link -->
            <div class="text-center pt-2 pb-1">
                <p class="text-xs text-stone-600">
                    New staff member? 
                    <a href="{{ route('register') }}" class="font-bold text-emerald-800 hover:text-emerald-950 underline underline-offset-2 transition">
                        Register here as Employee
                    </a>
                </p>
            </div>

            <!-- Quick Demo Credentials -->
            <div class="pt-4 border-t border-emerald-900/10">
                <p class="text-[10px] font-bold text-stone-500 uppercase tracking-wider text-center mb-2 flex items-center justify-center space-x-1">
                    <i data-lucide="key" class="w-3.5 h-3.5 text-stone-400"></i>
                    <span>Quick Fill (Demo / Testing)</span>
                </p>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <button type="button" onclick="fillCreds('admin@nidsandtaray.com', 'admin123')" class="p-2.5 bg-stone-50 hover:bg-emerald-50/80 hover:border-emerald-300 border border-stone-200 rounded-xl text-stone-800 font-bold text-center transition">
                        <span class="inline-flex items-center space-x-1">
                            <i data-lucide="shield" class="w-3.5 h-3.5 text-emerald-700"></i>
                            <span>Admin</span>
                        </span>
                        <span class="block text-[10px] text-stone-500 font-normal mt-0.5">admin@nidsandtaray.com</span>
                    </button>
                    <button type="button" onclick="fillCreds('tin@nidsandtaray.com', 'staff123')" class="p-2.5 bg-stone-50 hover:bg-emerald-50/80 hover:border-emerald-300 border border-stone-200 rounded-xl text-stone-800 font-bold text-center transition">
                        <span class="inline-flex items-center space-x-1">
                            <i data-lucide="user" class="w-3.5 h-3.5 text-emerald-700"></i>
                            <span>Staff (Tin)</span>
                        </span>
                        <span class="block text-[10px] text-stone-500 font-normal mt-0.5">tin@nidsandtaray.com</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fillCreds(email, pass) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = pass;
        }
        lucide.createIcons();
    </script>
</body>
</html>
