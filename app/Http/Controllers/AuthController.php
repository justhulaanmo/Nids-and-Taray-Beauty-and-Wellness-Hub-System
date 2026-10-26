<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\StaffProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'The provided email or password is incorrect.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'nickname' => 'required|string|max:50',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^09[0-9]{9}$/',
            ],
            'specialization' => 'required|in:all_around,manicurist,massage_therapist',
            'password' => [
                'required',
                'string',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.regex' => 'Please enter a valid email format (e.g. name@gmail.com).',
            'phone.regex' => 'Mobile number must start with 09 and contain exactly 11 digits (e.g. 09171234567).',
            'password.min' => 'Password must be at least 8 characters long.',
        ]);

        $fullName = trim(ucwords(strtolower($validated['first_name'])) . ' ' . ucwords(strtolower($validated['last_name'])));
        $nickname = trim(ucwords(strtolower($validated['nickname'])));

        // Create User account
        $user = User::create([
            'name' => $fullName,
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'role' => 'staff',
            'phone' => $validated['phone'],
            'is_active' => true,
        ]);

        // Next queue position
        $nextQueue = (StaffProfile::max('queue_position') ?? 0) + 1;

        // Create Staff Profile in rotation queue
        StaffProfile::create([
            'user_id' => $user->id,
            'name' => $fullName,
            'nickname' => $nickname,
            'phone' => $validated['phone'],
            'specialization' => $validated['specialization'],
            'status' => 'available',
            'queue_position' => $nextQueue,
            'commission_rate' => 40.00,
            'is_active' => true,
            'last_status_change' => now(),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Welcome to Nids & Taray, ' . $nickname . '! You are now registered and in the queue.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('info', 'You have been logged out successfully.');
    }

    public function showForgotPassword()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.forgot-password');
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.exists' => 'No employee account found with this email address.',
        ]);

        $user = User::where('email', $validated['email'])->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Account not found.'])->onlyInput('email');
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('login')->with('success', 'Password reset successfully! You can now log in with your new password.');
    }
}
