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
             class="w-full h-full object-cover object-center filter brightness-[0.55] contrast-[1.05] scale-105 transform">
        <!-- Deep Emerald & Warm Stone Gradient Veil -->
        <div class="absolute inset-0 bg-gradient-to-t from-stone-950/90 via-emerald-950/75 to-stone-900/80 backdrop-blur-[2px]"></div>
    </div>

    <!-- Register Card (Glassmorphism / Frosted Card) -->
    <div class="relative z-10 max-w-xl w-full bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl shadow-black/50 border border-white/60 overflow-hidden text-stone-800 my-8">
        
        <!-- Header Banner -->
        <div class="px-6 pt-7 pb-5 text-center border-b border-emerald-900/10 bg-gradient-to-b from-emerald-50/90 to-white">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-700 to-teal-600 text-white shadow-md shadow-emerald-700/20 mb-2.5">
                <i data-lucide="user-plus" class="w-7 h-7"></i>
            </div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-stone-900">Staff & Employee Registration</h1>
            <p class="text-emerald-800 text-[11px] font-extrabold uppercase tracking-widest mt-0.5">Nids & Taray Beauty & Wellness Hub</p>
            <p class="text-stone-500 text-xs mt-1 font-medium">Join our professional salon team &bull; Auto-queued for client service</p>
        </div>

        <!-- Form Body -->
        <div class="p-6 sm:p-7 space-y-4">
            @if(isset($errors) && $errors->any())
                <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-2xl font-medium space-y-1">
                    <div class="font-bold flex items-center space-x-1.5 text-rose-900">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-700"></i>
                        <span>Please correct the following:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 pl-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST" id="registerForm" autocomplete="off" class="space-y-3.5">
                @csrf
                
                <!-- Anti-autofill dummy traps -->
                <input type="text" style="display:none">
                <input type="password" style="display:none">

                <!-- 1. First Name & Last Name (Separated) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">First Name *</label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required 
                            placeholder="e.g. Maria"
                            oninput="capitalizeWords(this)"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-stone-900 placeholder-stone-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 text-xs font-bold transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Last Name *</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required 
                            placeholder="e.g. Santos"
                            oninput="capitalizeWords(this)"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-stone-900 placeholder-stone-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 text-xs font-bold transition">
                    </div>
                </div>

                <!-- 2. Nickname & Specialization -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Nickname / Screen Name *</label>
                        <input type="text" name="nickname" id="nickname" value="{{ old('nickname') }}" required 
                            placeholder="e.g. Mary"
                            oninput="capitalizeWords(this)"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-stone-900 placeholder-stone-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 text-xs font-bold transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Specialization / Role *</label>
                        <select name="specialization" required
                            class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-stone-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 text-xs font-bold transition">
                            <option value="manicurist" {{ old('specialization', 'manicurist') === 'manicurist' ? 'selected' : '' }}>Manicurist / Nail Tech</option>
                            <option value="massage_therapist" {{ old('specialization') === 'massage_therapist' ? 'selected' : '' }}>Massage Therapist</option>
                            <option value="all_around" {{ old('specialization') === 'all_around' ? 'selected' : '' }}>All-Around Attendant</option>
                        </select>
                    </div>
                </div>

                <!-- 3. Mobile Number (09 + 9 digits = 11 digits) & Email Address -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1 flex items-center justify-between">
                            <span>Mobile Phone Number *</span>
                            <span id="phone_counter" class="text-[10px] text-stone-400 font-semibold">0 / 11</span>
                        </label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required maxlength="11"
                            placeholder="09171234567"
                            autocomplete="off"
                            oninput="validateMobile(this)"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-stone-900 placeholder-stone-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 text-xs font-bold transition">
                        <p id="phone_error" class="text-[10px] text-stone-500 mt-1">Must start with <strong>09</strong> and be exactly 11 digits.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Email Address *</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required 
                            placeholder="e.g. maria@gmail.com"
                            autocomplete="new-password"
                            oninput="validateEmailField(this)"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-stone-900 placeholder-stone-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 text-xs font-bold transition">
                        <p id="email_error" class="text-[10px] text-stone-500 mt-1">Enter a valid email address.</p>
                    </div>
                </div>

                <!-- 4. Password & Confirm Password -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Password *</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required 
                                placeholder="Create strong password"
                                autocomplete="new-password"
                                oninput="checkPasswordStrength(this.value)"
                                class="w-full pl-3.5 pr-9 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-stone-900 placeholder-stone-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 text-xs font-bold transition">
                            <button type="button" onclick="togglePasswordVisibility('password', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-stone-400 hover:text-stone-700">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Confirm Password *</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" required 
                                placeholder="Repeat password"
                                autocomplete="new-password"
                                oninput="checkPasswordConfirmation()"
                                class="w-full pl-3.5 pr-9 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-stone-900 placeholder-stone-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 text-xs font-bold transition">
                            <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-stone-400 hover:text-stone-700">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Password Strength Progress Meter -->
                <div class="space-y-1.5 pt-1">
                    <div class="flex items-center justify-between text-[11px] font-bold">
                        <span class="text-stone-500">Password Security Tracker:</span>
                        <span id="strength_label" class="text-stone-400 font-extrabold uppercase tracking-wider text-[10px]">No Password Entered</span>
                    </div>
                    <div class="w-full h-1.5 bg-stone-200 rounded-full overflow-hidden flex gap-1">
                        <div id="bar_1" class="h-full w-1/4 bg-stone-200 transition-all duration-300"></div>
                        <div id="bar_2" class="h-full w-1/4 bg-stone-200 transition-all duration-300"></div>
                        <div id="bar_3" class="h-full w-1/4 bg-stone-200 transition-all duration-300"></div>
                        <div id="bar_4" class="h-full w-1/4 bg-stone-200 transition-all duration-300"></div>
                    </div>
                </div>

                <!-- Standard Security Checklist -->
                <div class="p-3 bg-stone-50/90 border border-stone-200/80 rounded-2xl text-[11px] space-y-1.5">
                    <span class="font-extrabold text-[10px] text-stone-500 uppercase tracking-wider block">Security Requirements:</span>
                    <div class="grid grid-cols-2 gap-1.5 text-stone-500">
                        <div id="chk_length" class="flex items-center space-x-1.5 transition">
                            <span class="w-3.5 h-3.5 rounded-full bg-stone-200 text-stone-600 flex items-center justify-center text-[9px] font-bold">&bull;</span>
                            <span>At least 8 characters</span>
                        </div>
                        <div id="chk_upper" class="flex items-center space-x-1.5 transition">
                            <span class="w-3.5 h-3.5 rounded-full bg-stone-200 text-stone-600 flex items-center justify-center text-[9px] font-bold">&bull;</span>
                            <span>One uppercase letter (A-Z)</span>
                        </div>
                        <div id="chk_lower" class="flex items-center space-x-1.5 transition">
                            <span class="w-3.5 h-3.5 rounded-full bg-stone-200 text-stone-600 flex items-center justify-center text-[9px] font-bold">&bull;</span>
                            <span>One lowercase letter (a-z)</span>
                        </div>
                        <div id="chk_number" class="flex items-center space-x-1.5 transition">
                            <span class="w-3.5 h-3.5 rounded-full bg-stone-200 text-stone-600 flex items-center justify-center text-[9px] font-bold">&bull;</span>
                            <span>One number (0-9)</span>
                        </div>
                        <div id="chk_symbol" class="flex items-center space-x-1.5 transition">
                            <span class="w-3.5 h-3.5 rounded-full bg-stone-200 text-stone-600 flex items-center justify-center text-[9px] font-bold">&bull;</span>
                            <span>One special symbol (!@#$)</span>
                        </div>
                        <div id="chk_match" class="flex items-center space-x-1.5 transition">
                            <span class="w-3.5 h-3.5 rounded-full bg-stone-200 text-stone-600 flex items-center justify-center text-[9px] font-bold">&bull;</span>
                            <span>Passwords match</span>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="submitBtn" class="w-full py-3.5 px-4 bg-emerald-700 hover:bg-emerald-800 text-white font-black rounded-xl shadow-md shadow-emerald-700/20 transition transform active:scale-[0.98] text-sm inline-flex items-center justify-center space-x-2 mt-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
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

        // 1. Auto Capitalize First Letter of Every Word
        function capitalizeWords(input) {
            const cursorPosition = input.selectionStart;
            const originalVal = input.value;
            // Title case each word
            const formatted = originalVal.replace(/\b\w/g, c => c.toUpperCase());
            input.value = formatted;
            input.setSelectionRange(cursorPosition, cursorPosition);
        }

        // 2. Mobile Validation (09 + 9 digits = 11 digits)
        function validateMobile(input) {
            // Strip non-digits
            let val = input.value.replace(/\D/g, '');
            if (val.length > 11) val = val.substring(0, 11);
            input.value = val;

            const counter = document.getElementById('phone_counter');
            const error = document.getElementById('phone_error');
            counter.innerText = val.length + ' / 11';

            if (val.length === 0) {
                error.innerHTML = 'Must start with <strong>09</strong> and be exactly 11 digits.';
                error.className = 'text-[10px] text-stone-500 mt-1';
                input.classList.remove('border-rose-400', 'border-emerald-500');
            } else if (!val.startsWith('09')) {
                error.innerText = 'Mobile number must start with 09.';
                error.className = 'text-[10px] text-rose-600 font-bold mt-1';
                input.classList.add('border-rose-400');
                input.classList.remove('border-emerald-500');
            } else if (val.length < 11) {
                error.innerText = 'Incomplete: ' + (11 - val.length) + ' digit(s) remaining.';
                error.className = 'text-[10px] text-amber-600 font-bold mt-1';
                input.classList.add('border-amber-400');
                input.classList.remove('border-emerald-500');
            } else {
                error.innerText = 'Valid mobile number format.';
                error.className = 'text-[10px] text-emerald-700 font-bold mt-1';
                input.classList.remove('border-rose-400', 'border-amber-400');
                input.classList.add('border-emerald-500');
            }
        }

        // 3. Email Trapping & Live Validation
        function validateEmailField(input) {
            const email = input.value.trim();
            const error = document.getElementById('email_error');
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

            if (email.length === 0) {
                error.innerText = 'Enter a valid email address.';
                error.className = 'text-[10px] text-stone-500 mt-1';
                input.classList.remove('border-rose-400', 'border-emerald-500');
            } else if (emailRegex.test(email)) {
                error.innerText = 'Valid email address format.';
                error.className = 'text-[10px] text-emerald-700 font-bold mt-1';
                input.classList.remove('border-rose-400');
                input.classList.add('border-emerald-500');
            } else {
                error.innerText = 'Please enter a valid email format (e.g. maria@gmail.com).';
                error.className = 'text-[10px] text-rose-600 font-bold mt-1';
                input.classList.add('border-rose-400');
                input.classList.remove('border-emerald-500');
            }
        }

        // 4. Password Visibility Toggle
        function togglePasswordVisibility(fieldId, btn) {
            const input = document.getElementById(fieldId);
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            btn.innerHTML = isPassword 
                ? '<i data-lucide="eye-off" class="w-4 h-4 text-emerald-700"></i>' 
                : '<i data-lucide="eye" class="w-4 h-4 text-stone-400"></i>';
            lucide.createIcons();
        }

        // 5. Password Security Tracker & Checklist
        function checkPasswordStrength(password) {
            const hasLength = password.length >= 8;
            const hasUpper = /[A-Z]/.test(password);
            const hasLower = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSymbol = /[^A-Za-z0-9]/.test(password);

            updateCheckItem('chk_length', hasLength);
            updateCheckItem('chk_upper', hasUpper);
            updateCheckItem('chk_lower', hasLower);
            updateCheckItem('chk_number', hasNumber);
            updateCheckItem('chk_symbol', hasSymbol);

            checkPasswordConfirmation();

            // Calculate score (0 to 5)
            const score = [hasLength, hasUpper, hasLower, hasNumber, hasSymbol].filter(Boolean).length;
            const label = document.getElementById('strength_label');
            const bars = [
                document.getElementById('bar_1'),
                document.getElementById('bar_2'),
                document.getElementById('bar_3'),
                document.getElementById('bar_4'),
            ];

            // Reset bars
            bars.forEach(b => b.className = 'h-full w-1/4 bg-stone-200 transition-all duration-300');

            if (password.length === 0) {
                label.innerText = 'No Password Entered';
                label.className = 'text-stone-400 font-extrabold uppercase tracking-wider text-[10px]';
            } else if (score <= 2) {
                label.innerText = 'Weak Password';
                label.className = 'text-rose-600 font-black uppercase tracking-wider text-[10px]';
                bars[0].className = 'h-full w-1/4 bg-rose-500 transition-all duration-300';
            } else if (score === 3) {
                label.innerText = 'Fair Password';
                label.className = 'text-amber-500 font-black uppercase tracking-wider text-[10px]';
                bars[0].className = 'h-full w-1/4 bg-amber-400 transition-all duration-300';
                bars[1].className = 'h-full w-1/4 bg-amber-400 transition-all duration-300';
            } else if (score === 4) {
                label.innerText = 'Good Password';
                label.className = 'text-sky-600 font-black uppercase tracking-wider text-[10px]';
                bars[0].className = 'h-full w-1/4 bg-sky-500 transition-all duration-300';
                bars[1].className = 'h-full w-1/4 bg-sky-500 transition-all duration-300';
                bars[2].className = 'h-full w-1/4 bg-sky-500 transition-all duration-300';
            } else if (score === 5) {
                label.innerText = 'Strong Password';
                label.className = 'text-emerald-700 font-black uppercase tracking-wider text-[10px]';
                bars.forEach(b => b.className = 'h-full w-1/4 bg-emerald-600 transition-all duration-300');
            }
        }

        function checkPasswordConfirmation() {
            const pwd = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirmation').value;
            const matches = pwd.length > 0 && confirm.length > 0 && pwd === confirm;
            updateCheckItem('chk_match', matches);
        }

        function updateCheckItem(elementId, isValid) {
            const item = document.getElementById(elementId);
            if (!item) return;

            const iconSpan = item.querySelector('span:first-child');
            if (isValid) {
                item.className = 'flex items-center space-x-1.5 font-bold text-emerald-800 transition';
                iconSpan.className = 'w-3.5 h-3.5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-black';
                iconSpan.innerHTML = '✓';
            } else {
                item.className = 'flex items-center space-x-1.5 text-stone-400 transition';
                iconSpan.className = 'w-3.5 h-3.5 rounded-full bg-stone-200 text-stone-500 flex items-center justify-center text-[9px] font-bold';
                iconSpan.innerHTML = '&bull;';
            }
        }
    </script>
</body>
</html>
