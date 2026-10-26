<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PayMongoController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\ServiceMenuController;
use App\Http\Controllers\StaffTrackerController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Home / Dashboard
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 1. Digital Menu & Price Setter (Customer & Admin)
    Route::get('/menu', [ServiceMenuController::class, 'index'])->name('menu.index');
    Route::get('/services/manage', [ServiceMenuController::class, 'manage'])->name('services.manage');
    Route::patch('/services/{id}/price', [ServiceMenuController::class, 'updatePrice'])->name('services.update-price');
    Route::post('/services', [ServiceMenuController::class, 'store'])->name('services.store');

    // 2. Staff "My Wallet" & Remittance Calculator
    Route::get('/my-wallet', [WalletController::class, 'index'])->name('wallet.index');

    // 3. Staff Availability & Queueing System (Pila)
    Route::prefix('queue')->name('queue.')->group(function () {
        Route::get('/', [QueueController::class, 'index'])->name('index');
        Route::patch('/{id}/status', [QueueController::class, 'updateStatus'])->name('update-status');
        Route::post('/{id}/move/{direction}', [QueueController::class, 'moveQueue'])->name('move');
    });

    // 4. Transactions & Service Orders (In-Shop & Home Service)
    Route::resource('transactions', TransactionController::class)->only(['index', 'create', 'store', 'show']);
    Route::patch('/transactions/{id}/mark-paid', [TransactionController::class, 'markPaid'])->name('transactions.mark-paid');
    Route::patch('/transactions/{id}/mark-completed', [TransactionController::class, 'markCompleted'])->name('transactions.mark-completed');
    Route::patch('/transactions/{id}/payment-method', [TransactionController::class, 'updatePaymentMethod'])->name('transactions.update-payment-method');

    // 5. PayMongo Integration & Simulation
    Route::get('/payments/simulate/{transaction_id}', [PayMongoController::class, 'simulate'])->name('paymongo.simulate');
    Route::post('/payments/simulate/{transaction_id}/confirm', [PayMongoController::class, 'confirmSimulation'])->name('paymongo.confirm-simulation');

    // 6. Overall Staff Tracker & Performance
    Route::get('/staff-tracker', [StaffTrackerController::class, 'index'])->name('staff-tracker.index');
    Route::get('/staff-tracker/{id}', [StaffTrackerController::class, 'showDetails'])->name('staff-tracker.show');
    Route::patch('/staff-tracker/{id}/role', [StaffTrackerController::class, 'updateEmployeeRole'])->name('staff-tracker.update-role');

    // 7. Employee & Admin Profile & Account Settings
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');

    // 8. Payouts & Cashout (Shop Profit & Staff Commission)
    Route::post('/payouts/admin', [\App\Http\Controllers\PayoutController::class, 'storeAdminPayout'])->name('payouts.admin');
    Route::post('/payouts/staff', [\App\Http\Controllers\PayoutController::class, 'storeStaffPayout'])->name('payouts.staff');
    Route::delete('/payouts/{id}/void', [\App\Http\Controllers\PayoutController::class, 'voidPayout'])->name('payouts.void');
});
