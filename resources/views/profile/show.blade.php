@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header Banner -->
    <div class="bg-white rounded-3xl border border-stone-200/80 p-5 sm:p-6 shadow-2xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-700 to-teal-600 text-white font-black text-xl flex items-center justify-center shadow-xs flex-shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-stone-900 tracking-tight">
                    {{ $user->name }}
                </h1>
                <div class="flex items-center space-x-2 mt-0.5">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200">
                        @if($user->role === 'admin')
                            Administrator
                        @elseif($staffProfile && $staffProfile->specialization)
                            {{ str_replace('_', ' ', $staffProfile->specialization) }}
                        @else
                            Employee
                        @endif
                    </span>
                    @if($staffProfile)
                        <span class="text-xs text-stone-400 font-semibold">&bull; {{ $staffProfile->commission_rate }}% Commission</span>
                    @endif
                </div>
            </div>
        </div>

        <a href="{{ route('dashboard') }}" class="px-3.5 py-2 rounded-xl border border-stone-200 hover:bg-stone-50 text-stone-700 text-xs font-bold transition inline-flex items-center space-x-1.5 self-start sm:self-auto">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Back to Dashboard</span>
        </a>
    </div>

    <!-- 1. Personal & Contact Information Form -->
    <div class="bg-white rounded-3xl border border-stone-200/80 p-5 sm:p-6 shadow-2xs space-y-4">
        <div class="border-b border-stone-100 pb-3">
            <h2 class="text-sm font-black uppercase tracking-wider text-stone-900 flex items-center space-x-2">
                <i data-lucide="user" class="w-4 h-4 text-emerald-800"></i>
                <span>Personal & Account Information</span>
            </h2>
            <p class="text-xs text-stone-500 mt-0.5">
                Update your name, contact phone number, and display nickname.
            </p>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Full Legal Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-xs font-bold text-stone-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                </div>

                @if($staffProfile)
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">Display Nickname in Salon</label>
                        <input type="text" name="nickname" value="{{ old('nickname', $staffProfile->nickname) }}"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-xs font-bold text-stone-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-xs font-bold text-stone-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Mobile Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="09123456789"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-xs font-bold text-stone-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition inline-flex items-center space-x-1.5 active:scale-[0.98]">
                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                    <span>Save Information</span>
                </button>
            </div>
        </form>
    </div>

    <!-- 2. Change Password Form -->
    <div class="bg-white rounded-3xl border border-stone-200/80 p-5 sm:p-6 shadow-2xs space-y-4">
        <div class="border-b border-stone-100 pb-3">
            <h2 class="text-sm font-black uppercase tracking-wider text-stone-900 flex items-center space-x-2">
                <i data-lucide="lock" class="w-4 h-4 text-emerald-800"></i>
                <span>Change Password</span>
            </h2>
            <p class="text-xs text-stone-500 mt-0.5">
                Ensure your account is protected with a secure password.
            </p>
        </div>

        <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">Current Password</label>
                <input type="password" name="current_password" required placeholder="••••••••"
                    class="w-full sm:max-w-md px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-xs font-bold text-stone-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">New Password</label>
                    <input type="password" name="password" required placeholder="Minimum 6 characters"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-xs font-bold text-stone-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required placeholder="Repeat new password"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-stone-50 border border-stone-200 text-xs font-bold text-stone-900 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition inline-flex items-center space-x-1.5 active:scale-[0.98]">
                    <i data-lucide="key" class="w-3.5 h-3.5"></i>
                    <span>Update Password</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
