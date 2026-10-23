<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f2f6f3]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login &bull; Nids and Taray Beauty & Wellness Hub</title>
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
<body class="h-full flex items-center justify-center p-4 bg-gradient-to-br from-[#ebf3ed] via-[#f5f8f5] to-[#e4eee6]">
    <div class="max-w-md w-full bg-white/95 backdrop-blur-xl rounded-3xl shadow-xl shadow-emerald-950/5 border border-emerald-900/10 overflow-hidden text-stone-800">
        
        <!-- Header Banner -->
        <div class="px-6 pt-8 pb-6 text-center border-b border-emerald-900/10 bg-gradient-to-b from-emerald-50/80 to-white">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-700 to-teal-600 text-white shadow-md shadow-emerald-700/20 mb-3">
                <i data-lucide="sparkles" class="w-7 h-7"></i>
            </div>
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

            @if(isset($errors) && $errors->any())
                <div class="p-3 bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl font-medium flex items-center space-x-1.5">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-700"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
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
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Password</label>
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
