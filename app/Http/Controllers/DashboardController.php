<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Payout;
use App\Models\StaffProfile;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $today = Carbon::today();
        $user = Auth::user();

        // 1. Staff / User Profile
        $isStaff = $user->role === 'staff';
        $myProfile = StaffProfile::where('user_id', $user->id)->first();
        if (!$myProfile && $isStaff) {
            $myProfile = StaffProfile::where('name', $user->name)->first();
        }

        // 2. Daily Core Metrics
        $todayTransactions = Transaction::whereDate('created_at', $today)->where('status', '!=', 'cancelled')->get();
        $totalCustomersToday = $todayTransactions->count();
        $inShopCustomersToday = $todayTransactions->where('service_type', 'in_shop')->count();
        $homeServiceCustomersToday = $todayTransactions->where('service_type', 'home_service')->count();

        $paidTransactions = $todayTransactions->where('payment_status', 'paid');
        $totalSalesToday = (float) $paidTransactions->sum('total_amount');
        $cashSalesToday = (float) $paidTransactions->where('payment_method', 'cash')->sum('total_amount');
        $qrSalesToday = (float) $paidTransactions->whereIn('payment_method', ['paymongo_qr', 'paymongo_link'])->sum('total_amount');

        $todayPaymongoFees = round($qrSalesToday * 0.025, 2);
        $todayCommissions = (float) TransactionItem::whereHas('transaction', function ($q) use ($today) {
            $q->whereDate('created_at', $today)->where('payment_status', 'paid')->where('status', '!=', 'cancelled');
        })->sum('staff_commission_amount');
        $todayNetShopProfit = max(0, $totalSalesToday - $todayCommissions - $todayPaymongoFees);

        // 3. Monthly Net Profit & Expenses Tracker
        $selectedMonth = $request->input('month', Carbon::now()->format('Y-m'));
        $monthlyFinancials = $this->getMonthlyFinancials($selectedMonth);

        // 4. All-Time Payout / Cashout Metrics
        $allTimePaidTransactions = Transaction::where('payment_status', 'paid')->where('status', '!=', 'cancelled')->get();
        $allTimeGross = (float) $allTimePaidTransactions->sum('total_amount');
        $allTimeQR = (float) $allTimePaidTransactions->whereIn('payment_method', ['paymongo_qr', 'paymongo_link'])->sum('total_amount');
        $allTimePaymongoFees = round($allTimeQR * 0.025, 2);
        $allTimeCommissions = (float) TransactionItem::whereHas('transaction', fn($q) => $q->where('payment_status', 'paid')->where('status', '!=', 'cancelled'))->sum('staff_commission_amount');
        $allTimeNetShopProfit = max(0, $allTimeGross - $allTimeCommissions - $allTimePaymongoFees);
        
        $totalCashedOut = (float) Payout::where('type', 'admin_shop')->where('status', 'completed')->sum('gross_amount');
        $availableForCashout = max(0, $allTimeNetShopProfit - $totalCashedOut);
        $recentPayouts = Payout::where('type', 'admin_shop')->latest()->take(5)->get();

        // 5. Staff Remittances & Queues
        $staffProfiles = $this->getStaffRemittances($today);
        $queueAvailable = StaffProfile::where('is_active', true)->where('status', 'available')->orderBy('queue_position')->get();
        $servingInShop = StaffProfile::where('is_active', true)->where('status', 'serving_in_shop')->get();
        $onHomeService = StaffProfile::where('is_active', true)->where('status', 'on_home_service')->get();
        $onBreak = StaffProfile::where('is_active', true)->where('status', 'on_break')->get();

        // 6. Recent Transactions
        $recentTransactions = Transaction::with(['items.service', 'items.staff'])
            ->latest()
            ->take(6)
            ->get();

        // 7. Staff Personal Stats & Active Jobs
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

            $myActiveJob = TransactionItem::where('staff_id', $myProfile->id)
                ->whereHas('transaction', fn($q) => $q->where('status', 'in_progress'))
                ->with(['transaction', 'service'])
                ->latest()
                ->first();

            if ($myProfile->status === 'available') {
                $queueIndex = $queueAvailable->search(fn($s) => $s->id === $myProfile->id);
                if ($queueIndex !== false) {
                    $myQueueTurn = $queueIndex + 1;
                }
            }
        }

        return view('dashboard.index', array_merge([
            'isStaff' => $isStaff,
            'myProfile' => $myProfile,
            'myPersonalStats' => $myPersonalStats,
            'myActiveJob' => $myActiveJob,
            'myQueueTurn' => $myQueueTurn,
            'totalCustomersToday' => $totalCustomersToday,
            'inShopCustomersToday' => $inShopCustomersToday,
            'homeServiceCustomersToday' => $homeServiceCustomersToday,
            'totalSalesToday' => $totalSalesToday,
            'cashSalesToday' => $cashSalesToday,
            'qrSalesToday' => $qrSalesToday,
            'todayPaymongoFees' => $todayPaymongoFees,
            'todayCommissions' => $todayCommissions,
            'todayNetShopProfit' => $todayNetShopProfit,
            'allTimeGross' => $allTimeGross,
            'allTimePaymongoFees' => $allTimePaymongoFees,
            'allTimeCommissions' => $allTimeCommissions,
            'allTimeNetShopProfit' => $allTimeNetShopProfit,
            'totalCashedOut' => $totalCashedOut,
            'availableForCashout' => $availableForCashout,
            'recentPayouts' => $recentPayouts,
            'staffProfiles' => $staffProfiles,
            'queueAvailable' => $queueAvailable,
            'servingInShop' => $servingInShop,
            'onHomeService' => $onHomeService,
            'onBreak' => $onBreak,
            'recentTransactions' => $recentTransactions,
        ], $monthlyFinancials));
    }

    /**
     * Calculate Gross Sales, Deductions, OPEX, and True Net Profit for a month
     */
    private function getMonthlyFinancials(string $selectedMonth): array
    {
        $monthDate = Carbon::parse($selectedMonth . '-01');
        $monthYear = $monthDate->year;
        $monthNum = $monthDate->month;

        $monthTransactions = Transaction::whereYear('created_at', $monthYear)
            ->whereMonth('created_at', $monthNum)
            ->where('payment_status', 'paid')
            ->where('status', '!=', 'cancelled')
            ->get();

        $monthGrossSales = (float) $monthTransactions->sum('total_amount');
        $monthQR = (float) $monthTransactions->whereIn('payment_method', ['paymongo_qr', 'paymongo_link'])->sum('total_amount');
        $monthPaymongoFees = round($monthQR * 0.025, 2);

        $monthCommissions = (float) TransactionItem::whereHas('transaction', function ($q) use ($monthYear, $monthNum) {
            $q->whereYear('created_at', $monthYear)
              ->whereMonth('created_at', $monthNum)
              ->where('payment_status', 'paid')
              ->where('status', '!=', 'cancelled');
        })->sum('staff_commission_amount');

        $monthTotalDeductions = $monthCommissions + $monthPaymongoFees;
        $monthGrossProfit = max(0, $monthGrossSales - $monthTotalDeductions);

        $monthExpensesList = Expense::whereYear('expense_date', $monthYear)
            ->whereMonth('expense_date', $monthNum)
            ->latest('expense_date')
            ->get();

        $monthTotalOPEX = (float) $monthExpensesList->sum('amount');
        $monthTrueNetProfit = $monthGrossProfit - $monthTotalOPEX;

        return [
            'selectedMonth' => $selectedMonth,
            'monthGrossSales' => $monthGrossSales,
            'monthCommissions' => $monthCommissions,
            'monthPaymongoFees' => $monthPaymongoFees,
            'monthTotalDeductions' => $monthTotalDeductions,
            'monthGrossProfit' => $monthGrossProfit,
            'monthExpensesList' => $monthExpensesList,
            'monthTotalOPEX' => $monthTotalOPEX,
            'monthTrueNetProfit' => $monthTrueNetProfit,
        ];
    }

    /**
     * Tally daily staff remittances, services, and commission breakdown
     */
    private function getStaffRemittances(Carbon $today): Collection
    {
        return StaffProfile::where('is_active', true)
            ->with(['transactionItems' => function ($q) use ($today) {
                $q->whereHas('transaction', function ($sub) use ($today) {
                    $sub->whereDate('created_at', $today)->where('status', '!=', 'cancelled');
                })->with(['service', 'transaction']);
            }])
            ->get()
            ->map(function ($staff) {
                $uniqueCustomers = $staff->transactionItems->pluck('transaction_id')->unique()->count();
                $totalServices = $staff->transactionItems->count();

                $paidItems = $staff->transactionItems->filter(fn($i) => $i->transaction && $i->transaction->payment_status === 'paid');
                $myCommission = (float) $paidItems->sum('staff_commission_amount');

                $cashItems = $paidItems->filter(fn($i) => $i->transaction && $i->transaction->payment_method === 'cash');
                $cashCollected = (float) $cashItems->sum('price');
                $cashCommission = (float) $cashItems->sum('staff_commission_amount');
                $cashToRemit = $cashCollected - $cashCommission;

                $staff->customers_count = $uniqueCustomers;
                $staff->services_count = $totalServices;
                $staff->commission_total = $myCommission;
                $staff->cash_collected = $cashCollected;
                $staff->cash_to_remit = $cashToRemit;
                return $staff;
            })
            ->sortByDesc('customers_count');
    }
}
