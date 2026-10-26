<?php

namespace App\Http\Controllers;

use App\Models\StaffProfile;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedDate = $request->input('date', Carbon::today()->format('Y-m-d'));
        $targetDate = Carbon::parse($selectedDate);

        // Find staff profile for this user
        $staffProfile = StaffProfile::where('user_id', $user->id)->first();

        // If admin doesn't have a staff profile yet, pick or let admin view their own or all
        if (!$staffProfile && $user->role === 'admin') {
            // Check if there's a staff profile with admin's name or pick first
            $staffProfile = StaffProfile::where('name', 'like', '%Nida%')
                ->orWhere('name', 'like', '%Admin%')
                ->first() ?? StaffProfile::first();
        }

        if (!$staffProfile) {
            return redirect()->route('dashboard')->with('info', 'No staff profile is linked to your account.');
        }

        // Get items serviced by this staff on selected date
        $items = TransactionItem::where('staff_id', $staffProfile->id)
            ->whereHas('transaction', function ($q) use ($targetDate) {
                $q->whereDate('created_at', $targetDate)->where('status', '!=', 'cancelled');
            })
            ->with(['service', 'transaction'])
            ->latest()
            ->get();

        // Calculate Net Commission per transaction item:
        // Formula: (Gross Service Price - PayMongo Fees if applicable) * Staff Commission Rate
        foreach ($items as $item) {
            $isPayMongo = $item->transaction && in_array($item->transaction->payment_method, ['paymongo_qr', 'paymongo_link']);
            $paymongoFee = $isPayMongo ? round((float) $item->price * 0.025, 2) : 0.0;
            $netBase = max(0, (float) $item->price - $paymongoFee);
            $commRate = (float) ($item->commission_rate ?: $staffProfile->commission_rate);
            
            $item->paymongo_fee = $paymongoFee;
            $item->net_commission = round(($netBase * $commRate) / 100, 2);
        }

        $totalCustomersCount = $items->pluck('transaction_id')->unique()->count();
        $totalServicesCount = $items->count();

        // My Take-Home Earnings Today (Only paid transactions)
        $todayTakeHomeEarnings = (float) $items->where('transaction.payment_status', 'paid')->sum('net_commission');
        $totalSalesHandled = (float) $items->sum('price');

        // All-Time Commission Calculation with Net formula
        $allTimeItems = TransactionItem::where('staff_id', $staffProfile->id)
            ->whereHas('transaction', function ($q) {
                $q->where('payment_status', 'paid')->where('status', '!=', 'cancelled');
            })
            ->with('transaction')
            ->get();

        $totalEarnedAllTime = (float) $allTimeItems->sum(function ($item) use ($staffProfile) {
            $isPayMongo = $item->transaction && in_array($item->transaction->payment_method, ['paymongo_qr', 'paymongo_link']);
            $paymongoFee = $isPayMongo ? round((float) $item->price * 0.025, 2) : 0.0;
            $netBase = max(0, (float) $item->price - $paymongoFee);
            $rate = (float) ($item->commission_rate ?: $staffProfile->commission_rate);
            return round(($netBase * $rate) / 100, 2);
        });

        $totalStaffWithdrawn = (float) \App\Models\Payout::where('staff_id', $staffProfile->id)
            ->where('status', 'completed')
            ->sum('gross_amount');

        $pendingPayouts = (float) \App\Models\Payout::where('staff_id', $staffProfile->id)
            ->where('status', 'pending')
            ->sum('gross_amount');

        $availableCommissionBalance = max(0, round($totalEarnedAllTime - $totalStaffWithdrawn - $pendingPayouts, 2));
        $staffPayouts = \App\Models\Payout::where('staff_id', $staffProfile->id)->latest()->take(5)->get();

        return view('wallet.index', compact(
            'staffProfile',
            'items',
            'selectedDate',
            'totalCustomersCount',
            'totalServicesCount',
            'totalSalesHandled',
            'todayTakeHomeEarnings',
            'totalEarnedAllTime',
            'totalStaffWithdrawn',
            'availableCommissionBalance',
            'staffPayouts'
        ));
    }
}
