<!DOCTYPE html>
<html lang="en" class="h-full bg-stone-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Employee Registration &bull; Nids and Taray Beauty & Wellness Hub</title>
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

    <!-- Storefront Background Image with Dimming & Tint -->
    <div class="fixed inset-0 z-0">
        <img src="{{ asset('images/nids-and-taray-bg.jpg') }}" 
             alt="Nids & Taray Beauty & Wellness Hub" 
             class="w-full h-full object-cover object-center filter brightness-[0.60] contrast-[1.05] scale-105 transform">
        <!-- Deep Emerald & Warm Stone Gradient Veil -->
        <div class="absolute inset-0 bg-gradient-to-t from-stone-950/90 via-emerald-950/75 to-stone-900/80 backdrop-blur-[2px]"></div>
    </div>

    <!-- Register Card (Glassmorphism / Frosted Card) -->
    <div class="relative z-10 max-w-lg w-full bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl shadow-black/50 border border-white/60 overflow-hidden text-stone-800 my-8">
        
        <!-- Header Banner -->
        <div class="px-6 pt-7 pb-5 text-center border-b border-emerald-900/10 bg-gradient-to-b from-emerald-50/90 to-white">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-700 to-teal-600 text-white shadow-md shadow-emerald-700/20 mb-2.5">
                <i data-lucide="user-plus" class="w-7 h-7"></i>
            </div>
            <h1 class="text-xl font-black tracking-tight text-stone-900">Staff & Employee Registration</h1>
            <p class="text-emerald-800 text-[11px] font-extrabold uppercase tracking-widest mt-0.5">Nids & Taray Beauty & Wellness Hub</p>
            <p class="text-stone-500 text-xs mt-1 font-medium">Join the team &bull; Automatically added to live queue</p>
        </div>

        <!-- Form Body -->
        <div class="p-6 sm:p-7 space-y-4">
            @if(isset($errors) && $errors->any())
                <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl font-medium space-y-1">
                    <div class="font-bold flex items-center space-x-1.5 text-rose-900">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-700"></i>
                        <span>Please fix the following:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 pl-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST" class="space-y-3.5">
                @csrf
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Full Name -->
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Full Legal Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required 
                            placeholder="e.g. Maria Santos"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-stone-900 placeholder-stone-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 text-xs font-medium transition">
                    </div>

                    <!-- Nickname / Display Name -->
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Nickname / Screen Name *</label>
                        <input type="text" name="nickname" value="{{ old('nickname') }}" required 
                            placeholder="e.g. Ate Mary"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-stone-900 placeholder-stone-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 text-xs font-medium transition">
                    </div>
                </div>

                <!-- Specialization & Phone -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Specialization / Role *</label>
                        <select name="specialization" required
                            class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-stone-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 text-xs font-medium transition">
                            <option value="all_around" {{ old('specialization') === 'all_around' ? 'selected' : '' }}>All-Around Attendant</option>
                            <option value="manicurist" {{ old('specialization') === 'manicurist' ? 'selected' : '' }}>Manicurist / Nail Tech</option>
                            <option value="massage_therapist" {{ old('specialization') === 'massage_therapist' ? 'selected' : '' }}>Massage Therapist</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Mobile / Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" 
                            placeholder="0917-xxx-xxxx"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-stone-900 placeholder-stone-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 text-xs font-medium transition">
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Email Address *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required 
                        placeholder="e.g. mary@nidsandtaray.com"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-stone-900 placeholder-stone-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 text-xs font-medium transition">
                </div>

                <!-- Password & Confirm Password -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Password *</label>
                        <input type="password" name="password" required 
                            placeholder="Min. 6 characters"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-stone-900 placeholder-stone-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 text-xs font-medium transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Confirm Password *</label>
                        <input type="password" name="password_confirmation" required 
                            placeholder="Repeat password"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-stone-900 placeholder-stone-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 text-xs font-medium transition">
                    </div>
                </div>

                <!-- Perks Note -->
                <div class="p-3 bg-emerald-50/70 border border-emerald-200/80 rounded-2xl text-[11px] text-emerald-900 flex items-start space-x-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-700 mt-0.5 flex-shrink-0"></i>
                    <div>
                        <strong>Standard 40% Commission</strong> &bull; Automatic queue slot allocation &bull; Real-time wallet tracking for daily earnings.
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 px-4 bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold rounded-xl shadow-md shadow-emerald-700/20 transition transform active:scale-[0.98] text-sm inline-flex items-center justify-center space-x-1.5 mt-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Complete Registration & Join Queue</span>
                </button>
            </form>

            <div class="text-center pt-2 border-t border-stone-100">
                <p class="text-xs text-stone-600">
                    Already an employee? 
                    <a href="{{ route('login') }}" class="font-bold text-emerald-800 hover:text-emerald-950 underline underline-offset-2 transition">
                        Sign in to your account
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
