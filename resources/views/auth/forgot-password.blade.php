<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f2f6f3]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Forgot Password &bull; Nids and Taray Beauty & Wellness Hub</title>
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

    <!-- Card (Glassmorphism / Frosted Card) -->
    <div class="relative z-10 max-w-md w-full bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl shadow-black/50 border border-white/60 overflow-hidden text-stone-800 my-6">
        
        <!-- Header Banner -->
        <div class="px-6 pt-8 pb-6 text-center border-b border-emerald-900/10 bg-gradient-to-b from-emerald-50/90 to-white">
            <img src="{{ asset('images/logo.jpg') }}" alt="Nids and Taray" class="w-16 h-16 rounded-2xl object-cover shadow-md mx-auto mb-3 border border-emerald-100 ring-2 ring-emerald-500/20">
            <h1 class="text-xl font-black tracking-tight text-stone-900">Reset Password</h1>
            <p class="text-emerald-800 text-[11px] font-extrabold uppercase tracking-widest mt-0.5">Nids and Taray Hub</p>
            <p class="text-stone-500 text-xs mt-1 font-medium">Enter your registered email and choose a new password</p>
        </div>

        <!-- Form Body -->
        <div class="p-6 sm:p-8 space-y-4">
            @if(session('info'))
                <div class="p-3 bg-sky-50 border border-sky-200 text-sky-800 text-xs rounded-xl font-medium flex items-center space-x-1.5">
                    <i data-lucide="info" class="w-4 h-4 text-sky-700"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-3 bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl space-y-1">
                    @foreach($errors->all() as $error)
                        <div class="flex items-center space-x-1">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5 text-rose-600"></i>
                            <span>{{ $error }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Registered Account Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-stone-200 bg-stone-50 text-xs font-semibold text-stone-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition"
                            placeholder="your.email@example.com">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">New Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </span>
                        <input type="password" name="password" required
                            class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-stone-200 bg-stone-50 text-xs font-semibold text-stone-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition"
                            placeholder="At least 6 characters">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Confirm New Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </span>
                        <input type="password" name="password_confirmation" required
                            class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-stone-200 bg-stone-50 text-xs font-semibold text-stone-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition"
                            placeholder="Repeat new password">
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-700 to-teal-700 hover:from-emerald-800 hover:to-teal-800 text-white font-extrabold text-xs shadow-md transition-all active:scale-[0.98] flex items-center justify-center space-x-2">
                    <i data-lucide="key" class="w-4 h-4"></i>
                    <span>Reset Password & Update</span>
                </button>
            </form>

            <div class="text-center pt-2">
                <a href="{{ route('login') }}" class="text-xs font-bold text-emerald-800 hover:text-emerald-950 hover:underline inline-flex items-center space-x-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Back to Sign In</span>
                </a>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
