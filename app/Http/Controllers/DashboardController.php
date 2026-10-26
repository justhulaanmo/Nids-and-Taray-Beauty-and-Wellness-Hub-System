<?php

namespace App\Http\Controllers;

use App\Models\StaffProfile;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $user = Auth::user();

        // 1. Check if user is staff or admin
        $isStaff = $user->role === 'staff';
        $myProfile = StaffProfile::where('user_id', $user->id)->first();

        // If staff user without a profile, fallback to first staff with same email/name
        if (!$myProfile && $isStaff) {
            $myProfile = StaffProfile::where('name', $user->name)->first();
        }

        // 2. Core Metrics for Today
        $todayTransactions = Transaction::whereDate('created_at', $today)->where('status', '!=', 'cancelled')->get();
        
        $totalCustomersToday = $todayTransactions->count();
        $inShopCustomersToday = $todayTransactions->where('service_type', 'in_shop')->count();
        $homeServiceCustomersToday = $todayTransactions->where('service_type', 'home_service')->count();

        // Financials (Paid only)
        $paidTransactions = $todayTransactions->where('payment_status', 'paid');
        $totalSalesToday = $paidTransactions->sum('total_amount');
        $cashSalesToday = $paidTransactions->where('payment_method', 'cash')->sum('total_amount');
        $qrSalesToday = $paidTransactions->whereIn('payment_method', ['paymongo_qr', 'paymongo_link'])->sum('total_amount');

        // Net Revenue Calculations: Gross - Commissions - PayMongo Fees (2.5% on QR)
        $todayPaymongoFees = round($qrSalesToday * 0.025, 2);
        $todayCommissions = (float) TransactionItem::whereHas('transaction', function ($q) use ($today) {
            $q->whereDate('created_at', $today)->where('payment_status', 'paid')->where('status', '!=', 'cancelled');
        })->sum('staff_commission_amount');
        $todayNetShopProfit = max(0, $totalSalesToday - $todayCommissions - $todayPaymongoFees);

        // All-Time Payout / Cashout Metrics (for Admin Cashout Button)
        $allTimePaidTransactions = Transaction::where('payment_status', 'paid')->where('status', '!=', 'cancelled')->get();
        $allTimeGross = (float) $allTimePaidTransactions->sum('total_amount');
        $allTimeQR = (float) $allTimePaidTransactions->whereIn('payment_method', ['paymongo_qr', 'paymongo_link'])->sum('total_amount');
        $allTimePaymongoFees = round($allTimeQR * 0.025, 2);
        $allTimeCommissions = (float) TransactionItem::whereHas('transaction', fn($q) => $q->where('payment_status', 'paid')->where('status', '!=', 'cancelled'))->sum('staff_commission_amount');
        $allTimeNetShopProfit = max(0, $allTimeGross - $allTimeCommissions - $allTimePaymongoFees);
        
        $totalCashedOut = (float) \App\Models\Payout::where('type', 'admin_shop')->where('status', 'completed')->sum('gross_amount');
        $availableForCashout = max(0, $allTimeNetShopProfit - $totalCashedOut);
        $recentPayouts = \App\Models\Payout::where('type', 'admin_shop')->latest()->take(5)->get();

        // 3. Remittance & Staff Tally (Shop Share vs Staff Commissions)
        $staffProfiles = StaffProfile::where('is_active', true)
            ->with(['transactionItems' => function ($q) use ($today) {
                $q->whereHas('transaction', function ($sub) use ($today) {
                    $sub->whereDate('created_at', $today)->where('status', '!=', 'cancelled');
                })->with(['service', 'transaction']);
            }])
            ->get()
            ->map(function ($staff) {
                $uniqueCustomers = $staff->transactionItems->pluck('transaction_id')->unique()->count();
                $totalServices = $staff->transactionItems->count();
                
                // Financials
                $paidItems = $staff->transactionItems->filter(fn($i) => $i->transaction && $i->transaction->payment_status === 'paid');
                $myCommission = $paidItems->sum('staff_commission_amount');
                
                // Cash collected on hand
                $cashItems = $paidItems->filter(fn($i) => $i->transaction && $i->transaction->payment_method === 'cash');
                $cashCollected = $cashItems->sum('price');
                $cashCommission = $cashItems->sum('staff_commission_amount');
                
                // Net cash to remit to shop = cash collected minus commission on cash
                $cashToRemit = $cashCollected - $cashCommission;

                $staff->customers_count = $uniqueCustomers;
                $staff->services_count = $totalServices;
                $staff->commission_total = $myCommission;
                $staff->cash_collected = $cashCollected;
                $staff->cash_to_remit = $cashToRemit;
                return $staff;
            })
            ->sortByDesc('customers_count');

        // 4. Queue Snapshot
        $queueAvailable = StaffProfile::where('is_active', true)->where('status', 'available')->orderBy('queue_position')->get();
        $servingInShop = StaffProfile::where('is_active', true)->where('status', 'serving_in_shop')->get();
        $onHomeService = StaffProfile::where('is_active', true)->where('status', 'on_home_service')->get();
        $onBreak = StaffProfile::where('is_active', true)->where('status', 'on_break')->get();

        // 5. Recent Transactions
        $recentTransactions = Transaction::with(['items.service', 'items.staff'])
            ->latest()
            ->take(6)
            ->get();

        // 6. For Staff Personal View:
        $myPersonalStats = null;
        $myActiveJob = null;
        $myQueueTurn = null;

        if ($myProfile) {
            $myStaffRecord = $staffProfiles->firstWhere('id', $myProfile->id);
            if ($myStaffRecord) {
                $myPersonalStats = [
                    'profile' => $myProfile,
                    'customers_today' => $myStaffRecord->customers_count,
                    'commission_today' => $myStaffRecord->commission_total,
                    'cash_collected' => $myStaffRecord->cash_collected,
                    'cash_to_remit' => $myStaffRecord->cash_to_remit,
                    'queue_position' => $myProfile->queue_position,
                    'status' => $myProfile->status,
                ];
            }

            // Find current active/in-progress job for this staff
            $myActiveJob = TransactionItem::where('staff_id', $myProfile->id)
                ->whereHas('transaction', function ($q) {
                    $q->where('status', 'in_progress');
                })
                ->with(['transaction', 'service'])
                ->latest()
                ->first();

            // Calculate turn number among available staff
            if ($myProfile->status === 'available') {
                $queueIndex = $queueAvailable->search(fn($s) => $s->id === $myProfile->id);
                if ($queueIndex !== false) {
                    $myQueueTurn = $queueIndex + 1;
                }
            }
        }

        return view('dashboard.index', compact(
            'isStaff',
            'myProfile',
            'myPersonalStats',
            'myActiveJob',
            'myQueueTurn',
            'totalCustomersToday',
            'inShopCustomersToday',
            'homeServiceCustomersToday',
            'totalSalesToday',
            'cashSalesToday',
            'qrSalesToday',
            'todayPaymongoFees',
            'todayCommissions',
            'todayNetShopProfit',
            'allTimeGross',
            'allTimePaymongoFees',
            'allTimeCommissions',
            'allTimeNetShopProfit',
            'totalCashedOut',
            'availableForCashout',
            'recentPayouts',
            'staffProfiles',
            'queueAvailable',
            'servingInShop',
            'onHomeService',
            'onBreak',
            'recentTransactions'
        ));
    }
}
